@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- ========= Encabezado centrado ========= --}}
  <div class="text-center mb-4">
    <h1 class="fw-bold display-5 text-primary mb-3" style="letter-spacing:.4px;">
      <i class="fas fa-user-circle me-2"></i>
      Seguimiento de {{ $paciente->nombre }} {{ $paciente->apellido }}
    </h1>

    <button type="button" class="btn btn-soft" onclick="window.location='{{ route('medico.dashboard') }}'">
      <i class="fas fa-arrow-left me-2"></i> Volver al dashboard
    </button>
  </div>

  {{-- ========= Timeline de citas ========= --}}
  <div class="card shadow border-0 mx-auto mb-4" style="max-width: 980px;">
    <div class="card-header text-center fw-semibold text-white" style="background-color: #b5c8e1;">
      <i class="fas fa-calendar-check me-2"></i> Línea de tiempo de citas
    </div>
    <div class="card-body p-4">
      @if($citas->isEmpty())
        <p class="text-center text-muted mb-0">No hay citas registradas.</p>
      @else
        <div class="timeline">
          @foreach($citas as $index => $cita)
            <div class="timeline-item">
              <div class="timeline-icon 
                  @if($cita->estado == 'realizada') bg-success 
                  @elseif($cita->estado == 'programada') bg-warning 
                  @elseif($cita->estado == 'cancelada') bg-danger 
                  @else bg-secondary @endif">
                <i class="fas fa-stethoscope text-white"></i>
              </div>
              <div class="timeline-content">
                <h6 class="fw-bold text-dark mb-1">
                  {{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}
                </h6>
                <p class="mb-1">
                  <strong>Estado:</strong>
                  <span class="text-capitalize">{{ $cita->estado }}</span>
                </p>
                <p class="mb-1"><strong>Motivo:</strong> {{ $cita->motivo }}</p>
                <p class="text-muted small mb-0">
                  <i class="fas fa-map-marker-alt me-1"></i> {{ $cita->ubicacion }}
                </p>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- ========= Actividades recientes ========= --}}
  <div class="card shadow-sm border-0 mx-auto mb-4" style="max-width: 980px;">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-app-indicator text-primary"></i>
        <span class="fw-bold">Actividades realizadas recientemente</span>
      </div>
      <small class="text-muted">Últimas 12</small>
    </div>

    <div class="activity-center py-2">
      @forelse($actividades as $a)
        <div class="list-group-item border-0 px-0">
          <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <span class="badge rounded-pill bg-soft-blue text-nowrap">{{ ucfirst($a->tipoContenido) }}</span>
            <span class="badge {{ $a->estado === 'completada' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' }}">
              {{ ucfirst($a->estado) }}
            </span>
          </div>

          <div class="fs-6 fw-semibold">{{ $a->titulo }}</div>

          <div class="text-muted small mt-1">
            <span class="me-2"><strong>Categoría:</strong> {{ $a->categoriaTerapeutica }}</span>
            <span><strong>Dirigida a:</strong> {{ $a->diagnosticoDirigido }}</span>
          </div>

          <div class="text-muted small">
            <strong>Asignada:</strong> {{ \Carbon\Carbon::parse($a->fechaAsignacion)->format('d/m/Y') }}
            @if($a->estado === 'completada' && $a->fechaFinalizacion)
              · <strong>Finalizada:</strong> {{ \Carbon\Carbon::parse($a->fechaFinalizacion)->format('d/m/Y') }}
            @endif
          </div>

          @if($a->indicaciones)
            <div class="small mt-1">{{ $a->indicaciones }}</div>
          @endif

          <hr class="my-3" />
        </div>
      @empty
        <div class="list-group-item text-muted text-center border-0">Sin actividades registradas.</div>
      @endforelse
    </div>
  </div>

  {{-- ========= Evolución emocional ========= --}}
  <div class="card shadow border-0 mx-auto" style="max-width: 980px; position: relative;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-activity text-primary"></i>
        <span class="fw-bold">Evolución emocional</span>
      </div>

      <div class="btn-group btn-group-sm" role="group" aria-label="tipo-grafica">
        <button id="btnLinea" type="button" class="btn btn-outline-primary active">Línea</button>
        <button id="btnBarras" type="button" class="btn btn-outline-primary">Barras</button>
      </div>
    </div>

    {{-- CHIPS --}}
    <div class="px-3 pt-3 pb-1">
      <div class="emochips">
        @php
          $emoColors = [
            'Tranquilo'   => '#fde615ff',
            'Ansioso'     => '#f3902dff',
            'Motivado'    => '#3edfb4ff',
            'Confundido'  => '#8a8ea0ff',
            'Frustrado'   => '#f8382aff',
            'Feliz'       => '#4ca6f5ff',
            'Triste'      => '#0b23e0ff',
            'Irritado'    => '#fa3479ff',
          ];
        @endphp
        @foreach($emoColors as $nombre=>$hex)
          <span class="chip-emo">
            <span class="dot" style="background: {{ $hex }}"></span>
            <span class="label">{{ $nombre }}</span>
          </span>
        @endforeach
      </div>
    </div>

    <div class="card-body">
      <canvas id="graficoEmociones" height="120"></canvas>
    </div>

    {{-- Mascota decorativa --}}
    <div class="mascot-side" aria-hidden="true" title="MindWare">
      <svg viewBox="0 0 120 120">
        <defs>
          <linearGradient id="mwGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%"  stop-color="#bea4d2"/>
            <stop offset="100%" stop-color="#b5c8e1"/>
          </linearGradient>
        </defs>
        <path d="M60 15c16 0 31 8 38 19 7 11 6 24 0 36-6 12-19 23-35 24-16 2-34-5-41-17-7-12-3-28 6-41 9-13 16-21 32-21z"
              fill="url(#mwGrad)"></path>
        <circle cx="48" cy="55" r="5" fill="#1f2d3d"/>
        <circle cx="72" cy="55" r="5" fill="#1f2d3d"/>
        <path d="M46 70c6 10 22 10 28 0" stroke="#1f2d3d" stroke-width="3" stroke-linecap="round" fill="none"/>
      </svg>
    </div>
  </div>
