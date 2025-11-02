@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="col-md-10 mx-auto">

        <h3 class="text-center mb-4">
            <i class="fas fa-heart text-danger"></i> Mis Emociones Registradas
        </h3>

        @if($emociones->isEmpty())
            <p class="text-center text-muted">No has registrado emociones todavía.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Actividad</th>
                            <th>Emociones</th>
                            <th>Intensidad</th>
                            <th>Comentario</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emociones as $emo)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($emo->fechaHoraRegistro)->format('d/m/Y H:i') }}</td>
                                <td>{{ $emo->actividad }}</td>
                                <td>
                                    @foreach(json_decode($emo->emocionesExperimentadas, true) ?? [] as $e)
                                        <span class="badge bg-primary">{{ $e }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $emo->intensidad ?? '-' }}</td>
                                <td>{{ $emo->comentario ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('paciente.emociones.edit', $emo->idEmocion) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Editar comentario
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
@endsection
