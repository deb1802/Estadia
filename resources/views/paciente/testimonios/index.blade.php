@extends('layouts.app')

@section('content')
<style>
  body {
    background: #e9f4ff;
  }

  .foro-wrapper {
    display: flex;
    justify-content: center;
    padding: 3rem 1rem;
  }

  .foro-container {
    width: 100%;
    max-width: 800px;
    background: #fff;
    border-radius: 18px;
    padding: 2rem 2rem 1rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
  }

  .foro-header {
    font-weight: 700;
    color: #1b3b6f;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
  }

  /* ===== Cuadro para escribir ===== */
  .composer {
    background: #f0f7ff;
    border-radius: 14px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.05);
  }

  .composer .icon-user {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #cfe2ff;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .composer textarea {
    flex: 1;
    resize: none;
    border: none;
    background: transparent;
    outline: none;
    font-size: 1rem;
  }

  .composer button {
    border: none;
    background: #1b3b6f;
    color: white;
    padding: 0.55rem 1.2rem;
    border-radius: 25px;
    font-weight: 600;
    transition: 0.3s;
  }

  .composer button:hover {
    background: #0043a7;
  }

  /* ===== Testimonios ===== */
  .testimonio {
    border-top: 1px solid #e0e8f5;
    padding-top: 1.3rem;
    margin-top: 1.3rem;
  }

  .testimonio .user-row {
    display: flex;
    gap: 0.9rem;
  }

  .user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #e2ecff;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .user-name {
    font-weight: 600;
    color: #1b3b6f;
  }

  .user-date {
    font-size: 0.85rem;
    color: #7b8da6;
  }

  .contenido {
    margin-top: 0.4rem;
    color: #2b2b2b;
    line-height: 1.55;
  }

  /* ===== Respuestas ===== */
  .respuestas {
    margin-left: 3.2rem;
    margin-top: 0.6rem;
  }

  .respuesta {
    background: #f5f9ff;
    border-radius: 12px;
    padding: 0.6rem 0.9rem;
    margin-bottom: 0.5rem;
  }

  .respuesta .r-name {
    font-weight: 600;
    color: #1b3b6f;
  }

  .respuesta .r-body {
    margin: 0.2rem 0 0;
  }

  /* ===== Mensaje vacío ===== */
  .foro-empty {
    text-align: center;
    color: #7b8da6;
    font-style: italic;
    margin-top: 1.5rem;
  }
</style>

<div class="foro-wrapper">
  <div class="foro-container">
    <h2 class="foro-header">Testimonios</h2>

    {{-- Cuadro para publicar (solo paciente) --}}
    @if(auth()->user() && auth()->user()->tipoUsuario === 'paciente')
      <form action="{{ route('paciente.testimonios.store') }}" method="POST">
        @csrf
        <div class="composer">
          <div class="icon-user">
            <i class="bi bi-person-fill fs-4 text-primary"></i>
          </div>
          <textarea name="contenido" rows="2" placeholder="Comparte tu experiencia..."></textarea>
          <button type="submit">Publicar</button>
        </div>
      </form>
    @endif

    {{-- Lista de testimonios --}}
    @forelse($testimonios as $t)
      <div class="testimonio">
        <div class="user-row">
          <div class="user-avatar">
            <i class="bi bi-person-fill fs-5 text-primary"></i>
          </div>
          <div>
            <div class="user-name">{{ $t->paciente->nombre_completo ?? 'Paciente' }}</div>
            <div class="user-date">{{ \Carbon\Carbon::parse($t->fecha)->translatedFormat('d \\de F \\de Y') }}</div>
          </div>
        </div>

        <div class="contenido">
          {!! nl2br(e($t->contenido)) !!}
        </div>

        {{-- Respuestas --}}
        <div class="respuestas">
          @foreach($t->respuestas as $r)
            <div class="respuesta">
              <div class="r-name">{{ $r->paciente->nombre_completo ?? 'Paciente' }}</div>
              <div class="r-body">{!! nl2br(e($r->contenido)) !!}</div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="foro-empty">
        Aún no hay testimonios. ¡Sé la primera persona en compartir!
      </div>
    @endforelse
  </div>
</div>
@endsection
