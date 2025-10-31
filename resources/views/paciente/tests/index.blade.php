@extends('layouts.app')
@section('title','Mis tests psicológicos')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --soft:#b5c8e1; --accent:#90aacc;
    --ink:#1b2a4a; --muted:#6b7280; --stroke:#e7eef7; --card:#fff;
  }
  body{ background:linear-gradient(180deg,var(--bg),#eef3f9); color:var(--ink); }
  .toolbar{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    background:#f7fbff; border:1px solid var(--stroke); border-radius:16px; padding:.75rem;
  }
  .input-nice, .select-nice{
    background:#fff; border:1px solid var(--stroke); border-radius:12px; padding:.55rem .75rem; min-width:240px;
  }
  .grid{ display:grid; gap:14px; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); }
  .card-test{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 10px 24px rgba(25,55,100,.06); overflow:hidden; display:flex; flex-direction:column;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .card-test:hover{ transform:translateY(-1px); box-shadow:0 14px 28px rgba(25,55,100,.1);}
  .thumb{
    height:110px; background:linear-gradient(90deg,var(--soft),var(--accent));
    display:flex; align-items:center; justify-content:center; color:#0e2a43; font-weight:700;
  }
  .body{ padding:12px; }
  .title-row{ display:flex; justify-content:space-between; gap:8px; align-items:center; }
  .title{ margin:0; font-weight:800; font-size:1.05rem; }
  .pill{ background:#eef6ff; border:1px solid var(--stroke); border-radius:999px; padding:.15rem .55rem; font-size:.78rem; }
  .meta{ display:flex; gap:10px; flex-wrap:wrap; margin-top:.35rem; font-size:.9rem; color:var(--muted); }
  .meta i{ opacity:.8; margin-right:4px; }
  .foot{ display:flex; justify-content:space-between; align-items:center; gap:8px; padding:10px 12px; border-top:1px solid var(--stroke); background:#fafcff; }
  .btn-primary{
    background:#2563eb; color:#fff; border:none; border-radius:10px; padding:.5rem .8rem; font-weight:700;
  }
  .status{ font-size:.86rem; }
  .status .dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; vertical-align:middle; }
  .st-pend .dot{ background:#f59e0b; }  .st-pend{ color:#a16207; }
  .st-resp .dot{ background:#10b981; }  .st-resp{ color:#065f46; }
  .st-exp  .dot{ background:#ef4444; }  .st-exp{ color:#7f1d1d; }
</style>
@endpush

@section('content')
<section class="content-header mb-3">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="h3 fw-bold m-0">Mis tests psicológicos</h1>
    <a href="{{ route('paciente.dashboard') }}" class="btn btn-light border rounded-3">
      <i class="bi bi-grid"></i> Panel
    </a>
  </div>
</section>

{{-- Toolbar: búsqueda/filtrado simple (cliente) --}}
<div class="toolbar mb-3">
  <div class="d-flex gap-2 flex-wrap align-items-center">
    <div class="d-flex align-items-center gap-2">
      <span class="text-muted small"><i class="bi bi-funnel"></i> Filtros</span>
    </div>
    <input id="q" type="text" class="input-nice" placeholder="Buscar por nombre o trastorno…">
    <select id="fEstado" class="select-nice">
      <option value="">Todos los estados</option>
      <option value="pendiente">Pendiente</option>
      <option value="respondido">Respondido</option>
      <option value="expirado">Expirado</option>
    </select>
  </div>
</div>

{{-- Grid de tarjetas --}}
<div class="grid" id="cards">
  @forelse(($asignaciones ?? []) as $a)
    @php
      // $a esperado con joins: idAsignacionTest, nombreTest, tipoTrastorno, fechaAsignacion, fechaRespuesta, puntajeTotal, estado
      $estado = $a->estado ?? ( $a->fechaRespuesta ? 'respondido' : 'pendiente' );
      $statusClass = $estado==='respondido' ? 'st-resp' : ($estado==='expirado' ? 'st-exp' : 'st-pend');
    @endphp
    <div class="card-test" data-name="{{ Str::lower(($a->nombreTest ?? '')) }}" data-tipo="{{ Str::lower(($a->tipoTrastorno ?? '')) }}" data-estado="{{ $estado }}">
      <div class="thumb">
        {{ $a->tipoTrastorno ?? 'Evaluación' }}
      </div>
      <div class="body">
        <div class="title-row">
          <h3 class="title">{{ $a->nombreTest }}</h3>
          <span class="pill">{{ $a->tipoTrastorno ?? 'General' }}</span>
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
          {{-- Aún no respondido: botón para ir a contestar --}}
          <a class="btn-primary" href="{{ route('paciente.tests.responder', $a->idAsignacionTest) }}">
            Responder
          </a>
        @else
    
        @endif
      </div>
    </div>
  @empty
    <div class="text-muted">No tienes tests asignados por el momento.</div>
  @endforelse
</div>

{{-- Paginación si viene paginado desde el controlador --}}
@if(isset($asignaciones) && method_exists($asignaciones,'links'))
  <div class="mt-3">
    {{ $asignaciones->links() }}
  </div>
@endif
@endsection

@push('scripts')
<script>
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
</script>
@endpush
