<?php

namespace App\Http\Controllers;

use App\Models\MealAssignment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MealController
{
    public function index(): View
    {
        $students = Student::with('mealAssignments')->alphabetical()->get();
        $rows = $students->map(function (Student $student) {
            $row = [
                'id' => $student->id,
                'name' => $student->full_name,
                'classroom' => $student->classroom,
                'shift' => Student::SHIFTS[$student->school_shift],
                'active' => $student->active,
            ];

            foreach ($student->mealAssignments as $assignment) {
                $row["d{$assignment->weekday}_{$assignment->meal}"] = true;
            }

            return $row;
        });

        return view('meals.index', [
            'rows' => $rows,
            'weekdays' => MealAssignment::WEEKDAYS,
            'meals' => MealAssignment::MEALS,
        ]);
    }

    public function update(Request $request): Response
    {
        $data = $request->validate([
            'changes' => ['required', 'array', 'list'],
            'changes.*' => ['required', 'array:student_id,weekday,meal,assigned'],
            'changes.*.student_id' => ['required', 'integer', 'min:1'],
            'changes.*.weekday' => ['required', 'integer', 'between:1,5'],
            'changes.*.meal' => ['required', Rule::in(array_keys(MealAssignment::MEALS))],
            'changes.*.assigned' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($data) {
            $studentIds = array_unique(array_column($data['changes'], 'student_id'));
            if (Student::whereIntegerInRaw('id', $studentIds)->count() !== count($studentIds)) {
                throw ValidationException::withMessages([
                    'changes' => 'Algún alumno fue eliminado. Recargá la grilla y volvé a editar las asignaciones.',
                ]);
            }

            foreach ($data['changes'] as $change) {
                $key = [
                    'student_id' => $change['student_id'],
                    'weekday' => $change['weekday'],
                    'meal' => $change['meal'],
                ];

                if ($change['assigned']) {
                    DB::table('meal_assignments')->upsert([$key], array_keys($key), ['meal']);
                } else {
                    DB::table('meal_assignments')->where($key)->delete();
                }
            }
        });

        return response()->noContent();
    }
}
