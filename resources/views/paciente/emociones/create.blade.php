@extends('layouts.app')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4" style="background: linear-gradient(145deg, #f3f7ff, #e8efff);">
            <div class="card-body p-5">

                {{-- 🔹 Encabezado --}}
                <h3 class="text-center mb-4 fw-bold text-dark">
                    <i class="fas fa-smile text-primary me-2"></i> Registrar Emoción
                </h3>

                <form method="POST" action="{{ route('paciente.emociones.store') }}">
                    @csrf
                    <input type="hidden" name="fkActividad" value="{{ $idActividad }}">

                    {{-- Emociones --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Emociones experimentadas:</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['Tranquilo', 'Ansioso', 'Motivado', 'Confundido', 'Frustrado', 'Feliz', 'Triste', 'Irritado'] as $emo)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="emocionesExperimentadas[]" value="{{ $emo }}" id="emo_{{ $emo }}">
                                    <label class="form-check-label" for="emo_{{ $emo }}">{{ $emo }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Intensidad --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Nivel de intensidad (1 a 5):</label>
                        <input type="number" name="intensidad" class="form-control text-center shadow-sm" 
                               min="1" max="5" placeholder="Ej. 3 (opcional)" 
                               style="max-width: 120px; margin: 0 auto;">
                    </div>

                    {{-- Comentario --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Comentario adicional:</label>
                        <textarea name="comentario" class="form-control shadow-sm" rows="3" 
                                  placeholder="Describe brevemente cómo te sentiste..."></textarea>
                    </div>

                    {{-- Botones --}}
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-save me-1"></i> Guardar
                        </button>
                        <a href="{{ route('paciente.emociones.index') }}" 
                           class="btn btn-outline-secondary px-4 py-2 rounded-pill ms-2">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 💅 Estilos personalizados --}}
<style>
    .form-check-label {
        font-weight: 500;
        color: #333;
    }

    .form-control {
        border-radius: 12px;
    }

    .btn-primary {
        background-color: #b5c8e1;
        border-color: #b5c8e1;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #a5b9d4;
        border-color: #a5b9d4;
    }

    .btn-outline-secondary:hover {
        background-color: #dfe7f3;
        border-color: #dfe7f3;
        color: #333;
    }
</style>
@endsection
