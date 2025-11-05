@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h3 class="text-center mb-4 fw-bold text-dark">
        🌿 Evolución Emocional
    </h3>

    @if($emociones->isEmpty())
        <p class="text-center text-muted">No has registrado emociones todavía.</p>
    @else

        <div class="timeline">

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

            @foreach($emociones as $emo)
                @php
                    $emotionList = json_decode($emo->emocionesExperimentadas, true) ?? [];
                    $intensidades = json_decode($emo->intensidades, true) ?? [];
                @endphp

                <div class="timeline-item shadow-sm">
                    <div class="time">{{ \Carbon\Carbon::parse($emo->fechaHoraRegistro)->format('d M, H:i') }}</div>
                    <div class="activity">{{ $emo->actividad }}</div>

                    @foreach($emotionList as $e)
                        <div class="emotion-row">
                            <div class="emotion-label">{{ $emojis[$e] ?? '🙂' }} {{ $e }}</div>

                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ ($intensidades[$e] ?? 1) * 20 }}%;"></div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Comentario --}}
                    @if($emo->comentario)
                        <div class="comment">“{{ $emo->comentario }}”</div>
                    @endif

                    {{-- Botón Editar Comentario --}}
                    <div class="text-end mt-2">
                        <a href="{{ route('paciente.emociones.edit', $emo->idEmocion) }}" class="btn btn-sm btn-outline-primary">
    ✏️ Editar comentario
</a>

                    </div>

                </div>

            @endforeach

        </div>

    @endif
</div>

<style>
.timeline { max-width: 750px; margin: 0 auto; }
.timeline-item {
    background: #f7faff;
    padding: 18px 22px;
    margin-bottom: 20px;
    border-radius: 16px;
    border-left: 6px solid #9ab6ff;
}
.time { font-size: 14px; color: #6e7d9b; }
.activity { font-size: 17px; font-weight: 600; margin-bottom: 10px; color:#3a4a6b; }
.emotion-row { margin-bottom: 12px; }
.progress-bar { background:#dde6fa; height:10px; border-radius:14px; }
.progress-fill { background:#5e8bff; height:100%; transition:width .4s; border-radius:14px; }
.comment { font-style:italic; color:#56627a; margin-top:8px; }

/* Botón Editar Comentario */
.btn-edit {
    display: inline-block;
    padding: 5px 12px;
    font-size: 14px;
    border-radius: 10px;
    text-decoration: none;
    color: #35507a;
    background: #e8efff;
    border: 1px solid #b9c8ff;
    transition: .25s;
}
.btn-edit:hover {
    background: #d5e3ff;
    transform: scale(1.03);
}
</style>
@endsection
