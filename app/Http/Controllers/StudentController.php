<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController
{
    public function index(Request $request): View
    {
        $search = trim($request->string('buscar')->toString());
        $state = $request->string('estado')->toString();
        $shift = $request->string('turno')->toString();

        $students = Student::query()
            ->when($search !== '', function ($query) use ($search) {
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';
                $query->where(function ($query) use ($pattern) {
                    $query->whereRaw("(first_name || ' ' || last_name) LIKE ? ESCAPE '!'", [$pattern])
                        ->orWhereRaw("(last_name || ' ' || first_name) LIKE ? ESCAPE '!'", [$pattern])
                        ->orWhereRaw("classroom LIKE ? ESCAPE '!'", [$pattern]);
                });
            })
            ->when(in_array($state, ['active', 'inactive'], true), fn ($query) => $query->where('active', $state === 'active'))
            ->when(isset(Student::SHIFTS[$shift]), fn ($query) => $query->where('school_shift', $shift))
            ->alphabetical()
            ->paginate(25)
            ->withQueryString();

        return view('students.index', compact('students', 'search', 'state', 'shift'));
    }

    public function create(): View
    {
        return view('students.form', ['student' => new Student(['active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validatedStudent($request));

        return to_route('students.index')->with('status', 'Alumno registrado.');
    }

    public function edit(Student $student): View
    {
        return view('students.form', compact('student'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validatedStudent($request));

        return to_route('students.index')->with('status', 'Alumno actualizado.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return to_route('students.index')->with('status', 'Alumno eliminado.');
    }

    private function validatedStudent(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'classroom' => ['required', 'string', 'max:80'],
            'school_shift' => ['required', Rule::in(array_keys(Student::SHIFTS))],
            'contact' => ['nullable', 'string', 'max:160'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'active' => ['required', 'boolean'],
        ]);
    }
}
