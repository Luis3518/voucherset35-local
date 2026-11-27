<?php

namespace App\Http\Controllers;

use App\Models\CalendarExclusion;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController
{
    public function importHolidays(Request $request): RedirectResponse
    {
        $data = $request->validate(['year' => ['required', 'integer', 'min:2016', 'max:2100']]);
        $year = (int) $data['year'];

        try {
            $response = Http::timeout(15)->get("https://api.argentinadatos.com/v1/feriados/{$year}");
        } catch (ConnectionException $exception) {
            return to_route('calendar.index')->withErrors(['year' => 'No se pudo consultar ArgentinaDatos. Intentá nuevamente.']);
        }

        $holidays = $response->json();
        if (! $response->successful() || ! is_array($holidays) || ! array_is_list($holidays) || $holidays === [] ||
            Validator::make(['holidays' => $holidays], [
                'holidays.*.fecha' => ['required', 'date_format:Y-m-d', "after_or_equal:{$year}-01-01", "before_or_equal:{$year}-12-31"],
                'holidays.*.nombre' => ['required', 'string', 'max:120'],
            ])->fails()) {
            return to_route('calendar.index')->withErrors(['year' => 'ArgentinaDatos no devolvió feriados válidos para ese año.']);
        }

        $count = DB::transaction(function () use ($holidays) {
            $count = 0;
            foreach ($holidays as $holiday) {
                $date = $holiday['fecha'];
                if (CalendarExclusion::whereDate('starts_on', '<=', $date)->whereDate('ends_on', '>=', $date)->exists()) {
                    continue;
                }
                CalendarExclusion::create(['kind' => 'holiday', 'description' => $holiday['nombre'], 'starts_on' => $date, 'ends_on' => $date]);
                $count++;
            }

            return $count;
        });

        return to_route('calendar.index')->with('status', "Se agregaron {$count} feriados de {$year}. Las fechas ya excluidas se conservaron.");
    }

    public function index(): View
    {
        $exclusions = CalendarExclusion::orderByDesc('starts_on')->orderByDesc('id')->paginate(25);

        return view('calendar.index', compact('exclusions'));
    }

    public function create(): View
    {
        return view('calendar.form', ['exclusion' => new CalendarExclusion]);
    }

    public function store(Request $request): RedirectResponse
    {
        CalendarExclusion::create($this->validatedExclusion($request));

        return to_route('calendar.index')->with('status', 'Exclusión registrada.');
    }

    public function edit(CalendarExclusion $exclusion): View
    {
        return view('calendar.form', compact('exclusion'));
    }

    public function update(Request $request, CalendarExclusion $exclusion): RedirectResponse
    {
        $exclusion->update($this->validatedExclusion($request));

        return to_route('calendar.index')->with('status', 'Exclusión actualizada.');
    }

    public function destroy(CalendarExclusion $exclusion): RedirectResponse
    {
        $exclusion->delete();

        return to_route('calendar.index')->with('status', 'Exclusión eliminada.');
    }

    private function validatedExclusion(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(array_keys(CalendarExclusion::KINDS))],
            'description' => ['required', 'string', 'max:120'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
        ]);

        $data['ends_on'] = $data['ends_on'] ?? $data['starts_on'];

        return $data;
    }
}
