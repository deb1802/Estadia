@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-semibold text-primary">Detalles del Tutor</h1>
        <a href="{{ route('paciente.tutores.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Regresar
        </a>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">

                {{-- 🔹 Nombre completo --}}
                <div class="col-sm-6 mb-3">
                    <strong>Nombre completo:</strong><br>
                    {{ $tutor->nombre }} {{ $tutor->apellido }}
                </div>

                {{-- 🔹 Parentesco --}}
                <div class="col-sm-6 mb-3">
                    <strong>Parentesco:</strong><br>
                    {{ $tutor->parentesco ?? '—' }}
                </div>

                {{-- 🔹 Teléfono --}}
                <div class="col-sm-6 mb-3">
                    <strong>Teléfono:</strong><br>
                    {{ $tutor->telefono ?? '—' }}
                </div>

                {{-- 🔹 Correo --}}
                <div class="col-sm-6 mb-3">
                    <strong>Correo electrónico:</strong><br>
                    {{ $tutor->correo ?? '—' }}
                </div>

                {{-- 🔹 Dirección --}}
                <div class="col-sm-12 mb-3">
                    <strong>Dirección:</strong><br>
                    {{ $tutor->direccion ?? '—' }}
                </div>

                {{-- 🔹 Observaciones --}}
                <div class="col-sm-12 mb-2">
                    <strong>Observaciones:</strong><br>
                    {{ $tutor->observaciones ?? '—' }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
