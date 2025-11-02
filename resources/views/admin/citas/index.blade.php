@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center mb-3">
        <h1 class="m-0">📅 Citas del sistema</h1>
    </div>
</section>

<div class="content px-3">
    @include('flash::message')

    <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #bea4d2, #c8b1da); color:white;">
            <strong>Listado de todas las citas registradas</strong>
        </div>
        <div class="card-body p-0">
            @if($citas->isEmpty())
                <p class="text-center py-3 text-muted">No hay citas registradas en el sistema.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Fecha y hora</th>
                                <th>Motivo</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($citas as $cita)
                                <tr>
                                    <td>{{ $cita->idCita }}</td>
                                    <td>{{ $cita->paciente }}</td>
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
                                    <td class="text-center">
                                        <a href="{{ route('admin.citas.show', $cita->idCita) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.citas.destroy', $cita->idCita) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta cita?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
@endsection
