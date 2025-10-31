@extends('layouts.app')
@section('title', 'Responder test')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --soft:#b5c8e1; --accent:#90aacc;
    --ink:#1b2a4a; --muted:#6b7280; --stroke:#e7eef7; --card:#fff;
  }
  body{ background:linear-gradient(180deg,var(--bg),#eef3f9); color:var(--ink);}
  .shell{ max-width:960px; margin:0 auto; }
  .hero{
    background:#f7fbff; border:1px solid var(--stroke); border-radius:18px;
    padding:1rem 1.25rem; margin-bottom:1rem;
    display:flex; justify-content:space-between; align-items:center; gap:.75rem; flex-wrap:wrap;
  }
  .test-title{ font-weight:800; margin:0; }
  .pill{ background:#eef6ff; border:1px solid var(--stroke); border-radius:999px; padding:.2rem .6rem; font-size:.85rem; }
  .card{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 10px 22px rgba(25,55,100,.06); overflow:hidden; margin-bottom:16px;
  }
  .card-h{ padding:12px 16px; border-bottom:1px solid var(--stroke); background:#fbfdff; font-weight:700; }
  .card-b{ padding:14px 16px; }
  .q{
    border:1px solid var(--stroke); border-radius:14px; padding:12px; margin-bottom:12px; background:#fff;
  }
  .q-title{ font-weight:700; margin:0 0 .35rem; }
  .opt{ display:flex; gap:.5rem; align-items:center; padding:.35rem .4rem; border-radius:10px; }
  .opt:hover{ background:#f6f9ff; }
  .textarea{ width:100%; min-height:100px; border:1px solid var(--stroke); border-radius:12px; padding:.6rem .8rem; }
  .actions{ display:flex; justify-content:space-between; gap:.75rem; padding:12px 16px; border-top:1px solid var(--stroke); background:#fbfdff; }
  .btn-primary{
    background:#2563eb; color:#fff; border:none; border-radius:10px; padding:.6rem 1rem; font-weight:700;
  }
  .btn-ghost{ background:#fff; border:1px solid var(--stroke); border-radius:10px; padding:.6rem 1rem; }
  .req{ color:#b91c1c; font-size:.85rem; }
  .progress-wrap{ display:flex; align-items:center; gap:.6rem; }
  .progress{
    height:10px; background:#e9eef7; border-radius:999px; width:200px; overflow:hidden;
  }
  .progress > span{ display:block; height:100%; background:linear-gradient(90deg,var(--soft),var(--accent)); width:0%; transition:width .2s ease; }
</style>
@endpush

@section('content')
<div class="shell">
  {{-- Encabezado --}}
  <div class="hero">
    <div>
      <h1 class="test-title h4">{{ $asignacion->nombreTest }}</h1>
      <div class="text-muted small">
        Asignado: {{ \Carbon\Carbon::parse($asignacion->fechaAsignacion)->format('d/m/Y H:i') }}
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="pill">Paciente</span>
      <a class="btn-ghost" href="{{ route('paciente.tests.index') }}"><i class="bi bi-arrow-left"></i> Mis tests</a>
    </div>
  </div>

  {{-- Descripción del test --}}
  @if(!empty($asignacion->descripcionTest))
  <div class="card">
    <div class="card-h">Descripción</div>
    <div class="card-b">
      <p class="mb-0 text-muted">{{ $asignacion->descripcionTest }}</p>
    </div>
  </div>
  @endif

  {{-- Formulario --}}
  <form id="formTest" method="POST" action="{{ route('paciente.tests.guardar', $asignacion->idAsignacionTest) }}">
    @csrf

    <div class="card">
      <div class="card-h d-flex justify-content-between align-items-center">
        <span>Cuestionario</span>
        <div class="progress-wrap">
          <small class="text-muted"><span id="doneCount">0</span>/<span id="totalCount">{{ $preguntas->count() }}</span> respondidas</small>
          <div class="progress"><span id="bar"></span></div>
        </div>
      </div>

      <div class="card-b">
        @foreach($preguntas as $p)
          @php
            $tipo = $p->tipo; // 'opcion_unica', 'opcion_multiple', 'abierta'
            $ops  = $opcionesPorPregunta[$p->idPregunta] ?? [];
            $nameBase = "respuestas[{$p->idPregunta}]";
          @endphp

          <div class="q" data-qid="{{ $p->idPregunta }}" data-tipo="{{ $tipo }}">
            <p class="q-title">{{ $p->orden }}. {{ $p->texto }} <span class="req d-none">*</span></p>

            @if($tipo === 'opcion_unica')
              @foreach($ops as $op)
                <label class="opt">
                  <input type="radio" name="{{ $nameBase }}" value="{{ $op->idOpcion }}">
                  <span>{{ $op->etiqueta }}</span>
                </label>
              @endforeach

            @elseif($tipo === 'opcion_multiple')
              @foreach($ops as $op)
                <label class="opt">
                  <input type="checkbox" name="{{ $nameBase }}[]" value="{{ $op->idOpcion }}">
                  <span>{{ $op->etiqueta }}</span>
                </label>
              @endforeach

            @elseif($tipo === 'abierta')
              <textarea class="textarea" name="{{ $nameBase }}" placeholder="Escribe tu respuesta..."></textarea>
            @endif

          </div>
        @endforeach

        @if ($errors->any())
          <div class="alert alert-danger mt-2">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>

      <div class="actions">
        <button type="button" class="btn-ghost" onclick="history.back()"><i class="bi bi-arrow-left"></i> Volver</button>
        <button type="submit" class="btn-primary">Enviar respuestas</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // Progreso simple (cuenta preguntas contestadas)
  const total = Number(document.getElementById('totalCount').textContent || '0');
  const doneEl = document.getElementById('doneCount');
  const bar = document.getElementById('bar');

  function computeDone(){
    let done = 0;
    document.querySelectorAll('.q').forEach(q=>{
      const tipo = q.dataset.tipo;
      if (tipo === 'abierta'){
        const t = q.querySelector('textarea');
        if (t && t.value.trim() !== '') done++;
      } else if (tipo === 'opcion_unica'){
        if (q.querySelector('input[type=radio]:checked')) done++;
      } else if (tipo === 'opcion_multiple'){
        if (q.querySelectorAll('input[type=checkbox]:checked').length > 0) done++;
      }
    });
    doneEl.textContent = done;
    const pct = total ? Math.round(done * 100 / total) : 0;
    bar.style.width = pct + '%';
  }

  document.addEventListener('input', e=>{
    if (e.target.matches('input, textarea')) computeDone();
  });

  // Validación básica al enviar (todas obligatorias)
  document.getElementById('formTest').addEventListener('submit', function(e){
    let ok = true;
    document.querySelectorAll('.q').forEach(q=>{
      const tipo = q.dataset.tipo;
      let valid = false;
      if (tipo === 'abierta'){
        const t = q.querySelector('textarea');
        valid = t && t.value.trim() !== '';
      } else if (tipo === 'opcion_unica'){
        valid = !!q.querySelector('input[type=radio]:checked');
      } else if (tipo === 'opcion_multiple'){
        valid = q.querySelectorAll('input[type=checkbox]:checked').length > 0;
      }
      q.querySelector('.req')?.classList.toggle('d-none', valid);
      if (!valid) ok = false;
    });
    if (!ok){
      e.preventDefault();
      alert('Por favor, responde todas las preguntas antes de enviar.');
    }
  });

  // init
  computeDone();
</script>
@endpush
