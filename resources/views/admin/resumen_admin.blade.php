@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b3b6f; --ink-2:#2c4c86; --sky:#eaf3ff; --card-b:#f8fbff; --stroke:#e6eefc; --chip:#eef6ff;
  }
  .wrap{
    background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%);
    min-height: calc(100vh - var(--navbar-h,56px));
    padding: 20px 14px 32px;
  }
  .toolbar{
    background: linear-gradient(180deg,#dfeeff, #eaf4ff);
    border: 1px solid var(--stroke);
    border-radius: 16px;
    padding: 12px;
    margin-bottom: 14px;
    display:flex; align-items:center; gap:10px; flex-wrap: wrap;
  }
  .toolbar .title{ font-weight: 800; color: var(--ink); margin-right:auto; font-size:1.4rem; }

  /* Filtro sexo con iconos */
  .segmented{ background:#fff; border:1px solid var(--stroke); border-radius:12px; padding:4px; display:inline-flex; gap:4px; }
  .segmented .seg{ border:0; background:transparent; padding:6px 10px; border-radius:10px; display:flex; align-items:center; gap:6px; color:#4a5d80; font-weight:700; }
  .segmented .seg.active{ background:#e7f1ff; color:#113869; }

  /* ===== KPI compactos en línea ===== */
  .kpi-grid{
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    gap: 12px;
    margin-bottom: 12px;
  }
  @media (max-width: 1200px){ .kpi-grid{ grid-template-columns: repeat(3,1fr); } }
  @media (max-width: 768px){  .kpi-grid{ grid-template-columns: repeat(2,1fr); } }
  @media (max-width: 480px){  .kpi-grid{ grid-template-columns: 1fr; } }

  .mf-card{
    background:#fff; border:1px solid var(--stroke); border-radius:16px; padding:12px;
    box-shadow:0 8px 20px rgba(27,59,111,.06); height:100%;
    transition: transform .15s ease, box-shadow .2s ease;
  }
  .mf-card:hover{ transform: translateY(-1px); box-shadow:0 12px 26px rgba(27,59,111,.08); }

  .kpi{ display:flex; align-items:center; gap:10px; }
  .kpi .ico{
    width:36px; height:36px; border-radius:10px; display:grid; place-items:center; font-size:18px;
    background:linear-gradient(180deg,#e9f2ff,#f4f9ff); border:1px solid var(--stroke); color:#20457a;
  }
  .kpi .val{ font-size:1.6rem; font-weight:800; color:#213b6b; line-height:1; }
  .kpi .sub{ color:#5d6e91; font-weight:700; font-size:.9rem; }

  /* Tarjetas de gráficos */
  .chart-card .hbox{ position:relative; width:100%; height:260px; }
  .mf-title{ font-weight:800; color:#2a497f; font-size:1rem; margin-bottom:6px; }
  .legend-pill{ background:var(--chip); border:1px solid var(--stroke); color:#2e4c84; font-weight:600; border-radius:10px; padding:4px 8px; display:inline-flex; gap:6px; align-items:center; font-size:.85rem; }

  .progress.sex{ height:8px; background:#eef4ff; }
  .badge-soft{ background:#eef6ff; border:1px solid var(--stroke); color:#2e4c84; font-weight:600; }
  .tiny{ font-size:.85rem; color:#7a8cae; }
  .actions{ display:flex; gap:8px; }
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
      <span id="lastUpdate" class="tiny ms-2"></span>
    </div>
  </div>

  {{-- KPIs (compactos y en línea) --}}
  <div class="kpi-grid">
    <div class="mf-card">
      <div class="kpi">
        <div class="ico"><i class="bi bi-people-fill"></i></div>
        <div>
          <div class="sub">Total de usuarios</div>
          <div id="usuarios_total" class="val">0</div>
        </div>
      </div>
    </div>

    <div class="mf-card">
      <div class="kpi">
        <div class="ico"><i class="bi bi-person-hearts"></i></div>
        <div>
          <div class="sub">Pacientes</div>
          <div id="pacientes_total" class="val">0</div>
        </div>
      </div>
    </div>

    <div class="mf-card">
      <div class="kpi">
        <div class="ico"><i class="bi bi-stethoscope"></i></div>
        <div>
          <div class="sub">Médicos</div>
          <div id="medicos_total" class="val">0</div>
        </div>
      </div>
    </div>

    <div class="mf-card">
      <div class="kpi">
        <div class="ico"><i class="bi bi-heart-pulse"></i></div>
        <div>
          <div class="sub">Actividades terapéuticas</div>
          <div id="actividades_total" class="val">0</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="mf-card chart-card">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="mf-title">Pacientes por sexo</span>
          <button class="btn btn-light btn-sm badge-soft" data-export="sexo"><i class="bi bi-download"></i> Exportar PNG</button>
        </div>
        <div class="hbox"><canvas id="sexoChart"></canvas></div>
        <div class="mt-3">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge badge-soft"><i class="bi bi-gender-male"></i> Masculino</span>
            <div class="flex-grow-1">
              <div class="progress sex"><div id="barH" class="progress-bar bg-primary" style="width:0%"></div></div>
            </div>
            <span id="pctH" class="tiny">0%</span>
          </div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge badge-soft"><i class="bi bi-gender-female"></i> Femenino</span>
            <div class="flex-grow-1">
              <div class="progress sex"><div id="barM" class="progress-bar" style="background:#ff6ea7;width:0%"></div></div>
            </div>
            <span id="pctM" class="tiny">0%</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge badge-soft"><i class="bi bi-gender-ambiguous"></i> Otro</span>
            <div class="flex-grow-1">
              <div class="progress sex"><div id="barO" class="progress-bar" style="background:#8b8dbb;width:0%"></div></div>
            </div>
            <span id="pctO" class="tiny">0%</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="mf-card chart-card">
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
    </div>
  </div>

  {{-- Chip de tests (datos del backend) --}}
  <div class="mf-card mt-3">
    <div class="d-flex align-items-center gap-3">
      <i class="bi bi-emoji-smile fs-4 text-primary"></i>
      <div class="fw-bold">Test psicológicos</div>
      <span class="legend-pill">Respondidos: <span id="tests_total">0</span></span>
      <span class="legend-pill">Comentarios: <span id="respuestas_test">0</span></span>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const API_URL = "{{ url('/api/dashboard/summary') }}";

  const sexoCtx  = document.getElementById("sexoChart");
  const rolesCtx = document.getElementById("rolesChart");
  let sexoChart, rolesChart, timer = null;

  const lastUpdate = document.getElementById("lastUpdate");

  // Filtro por sexo (botones segmentados)
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

  // Acciones
  document.getElementById("btnReload").addEventListener("click", cargar);
  document.getElementById("autoRefresh").addEventListener("change", (e) => {
    if (e.target.checked){
      timer = setInterval(cargar, 30000);
    } else {
      clearInterval(timer); timer = null;
    }
  });

  // Exportar imágenes de las gráficas
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

  // Carga de datos
  async function cargar(){
    const params = {
      sexo: sexoActual,
      from: document.getElementById("fromDate").value || "",
      to:   document.getElementById("toDate").value   || ""
    };
    const query = new URLSearchParams(params).toString();
    const res = await fetch(`${API_URL}?${query}`);
    const data = await res.json();

    actualizarTarjetas(data.cards);
    actualizarPorcentajes(data.charts?.pacientes_por_sexo?.porcentaje || {});
    renderCharts(data.charts, data.cards);

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

    if (sexoChart) sexoChart.destroy();
    sexoChart = new Chart(sexoCtx, {
      type:"bar",
      data:{ labels: labelsSexo, datasets:[{
        label:"Pacientes",
        data: dataSexo,
        backgroundColor:"rgba(53,148,255,.75)",
        borderColor:"rgba(53,148,255,1)",
        borderWidth:1,
        borderRadius:8
      }]},
      options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, tooltip:{ mode:'index', intersect:false } },
        scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true, ticks:{ precision:0 } } }
      }
    });

    const rolesObj = cards?.usuarios_por_rol || {};
    const rolLabels = Object.keys(rolesObj);
    const rolData   = Object.values(rolesObj);
    if (rolesChart) rolesChart.destroy();
    rolesChart = new Chart(rolesCtx, {
      type:"doughnut",
      data:{ labels:rolLabels, datasets:[{
        data: rolData, borderWidth:1,
        backgroundColor:["#3a86ff","#00c48c","#ffb703"]
      }]},
      options:{ responsive:true, maintainAspectRatio:false, cutout:"58%" }
    });
  }

  // Primera carga
  cargar();
});
</script>
@endpush
