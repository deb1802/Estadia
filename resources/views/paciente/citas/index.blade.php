@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-5" style="min-height: 80vh;">

    <div class="col-md-10 col-lg-8">

        {{-- 🔹 Encabezado --}}
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">
                <i class="fas fa-calendar-alt text-primary"></i> Mis Citas
            </h2>
        </div>

        {{-- 🔹 Contenedor principal --}}
        <div class="card shadow border-0 rounded-4 mx-auto" style="max-width: 900px;">
            <div class="card-header text-white text-center rounded-top-4"
                 style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
                <strong>Historial de citas programadas</strong>
            </div>

            <div class="card-body d-flex justify-content-center">
                @if($citas->isEmpty())
                    <p class="text-center text-muted py-4 mb-0">No tienes citas registradas actualmente.</p>
                @else
                    <div class="table-responsive" style="max-width: 95%;">
                        <table class="table table-striped align-middle text-center shadow-sm"
                               style="width: auto; margin: 0 auto; border-radius: 10px; overflow: hidden;">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Médico responsable</th>
                                    <th>Fecha y hora</th>
                                    <th>Motivo</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($citas as $cita)
                                    <tr>
                                        <td>{{ $cita->idCita }}</td>
                                        <td>{{ $cita->medico }}</td>
                                        <td>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $cita->motivo }}</td>
                                        <td>{{ $cita->ubicacion }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $cita->estado === 'realizada' ? 'success' : 
                                                ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($cita->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('paciente.citas.show', $cita->idCita) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                               <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
