@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh; max-width: 900px; margin: 0 auto;">
    <div class="col-md-7">

        <div class="card shadow-lg border-0 rounded-4" style="background: linear-gradient(145deg, #f4f8ff, #e8efff);">
            <div class="card-body p-5">

                {{-- Título --}}
                <h3 class="text-center mb-4 fw-bold text-dark">
                    ✏️ Editar comentario de emoción
                </h3>

                {{-- Información resumen --}}
                @php
                    $emociones = json_decode($emocion->emocionesExperimentadas, true) ?? [];
                @endphp

                <div class="text-center mb-3">
                    <strong style="color: #3a4a6b;">Emociones registradas:</strong><br>
                    @foreach($emociones as $emo)
                        <span class="badge rounded-pill" style="background:#dfe8ff; color:#4a5c8a; padding:7px 12px; font-size:14px; margin:3px;">
                            {{ $emo }}
                        </span>
                    @endforeach
                </div>

                {{-- Fecha --}}
                <div class="text-center text-muted mb-4" style="font-size:14px;">
                    Registrado el {{ \Carbon\Carbon::parse($emocion->fechaHoraRegistro)->format('d/m/Y H:i') }}
                </div>


                {{-- Formulario --}}
                <form action="{{ route('paciente.emociones.update', $emocion->idEmocion) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-semibold text-secondary">Comentario:</label>
                    <textarea name="comentario" class="form-control shadow-sm rounded-3" rows="4"
                              placeholder="Describe lo que sentiste...">{{ $emocion->comentario }}</textarea>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            💾 Guardar cambios
                        </button>
                        <a href="{{ route('paciente.emociones.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill ms-2">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

{{-- Estilo suave --}}
<style>
    .btn-primary {
        background-color: #9cb8ff;
        border-color: #9cb8ff;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: #84a7ff;
        border-color: #84a7ff;
    }
    .btn-outline-secondary:hover {
        background-color: #e7edfb;
        border-color: #e7edfb;
        color: #3a4a6b;
    }
</style>
@endsection
