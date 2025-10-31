@extends('layouts.app')

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

  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:.75rem;
  }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:10px; padding:.45rem .65rem; font-weight:700;
    color:#1c3455;
  }
  .btn-ghost:hover{ background:#f7fbff; }
  .btn-soft{
    background: var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:12px;
    padding:.6rem .9rem;
  }

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
  .section-title{
    font-size:1.1rem; font-weight:700; margin-top:1.5rem; border-bottom:2px solid var(--soft); padding-bottom:4px;
  }
  .chip{ background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem; font-size:.83rem; color:#1f3b5a; font-weight:600; }
  .dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
  .on{ background:#22c55e; } .off{ background:#94a3b8; }

  .question{
    border:1px solid var(--stroke); border-radius:14px; background:#fff; padding:12px; margin-bottom:10px;
  }
  .question h5{ font-weight:700; margin-bottom:6px; }
  .option-list{ margin-left:1rem; }
  .option{ background:#f7fbff; border:1px solid var(--stroke); border-radius:10px; padding:4px 10px; margin-bottom:4px; }
  .range-card{
    border:1px solid var(--stroke); border-radius:10px; background:#f9fcff; padding:8px 12px; margin-bottom:8px;
  }
</style>
@endpush

@section('content')
<section class="content-header">
  <div class="page-head">
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left"></i></a>
      <h1 class="page-title h4">Detalle del test</h1>
    </div>
    <div class="d-flex gap-2">
      @if(Route::has('medico.tests.builder.edit'))
      <a href="{{ route('medico.tests.builder.edit', $test->idTest) }}" class="btn btn-soft">
        <i class="bi bi-sliders me-1"></i> Editar contenido
      </a>
      @endif
      <a href="{{ route('medico.tests.edit', $test->idTest) }}" class="btn btn-ghost">
        <i class="bi bi-pencil-square me-1"></i> Editar datos
      </a>
    </div>
  </div>
</section>

<section class="content-body">
  <div class="card">
    <div class="card-head">
      <i class="bi bi-clipboard-data me-1"></i> {{ $test->nombre }}
    </div>
    <div class="card-body">
      <div class="mb-2">
        <span class="chip"><i class="bi bi-hash me-1"></i>ID {{ $test->idTest }}</span>
        @if($test->tipoTrastorno)
          <span class="chip"><i class="bi bi-heart-pulse me-1"></i>{{ $test->tipoTrastorno }}</span>
        @endif
        <span class="chip">
          <span class="dot {{ $test->estado==='activo'?'on':'off' }}"></span>
          {{ ucfirst($test->estado) }}
        </span>
        <span class="chip"><i class="bi bi-calendar-event me-1"></i>{{ \Illuminate\Support\Carbon::parse($test->fechaCreacion)->format('d/m/Y') }}</span>
      </div>

      @if($test->descripcion)
        <p class="text-muted">{{ $test->descripcion }}</p>
      @endif

      {{-- ===== Preguntas ===== --}}
      <div class="section-title"><i class="bi bi-ui-checks-grid me-1"></i> Preguntas</div>
      @forelse($test->preguntas as $p)
        <div class="question">
          <h5>{{ $p->orden }}. {{ $p->texto }}</h5>
          <div class="hint mb-1"><i class="bi bi-chat-dots me-1"></i>{{ ucfirst(str_replace('_',' ',$p->tipo)) }}</div>
          @if(in_array($p->tipo, ['opcion_unica','opcion_multiple']))
            <div class="option-list">
              @foreach($p->opciones as $o)
                <div class="option d-flex justify-content-between">
                  <span>{{ $o->orden }}. {{ $o->etiqueta }}</span>
                  <span class="text-muted">Puntaje: <strong>{{ $o->puntaje }}</strong></span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @empty
        <p class="text-muted">No se han agregado preguntas aún.</p>
      @endforelse

      {{-- ===== Rangos ===== --}}
      <div class="section-title"><i class="bi bi-graph-up-arrow me-1"></i> Rangos de evaluación</div>
      @forelse($test->rangos as $r)
        <div class="range-card">
          <div class="d-flex justify-content-between flex-wrap">
            <div>
              <strong>{{ $r->diagnostico }}</strong>
              <span class="text-muted">({{ $r->minPuntaje }} – {{ $r->maxPuntaje }})</span>
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

  <div class="mt-3">
    <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i> Volver al listado</a>
  </div>
</section>
@endsection

