@extends('layouts.app')

@section('content')
<section class="content-header text-center mb-3">
    <div class="container-fluid">
        <h1 class="fw-semibold text-primary">
            <i class="fas fa-user-shield me-2"></i> Detalles del Tutor
        </h1>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Nombre:</strong>
                    <p>{{ $tutor->nombre }} {{ $tutor->apellido }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Parentesco:</strong>
                    <p>{{ $tutor->parentesco ?? 'Sin especificar' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Teléfono:</strong>
                    <p>{{ $tutor->telefono ?? 'No registrado' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Correo electrónico:</strong>
                    <p>{{ $tutor->correo ?? 'No disponible' }}</p>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>Dirección:</strong>
                    <p>{{ $tutor->direccion ?? 'Sin especificar' }}</p>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>Observaciones:</strong>
                    <p>{{ $tutor->observaciones ?? 'Ninguna observación' }}</p>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>Paciente asignado:</strong>
                    <p>{{ $tutor->paciente_nombre }} {{ $tutor->paciente_apellido }}</p>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('medico.tutores.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Regresar
            </a>
        </div>
    </div>
</div>
@endsection
