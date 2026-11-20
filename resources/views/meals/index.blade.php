@extends('layout')
@section('title', 'Comidas')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Comidas</h1>
            <p>Marcá las comidas de cada alumno y guardá los cambios de la semana.</p>
        </div>
        @if ($rows->isNotEmpty())
            <button id="save-meals" class="btn btn-primary" type="button" disabled>Guardar cambios</button>
        @endif
    </div>

    @if ($rows->isEmpty())
        <div class="panel panel-body">
            <p>Todavía no hay alumnos para asignar comidas.</p>
            <div><a class="btn btn-outline-primary" href="{{ route('students.create') }}">Registrar alumno</a></div>
        </div>
    @else
        <section class="panel" aria-label="Asignaciones semanales">
            <div class="panel-body d-flex align-items-end flex-wrap gap-3">
                <div>
                    <label for="meal-search" class="form-label">Buscar en la grilla</label>
                    <input id="meal-search" type="search" class="form-control" placeholder="Alumno, curso o turno">
                </div>
                <p id="meal-status" class="mb-2 text-secondary" role="status"></p>
            </div>
            <div id="meal-error" class="alert alert-danger mx-3" role="alert" hidden></div>
            <div id="meal-grid" data-update-url="{{ route('meals.update') }}"></div>
        </section>
        @php($mealData = compact('rows', 'weekdays', 'meals'))
        <script id="meal-data" type="application/json">@json($mealData)</script>
    @endif
@endsection
