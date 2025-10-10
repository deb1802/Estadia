@extends('layouts.public')

@section('content')
<div class="text-center py-20">
    <h1 class="text-5xl font-bold text-indigo-600 mb-6">Bienvenido a Mindware</h1>
    <p class="text-gray-700 text-lg max-w-2xl mx-auto mb-10">
        Plataforma digital para el seguimiento emocional y acompañamiento terapéutico.  
        Conecta pacientes y profesionales de la salud mental en un entorno seguro y accesible.
    </p>

    <a href="{{ route('login') }}"
       class="px-8 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
       Iniciar sesión
    </a>
</div>
@endsection