</div>

{{-- ====== Chart.js + adaptador fechas ====== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
<script>
  // ---- Datos desde PHP ----
  const raw = @json($emociones);

  // Paleta por emoción (coincide con chips)
  const EMO = {
    'Tranquilo':'#fde615ff','Ansioso':'#f3902dff','Motivado':'#3edfb4ff','Confundido':'#8a8ea0ff',
    'Frustrado':'#f8382aff','Feliz':'#4ca6f5ff','Triste':'#0b23e0ff','Irritado':'#fa3479ff'
  };
  const ORDER = Object.keys(EMO);

  // Helpers de fecha
  const toDayStr = (val) => {
    const d = new Date(val); d.setHours(0,0,0,0);
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    return `${d.getFullYear()}-${mm}-${dd}`;
  };
  const safeJson = (v) => {
    if (v == null) return null;
    if (typeof v === 'string') { try { return JSON.parse(v); } catch { return null; } }
    return v;
  };
  const fmt = (dstr) => { const [y,m,d]=dstr.split('-'); return `${d}/${m}/${y}`; };

  // Fechas únicas (solo registradas)
  const daySet = new Set();
  raw.forEach(r => daySet.add(toDayStr(r.fechaHoraRegistro)));
  const LABELS_RAW = Array.from(daySet).sort();    // YYYY-MM-DD
  const LABELS = LABELS_RAW.map(fmt);              // dd/mm/yyyy
  const dayIndex = Object.fromEntries(LABELS_RAW.map((d,i)=>[d,i]));

  // Datasets base (por emoción)
  const byEmo = {};
  ORDER.forEach(e=> byEmo[e] = { label:e, color:EMO[e], points:[] });

  // Relleno a partir de: emocionesExperimentadas + intensidades (array o objeto)
  raw.forEach(r=>{
    const dstr = toDayStr(r.fechaHoraRegistro);
    const x = dayIndex[dstr];
    const emos = safeJson(r.emocionesExperimentadas) || [];
    const ints = safeJson(r.intensidades);

    if (!Array.isArray(emos) || emos.length === 0) return;

    emos.forEach((emocion, idx) => {
      if (!(emocion in byEmo)) return;

      let y = null;
      if (Array.isArray(ints)) y = Number(ints[idx]);
      else if (ints && typeof ints === 'object') y = Number(ints[emocion]);
      else if (typeof r.intensidad !== 'undefined') y = Number(r.intensidad); // compat
      if (!Number.isFinite(y)) return;

      y = Math.max(0, Math.min(5, y));
      byEmo[emocion].points.push({ x, y, _day: dstr });
    });
  });

  // Escalas comunes (categoría = fechas registradas)
  const commonScales = {
    x: { type: 'category', labels: LABELS, grid: { color: 'rgba(0,0,0,.06)' } },
    y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,.06)' } }
  };

  // Tooltip externo por día
  function externalLegendTooltip(ctx){
    const { chart, tooltip } = ctx;
    let tip = document.getElementById('mwChartTip');
    if(!tip){
      tip = document.createElement('div');
      tip.id = 'mwChartTip';
      tip.className = 'mw-tip';
      tip.innerHTML = '<div class="mw-tip-date"></div><div class="mw-tip-items"></div>';
      document.body.appendChild(tip);
    }
    if (tooltip.opacity === 0){ tip.style.opacity = 0; return; }

    const idx = tooltip.dataPoints?.[0]?.parsed?.x ?? 0;
    const dstr = LABELS[idx] || '';
    tip.querySelector('.mw-tip-date').textContent = dstr;

    const itemsBox = tip.querySelector('.mw-tip-items');
    itemsBox.innerHTML = '';
    const items = tooltip.dataPoints
      .filter(dp => (dp.parsed?.x ?? -1) === idx)
      .map(dp => ({ label: dp.dataset.label, y: dp.parsed.y, color: dp.dataset.backgroundColor }))
      .filter(it => Number.isFinite(it.y))
      .sort((a,b)=> a.label.localeCompare(b.label));

    items.forEach(it=>{
      const row = document.createElement('div');
      row.className = 'mw-tip-row';
      row.innerHTML =
        `<span class="mw-tip-dot" style="background:${it.color}"></span>
         <span class="mw-tip-text">${it.label} · Intensidad ${it.y}</span>`;
      itemsBox.appendChild(row);
    });

    const rect = chart.canvas.getBoundingClientRect();
    const x = rect.left + window.scrollX + tooltip.caretX + 12;
    const y = rect.top  + window.scrollY + tooltip.caretY  - 12;
    tip.style.left = x + 'px';
    tip.style.top  = y + 'px';
    tip.style.opacity = 1;
  }

  // Plugin SOLO para LÍNEA: separa bolitas en mismo día y dibuja la “conexión del día”
  const sameDaySpread = {
    id:'sameDaySpread',
    afterDatasetsDraw(chart){
      const ctx = chart.ctx;
      const groups = new Map();
      chart.data.datasets.forEach((ds,di)=>{
        const meta = chart.getDatasetMeta(di);
        meta.data.forEach(el=>{
          if (!el?.$context?.parsed) return;
          const {x,y} = el.$context.parsed; if (x == null) return;
          (groups.get(x) ?? groups.set(x,[]).get(x)).push({ el, fill: ds.backgroundColor });
        });
      });

      const shifts = [-18,-10,-4,4,10,18,-26,26];

      groups.forEach(list=>{
        list.sort((a,b)=>a.el.y - b.el.y);
        const withPos = list.map((it,i)=>{
          const dx = (list.length===1 ? 0 : shifts[i % shifts.length]);
          const x = it.el.x + dx, y = it.el.y;
          ctx.save();
          ctx.fillStyle = it.fill; ctx.strokeStyle = '#fff'; ctx.lineWidth = 3;
          ctx.beginPath(); ctx.arc(x, y, 8, 0, Math.PI*2); ctx.fill(); ctx.stroke();
          ctx.restore();
          return {x,y};
        });
        if (withPos.length > 1) {
          ctx.save(); ctx.strokeStyle = '#111'; ctx.lineWidth = 2;
          ctx.beginPath(); ctx.moveTo(withPos[0].x, withPos[0].y);
          for (let i=1;i<withPos.length;i++) ctx.lineTo(withPos[i].x, withPos[i].y);
          ctx.stroke(); ctx.restore();
        }
      });
    }
  };

  // Opciones base
  const baseOptionsLine = {
    responsive: true,
    interaction: { mode: 'nearest', axis: 'x', intersect: false },
    plugins: { legend: { display: false }, tooltip: { enabled: false, external: externalLegendTooltip } },
    scales: commonScales,
    elements: { point: { radius: 0, hitRadius: 18 } }
  };
  const baseOptionsBar = {
    responsive: true,
    interaction: { mode: 'nearest', axis: 'x', intersect: false },
    plugins: { legend: { display: false }, tooltip: { enabled: false, external: externalLegendTooltip } },
    scales: commonScales,
    elements: { point: { radius: 0 } },
    maintainAspectRatio: true
    // Para barras apiladas, descomenta:
    // , scales: { x: { ...commonScales.x, stacked: true }, y: { ...commonScales.y, stacked: true } }
  };

  // Armar datasets según tipo
  const makeLineDatasets = () => Object.values(byEmo).map(e => ({
    label: e.label,
    data: e.points,                   // [{x:indexFecha, y:intensidad}]
    borderColor: e.color + 'CC',
    backgroundColor: e.color,
    pointRadius: 6,
    pointHoverRadius: 7,
    pointHitRadius: 18,
    showLine: true,
    spanGaps: false,
    tension: .35,
    parsing: false
  }));

  const makeBarDatasets = () => Object.values(byEmo).map(e => ({
    type: 'bar',
    label: e.label,
    data: e.points,
    backgroundColor: e.color,
    borderWidth: 0,
    parsing: false,
    barPercentage: 0.75,
    categoryPercentage: 0.8
  }));

  // Render inicial: LÍNEA
  const ctx = document.getElementById('graficoEmociones');
  let chart = new Chart(ctx, {
    type: 'line',
    data: { datasets: makeLineDatasets() },
    options: baseOptionsLine,
    plugins: [sameDaySpread]
  });

  // Toggle Línea/Barras
  const btnLinea  = document.getElementById('btnLinea');
  const btnBarras = document.getElementById('btnBarras');
  const setActive = (btn)=>[btnLinea,btnBarras].forEach(b=>b.classList.toggle('active', b===btn));

  btnLinea.addEventListener('click', () => {
    if (chart.config.type === 'line') return;
    chart.destroy();
    chart = new Chart(ctx, {
      type: 'line',
      data: { datasets: makeLineDatasets() },
      options: baseOptionsLine,
      plugins: [sameDaySpread]
    });
    setActive(btnLinea);
  });

  btnBarras.addEventListener('click', () => {
    if (chart.config.type === 'bar') return;
    chart.destroy();
    chart = new Chart(ctx, {
      type: 'bar',
      data: { datasets: makeBarDatasets() },
      options: baseOptionsBar,
      plugins: [] // sin líneas
    });
    setActive(btnBarras);
  });
</script>

<style>
  :root{ --stroke:#e6effc; --ink:#21374f; }

  .btn-soft{
    background:#fff; border:1px solid var(--stroke); color:var(--ink);
    border-radius:999px; padding:.55rem 1.3rem; font-weight:600;
    transition:.2s ease; box-shadow:0 6px 16px rgba(17,35,61,.08);
  }
  .btn-soft:hover{ background:#f2f7ff; color:#0f2442; }

  .activity-center{ max-width:760px; margin:0 auto; padding:.5rem 1.25rem; text-align:left; }
  .bg-soft-blue{ background:#eaf3ff; color:#1f3b60; }

  .emochips{ display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:center; }
  .chip-emo{ display:inline-flex; align-items:center; gap:8px; padding:.35rem .6rem; border:1px solid var(--stroke); border-radius:999px; background:#fff; box-shadow:0 6px 14px rgba(33,55,79,.06); font-weight:600; color:var(--ink); line-height:1; }
  .chip-emo .dot{ width:10px; height:10px; border-radius:50%; }

  /* Tooltip externo */
  .mw-tip{
    position:absolute; pointer-events:none; opacity:0;
    background:#fff; border:1px solid var(--stroke); border-radius:12px;
    padding:.45rem .65rem; box-shadow:0 10px 22px rgba(17,35,61,.15);
    color:#21374f; z-index:1050; min-width: 170px;
  }
  .mw-tip-date{ font-weight:800; font-size:.86rem; margin-bottom:.25rem; opacity:.85; }
  .mw-tip-items{ display:flex; flex-direction:column; gap:.15rem; }
  .mw-tip-row{ display:flex; align-items:center; gap:.45rem; font-weight:700; white-space:nowrap; }
  .mw-tip-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; box-shadow:0 0 0 2px #fff inset, 0 0 0 1px rgba(33,55,79,.08); }
  .mw-tip-text{ line-height:1; }

  .mascot-side{ position:absolute; right:-64px; top:10px; width:120px; height:120px; filter:drop-shadow(0 10px 18px rgba(17,35,61,.16)); pointer-events:none; animation:bob 3.2s ease-in-out infinite; }
  @keyframes bob{ 0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)} }

  /* ===== Timeline intacto ===== */
  .timeline{ position:relative; margin:0 auto; padding:10px 0; max-width:700px; }
  .timeline::before{ content:""; position:absolute; left:50%; top:0; transform:translateX(-50%); width:4px; height:100%; background-color:#b5c8e1; border-radius:2px; }
  .timeline-item{ display:flex; align-items:flex-start; margin-bottom:2rem; position:relative; }
  .timeline-item:nth-child(odd) .timeline-content{ margin-left:calc(50% + 30px); text-align:left; }
  .timeline-item:nth-child(even) .timeline-content{ margin-right:calc(50% + 30px); text-align:right; }
  .timeline-icon{ position:absolute; left:50%; transform:translateX(-50%); width:45px; height:45px; border-radius:50%; display:flex; justify-content:center; align-items:center; color:white; box-shadow:0 3px 6px rgba(0,0,0,0.2); }
  .timeline-content{ background:#f9fbff; padding:15px 20px; border-radius:12px; box-shadow:0 3px 8px rgba(0,0,0,0.08); width:45%; transition:.3s ease-in-out; }
  .timeline-content:hover{ transform:scale(1.02); background-color:#eef3fb; }
  .bg-success{ background-color:#a8d5a2 !important; }
  .bg-warning{ background-color:#ffe29a !important; color:#333; }
  .bg-danger{ background-color:#f5a3a3 !important; }
  .bg-secondary{ background-color:#b5c8e1 !important; }
</style>
@include('medico.bottom-navbar')
@endsection
