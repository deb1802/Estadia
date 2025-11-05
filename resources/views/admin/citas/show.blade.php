@extends('layouts.app')

@section('content')
<section class="content-header text-center mb-3">
  <div class="container-fluid">
    <h1 class="fw-semibold text-primary">
      <i class="fas fa-calendar-check me-2"></i> Detalles de la Cita
    </h1>
    <p class="text-muted mb-0">ID de cita: #{{ $cita->idCita }}</p>
  </div>
</section>

<div class="content px-3">

  <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
    <div class="card-header text-white" style="background: linear-gradient(90deg, #b5a8d9, #9ec5f8);">
      <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Información general</h5>
    </div>

    <div class="card-body">

      {{-- Paciente --}}
      <div class="mb-4">
        <h5 class="text-secondary"><i class="fas fa-user me-2"></i>Paciente</h5>
        <p class="mb-1"><strong>Nombre:</strong> {{ $cita->paciente_nombre }} {{ $cita->paciente_apellido }}</p>
        <p class="mb-1"><strong>Correo:</strong> {{ $cita->paciente_email }}</p>
      </div>

      {{-- Médico --}}
      <div class="mb-4">
        <h5 class="text-secondary"><i class="fas fa-user-md me-2"></i>Médico</h5>
        <p class="mb-1"><strong>Nombre:</strong> {{ $cita->medico_nombre }} {{ $cita->medico_apellido }}</p>
      </div>

      {{-- Datos de la cita --}}
      <div class="mb-4">
        <h5 class="text-secondary"><i class="fas fa-clock me-2"></i>Detalles de la cita</h5>
        <p class="mb-1"><strong>Fecha y hora:</strong> {{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</p>
        <p class="mb-1"><strong>Motivo:</strong> {{ $cita->motivo }}</p>
        <p class="mb-1"><strong>Ubicación:</strong> {{ $cita->ubicacion }}</p>

        <p class="mt-2">
          <strong>Estado:</strong>
          <span class="badge
            @if($cita->estado === 'programada') bg-primary
            @elseif($cita->estado === 'cancelada') bg-danger
            @elseif($cita->estado === 'realizada') bg-success
            @else bg-secondary @endif">
            {{ ucfirst($cita->estado) }}
          </span>
        </p>
      </div>

      {{-- Botón regresar --}}
      <div class="text-end">
        <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-primary px-4">
          <i class="fas fa-arrow-left me-2"></i> Regresar
        </a>
      </div>

    </div>
  </div>

</div>
@endsection
