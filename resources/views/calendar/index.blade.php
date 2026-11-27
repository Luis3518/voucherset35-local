@extends('layout')
@section('title', 'Calendario')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Calendario</h1>
            <p>Registrá los días y rangos de fechas que se excluyen de la generación.</p>
        </div>
        <a href="{{ route('calendar.create') }}" class="btn btn-primary">Nueva exclusión</a>
    </div>

    <form method="post" action="{{ route('calendar.import-holidays') }}" class="d-flex flex-wrap align-items-end gap-2 mb-4">
        @csrf
        <div>
            <label for="year" class="form-label">Año de los feriados</label>
            <input id="year" name="year" type="number" class="form-control" min="2016" max="2100" required value="{{ old('year', now()->year) }}">
        </div>
        <button type="submit" class="btn btn-outline-primary">Consultar y guardar feriados</button>
        <span class="text-muted">Se conservan las exclusiones existentes.</span>
    </form>

    <section class="panel" aria-label="Exclusiones del calendario">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr>
                    <th scope="col">Motivo</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Desde</th>
                    <th scope="col">Hasta</th>
                    <th scope="col" class="text-end">Acciones</th>
                </tr></thead>
                <tbody>
                    @forelse ($exclusions as $exclusion)
                        <tr>
                            <th scope="row">{{ $exclusion->description }}</th>
                            <td>{{ \App\Models\CalendarExclusion::KINDS[$exclusion->kind] }}</td>
                            <td>{{ $exclusion->starts_on->format('d/m/Y') }}</td>
                            <td>{{ $exclusion->ends_on->format('d/m/Y') }}</td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('calendar.edit', $exclusion) }}">Editar</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-table">Todavía no hay exclusiones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($exclusions->hasPages())
            <div class="panel-body border-top">{{ $exclusions->links('pagination::bootstrap-5') }}</div>
        @endif
    </section>
@endsection
