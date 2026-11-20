@extends('layout')
@section('title', $exclusion->exists ? 'Editar exclusión' : 'Nueva exclusión')

@section('content')
    <a class="back-link" href="{{ route('calendar.index') }}">← Volver al calendario</a>
    <div class="page-heading"><h1>{{ $exclusion->exists ? 'Editar exclusión' : 'Nueva exclusión' }}</h1></div>

    <div class="panel panel-body form-panel">
        <form method="post" action="{{ $exclusion->exists ? route('calendar.update', $exclusion) : route('calendar.store') }}">
            @csrf
            @if ($exclusion->exists)
                @method('PUT')
            @endif
            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="kind" class="form-label">Tipo</label>
                    <select id="kind" name="kind" class="form-select" required>
                        <option value="">Seleccionar tipo</option>
                        @foreach (\App\Models\CalendarExclusion::KINDS as $value => $label)
                            <option value="{{ $value }}" @selected(old('kind', $exclusion->kind) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="description" class="form-label">Motivo</label>
                    <input id="description" name="description" class="form-control" maxlength="120" required value="{{ old('description', $exclusion->description) }}">
                </div>
                <div class="col-sm-6">
                    <label for="starts_on" class="form-label">Desde</label>
                    <input type="date" id="starts_on" name="starts_on" class="form-control" required value="{{ old('starts_on', $exclusion->starts_on?->toDateString()) }}">
                </div>
                <div class="col-sm-6">
                    <label for="ends_on" class="form-label">Hasta <span class="text-secondary fw-normal">(opcional)</span></label>
                    <input type="date" id="ends_on" name="ends_on" class="form-control" aria-describedby="ends-help" value="{{ old('ends_on', $exclusion->ends_on?->toDateString()) }}">
                    <div id="ends-help" class="form-text">Dejá este campo vacío para excluir un solo día.</div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary">Guardar exclusión</button>
                <a class="btn btn-outline-secondary" href="{{ route('calendar.index') }}">Cancelar</a>
            </div>
        </form>
        @if ($exclusion->exists)
            <form class="mt-4 pt-3 border-top" method="post" action="{{ route('calendar.destroy', $exclusion) }}"
                data-confirm="¿Eliminar esta exclusión del calendario?">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">Eliminar exclusión</button>
            </form>
        @endif
    </div>
@endsection
