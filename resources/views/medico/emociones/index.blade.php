@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="col-md-11 mx-auto">

        <h3 class="text-center mb-4">
            <i class="fas fa-heartbeat text-danger"></i> Registro Emocional de Pacientes
        </h3>

        {{-- 🔹 Filtros --}}
        <form method="GET" action="{{ route('medico.emociones.index') }}" class="row g-3 mb-4">
            <div class="col-md-5">
                <input type="text" name="paciente" class="form-control" placeholder="Buscar por paciente" value="{{ $filtroPaciente }}">
            </div>
            <div class="col-md-5">
                <input type="text" name="actividad" class="form-control" placeholder="Buscar por actividad" value="{{ $filtroActividad }}">
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        @if($emociones->isEmpty())
            <p class="text-center text-muted">No se encontraron emociones registradas.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Paciente</th>
                            <th>Actividad</th>
                            <th>Emociones</th>
                            <th>Intensidad</th>
                            <th>Comentario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emociones as $emo)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($emo->fechaHoraRegistro)->format('d/m/Y H:i') }}</td>
                                <td>{{ $emo->paciente }}</td>
                                <td>{{ $emo->actividad }}</td>
                                <td>
                                    @foreach(json_decode($emo->emocionesExperimentadas, true) ?? [] as $e)
                                        <span class="badge bg-info text-dark">{{ $e }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $emo->intensidad ?? '-' }}</td>
                                <td>{{ $emo->comentario ?? '—' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('medico.emociones.destroy', $emo->idEmocion) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Eliminar
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
@endsection
