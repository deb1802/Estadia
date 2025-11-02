@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- 🔹 Encabezado --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center text-white rounded-top"
             style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <h3 class="mb-1"><i class="fas fa-calendar-check"></i> Detalle de la Cita</h3>
            <p class="mb-0">ID de cita: <strong>#{{ $cita->idCita }}</strong></p>
        </div>

        {{-- 🔹 Información principal --}}
        <div class="card-body text-center">
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-user-md text-success"></i> Médico</h5>
                    <p>{{ $cita->medico }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-map-marker-alt text-danger"></i> Ubicación</h5>
                    <p>{{ $cita->ubicacion }}</p>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-calendar-day text-info"></i> Fecha y hora</h5>
                    <p>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-clipboard-list text-warning"></i> Motivo</h5>
                    <p>{{ $cita->motivo }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <h5><i class="fas fa-info-circle text-secondary"></i> Estado</h5>
                    <span class="badge bg-{{ 
                        $cita->estado === 'realizada' ? 'success' : 
                        ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                        {{ ucfirst($cita->estado) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 🔹 Botón volver --}}
        <div class="card-footer text-center">
            <a href="{{ route('paciente.citas.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Volver a mis citas
            </a>
        </div>
    </div>

</div>
@endsection
