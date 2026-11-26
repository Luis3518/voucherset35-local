@extends('layout')
@section('title', 'Vouchers')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Vouchers</h1>
            <p>Elegí el período y los alumnos para generar el PDF de impresión.</p>
        </div>
    </div>

    @if ($students->isEmpty())
        <div class="panel panel-body">
            <p>Necesitás al menos un alumno activo para generar vouchers.</p>
            <div><a class="btn btn-outline-primary" href="{{ route('students.index') }}">Ir a alumnos</a></div>
        </div>
    @else
        <form id="voucher-form" method="post" action="{{ route('vouchers.generate') }}">
            @csrf
            <div class="row g-3">
                <div class="col-lg-5">
                    <fieldset class="panel panel-body h-100">
                        <legend class="h5">Período</legend>
                        <label for="period" class="form-label">Elegir período</label>
                        <select id="period" class="form-select" data-periods='@json($periods)'>
                            <option value="current">Mes actual</option>
                            <option value="next">Mes siguiente</option>
                            <option value="custom">Rango personalizado</option>
                        </select>
                        <div class="row g-3 mt-1">
                            <div class="col-sm-6">
                                <label for="voucher-start" class="form-label">Desde</label>
                                <input id="voucher-start" name="starts_on" type="date" class="form-control" value="{{ $periods['current'][0] }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="voucher-end" class="form-label">Hasta</label>
                                <input id="voucher-end" name="ends_on" type="date" class="form-control" value="{{ $periods['current'][1] }}" required>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-lg-7">
                    <fieldset class="panel panel-body">
                        <legend class="h5">Alumnos activos</legend>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" id="select-all" class="btn btn-sm btn-outline-primary">Seleccionar todos</button>
                            <button type="button" id="select-none" class="btn btn-sm btn-outline-secondary">Deseleccionar todos</button>
                            <span id="selection-count" class="align-self-center ms-sm-auto text-secondary" role="status">0 de {{ $students->count() }} seleccionados</span>
                        </div>
                        <div class="student-options">
                            @foreach ($students as $student)
                                <label class="student-option">
                                    <input class="form-check-input mt-0" type="checkbox" name="student_ids[]" value="{{ $student->id }}">
                                    <span>{{ $student->full_name }} <span class="text-secondary">· {{ $student->classroom }}</span></span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="mt-3" id="voucher-error" role="alert" hidden></div>
            <div id="voucher-progress" class="voucher-progress mt-3" role="status" hidden>
                <label for="voucher-progress-bar" class="form-label mb-1">Generando el PDF...</label>
                <progress id="voucher-progress-bar" class="w-100" aria-describedby="voucher-progress-help"></progress>
                <p id="voucher-progress-help" class="text-secondary mb-0">Esto puede tardar varios minutos si seleccionaste muchos alumnos. Mantené esta página abierta.</p>
            </div>
            <button type="submit" id="generate-vouchers" class="btn btn-primary mt-3">Generar vouchers</button>
        </form>

        <section id="voucher-preview" class="panel panel-body mt-4" hidden aria-labelledby="preview-title">
            <div class="page-heading mb-3">
                <div>
                    <h2 id="preview-title" class="h4">Vista previa del PDF</h2>
                    <p id="voucher-total" role="status"></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a id="voucher-print" class="btn btn-outline-primary" target="_blank" rel="noopener">Abrir para imprimir</a>
                    <a id="voucher-download" class="btn btn-primary" download>Descargar PDF</a>
                </div>
            </div>
            <iframe id="voucher-frame" title="Vista previa de los vouchers"></iframe>
        </section>
    @endif
@endsection
