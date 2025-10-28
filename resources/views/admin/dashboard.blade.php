@php($base = request()->routeIs('admin.*') ? 'admin.' : 'medico.')
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<style>
  :root{
    --ink:#1b3b6f; --ink-2:#2c4c86; --sky:#eaf3ff; --card-b:#f8fbff; --stroke:#e6eefc; --chip:#eef6ff;
    --male:#3a86ff; --female:#ff6ea7; --other:#8b8dbb;
    --emerald:#00c48c; --amber:#ffb703;
    --violet:#8b80f9; --teal:#00bcd4;
  }

  .wrap{
    position: relative;
    background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%);
    min-height: calc(100vh - var(--navbar-h,56px));
    padding: 18px 14px 40px;
    overflow: hidden;
  }

  .toolbar{
    background: linear-gradient(180deg,#dfeeff, #eaf4ff);
    border: 1px solid var(--stroke);
    border-radius: 16px;
    padding: 12px;
    margin-bottom: 16px;
    display:flex; align-items:center; gap:10px; flex-wrap: wrap;
    box-shadow: 0 8px 22px rgba(27,59,111,.06);
  }
  .toolbar .title{ font-weight: 800; color: var(--ink); margin-right:auto; font-size:1.1rem; letter-spacing:.2px; }
  .segmented{ background:#fff; border:1px solid var(--stroke); border-radius:14px; padding:4px; display:inline-flex; gap:4px; }
  .segmented .seg{
    border:0; background:transparent; padding:8px 12px; border-radius:10px; display:flex; align-items:center; gap:6px;
    color:#41567e; font-weight:700; transition: background .2s ease, transform .12s ease, color .2s ease, box-shadow .2s;
  }
  .segmented .seg:hover{ transform: translateY(-1px); background:#f2f7ff; }
  .segmented .seg.active{ background:#e7f1ff; color:#113869; box-shadow: 0 6px 16px rgba(27,59,111,.08) inset; }
  input[type="date"].form-control-sm{ border-radius:10px; border:1px solid var(--stroke); background:#fff; }

  /* ===== GRID CHARTS ===== */
  .dashboard-grid{
    display:grid;
    grid-template-columns: repeat(12, minmax(0,1fr));
    grid-auto-rows: 130px;
    gap: 12px;
  }
  .grid-s1{ grid-column: span 3; grid-row: span 1; }
  .grid-l4{ grid-column: span 8; grid-row: span 2; }
  .grid-m3t{ grid-column: span 4; grid-row: span 3; }
  .grid-full{ grid-column: 1 / -1; grid-row: span 1; }

  @media (max-width: 1200px){
    .dashboard-grid{ grid-template-columns: repeat(8,1fr); grid-auto-rows: 120px; }
    .grid-s1{ grid-column: span 2; }
    .grid-l4{ grid-column: span 8; grid-row: span 2; }
    .grid-m3t{ grid-column: span 8; grid-row: span 3; }
  }
  @media (max-width: 768px){
    .dashboard-grid{ grid-template-columns: repeat(4,1fr); grid-auto-rows: 120px; }
    .grid-s1,.grid-l4,.grid-m3t,.grid-full{ grid-column: 1 / -1; }
  }

  /* ===== Tarjetas KPIs y charts ===== */
  .mf-card{
    background:linear-gradient(180deg,#ffffff, #fbfdff);
    border:1px solid var(--stroke); border-radius:16px; padding:12px;
    box-shadow:0 10px 26px rgba(27,59,111,.07);
    height:100%;
    transition: transform .18s ease, box-shadow .22s ease;
    position:relative; overflow:hidden;
    display:flex; flex-direction:column;
  }
  .mf-card:hover{ transform: translateY(-2px) scale(1.01); box-shadow:0 14px 34px rgba(27,59,111,.10); }
  .chart-card{ overflow: visible; }

  .kpi{ display:flex; align-items:center; gap:12px; }
  .kpi .ico{
    width:42px; height:42px; border-radius:12px; display:grid; place-items:center;
    font-size:20px; line-height:1;
    background:linear-gradient(180deg,#e9f2ff,#f4f9ff); border:1px solid var(--stroke); color:#20457a;
    box-shadow: inset 0 0 0 6px rgba(58,134,255,.05);
  }
  .kpi .val{ font-size:1.6rem; font-weight:850; color:#203a6a; line-height:1; letter-spacing:.2px; }
  .kpi .sub{ color:#5d6e91; font-weight:700; font-size:.92rem; }
  .mf-title{ font-weight:800; color:#284a85; font-size:1rem; margin-bottom:6px; letter-spacing:.2px; }
  .legend-pill{
    background:var(--chip); border:1px solid var(--stroke); color:#2e4c84; font-weight:700;
    border-radius:999px; padding:6px 10px; display:inline-flex; gap:8px; align-items:center; font-size:.86rem;
  }

  .chart-card .hbox{ position:relative; width:100%; height:100%; min-height: 200px; flex:1; }
  .chart-card canvas{ display:block; width:100% !important; height:100% !important; }

  .progress.sex{ height:8px; background:#eef4ff; border-radius:999px; overflow:hidden; }
  .progress.sex .progress-bar{ border-radius:999px; transition: width .6s cubic-bezier(.2,.8,.2,1); }
  #barH{ background: var(--male) !important; }
  #barM{ background: var(--female) !important; }
  #barO{ background: var(--other) !important; }

  .badge-soft{ background:#f4f8ff; border:1px solid var(--stroke); color:#284a85; font-weight:700; }
  .tiny{ font-size:.86rem; color:#7386a7; }
  .actions{ display:flex; gap:8px; align-items:center; }
  .btn-outline-primary.btn-sm{ border-radius:10px; font-weight:700; border-color:#cfe0ff; color:#2a4b86; background:#fff; }
  .btn-outline-primary.btn-sm:hover{ background:#eaf2ff; }

  .badge-auto{
    font-weight:800; letter-spacing:.2px;
    border-radius:999px; padding:4px 10px;
    background:#e6fffa; border:1px solid #b2f5ea; color:#0f766e;
  }

  .mf-card.compact{ padding:10px 12px; min-height:auto; }
  .mf-card.compact .legend-pill{ padding:4px 8px; font-size:.82rem; }
  .mf-card.compact .fs-4{ font-size:1.1rem !important; }

  /* ===== CTA Botón Gestionar ===== */
  .manage-cta{ display:flex; justify-content:center; }
  .btn-gradient{
    --g1:#7a9cc6; --g2:#8b80f9;
    background: linear-gradient(135deg, var(--g1), var(--g2));
    color:#fff; border:0; border-radius:999px; padding:.9rem 1.4rem; font-weight:800;
    box-shadow: 0 10px 24px rgba(139,128,249,.25);
    transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
  }
  .btn-gradient i{ margin-right:.4rem; }
  .btn-gradient:hover{ transform: translateY(-2px); filter: brightness(1.03); box-shadow: 0 14px 32px rgba(139,128,249,.35); }
  .btn-gradient:active{ transform: translateY(0); }

  /* ===== Sección de Gestión ===== */
  .dashboard-bg {
    position: absolute; inset: 0;
    background: radial-gradient(circle at 20% 20%, #f0f4ff 0%, transparent 70%),
                radial-gradient(circle at 80% 80%, #e8f7ff 0%, transparent 70%);
    z-index: 0; animation: float 10s ease-in-out infinite alternate;
  }
  @keyframes float {
    0% { background-position: 0% 0%, 100% 100%; }
    100% { background-position: 50% 50%, 50% 50%; }
  }

  .gestion-head{ position:relative; z-index:1; } /* <- asegura que el botón cierre esté encima */
  .gestion-grid{
    position: relative; z-index: 1;
    display:flex; flex-wrap:wrap; justify-content:center; gap:30px 40px;
  }
  .gestion-card{
    display:block; text-align:center; background:#fff;
    border-radius:16px; padding:25px 15px; width:220px; margin:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.1); text-decoration:none; color:#333;
    transition: all .3s ease; position:relative; overflow:hidden; transform-style:preserve-3d; perspective:800px;
  }
  .gestion-card:hover{ transform: translateY(-8px) rotateX(4deg) rotateY(-4deg); box-shadow:0 8px 25px rgba(0,0,0,.2); }
  .gestion-card h4{ font-weight:700; color:#374151; font-size:1.1rem; }
  .gestion-card p{ font-size:.85rem; color:#6b7280; }

  .gestion-card::after{
    content:""; position:absolute; top:-75%; left:-75%; width:50%; height:200%;
    background:rgba(255,255,255,.3); transform:rotate(25deg); transition:.6s;
  }
  .gestion-card:hover::after{ left:125%; }

  .icon-box{
    position:relative; width:80px; height:80px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    margin:0 auto 15px; font-size:2.2rem;
    background: linear-gradient(135deg, #6c63ff, #00bcd4); color:#fff;
    transition: transform .4s ease, box-shadow .4s ease;
    box-shadow:0 0 15px rgba(108,99,255,.4); animation: pulseGlow 3s ease-in-out infinite;
  }
  @keyframes pulseGlow{
    0%{ box-shadow:0 0 15px rgba(108,99,255,.4), 0 0 25px rgba(0,188,212,.3); transform:scale(1);}
    50%{ box-shadow:0 0 30px rgba(139,128,249,.6), 0 0 45px rgba(0,188,212,.5); transform:scale(1.05);}
    100%{ box-shadow:0 0 15px rgba(108,99,255,.4), 0 0 25px rgba(0,188,212,.3); transform:scale(1);}
  }

  .btn-outline-pill{
    border:1px solid var(--stroke);
    background:#fff;
    color:#284a85;
    border-radius:999px;
    padding:.55rem 1rem;
    font-weight:800;
  }
  .btn-outline-pill:hover{ background:#eef6ff; }

  /* estado visible/oculto del panel */
  .gestion-panel{ display:none; }
  .gestion-panel.is-open{ display:block; }
</style>
@endpush

@section('content')
<div class="wrap container-fluid">

  {{-- Toolbar y filtros --}}
  <div class="toolbar">
    <div class="title">Resumen general del sistema</div>

    <div class="segmented" id="sexoGroup" role="group" aria-label="Filtro por sexo">
      <button class="seg active" data-sex=""><i class="bi bi-sliders"></i> Todos</button>
      <button class="seg" data-sex="masculino"><i class="bi bi-gender-male"></i> Masculino</button>
      <button class="seg" data-sex="femenino"><i class="bi bi-gender-female"></i> Femenino</button>
      <button class="seg" data-sex="otro"><i class="bi bi-gender-ambiguous"></i> Otro</button>
    </div>

    <input type="date" id="fromDate" class="form-control form-control-sm" style="max-width:160px">
    <input type="date" id="toDate"   class="form-control form-control-sm" style="max-width:160px">

    <div class="actions">
      <button class="btn btn-outline-primary btn-sm" id="btnReload"><i class="bi bi-arrow-repeat"></i> Recargar</button>
      <div class="form-check form-switch d-flex align-items-center ms-1">
        <input class="form-check-input" type="checkbox" id="autoRefresh">
        <label class="form-check-label tiny ms-1" for="autoRefresh">Auto</label>
      </div>
      <span id="autoBadge" class="badge-auto d-none">Auto ON</span>
      <span id="lastUpdate" class="tiny ms-2"></span>
    </div>
  </div>

  {{-- CTA centrado para gestionar --}}
  <div class="manage-cta mb-3">
    <button id="btnGestionar" type="button" class="btn btn-gradient btn-lg px-4">
      <i class="bi bi-grid-3x3-gap-fill"></i> Gestionar recursos y más
    </button>
  </div>

  {{-- GRID de KPIs y CHARTS (siempre visible) --}}
  <div id="dashboardGrid" class="dashboard-grid">

    {{-- KPIs --}}
    <div class="mf-card grid-s1">
      <div class="kpi">
        <div class="ico"><i class="bi bi-people-fill" aria-hidden="true"></i></div>
        <div>
          <div class="sub">Total de usuarios</div>
          <div id="usuarios_total" class="val">0</div>
        </div>
      </div>
    </div>

    <div class="mf-card grid-s1">
      <div class="kpi">
        <div class="ico"><i class="bi bi-person-hearts" aria-hidden="true"></i></div>
        <div>
          <div class="sub">Pacientes</div>
          <div id="pacientes_total" class="val">0</div>
        </div>
      </div>
    </div>

    <div class="mf-card grid-s1">
      <div class="kpi">
        <div class="ico"><i class="bi bi-stethoscope" aria-hidden="true"></i></div>
        <div>
          <div class="sub">Médicos</div>
          <div id="medicos_total" class="val">0</div>
        </div>
      </div>
    </div>

    <div class="mf-card grid-s1">
      <div class="kpi">
        <div class="ico"><i class="bi bi-heart-pulse" aria-hidden="true"></i></div>
        <div>
          <div class="sub">Actividades terapéuticas</div>
          <div id="actividades_total" class="val">0</div>
        </div>
      </div>
    </div>

    {{-- Barras --}}
    <div class="mf-card chart-card grid-l4">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="mf-title">Pacientes por sexo</span>
        <button class="btn btn-light btn-sm badge-soft" data-export="sexo"><i class="bi bi-download"></i> Exportar PNG</button>
      </div>
      <div class="hbox"><canvas id="sexoChart"></canvas></div>

      <div class="mt-3">
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="legend-pill"><span style="width:10px;height:10px;border-radius:50%;background:var(--male);display:inline-block"></span> Masculino</span>
          <div class="flex-grow-1">
            <div class="progress sex"><div id="barH" class="progress-bar" style="width:0%"></div></div>
          </div>
          <span id="pctH" class="tiny">0%</span>
        </div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="legend-pill"><span style="width:10px;height:10px;border-radius:50%;background:var(--female);display:inline-block"></span> Femenino</span>
          <div class="flex-grow-1">
            <div class="progress sex"><div id="barM" class="progress-bar" style="width:0%"></div></div>
          </div>
          <span id="pctM" class="tiny">0%</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="legend-pill"><span style="width:10px;height:10px;border-radius:50%;background:var(--other);display:inline-block"></span> Otro</span>
          <div class="flex-grow-1">
            <div class="progress sex"><div id="barO" class="progress-bar" style="width:0%"></div></div>
          </div>
          <span id="pctO" class="tiny">0%</span>
        </div>
      </div>
    </div>

    {{-- Dona --}}
    <div class="mf-card chart-card grid-m3t">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="mf-title">Usuarios por rol</span>
        <div class="d-flex gap-2">
          <span class="legend-pill"><span style="width:10px;height:10px;border-radius:50%;background:#3a86ff;display:inline-block"></span> Admin</span>
          <span class="legend-pill"><span style="width:10px;height:10px;border-radius:50%;background:#00c48c;display:inline-block"></span> Médico</span>
          <span class="legend-pill"><span style="width:10px;height:10px;border-radius:50%;background:#ffb703;display:inline-block"></span> Paciente</span>
        </div>
      </div>
      <div class="hbox"><canvas id="rolesChart"></canvas></div>
      <div class="mt-2 tiny">Tip: haz clic en las leyendas del gráfico para mostrar/ocultar un rol.</div>
    </div>

    {{-- Test --}}
    <div class="mf-card compact grid-full">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <i class="bi bi-emoji-smile fs-4 text-primary" aria-hidden="true"></i>
        <div class="fw-bold me-2">Test psicológicos</div>
        <span class="legend-pill">Respondidos: <span id="tests_total">0</span></span>
        <span class="legend-pill">Comentarios: <span id="respuestas_test">0</span></span>
      </div>
    </div>

  </div>

  {{-- =================== SECCIÓN GESTIÓN =================== --}}
  <section id="gestionPanel" class="gestion-panel mt-5">
    <div class="position-relative w-100" style="background: transparent;">
      <div class="dashboard-bg"></div>

      <div class="d-flex justify-content-between align-items-center mb-3 gestion-head">
        <h2 class="fw-semibold mt-2 mb-0" style="font-size: 2rem; color: #5c6ac4;">Centro de gestión</h2>
        <button type="button" class="btn btn-outline-pill" data-close-gestion>
          <i class="bi bi-x-lg"></i> Cerrar
        </button>
      </div>
    </div>

    <div class="gestion-grid">
      <a href="{{ route('admin.usuarios.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="100">
        <div class="icon-box" style="background: linear-gradient(135deg, #7a9cc6, #8b80f9);">
          <i class="fas fa-users"></i>
        </div>
        <h4>Usuarios</h4>
        <p>Administra todas las cuentas registradas.</p>
      </a>

      <a href="{{ route('admin.tutores.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="150">
        <div class="icon-box" style="background: linear-gradient(135deg, #74b9ff, #a29bfe);">
          <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <h4>Tutores</h4>
        <p>Gestiona la información de tutores asignados.</p>
      </a>

      <a href="{{ route('admin.medicamentos.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="200">
        <div class="icon-box" style="background: linear-gradient(135deg, #00bcd4, #6c63ff);">
          <i class="fas fa-capsules"></i>
        </div>
        <h4>Medicamentos</h4>
        <p>Controla el catálogo de tratamientos.</p>
      </a>

      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="250">
        <div class="icon-box" style="background: linear-gradient(135deg, #a29bfe, #74b9ff);">
          <i class="fas fa-brain"></i>
        </div>
        <h4>Tests Psicológicos</h4>
        <p>Administra los tests aplicados a pacientes.</p>
      </a>

      <a href="{{ route($base.'actividades_terap.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="300">
        <div class="icon-box" style="background: linear-gradient(135deg, #6c5ce7, #00cec9);">
          <i class="fas fa-heart"></i>
        </div>
        <h4>Actividades Terapéuticas</h4>
        <p>Registra y supervisa terapias personalizadas.</p>
      </a>

      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="350">
        <div class="icon-box" style="background: linear-gradient(135deg, #74b9ff, #6c63ff);">
          <i class="fas fa-calendar-check"></i>
        </div>
        <h4>Citas</h4>
        <p>Agenda, modifica o cancela citas médicas.</p>
      </a>

      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="400">
        <div class="icon-box" style="background: linear-gradient(135deg, #81ecec, #a29bfe);">
          <i class="fas fa-smile-beam"></i>
        </div>
        <h4>Emociones</h4>
        <p>Analiza los registros emocionales de los pacientes.</p>
      </a>

      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="450">
        <div class="icon-box" style="background: linear-gradient(135deg, #00cec9, #6c5ce7);">
          <i class="fas fa-file-medical-alt"></i>
        </div>
        <h4>Expediente Clínico</h4>
        <p>Consulta y administra expedientes de pacientes.</p>
      </a>

      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="500">
        <div class="icon-box" style="background: linear-gradient(135deg, #6c63ff, #00bcd4);">
          <i class="fas fa-chart-bar"></i>
        </div>
        <h4>Reportes</h4>
        <p>Visualiza estadísticas y reportes del sistema.</p>
      </a>

      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="550">
        <div class="icon-box" style="background: linear-gradient(135deg, #b388ff, #82b1ff);">
          <i class="fas fa-cog"></i>
        </div>
        <h4>Configuración</h4>
        <p>Administra opciones y ajustes del sistema.</p>
      </a>
    </div>
  </section>
  {{-- =================== FIN SECCIÓN GESTIÓN =================== --}}

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const API_URL = "{{ url('/api/dashboard/summary') }}";

  const sexoCtx  = document.getElementById("sexoChart");
  const rolesCtx = document.getElementById("rolesChart");
  let sexoChart, rolesChart, timer = null;

  const lastUpdate = document.getElementById("lastUpdate");
  const autoBadge  = document.getElementById("autoBadge");
  const autoToggle = document.getElementById("autoRefresh");

  const sexoGroup = document.getElementById("sexoGroup");
  let sexoActual = "";
  sexoGroup.querySelectorAll(".seg").forEach(btn => {
    btn.addEventListener("click", () => {
      sexoGroup.querySelectorAll(".seg").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      sexoActual = btn.dataset.sex || "";
      cargar();
    });
  });

  document.getElementById("btnReload").addEventListener("click", cargar);
  autoToggle.addEventListener("change", (e) => toggleAuto(e.target.checked));
  function toggleAuto(on){
    if (on){ autoBadge.classList.remove("d-none"); timer = setInterval(cargar, 30000); }
    else   { autoBadge.classList.add("d-none"); clearInterval(timer); timer = null; }
  }

  // Export PNG
  document.querySelectorAll("[data-export]").forEach(b=>{
    b.addEventListener("click", ()=>{
      const which = b.getAttribute("data-export");
      const canvas = which === "sexo" ? sexoCtx : rolesCtx;
      const a = document.createElement("a");
      a.href = canvas.toDataURL("image/png");
      a.download = `${which}-chart.png`;
      a.click();
    });
  });

  function mapSexoColors(labels){
    const base = { masculino: getCSS('--male'), femenino: getCSS('--female'), otro: getCSS('--other') };
    return labels.map(l => base[String(l||'').toLowerCase().trim()] || 'rgba(53,148,255,.75)');
  }
  function getCSS(varName){ return getComputedStyle(document.documentElement).getPropertyValue(varName).trim(); }
  function makeVerticalGradient(ctx, color){
    const g = ctx.createLinearGradient(0,0,0,ctx.canvas.height);
    g.addColorStop(0, color); g.addColorStop(1, 'rgba(255,255,255,0.65)');
    return g;
  }
  function calcBarThickness(canvas, labelsCount){
    const min = 28, max = 64, padding = 36;
    const width = canvas.clientWidth - padding;
    return Math.max(min, Math.min(max, Math.floor(width / (labelsCount * 1.6))));
  }

  async function cargar(){
    const params = {
      sexo: sexoActual,
      from: document.getElementById("fromDate").value || "",
      to:   document.getElementById("toDate").value   || ""
    };
    const query = new URLSearchParams(params).toString();
    const res = await fetch(`${API_URL}?${query}`);
    const data = await res.json();

    actualizarTarjetas(data.cards || {});
    actualizarPorcentajes(data.charts?.pacientes_por_sexo?.porcentaje || {});
    renderCharts(data.charts || {}, data.cards || {});

    const t = new Date();
    lastUpdate && (lastUpdate.textContent = `Última actualización: ${t.toLocaleTimeString()}`);
  }

  function actualizarTarjetas(cards){
    document.getElementById("usuarios_total").textContent    = cards.usuarios_total ?? 0;
    document.getElementById("pacientes_total").textContent   = cards.pacientes_total ?? 0;
    document.getElementById("medicos_total").textContent     = cards.medicos_total ?? 0;
    document.getElementById("actividades_total").textContent = cards.actividades_total ?? 0;
    document.getElementById("tests_total").textContent       = cards.tests_total ?? 0;
    document.getElementById("respuestas_test").textContent   = cards.respuestas_test ?? 0;
  }

  function actualizarPorcentajes(pct){
    const h = pct.masculino ?? 0, m = pct.femenino ?? 0, o = pct.otro ?? 0;
    document.getElementById("barH").style.width = h + "%";
    document.getElementById("barM").style.width = m + "%";
    document.getElementById("barO").style.width = o + "%";
    document.getElementById("pctH").textContent = h + "%";
    document.getElementById("pctM").textContent = m + "%";
    document.getElementById("pctO").textContent = o + "%";
  }

  function renderCharts(charts, cards){
    const pacSexoAbs = charts?.pacientes_por_sexo?.absolutos || {};
    const labelsSexo = Object.keys(pacSexoAbs);
    const dataSexo   = Object.values(pacSexoAbs);

    const colorList = mapSexoColors(labelsSexo);
    const bgList    = colorList.map(c => makeVerticalGradient(sexoCtx.getContext('2d'), c));
    const barT      = calcBarThickness(sexoCtx, Math.max(1, labelsSexo.length));

    if (sexoChart) sexoChart.destroy();
    sexoChart = new Chart(sexoCtx, {
      type:"bar",
      data:{ labels: labelsSexo, datasets:[{
        label:"Pacientes",
        data: dataSexo,
        backgroundColor: bgList,
        borderColor: colorList,
        borderWidth:1.2,
        borderRadius:10,
        hoverBorderWidth:1.6,
        hoverBorderColor: colorList,
        barThickness: barT,
        maxBarThickness: Math.min(72, barT + 12),
        categoryPercentage: 0.7,
        barPercentage: 0.9
      }]},
      options:{
        responsive:true, maintainAspectRatio:false,
        animation:{ duration: 900, easing: 'cubicBezier(.2,.8,.2,1)', delay: (ctx) => ctx.dataIndex * 80 },
        hover: { mode:'index', intersect:false },
        plugins:{
          legend:{ display:false },
          tooltip:{ mode:'index', intersect:false, backgroundColor:'rgba(15,23,42,.92)', titleColor:'#fff', bodyColor:'#e2e8f0', padding:10, borderWidth:0, displayColors:true },
          datalabels:{ anchor:'end', align:'end', offset:4, color:'#203a6a', font:{ weight:700 }, formatter:(v)=> v }
        },
        scales:{ x:{ grid:{ display:false }, ticks:{ font:{ weight:700 } } }, y:{ beginAtZero:true, ticks:{ precision:0 } } }
      },
      plugins: [ChartDataLabels]
    });

    const rolesObj = cards?.usuarios_por_rol || {};
    const rolLabels = Object.keys(rolesObj);
    const rolData   = Object.values(rolesObj);

    if (rolesChart) rolesChart.destroy();
    rolesChart = new Chart(rolesCtx, {
      type:"doughnut",
      data:{ labels:rolLabels, datasets:[{
        data: rolData, borderWidth:1,
        backgroundColor:[getCSS('--male'), getCSS('--emerald'), getCSS('--amber')]
      }]},
      options:{
        responsive:true, maintainAspectRatio:false, cutout:"58%",
        animation:{ animateRotate:true, animateScale:true, duration: 950, easing: 'easeOutQuart' },
        plugins:{
          legend:{ position:'bottom', labels:{ usePointStyle:true, pointStyle:'circle', boxWidth:8, font:{ weight:700 } } },
          tooltip:{ backgroundColor:'rgba(15,23,42,.92)', titleColor:'#fff', bodyColor:'#e2e8f0', padding:10, borderWidth:0, displayColors:true },
          datalabels:{ display:false }
        }
      },
      plugins: [ChartDataLabels]
    });
  }

  // ======== Gestión simple ========
  const gestionPanel = document.getElementById('gestionPanel');
  const btnGestionar = document.getElementById('btnGestionar');

  // abrir siempre (no oculta dashboard)
  btnGestionar.addEventListener('click', () => {
    gestionPanel.classList.add('is-open');
    gestionPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // Cerrar con delegación en todo el documento (por si re-renderiza)
  document.addEventListener('click', (ev) => {
    const closeBtn = ev.target.closest('[data-close-gestion]');
    if (closeBtn && gestionPanel.classList.contains('is-open')) {
      gestionPanel.classList.remove('is-open');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }, true);

  // Cerrar con ESC
  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape' && gestionPanel.classList.contains('is-open')) {
      gestionPanel.classList.remove('is-open');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });

  // ======== Init ========
  AOS.init({ duration: 900, once: true, easing: 'ease-out-back' });
  cargar();
  toggleAuto(autoToggle.checked);
});
</script>
@endpush
