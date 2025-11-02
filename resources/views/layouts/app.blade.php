<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- 🔹 Título + Favicon --}}
    <title>Mindware</title>
    <link rel="icon" type="image/png" href="{{ asset('img/mindware-logo.png') }}">

    {{-- 🔹 Fuentes e íconos --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- ✅ Tus assets con Vite (Tailwind + JS de tu app) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- (Opcional) otros CSS tuyos si existen en resources/css --}}
    @vite(['resources/css/crud-users.css'])

    {{-- Pila para estilos por vista --}}
    @stack('styles')

    {{-- CSRF para peticiones fetch --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- 🔹 Estilos para breadcrumbs --}}
    <style>
        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #6b7280;
            margin: 0 .3rem;
        }
        .breadcrumb-item a {
            color: #2563eb;
            text-decoration: none;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: #374151;
            font-weight: 600;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">

        {{-- 🔹 Navbar superior común --}}
        @include('layouts.navigation')

        {{-- 🔹 Header opcional --}}
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- 🔹 Breadcrumbs (rastro de navegación) --}}
        @php($crumbName = optional(request()->route())->getName())
        @if ($crumbName && \Diglactic\Breadcrumbs\Breadcrumbs::exists($crumbName))
            <nav class="bg-white border-bottom py-2 px-3 shadow-sm">
                <div class="max-w-7xl mx-auto text-sm text-gray-700">
                    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render() }}
                </div>
            </nav>
        @endif


        {{-- 🔹 Contenido principal --}}
        <main class="py-4">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
    </div>

    {{-- ✅ jQuery + Bootstrap 4 JS (sin CSS para no alterar tu diseño) --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    {{-- SweetAlert2 (si lo usas) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ✅ Pila de modales globales (para médico, paciente, etc.) --}}
    @stack('modals')

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
          new bootstrap.Tooltip(tooltipTriggerEl)
        })
      });
    </script>

    {{-- ✅ Pila de scripts por vista --}}
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
