@extends('layouts.app')

@section('content')
<section class="content-header py-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="fw-bold text-primary mb-0">
            <i class="fas fa-user-circle me-2"></i> Detalle del paciente
        </h1>
        <a href="{{ route('medico.pacientes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
</section>

<div class="content px-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

        {{-- HEADER --}}
        <div class="card-header bg-primary text-white text-center py-4">
            <h4 class="mb-1 fw-semibold">
                {{ $usuario->nombre }} {{ $usuario->apellido }}
            </h4>
            <p class="mb-2 small text-light">Paciente</p>
            @if($usuario->estadoCuenta === 'activo')
                <span class="badge bg-success px-3 py-2 fs-6">Activo</span>
            @else
                <span class="badge bg-danger px-3 py-2 fs-6">Inactivo</span>
            @endif
        </div>

        {{-- BODY --}}
        <div class="card-body bg-light py-4 px-5">

            {{-- 📨 Email --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-envelope me-1 text-primary"></i>Correo electrónico
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->email }}</p>
            </div>

            {{-- 📞 Teléfono --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-phone me-1 text-primary"></i>Teléfono
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->telefono }}</p>
            </div>

            {{-- 🎂 Fecha de nacimiento --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-birthday-cake me-1 text-primary"></i>Fecha de nacimiento
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->fechaNacimiento }}</p>
            </div>

            {{-- ⚧ Sexo --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-venus-mars me-1 text-primary"></i>Sexo
                </h6>
                <p class="fw-semibold text-dark">{{ ucfirst($usuario->sexo) }}</p>
            </div>

            {{-- 🆔 Tipo de usuario --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-id-badge me-1 text-primary"></i>Tipo de usuario
                </h6>
                <p class="fw-semibold text-dark text-capitalize">{{ $usuario->tipoUsuario }}</p>
            </div>

            {{-- 🔘 Estado de cuenta --}}
            <div class="info-box mb-4">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-toggle-on me-1 text-primary"></i>Estado de cuenta
                </h6>
                @if($usuario->estadoCuenta === 'activo')
                    <p class="fw-semibold text-success">Activo</p>
                @else
                    <p class="fw-semibold text-danger">Inactivo</p>
                @endif
            </div>

            {{-- ===== Padecimientos (parte importante) ===== --}}
            <hr class="my-4">
            <div class="mt-3">
                <h5 class="fw-semibold d-flex align-items-center mb-3">
                    <i class="fas fa-notes-medical me-2 text-primary"></i>
                    Sintomatología inicial 
                </h5>

                @php
                    $p = trim((string) $paciente->padecimientos);
                @endphp

                @if($p !== '')
                    {{-- Si guardas en texto libre --}}
                    <div class="p-3 bg-white rounded-3 shadow-sm border">
                        <p class="mb-0" style="white-space:pre-line;">{{ $p }}</p>
                    </div>

                    {{-- Si en el futuro los guardas separados por comas: --}}
                    {{-- 
                    <ul class="list-group">
                        @foreach(explode(',', $p) as $item)
                            <li class="list-group-item">{{ trim($item) }}</li>
                        @endforeach
                    </ul>
                    --}}
                @else
                    <div class="alert alert-info mb-0">
                        No hay padecimientos registrados para este paciente.
                    </div>
                @endif
            </div>
        </div>

        {{-- FOOTER opcional: acciones solo de lectura para médico --}}
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                <a href="{{ route('medico.pacientes.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
