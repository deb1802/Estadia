@extends('layouts.app')
@section('title','Mis tests psicológicos')

@php
  use Illuminate\Support\Str;
@endphp

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --soft:#b5c8e1; --accent:#90aacc;
    --ink:#1b2a4a; --muted:#6b7280; --stroke:#e7eef7; --card:#fff;
    --ink-strong:#0e2442;
  }
  body{ background:linear-gradient(180deg,var(--bg),#eef3f9); color:var(--ink); }
  .page-wrap{ max-width:1100px; margin:0 auto; padding:18px 14px 24px; }

  /* === Bloque unificado (hero + barra + botón) === */
  .hero-section{
    background:#f8fbff;
    border:1px solid var(--stroke);
    border-radius:18px;
    box-shadow:0 8px 22px rgba(20,40,70,.06);
    padding:1.5rem 1.25rem 1.75rem;
    display:flex; flex-direction:column; align-items:center; gap:1.1rem;
  }
  .hero-section h1{
    font-size:1.75rem; font-weight:900; color:var(--ink-strong);
    text-align:center; margin:0;
  }
  .hero-section p{
    font-size:.95rem; color:var(--muted); text-align:center; margin:0;
  }

  /* === Toolbar integrada === */
  .toolbar{
    display:flex; flex-wrap:wrap; align-items:center; justify-content:center;
    gap:10px; background:#fff; border:1px solid var(--stroke);
    border-radius:14px; padding:.6rem .8rem;
    width:100%; max-width:880px;
    box-shadow:0 2px 6px rgba(20,40,70,.04);
  }
  .toolbar .label{
    display:inline-flex; align-items:center; gap:6px; color:#4b5e7a; font-weight:600; font-size:.9rem;
  }
  .input-nice, .select-nice{
    background:#fff; border:1px solid var(--stroke); border-radius:10px;
    padding:.55rem .75rem; min-width:240px;
    box-shadow:0 1px 0 rgba(0,0,0,.02);
  }

  /* === Botón volver (debajo) === */
  .btn-soft{
    background:#fff; border:1px solid var(--stroke); color:#263a56;
    border-radius:999px; padding:.55rem 1.25rem; font-weight:600;
    box-shadow:0 2px 5px rgba(0,0,0,.05);
    transition:all .18s ease;
  }
  .btn-soft:hover{ transform:translateY(-1px); box-shadow:0 6px 14px rgba(20,40,70,.10); }

  /* === Grid === */
  .grid{
    display:grid; gap:16px;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    max-width:1000px; margin:22px auto 0;
  }

  .card-test{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 10px 24px rgba(25,55,100,.06); overflow:hidden;
    display:flex; flex-direction:column;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .card-test:hover{ transform:translateY(-2px); box-shadow:0 14px 28px rgba(25,55,100,.10); }
  .thumb{
    height:112px; background:linear-gradient(90deg,var(--soft),var(--accent));
    display:flex; align-items:center; justify-content:center;
    color:#0e2a43; font-weight:800; font-size:1rem;
  }
  .body{ padding:12px 12px 8px; }
  .title-row{ display:flex; justify-content:space-between; gap:8px; align-items:center; }
  .title{ margin:0; font-weight:900; font-size:1.05rem; color:var(--ink-strong); line-height:1.2; }
  .pill{ background:#eef6ff; border:1px solid var(--stroke); border-radius:999px; padding:.2rem .6rem; font-size:.78rem; font-weight:700; color:#1c3455; }
  .meta{ display:flex; gap:10px; flex-wrap:wrap; margin-top:.45rem; font-size:.9rem; color:var(--muted); }
  .meta i{ opacity:.85; margin-right:4px; }

  .foot{ display:flex; justify-content:space-between; align-items:center; gap:8px; padding:10px 12px; border-top:1px solid var(--stroke); background:#fafcff; }
  .btn-primary{
    background:#2563eb; color:#fff; border:none; border-radius:12px; padding:.55rem .95rem; font-weight:800;
    display:inline-flex; align-items:center; gap:.4rem; box-shadow:0 6px 16px rgba(37,99,235,.18);
    transition:transform .15s ease, box-shadow .15s ease, filter .15s ease;
  }
  .btn-primary:hover{ transform:translateY(-1px); filter:brightness(1.03); box-shadow:0 10px 22px rgba(37,99,235,.22); }

  .status{ font-size:.86rem; display:inline-flex; align-items:center; gap:.4rem; font-weight:700; }
  .status .dot{ width:9px; height:9px; border-radius:50%; display:inline-block; }
  .st-pend .dot{ background:#f59e0b; }  .st-pend{ color:#a16207; }
  .st-resp .dot{ background:#10b981; }  .st-resp{ color:#065f46; }
  .st-exp  .dot{ background:#ef4444; }  .st-exp{ color:#7f1d1d; }

  .empty{ text-align:center; background:#fff; border:1px dashed var(--soft); border-radius:16px; padding:28px; color:#5b6b84; max-width:720px; margin:18px auto 0; }
</style>
@endpush

@section('content')
<div class="page-wrap">
  {{-- ==== HERO + filtros + volver (todo unificado) ==== --}}
  <div class="hero-section">
    <div class="text-center">
      <h1>Mis tests psicológicos</h1>
      <p>Gestiona y responde tus evaluaciones asignadas. Usa los filtros para encontrarlas más rápido.</p>
    </div>

    <div class="toolbar" role="search" aria-label="Filtrar tests">
      <span class="label"><i class="bi bi-funnel"></i> Filtros</span>
      <input id="q" type="text" class="input-nice" placeholder="Buscar por nombre o trastorno…" aria-label="Buscar por nombre o trastorno">
      <select id="fEstado" class="select-nice" aria-label="Filtrar por estado">
        <option value="">Todos los estados</option>
        <option value="pendiente">Pendiente</option>
        <option value="respondido">Respondido</option>
        <option value="expirado">Expirado</option>
      </select>
    </div>

    <a href="{{ route('paciente.dashboard') }}" class="btn-soft mt-2">
      <i class="bi bi-arrow-90deg-left me-1"></i> Volver al panel
    </a>
  </div>

  {{-- ==== GRID ==== --}}
  <div class="grid" id="cards">
    @forelse(($asignaciones ?? []) as $a)
      @php
        $estado = $a->estado ?? ( $a->fechaRespuesta ? 'respondido' : 'pendiente' );
        $statusClass = $estado==='respondido' ? 'st-resp' : ($estado==='expirado' ? 'st-exp' : 'st-pend');
      @endphp
      <div class="card-test"
           data-name="{{ Str::lower(($a->nombreTest ?? '')) }}"
           data-tipo="{{ Str::lower(($a->tipoTrastorno ?? '')) }}"
           data-estado="{{ $estado }}">
        <div class="thumb">
          <i class="bi bi-clipboard2-pulse me-2"></i>{{ $a->tipoTrastorno ?? 'Evaluación' }}
        </div>

        <div class="body">
          <div class="title-row">
            <h3 class="title" title="{{ $a->nombreTest }}">{{ $a->nombreTest }}</h3>
            <span class="pill"><i class="bi bi-tags me-1"></i>{{ $a->tipoTrastorno ?? 'General' }}</span>
          </div>

          <div class="meta">
            <span><i class="bi bi-calendar2-plus"></i> Asignado: {{ \Carbon\Carbon::parse($a->fechaAsignacion)->format('d/m/Y H:i') }}</span>
            @if($a->fechaRespuesta)
              <span><i class="bi bi-check2-circle"></i> Respondido: {{ \Carbon\Carbon::parse($a->fechaRespuesta)->format('d/m/Y H:i') }}</span>
            @endif
            @if(!is_null($a->puntajeTotal))
              <span><i class="bi bi-bar-chart"></i> Puntaje: <strong>{{ $a->puntajeTotal }}</strong></span>
            @endif
          </div>
        </div>

        <div class="foot">
          <div class="status {{ $statusClass }}">
            <span class="dot"></span> {{ ucfirst($estado) }}
          </div>

          @if(!$a->fechaRespuesta)
            <a class="btn-primary" href="{{ route('paciente.tests.responder', $a->idAsignacionTest) }}">
              <i class="bi bi-pencil-square"></i> Responder
            </a>
          @else
            <span class="text-success fw-semibold">
              <i class="bi bi-check2-circle me-1"></i> Completado
            </span>
          @endif
        </div>
      </div>
    @empty
      <div class="empty">
        <div class="fs-5 fw-bold mb-1">No tienes tests asignados por el momento</div>
        <p class="mb-0">Cuando tu médico te asigne uno, aparecerá aquí para que puedas responderlo.</p>
      </div>
    @endforelse
  </div>

  @if(isset($asignaciones) && method_exists($asignaciones,'links'))
    <div class="mt-3 d-flex justify-content-center">
      {{ $asignaciones->links() }}
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  // Filtro en vivo
  (function(){
    const q = document.getElementById('q');
    const fEstado = document.getElementById('fEstado');
    const cards = document.querySelectorAll('#cards .card-test');

    function filtrar(){
      const term = (q.value || '').trim().toLowerCase();
      const est  = fEstado.value;
      cards.forEach(c=>{
        const n = c.dataset.name || '';
        const t = c.dataset.tipo || '';
        const e = c.dataset.estado || '';
        const matchTxt = !term || n.includes(term) || t.includes(term);
        const matchEst = !est || e === est;
        c.style.display = (matchTxt && matchEst) ? '' : 'none';
      });
    }
    q.addEventListener('input', filtrar);
    fEstado.addEventListener('change', filtrar);
  })();
</script>
@endpush
