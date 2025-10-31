@extends('layouts.app')

@section('title', 'Detalle de test respondido')

@push('styles')
<style>
  :root{
    --c1:#d7dfe9; /* fondo suave */
    --c2:#b5c8e1; /* borde/acento */
    --c3:#90aacc; /* títulos/botones */
    --ink:#0f172a;
    --muted:#6b7280;
  }
  body{ color:var(--ink); }
  .wrap-page{ max-width: 1100px; margin:0 auto; padding: 16px; }

  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    margin-bottom:12px;
  }
  .btn-soft{
    background:#fff; border:1px solid var(--c2); color:#1f2937; border-radius:10px; padding:.5rem .8rem;
  }
  .btn-soft:hover{ background:#f7f9fc; }

  .hero{
    background: linear-gradient(180deg, var(--c1), #ffffff);
    border:1px solid var(--c2);
    border-radius:14px; padding:14px 16px; margin-bottom:14px;
  }

  .grid-2{
    display:grid; grid-template-columns: 1fr 1fr; gap:14px;
  }
  @media (max-width: 992px){
    .grid-2{ grid-template-columns: 1fr; }
  }

  .card{
    background:#fff; border:1px solid var(--c2); border-radius:14px;
    box-shadow:0 6px 18px rgba(2,6,23,.06);
  }
  .card .card-header{
    padding:.75rem 1rem; border-bottom:1px solid var(--c2);
    font-weight:800; color:#0f172a; background:#f7f9fc;
  }
  .card .card-body{ padding:1rem; }

  .list-group{ list-style:none; margin:0; padding:0; }
  .list-group-item{ padding:.75rem 1rem; border-bottom:1px solid #eef2f6; }
  .list-group-item:last-child{ border-bottom:0; }

  .pill{
    display:inline-block; padding:.15rem .5rem; border-radius:999px;
    border:1px solid var(--c2); background:#fff; color:#334155; font-size:.8rem; font-weight:700;
  }

  /* Caja de respuestas con scroll propio */
  .answers-scroll{
    max-height: 55vh; overflow:auto; border-radius:12px; border:1px solid var(--c2);
  }

  .form-control, .form-select{
    border-radius:10px; border:1px solid var(--c2);
  }
  .btn-primary{
    background: var(--c3); border-color: var(--c3);
  }
  .btn-primary:hover{
    filter: brightness(.95);
  }
  .small-muted{ color:var(--muted); font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="wrap-page">

  {{-- Encabezado + volver --}}
  <div class="page-head">
    <h1 class="h4 m-0">Detalle del test respondido</h1>

    <button type="button" class="btn btn-soft"
            onclick="window.history.length>1 ? history.back() : window.location='{{ route('medico.tests.index') }}'">
      <i class="bi bi-arrow-90deg-left mr-1"></i> Volver
    </button>
  </div>

  {{-- Mensajes flash / errores --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Hero con resumen rápido --}}
  <div class="hero">
    @php
      $pacienteNombre = trim(($asig->nomPac ?? '').' '.($asig->apePac ?? ''));
    @endphp
    <div class="d-flex flex-wrap align-items-center gap-2">
      <div class="mr-3">
        <div class="small-muted">Paciente</div>
        <div class="h6 m-0">{{ $pacienteNombre !== '' ? $pacienteNombre : '—' }}</div>
      </div>
      <div class="mr-3">
        <div class="small-muted">Test</div>
        <div class="h6 m-0">{{ $asig->nombreTest ?? '—' }}</div>
      </div>
      <div class="mr-3">
        <div class="small-muted">Fecha respuesta</div>
        <div class="h6 m-0">
          {{ optional(\Carbon\Carbon::parse($asig->fechaRespuesta ?? null))->format('d/m/Y H:i') ?? '—' }}
        </div>
      </div>
      <div class="mr-3">
        <div class="small-muted">Puntaje total</div>
        <div class="h6 m-0">{{ isset($asig->puntajeTotal) ? $asig->puntajeTotal : '—' }}</div>
      </div>
      <div class="mr-3">
        <div class="small-muted">Sugerido</div>
        <span class="pill">{{ $asig->diagnosticoSugerido ?? '—' }}</span>
      </div>
      @if(!empty($asig->diagnosticoConfirmado))
        <div class="mr-3">
          <div class="small-muted">Confirmado</div>
          <span class="pill">{{ $asig->diagnosticoConfirmado }}</span>
        </div>
      @endif
    </div>
  </div>

  <div class="grid-2">
    {{-- Columna izquierda: Respuestas (con scroll) --}}
    <div class="card">
      <div class="card-header">Detalle de respuestas</div>
      <div class="card-body p-0">
        <div class="answers-scroll">
          <ul class="list-group list-group-flush">
            @forelse($respuestas as $r)
              <li class="list-group-item">
                <div class="small-muted mb-1">{{ $r->pregunta }}</div>
                @if($r->opcion)
                  <div>Respuesta: {{ $r->opcion }}</div>
                @elseif($r->respuestaAbierta)
                  <div>Respuesta: {{ $r->respuestaAbierta }}</div>
                @else
                  <div>Respuesta: —</div>
                @endif
                <div class="small">Puntaje: {{ (int) $r->puntajeObtenido }}</div>
              </li>
            @empty
              <li class="list-group-item text-muted">Sin respuestas registradas.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    {{-- Columna derecha: Confirmar diagnóstico --}}
    <div class="card">
      <div class="card-header">Confirmar diagnóstico</div>
      <div class="card-body">
        @php
          $prefillDiag = $asig->diagnosticoConfirmado ?? $asig->diagnosticoSugerido ?? '';
        @endphp

        <form method="POST"
              action="{{ route('medico.tests.asignaciones.confirmar', $asig->idAsignacionTest) }}"
              onsubmit="this.querySelector('button[type=submit]').disabled=true;">
          @csrf

          <div class="form-group">
            <label for="diagConfirmado">Diagnóstico</label>
            <input type="text"
                  id="diagConfirmado"
                  name="diagnostico_confirmado"
                  class="form-control"
                  maxlength="150"
                  value="{{ old('diagnostico_confirmado', $prefillDiag) }}"
                  placeholder="Ej. Ansiedad moderada">
            <small class="form-text text-muted">
              Puedes ajustar el diagnóstico final antes de confirmarlo.
            </small>
          </div>

          <div class="form-group">
            <label for="notasClinicas">Observaciones a cerca del puntaje obtenido (opcional)</label>
            <textarea id="notasClinicas"
                      name="notas_clinicas"
                      rows="5"
                      class="form-control"
                      placeholder="Observaciones clínicas, recomendaciones, plan de seguimiento...">{{ old('notas_clinicas', $asig->notasClinicas ?? '') }}</textarea>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check2-circle mr-1"></i> Confirmar
            </button>
            <a href="{{ route('medico.tests.index') }}" class="btn btn-soft">Ir a mis tests</a>
          </div>

          @if(!empty($asig->diagnosticoConfirmado))
            <div class="small-muted mt-2">
              Última confirmación: {{ optional(\Carbon\Carbon::parse($asig->fechaConfirmacion ?? null))->format('d/m/Y H:i') ?? '—' }}
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
