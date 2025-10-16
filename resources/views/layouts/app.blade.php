@vite(['resources/css/app.css','resources/js/app.js'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- 🔹 Título de la pestaña --}}
    <title>Mindware</title>

    {{-- 🔹 Favicon personalizado (usa el que tengas en /public/img) --}}
    <link rel="icon" type="image/png" href="{{ asset('img/mindware-logo.png') }}">
    {{-- Cambia el nombre del archivo según el tuyo, por ejemplo: mindware.ico, logo.png, etc. --}}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <!-- ✅ Íconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Estilos personalizados -->
    @vite(['resources/css/crud-users.css'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>



<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">

        {{-- Barra de navegación (común para todos los roles) --}}
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-4">
            {{-- ✅ Compatible con ambos estilos --}}
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

    </div>
    @stack('scripts')

</body>
</html>
