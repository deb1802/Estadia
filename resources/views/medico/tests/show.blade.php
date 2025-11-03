{{-- resources/views/medico/tests/show.blade.php  (reusada por admin) --}}
@extends('layouts.app')
@php
  // Detecta área por URL
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
  $isAdmin   = !request()->is('medico/*');
@endphp

@section('title', 'Detalle del test')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9;
    --card:#ffffff;
    --ink:#1b2a4a;
    --muted:#5b6b84;
    --soft:#b5c8e1;
    --accent:#90aacc;
    --stroke:#e7eef7;
  }
  body{ background:var(--bg); color:var(--ink); }

  /* Layout centrado */
  .page-wrap{ padding:18px 14px; }
  .container-narrow{ max-width:980px; margin:0 auto; }

  /* Header */
  .page-head{ margin-bottom:.75rem; }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }

  .meta-row{ display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
  .chip{
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem;
    font-size:.83rem; color:#1f3b5a; font-weight:600;
  }
  .dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
  .on{ background:#22c55e; } .off{ background:#94a3b8; }

  /* Botones */
  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:10px; padding:.5rem .8rem; font-weight:700;
    color:#1c3455; white-space:nowrap;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  .btn-soft {
    background:#fff; border:1px solid #ccc; color:#333;
    border-radius:50px; padding:.5rem 1.25rem; transition:.2s ease; white-space:nowrap;
  }
  .btn-soft:hover{ background:#f2f2f2; color:#000; }

  .btn-wrap{ display:flex; flex-wrap:wrap; gap:10px; }
  .btn-wrap-right{ justify-content:flex-end; }
  @media (max-width:576px){
    .btn-wrap,.btn-wrap-right{ display:grid; grid-template-columns:1fr; }
  }

  /* Tarjeta principal */
  .card{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 6px 20px rgba(10,30,60,.06);
    overflow:hidden;
  }
  .card-head{
    background: linear-gradient(90deg, var(--soft), var(--accent));
    padding:14px 16px; font-weight:800; color:#0d223d;
  }
  .card-body{ padding:16px; }

  /* Secciones */
  .section-title{
    font-size:1.1rem; font-weight:800; margin-top:1.2rem;
    border-bottom:2px solid var(--soft); padding-bottom:4px; color:#162945;
    display:flex; align-items:center; gap:8px;
  }

  /* Preguntas y opciones */
  .q-card{
    border:1px solid var(--stroke); border-radius:14px; background:#fff;
    margin-bottom:12px; overflow:hidden;
  }
  .q-head{
    background:#eef4fd; border-bottom:1px solid var(--stroke);
    padding:8px 12px; display:flex; align-items:center; justify-content:space-between; gap:8px;
  }
  .q-title{ font-weight:800; margin:0; color:#0f1d36; }
  .q-badges{ display:flex; gap:8px; flex-wrap:wrap; }

  .q-body{ padding:12px; }
  .q-type{ font-size:.9rem; color:#35507a; display:flex; align-items:center; gap:6px; }

  .opt-list{ display:flex; flex-direction:column; gap:6px; margin-top:8px; }
  .opt-item{
    display:flex; align-items:center; justify-content:space-between; gap:8px;
    background:#f7fbff; border:1px solid var(--stroke); border-radius:10px; padding:8px 10px;
  }
  .opt-left{ display:flex; align-items:center; gap:10px; }
  .opt-order{
    background:#eaf1ff; border:1px solid var(--stroke); color:#173257; font-weight:800;
    border-radius:8px; min-width:36px; text-align:center; padding:3px 8px;
  }
  .opt-label{ color:#0f1d36; font-weight:600; }

  .badge-soft{
    background:#eef4fd; border:1px solid var(--stroke); color:#163154;
    border-radius:999px; padding:.15rem .55rem; font-weight:700; font-size:.82rem;
  }
  .badge-score{
    background:#e6f7ef; border:1px solid #bdebd2; color:#0f5132;
    border-radius:999px; padding:.15rem .55rem; font-weight:800; font-size:.82rem;
  }

  /* Rangos */
  .range-card{
    border:1px solid var(--stroke); border-radius:12px; background:#f9fcff;
    padding:10px 12px; margin-bottom:10px;
  }
  .range-dx{ font-weight:800; color:#0f1d36; }
  .range-span{ color:#35507a; font-weight:700; }

  /* Texto descriptivo */
  .lead-muted{ color:#5b6b84; }
</style>
@endpush

@section('content')


<div class="page-wrap">
  <div class="container-narrow">

    <!-- Header centrado: Título -> Chips -> Botón Volver -> Acciones -->
    <section class="page-head">
      <h1 class="page-title h3 mb-1">Detalle del test</h1>

      <div class="meta-row mb-2">
        <span class="chip"><i class="bi bi-hash me-1"></i>ID {{ $test->idTest }}</span>
        @if($test->tipoTrastorno)
          <span class="chip"><i class="bi bi-heart-pulse me-1"></i>{{ $test->tipoTrastorno }}</span>
        @endif
        <span class="chip">
          <span class="dot {{ $test->estado==='activo'?'on':'off' }}"></span>{{ ucfirst($test->estado) }}
        </span>
        @if($test->fechaCreacion)
          <span class="chip"><i class="bi bi-calendar-event me-1"></i>{{ \Illuminate\Support\Carbon::parse($test->fechaCreacion)->format('d/m/Y') }}</span>
        @endif
      </div>

      <!-- Botón Volver al dashboard (blanco redondo) -->
      <div class="btn-wrap mb-2">
        <button type="button" class="btn btn-soft"
                onclick="window.location='{{ route($routeArea.'tests.index') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </button>
      </div>

      <!-- Acciones a la derecha -->
      <div class="btn-wrap btn-wrap-right">
        @if(Route::has($routeArea.'tests.builder.edit'))
          <a href="{{ route($routeArea.'tests.builder.edit', $test->idTest) }}" class="btn btn-ghost">
            <i class="bi bi-sliders me-1"></i> Editar contenido
          </a>
        @endif
        <a href="{{ route($routeArea.'tests.edit', $test->idTest) }}" class="btn btn-ghost">
          <i class="bi bi-pencil-square me-1"></i> Editar datos
        </a>
        <a href="{{ route($routeArea.'tests.index') }}" class="btn btn-ghost">
          <i class="bi bi-list-ul me-1"></i> Listado de tests
        </a>
      </div>
    </section>

    <!-- Contenido -->
    <section class="content-body">
      <div class="card">
        <div class="card-head">
          <i class="bi bi-clipboard-data me-1"></i> {{ $test->nombre }}
        </div>
        <div class="card-body">

          @if($test->descripcion)
            <p class="lead-muted mb-3"><i class="bi bi-info-circle me-1"></i>{{ $test->descripcion }}</p>
          @endif

          {{-- Preguntas --}}
          <div class="section-title">
            <i class="bi bi-ui-checks-grid"></i> Preguntas
          </div>

          @forelse($test->preguntas as $p)
            <div class="q-card">
              <div class="q-head">
                <h5 class="q-title mb-0">{{ $p->orden }}. {{ $p->texto }}</h5>
                <div class="q-badges">
                  <span class="badge-soft"><i class="bi bi-diagram-3 me-1"></i>{{ ucfirst(str_replace('_',' ',$p->tipo)) }}</span>
                  @if(in_array($p->tipo, ['opcion_unica','opcion_multiple']))
                    <span class="badge-soft" title="Cantidad de opciones">
                      <i class="bi bi-list-check me-1"></i>{{ count($p->opciones) }} opc.
                    </span>
                  @endif
                </div>
              </div>

              <div class="q-body">
                @if(in_array($p->tipo, ['opcion_unica','opcion_multiple']))
                  <div class="opt-list">
                    @foreach($p->opciones as $o)
                      <div class="opt-item">
                        <div class="opt-left">
                          <span class="opt-order" title="Orden">{{ $o->orden }}</span>
                          <span class="opt-label">{{ $o->etiqueta }}</span>
                        </div>
                        <span class="badge-score" title="Puntaje de la opción">
                          <i class="bi bi-bullseye me-1"></i> {{ $o->puntaje }}
                        </span>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="q-type">
                    <i class="bi bi-chat-left-text"></i> Respuesta abierta
                  </div>
                @endif
              </div>
            </div>
          @empty
            <p class="text-muted">No se han agregado preguntas aún.</p>
          @endforelse

          {{-- Rangos --}}
          <div class="section-title">
            <i class="bi bi-graph-up-arrow"></i> Rangos de evaluación
          </div>

          @forelse($test->rangos as $r)
            <div class="range-card">
              <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                  <span class="range-dx"><i class="bi bi-activity me-1"></i>{{ $r->diagnostico }}</span>
                  <span class="range-span ms-1">({{ $r->minPuntaje }} – {{ $r->maxPuntaje }})</span>
                </div>
                @if($r->descripcion)
                  <div class="text-muted">{{ $r->descripcion }}</div>
                @endif
              </div>
            </div>
          @empty
            <p class="text-muted">No se han definido rangos aún.</p>
          @endforelse

        </div>
      </div>

      <div class="mt-3 btn-wrap">
        <a href="{{ route($routeArea.'tests.index') }}" class="btn btn-ghost">
          <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
      </div>
    </section>

    {{-- Navbar inferior sólo para MÉDICO --}}
    @if(!$isAdmin)
      @include('medico.bottom-navbar')
    @endif

  </div>
</div>
@endsection
