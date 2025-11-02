@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- 🔹 Encabezado --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center text-white rounded-top"
             style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <h3 class="mb-1"><i class="fas fa-calendar-alt"></i> Detalles de la Cita</h3>
            <p class="mb-0">ID de cita: <strong>#{{ $cita->idCita }}</strong></p>
        </div>

        {{-- 🔹 Contenido principal --}}
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-user text-primary"></i> Paciente</h5>
                    <p class="mb-0">{{ $cita->paciente }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-user-md text-success"></i> Médico</h5>
                    <p class="mb-0">{{ $cita->medico }}</p>
                </div>
            </div>

            <hr>

            <div class="row text-center">
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-calendar-day text-info"></i> Fecha y hora</h5>
                    <p>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-map-marker-alt text-danger"></i> Ubicación</h5>
                    <p>{{ $cita->ubicacion }}</p>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-md-12 mb-3">
                    <h5><i class="fas fa-clipboard-list text-warning"></i> Motivo</h5>
                    <p>{{ $cita->motivo }}</p>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-md-12">
                    <h5><i class="fas fa-info-circle text-secondary"></i> Estado actual</h5>
                    <span class="badge bg-{{ 
                        $cita->estado === 'realizada' ? 'success' : 
                        ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                        {{ ucfirst($cita->estado) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 🔹 Botones --}}
        <div class="card-footer text-center">
            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>

            <form action="{{ route('admin.citas.destroy', $cita->idCita) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"
                        onclick="return confirm('¿Eliminar esta cita permanentemente?')">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
