@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-check"></i> Detalles de la Cita</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a class="btn btn-outline-secondary" href="{{ route('medico.citas.index') }}">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #74b9ff, #6c63ff);">
            <h5 class="mb-0"><i class="fas fa-user-md"></i> Información de la cita</h5>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong><i class="fas fa-user"></i> Paciente:</strong><br>
                    {{ $cita->paciente_nombre }} {{ $cita->paciente_apellido }}
                </div>
                <div class="col-md-6">
                    <strong><i class="fas fa-map-marker-alt"></i> Ubicación:</strong><br>
                    {{ $cita->ubicacion }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong><i class="fas fa-calendar-alt"></i> Fecha y hora:</strong><br>
                    {{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}
                </div>
                <div class="col-md-6">
                    <strong><i class="fas fa-info-circle"></i> Motivo:</strong><br>
                    {{ $cita->motivo }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <strong><i class="fas fa-clipboard-check"></i> Estado actual:</strong><br>
                    @switch($cita->estado)
                        @case('programada')
                            <span class="badge bg-warning text-dark">Programada</span>
                            @break
                        @case('realizada')
                            <span class="badge bg-success">Realizada</span>
                            @break
                        @case('cancelada')
                            <span class="badge bg-danger">Cancelada</span>
                            @break
                        @default
                            <span class="badge bg-secondary">Desconocido</span>
                    @endswitch
                </div>

                <div class="col-md-6">
                    <strong><i class="fas fa-clock"></i> Última actualización:</strong><br>
                    {{ $cita->updated_at ?? '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Opciones del médico --}}
    <div class="card mt-4 shadow-sm border-0">
        <div class="card-body text-center">
            <a href="{{ route('medico.citas.edit', $cita->idCita) }}" class="btn btn-outline-primary mx-1">
                <i class="fas fa-edit"></i> Editar Cita
            </a>

            <form action="{{ route('medico.citas.destroy', $cita->idCita) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('¿Deseas eliminar esta cita definitivamente?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger mx-1">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
