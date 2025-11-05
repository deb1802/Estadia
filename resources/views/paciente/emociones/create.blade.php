@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh; max-width: 1100px; margin: 0 auto;">

    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4" style="background: linear-gradient(145deg, #f3f7ff, #e8efff);">
            <div class="card-body p-5">

                {{-- Encabezado --}}
                <h3 class="text-center mb-4 fw-bold text-dark">
                    <span style="font-size: 32px;">😊</span> Registrar Emoción
                </h3>

                <form method="POST" action="{{ route('paciente.emociones.store') }}">
                    @csrf
                    <input type="hidden" name="fkActividad" value="{{ $idActividad }}">

                    {{-- Emociones --}}
                    <label class="form-label fw-semibold text-secondary">Selecciona cómo te sentiste:</label>

                    <div class="emotion-grid mb-4 text-center">
                        @php
                            $emojis = [
                                'Tranquilo' => '😌',
                                'Ansioso' => '😰',
                                'Motivado' => '🔥',
                                'Confundido' => '🤔',
                                'Frustrado' => '😣',
                                'Feliz' => '😄',
                                'Triste' => '😢',
                                'Irritado' => '😡'
                            ];
                        @endphp

                        @foreach($emojis as $texto => $emoji)
                            <div class="emotion-option" data-name="{{ $texto }}">
                                <span class="emoji-display">{{ $emoji }}</span>
                                <small class="d-block">{{ $texto }}</small>
                            </div>
                        @endforeach
                    </div>

                    {{-- Emociones seleccionadas --}}
                    <input type="hidden" id="emocionesSeleccionadas" name="emocionesExperimentadas" value="[]">


                    {{-- Intensidades dinámicas --}}
                    <div id="intensidadesContainer"></div>

                    {{-- Comentario --}}
                    <div class="mb-4 mt-3">
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

{{-- 🎨 Estilos --}}
<style>
    .emotion-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }
    .emotion-option {
        cursor: pointer;
        padding: 12px;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        transition: transform 0.25s, background 0.35s;
    }
    .emotion-option.active {
        background: #cfe2ff;
        transform: scale(1.1);
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    }
    .emoji-display {
        font-size: 34px;
        display: block;
    }
    .styled-slider {
        width: 100%;
        height: 9px;
        appearance: none;
        background: #d7e3ff;
        border-radius: 10px;
        margin-top: 10px;
        cursor: pointer;
    }
    .styled-slider::-webkit-slider-thumb {
        width: 22px;
        height: 22px;
        background: #7ea3ff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        transition: 0.25s;
    }
    .styled-slider::-webkit-slider-thumb:hover {
        transform: scale(1.18);
    }
    .range-labels {
        display: flex;
        justify-content: space-between;
        color: #6b7b93;
        font-weight: 600;
        padding: 0 3px;
    }
</style>

{{-- 🎮 Javascript --}}
<script>
    const selected = new Set();
    const intensityContainer = document.getElementById('intensidadesContainer');
    const inputHidden = document.getElementById('emocionesSeleccionadas');

    document.querySelectorAll('.emotion-option').forEach(item => {
        item.addEventListener('click', () => {
            const emotion = item.dataset.name;
            if (selected.has(emotion)) {
                selected.delete(emotion);
                item.classList.remove('active');
                document.getElementById("int_" + emotion)?.remove();
            } else {
                selected.add(emotion);
                item.classList.add('active');
                addIntensitySlider(emotion);
            }
            inputHidden.value = JSON.stringify([...selected]);

        });
    });

    function addIntensitySlider(emotion) {
        const block = document.createElement('div');
        block.id = "int_" + emotion;
        block.classList.add("mb-3");
        block.innerHTML = `
            <label class="fw-semibold d-block text-center mb-2">${emotion} - intensidad:</label>
            <input type="range" name="intensidades[${emotion}]" min="1" max="5" value="3" class="styled-slider">
            <div class="range-labels"><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span></div>
        `;
        intensityContainer.appendChild(block);
    }
</script>

@endsection
