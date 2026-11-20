@extends('layout')
@section('title', 'Alumnos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Alumnos</h1>
            <p>Administrá el padrón y la vigencia de cada beca.</p>
        </div>
        <a href="{{ route('students.create') }}" class="btn btn-primary">Nuevo alumno</a>
    </div>

    <section class="panel" aria-label="Padrón de alumnos">
        <form method="get" class="row g-3 align-items-end panel-body border-bottom">
            <div class="col-md-5">
                <label for="buscar" class="form-label">Buscar</label>
                <input id="buscar" name="buscar" class="form-control" value="{{ $search }}" placeholder="Nombre, apellido o curso">
            </div>
            <div class="col-sm-6 col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" @selected($state === 'active')>Activos</option>
                    <option value="inactive" @selected($state === 'inactive')>Inactivos</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label for="turno" class="form-label">Turno</label>
                <select id="turno" name="turno" class="form-select">
                    <option value="">Todos</option>
                    @foreach (\App\Models\Student::SHIFTS as $value => $label)
                        <option value="{{ $value }}" @selected($shift === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button class="btn btn-outline-primary">Filtrar</button>
                @if ($search !== '' || $state !== '' || $shift !== '')
                    <a class="btn btn-link" href="{{ route('students.index') }}">Limpiar</a>
                @endif
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr>
                    <th scope="col">Alumno</th>
                    <th scope="col">Curso / turno</th>
                    <th scope="col">Vigencia de la beca</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="text-end">Acciones</th>
                </tr></thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <th scope="row">{{ $student->full_name }}</th>
                            <td>{{ $student->classroom }} · {{ \App\Models\Student::SHIFTS[$student->school_shift] }}</td>
                            <td>{{ $student->starts_on->format('d/m/Y') }} – {{ $student->ends_on?->format('d/m/Y') ?? 'Sin fin' }}</td>
                            <td><span class="badge {{ $student->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $student->active ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('students.edit', $student) }}">Editar</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-table">{{ $search || $state || $shift ? 'No hay alumnos que coincidan con los filtros.' : 'Todavía no hay alumnos registrados.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->hasPages())
            <div class="panel-body border-top">{{ $students->links('pagination::bootstrap-5') }}</div>
        @endif
    </section>
@endsection
