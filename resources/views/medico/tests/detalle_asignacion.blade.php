@extends('layouts.app')

@section('title', 'Detalle de test respondido')

@push('styles')
<style>
  :root{
    --c1:#d7dfe9; /* azul suave */
    --c2:#b5c8e1; /* azul medio */
    --c3:#f0e2f8; /* morado pastel */
    --ink:#0f172a;
    --muted:#6b7280;
  }

  body{ color:var(--ink); background:linear-gradient(180deg,#eef3fb,#f7f5fb); }

  .wrap-page{ max-width: 1100px; margin:0 auto; padding: 16px; }

  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    margin-bottom:14px;
  }
  .btn-soft{
    background:#fff; border:1px solid var(--c2); color:#1f2937; border-radius:10px; padding:.5rem .8rem;
    box-shadow:0 3px 10px rgba(20,40,70,.08);
  }
  .btn-soft:hover{ background:#f7f9fc; }

  /* === Hero actualizado === */
  .hero{
    background: linear-gradient(180deg, var(--c1) 0%, var(--c3) 100%);
    border:1px solid var(--c2);
    border-radius:16px;
    padding:20px 22px;
    margin-bottom:16px;
    box-shadow:0 10px 30px rgba(30,40,80,.08);
  }

  .hero .info-row{
    display:flex; flex-wrap:wrap; gap:1.2rem;
  }

  .hero .info-block{
    background:rgba(255,255,255,0.45);
    backdrop-filter:blur(6px);
    padding:10px 14px; border-radius:10px;
    box-shadow:inset 0 0 6px rgba(255,255,255,.25);
  }

  .hero .info-label{
    font-size:.9rem; font-weight:600; color:#283b5b; text-transform:uppercase; letter-spacing:.4px;
    margin-bottom:2px;
  }

  .hero .info-value{
    font-weight:700; color:#1a253f; font-size:1rem;
  }

  .pill{
    display:inline-block; padding:.2rem .6rem; border-radius:999px;
    border:1px solid #a6bce5; background:#fff; color:#203a60; font-size:.83rem; font-weight:700;
  }

  /* === Estructura general === */
  .grid-2{
    display:grid; grid-template-columns: 1fr 1fr; gap:16px;
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
    font-weight:800; color:#0f172a; background:linear-gradient(180deg,#edf3ff,#f6f8ff);
  }
  .card .card-body{ padding:1rem; }

  .list-group{ list-style:none; margin:0; padding:0; }
  .list-group-item{ padding:.75rem 1rem; border-bottom:1px solid #eef2f6; }
  .list-group-item:last-child{ border-bottom:0; }

  .answers-scroll{
    max-height: 55vh; overflow:auto; border-radius:12px; border:1px solid var(--c2);
  }

  .form-control, .form-select{
    border-radius:10px; border:1px solid var(--c2);
  }
  .btn-primary{
    background: var(--c3); border-color: var(--c3); color:#1f1f2e; font-weight:600;
  }
  .btn-primary:hover{ filter:brightness(.95); }

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
    <div class="info-row">
      <div class="info-block">
        <div class="info-label">Paciente</div>
        <div class="info-value">{{ $pacienteNombre !== '' ? $pacienteNombre : '—' }}</div>
      </div>

      <div class="info-block">
        <div class="info-label">Test</div>
        <div class="info-value">{{ $asig->nombreTest ?? '—' }}</div>
      </div>

      <div class="info-block">
        <div class="info-label">Fecha respuesta</div>
        <div class="info-value">
          {{ optional(\Carbon\Carbon::parse($asig->fechaRespuesta ?? null))->format('d/m/Y H:i') ?? '—' }}
        </div>
      </div>

      <div class="info-block">
        <div class="info-label">Puntaje total</div>
        <div class="info-value">{{ isset($asig->puntajeTotal) ? $asig->puntajeTotal : '—' }}</div>
      </div>

      <div class="info-block">
        <div class="info-label">Sugerido</div>
        <span class="pill">{{ $asig->diagnosticoSugerido ?? '—' }}</span>
      </div>

      @if(!empty($asig->diagnosticoConfirmado))
      <div class="info-block">
        <div class="info-label">Confirmado</div>
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

    {{-- Columna derecha: Confirmar diagnóstico + Asignar actividad --}}
    <div class="card">
      <div class="card-header">Confirmar diagnóstico</div>
      <div class="card-body">
        @php
          $prefillDiag = $asig->diagnosticoConfirmado ?? $asig->diagnosticoSugerido ?? '';
          $rutaAsignar = \Illuminate\Support\Facades\Route::has('medico.actividades_terap.index')
            ? route('medico.actividades_terap.index')
            : (\Illuminate\Support\Facades\Route::has('medico.actvidades_terap.index')
                ? route('medico.actvidades_terap.index')
                : '#');
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
            <label for="notasClinicas">Observaciones acerca del puntaje obtenido (opcional)</label>
            <textarea id="notasClinicas"
                      name="notas_clinicas"
                      rows="5"
                      class="form-control"
                      placeholder="Observaciones clínicas, recomendaciones, plan de seguimiento...">{{ old('notas_clinicas', $asig->notasClinicas ?? '') }}</textarea>
          </div>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check2-circle mr-1"></i> Confirmar
            </button>

            <a href="{{ route('medico.tests.index') }}" class="btn btn-soft">Ir a mis tests</a>
          </div>

          {{-- Botón adicional debajo, para evitar superposición --}}
          <div class="mt-3">
            <a href="{{ $rutaAsignar }}" class="btn btn-primary w-100">
              <i class="bi bi-clipboard2-plus mr-1"></i>
              Asignar actividad terapéutica al paciente
            </a>
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
@include('medico.bottom-navbar')
@endsection
