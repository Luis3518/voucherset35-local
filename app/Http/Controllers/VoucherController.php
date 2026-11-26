<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\VoucherGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VoucherController
{
    public function index(): View
    {
        $current = CarbonImmutable::today()->startOfMonth();
        $next = $current->addMonth();

        return view('vouchers.index', [
            'students' => Student::where('active', true)->alphabetical()->get(),
            'periods' => [
                'current' => [$current->toDateString(), $current->endOfMonth()->toDateString()],
                'next' => [$next->toDateString(), $next->endOfMonth()->toDateString()],
            ],
        ]);
    }

    public function generate(Request $request, VoucherGenerator $generator): Response
    {
        $data = $request->validate([
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'student_ids' => ['required', 'array', 'list'],
            'student_ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ], ['student_ids.required' => 'Seleccioná al menos un alumno.']);

        $students = Student::where('active', true)
            ->whereIntegerInRaw('id', $data['student_ids'])
            ->with('mealAssignments')
            ->alphabetical()
            ->get();

        if ($students->count() !== count($data['student_ids'])) {
            throw ValidationException::withMessages([
                'student_ids' => 'Algún alumno ya no está activo. Recargá la página y revisá la selección.',
            ]);
        }

        $vouchers = $generator->generate(
            $students,
            CarbonImmutable::parse($data['starts_on']),
            CarbonImmutable::parse($data['ends_on']),
        );

        if ($vouchers->isEmpty()) {
            throw ValidationException::withMessages([
                'student_ids' => 'No hay comidas asignadas para alumnos con beca vigente en los días hábiles del período.',
            ]);
        }

        $filename = "vouchers-{$data['starts_on']}_{$data['ends_on']}.pdf";

        $pages = $vouchers->groupBy('student_id')->flatMap(fn ($studentVouchers) => $studentVouchers->chunk(12));

        return Pdf::loadView('vouchers.pdf', ['pages' => $pages])
            ->setPaper('a4')
            ->stream($filename)
            ->header('X-Voucher-Count', (string) $vouchers->count())
            ->header('Cache-Control', 'no-store, private');
    }
}
