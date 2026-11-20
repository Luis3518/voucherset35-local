<!doctype html>
<html lang="es-AR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · Cantina La 35</title>
    @vite('resources/js/app.js')
</head>
<body>
    <header class="app-header">
        <div class="container-fluid app-width d-flex flex-wrap align-items-center justify-content-between column-gap-4">
            <a class="app-brand" href="{{ route('students.index') }}">Cantina La 35</a>
            <nav class="app-nav" aria-label="Navegación principal">
                @foreach (['students' => 'Alumnos', 'meals' => 'Comidas', 'calendar' => 'Calendario', 'vouchers' => 'Vouchers'] as $module => $label)
                    <a href="{{ route($module.'.index') }}" @if (request()->routeIs($module.'.*')) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="container-fluid app-width py-4 py-md-5">
        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
