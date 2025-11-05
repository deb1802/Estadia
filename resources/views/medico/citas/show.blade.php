@extends('layouts.app')

@section('content')
<style>
  body {
    background: linear-gradient(180deg, #f4f9ff 0%, #e8f0ff 100%);
  }
  .cita-container {
    min-height: calc(100vh - 120px);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .cita-card {
    width: 95%;
    max-width: 800px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    padding: 40px;
    transition: all 0.3s ease;
  }
  .cita-card:hover {
    transform: scale(1.01);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  }
</style>

<div class="cita-container">
  <div class="cita-card">

    {{-- 🔹 Encabezado --}}
    <div class="text-center mb-4">
      <h3 class="fw-bold text-primary mb-1">
        <i class="bi bi-calendar3"></i> Detalles de la Cita
      </h3>
      <p class="text-secondary mb-0">ID de cita: #{{ $cita->idCita }}</p>
    </div>

    {{-- 🔹 Información de la cita --}}
    <div class="row g-4">
      <div class="col-md-6">
        <h6 class="text-muted"><i class="bi bi-person-fill me-1"></i> Paciente</h6>
        <p class="fs-5 text-dark fw-semibold">{{ $cita->paciente_nombre }} {{ $cita->paciente_apellido }}</p>
      </div>

      <div class="col-md-6">
        <h6 class="text-muted"><i class="bi bi-clock-fill me-1"></i> Fecha y Hora</h6>
        <p class="fs-5 text-dark fw-semibold">{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</p>
      </div>

      <div class="col-md-6">
        <h6 class="text-muted"><i class="bi bi-info-circle-fill me-1"></i> Motivo</h6>
        <p class="fs-6 text-dark">{{ $cita->motivo }}</p>
      </div>

      <div class="col-md-6">
        <h6 class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i> Ubicación</h6>
        <p class="fs-6 text-dark">{{ $cita->ubicacion }}</p>
      </div>

      <div class="col-md-6">
        <h6 class="text-muted"><i class="bi bi-tags-fill me-1"></i> Estado</h6>
        @php
          $color = match($cita->estado) {
            'programada' => 'info',
            'realizada' => 'success',
            'cancelada' => 'danger',
            default => 'secondary',
          };
        @endphp
        <span class="badge bg-{{ $color }} px-3 py-2 text-capitalize">{{ $cita->estado }}</span>
      </div>
    </div>

    {{-- 🔹 Botón regresar --}}
    <div class="text-center mt-5">
      <a href="{{ route('medico.citas.index') }}" class="btn btn-outline-primary px-5 py-2 fw-semibold shadow-sm">
        <i class="bi bi-arrow-left-circle me-1"></i> Regresar
      </a>
    </div>

  </div>
</div>

@include('medico.bottom-navbar')
@endsection

