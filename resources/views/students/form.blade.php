@extends('layout')
@section('title', $student->exists ? 'Editar alumno' : 'Nuevo alumno')

@section('content')
    <a class="back-link" href="{{ route('students.index') }}">← Volver a alumnos</a>
    <div class="page-heading"><h1>{{ $student->exists ? 'Editar alumno' : 'Nuevo alumno' }}</h1></div>

    <div class="panel panel-body form-panel">
        <form method="post" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}">
            @csrf
            @if ($student->exists)
                @method('PUT')
            @endif
            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="first_name" class="form-label">Nombre</label>
                    <input id="first_name" name="first_name" class="form-control" maxlength="80" required value="{{ old('first_name', $student->first_name) }}">
                </div>
                <div class="col-sm-6">
                    <label for="last_name" class="form-label">Apellido</label>
                    <input id="last_name" name="last_name" class="form-control" maxlength="80" required value="{{ old('last_name', $student->last_name) }}">
                </div>
                <div class="col-sm-6">
                    <label for="classroom" class="form-label">Curso / división</label>
                    <input id="classroom" name="classroom" class="form-control" maxlength="80" required value="{{ old('classroom', $student->classroom) }}">
                </div>
                <div class="col-sm-6">
                    <label for="school_shift" class="form-label">Turno escolar</label>
                    <select id="school_shift" name="school_shift" class="form-select" required>
                        <option value="">Seleccionar turno</option>
                        @foreach (\App\Models\Student::SHIFTS as $value => $label)
                            <option value="{{ $value }}" @selected(old('school_shift', $student->school_shift) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label for="contact" class="form-label">Contacto <span class="text-secondary fw-normal">(opcional)</span></label>
                    <input id="contact" name="contact" class="form-control" maxlength="160" value="{{ old('contact', $student->contact) }}">
                </div>
                <div class="col-sm-6">
                    <label for="starts_on" class="form-label">Inicio de la beca</label>
                    <input type="date" id="starts_on" name="starts_on" class="form-control" required value="{{ old('starts_on', $student->starts_on?->toDateString()) }}">
                </div>
                <div class="col-sm-6">
                    <label for="ends_on" class="form-label">Fin de la beca <span class="text-secondary fw-normal">(opcional)</span></label>
                    <input type="date" id="ends_on" name="ends_on" class="form-control" value="{{ old('ends_on', $student->ends_on?->toDateString()) }}">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" id="active" name="active" value="1" class="form-check-input" @checked(old('active', $student->active))>
                        <label for="active" class="form-check-label">Alumno activo</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary">Guardar alumno</button>
                <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Cancelar</a>
            </div>
        </form>
        @if ($student->exists)
            <form class="mt-4 pt-3 border-top" method="post" action="{{ route('students.destroy', $student) }}"
                data-confirm="¿Eliminar definitivamente a {{ $student->full_name }} y sus asignaciones de comidas?">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">Eliminar alumno</button>
            </form>
        @endif
    </div>
@endsection
