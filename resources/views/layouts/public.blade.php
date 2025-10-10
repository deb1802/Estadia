<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mindware') }}</title>

        <!-- Fuentes -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Estilos y scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-800">
        <div class="min-h-screen bg-gray-100">

            <!-- 🔹 Barra de navegación pública -->
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo -->
                        <div class="flex">
                            <a href="{{ url('/') }}" class="flex items-center text-indigo-600 font-bold text-xl">
                                🧠 Mindware
                            </a>
                        </div>

                        <!-- Enlaces principales -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-8">
                            <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Inicio</a>
                            <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Servicios</a>
                            <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Contacto</a>
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Iniciar sesión</a>
                        </div>

                        <!-- Menú móvil -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = ! open"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menú desplegable móvil -->
                <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Inicio</a>
                        <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Servicios</a>
                        <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Contacto</a>
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-indigo-600 hover:bg-indigo-100">Iniciar sesión</a>
                    </div>
                </div>
            </nav>

            <!-- Contenido -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-gray-100 border-t border-gray-300 mt-10 py-6 text-center text-sm text-gray-600">
                © {{ date('Y') }} Mindware — Plataforma de seguimiento emocional y terapéutico.
            </footer>
        </div>
    </body>
</html>
