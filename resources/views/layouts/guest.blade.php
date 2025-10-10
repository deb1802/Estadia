<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mindware') }}</title>

    <!-- Fuentes y estilos -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* 🎨 Fondo animado personalizado */
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(-45deg, #90aacc, #b5c8e1, #c8bfec, #bea4d2);
            background-size: 400% 400%;
            animation: gradient 4s ease infinite; /* transición más rápida */
        }

        @keyframes gradient {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* 💫 Ajustes generales */
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="flex flex-col justify-center items-center min-h-screen px-4">

        <!-- 🔹 Contenedor del logo + formulario -->
        <div class="w-full sm:max-w-md px-6 py-12 login-card flex flex-col items-center">
            
            <!-- Logo dentro del cuadro -->
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="w-24 h-24 fill-current text-gray-500" />
                </a>
            </div>

            <!-- Formulario -->
            <div class="w-full">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>

</html>
