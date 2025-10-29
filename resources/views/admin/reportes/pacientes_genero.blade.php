@extends('layouts.app')

@section('title', 'Reporte | Pacientes por género')

@push('styles')
<style>
  :root{
    --ink:#1b2a4a;    /* texto oscuro */
    --sub:#2e4672;
    --stroke:#e5eeff;

    /* Colores para la GRÁFICA */
    --c-male:#5B8DEF;  /* azul */
    --c-fem:#FF6FB1;   /* rosa */
    --c-oth:#C6A7FE;   /* lila */
  }

  body { color: var(--ink); }

  /* Contenedor centrado y ancho cómodo */
  .wrap { max-width: 1200px; margin: 0 auto; padding: 1rem 1rem 2.5rem; }

  /* Botón volver (centrado) */
  .back-row{ display:flex; justify-content:center; margin-bottom:.75rem; }
  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); color:var(--sub);
    border-radius:10px; padding:.45rem .9rem; font-weight:600;
    box-shadow:0 6px 18px rgba(0,0,0,.05);
  }

  /* Encabezado centrado */
  .page-header{ text-align:center; margin-bottom:1rem; }
  .page-title{ margin:0; font-weight:800; letter-spacing:.2px; color:var(--ink); }
  .page-sub{ color:var(--sub); }

  /* Toolbar/filtros centrados */
  .toolbar {
    background: linear-gradient(180deg, #fbfdff 0%, #f1f6ff 100%);
    border: 1px solid var(--stroke); border-radius: 16px;
    padding:1rem; box-shadow:0 6px 18px rgba(0,0,0,.05);
  }
  .filter-row{
    display:flex; align-items:end; gap:.8rem; flex-wrap:wrap;
    justify-content:center;  /* ⬅️ centrado */
  }
  .filter-chip{
    background:#eef4ff; border:1px solid var(--stroke); color:var(--sub);
    padding:.45rem .85rem; border-radius:999px; font-weight:700;
    display:inline-flex; gap:.35rem; align-items:center;
  }
  .fctl{ display:flex; flex-direction:column; align-items:flex-start; }
  .fctl label{ font-size:.8rem; color:var(--sub); margin:0 0 .25rem; font-weight:700; }
  .fctl .form-control, .fctl .form-select{ height:42px; min-width:165px; border-radius:10px; }

  /* Línea de botones de filtros centrada */
  .btn-row{
    width:100%;
    display:flex; justify-content:center; align-items:center; gap:.6rem; margin-top:.6rem;
  }

  .card-soft{ border:1px solid var(--stroke); border-radius:16px; background:#fff; box-shadow:0 10px 26px rgba(19,43,93,.05); }
  .card-soft .card-header{
    background:#f6f9ff; border-bottom:1px solid var(--stroke);
    border-top-left-radius:16px; border-top-right-radius:16px;
    color:var(--ink) !important;
  }
  .card-soft .card-header *{ color:var(--ink) !important; }

  .chip{
    display:inline-block; padding:.28rem .6rem; border-radius:999px;
    background:#eef4ff; border:1px solid var(--stroke); font-size:.85rem; color:var(--sub);
  }

  /* Tabla: colores estándar legibles */
  .table thead th{ background:#f1f5fb !important; color:#27364a !important; }
  .table tbody tr:hover{ background:#f7faff; }

  .legend-mini{ display:flex; gap:.75rem; flex-wrap:wrap; }
  .legend-mini .dot{ width:.75rem; height:.75rem; border-radius:50%; display:inline-block; }
  .chart-border{ filter: drop-shadow(0 2px 6px rgba(0,0,0,.06)); }
</style>
@endpush

@section('content')
<div class="wrap">
  <!-- Botón Volver al panel de reportes -->
  <div class="back-row">
    <a href="{{ route('admin.reportes.index') }}" class="btn-ghost" title="Volver al panel de reportes">
      <i class="bi bi-arrow-left"></i> Volver a a reportes
    </a>
  </div>

  <!-- Encabezado centrado -->
  <div class="page-header">
    <h2 class="page-title">Pacientes por género</h2>
    <small class="page-sub">Filtra por sexo, rango de edad y <strong>fecha de registro</strong>. La tabla y la gráfica se actualizan al aplicar.</small>
  </div>

  {{-- Filtros horizontales centrados --}}
  <div class="toolbar mb-4">
    <form id="formFiltros" class="filter-row">
      <span class="filter-chip"><i class="bi bi-funnel"></i> Filtros</span>

      <div class="fctl">
        <label>Sexo</label>
        <select name="sexo" class="form-select">
          <option value="">Todos</option>
          <option value="masculino" {{ (request('sexo')==='masculino')?'selected':'' }}>Masculino</option>
          <option value="femenino"  {{ (request('sexo')==='femenino')?'selected':'' }}>Femenino</option>
          <option value="otro"      {{ (request('sexo')==='otro')?'selected':'' }}>Otro</option>
        </select>
      </div>

      <div class="fctl">
        <label>Edad mín.</label>
        <input type="number" min="0" name="edad_min" value="{{ request('edad_min') }}" class="form-control" placeholder="Ej. 18">
      </div>

      <div class="fctl">
        <label>Edad máx.</label>
        <input type="number" min="0" name="edad_max" value="{{ request('edad_max') }}" class="form-control" placeholder="Ej. 30">
      </div>

      <div class="fctl">
        <label>Fecha de registro (desde)</label>
        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control">
      </div>

      <div class="fctl">
        <label>Fecha de registro (hasta)</label>
        <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control">
      </div>

      <!-- Botones centrados -->
      <div class="btn-row">
        <a href="{{ route('admin.reportes.pacientes.genero') }}" class="btn btn-light border">
          <i class="bi bi-x-circle"></i> Limpiar
        </a>
        <button id="btnAplicar" type="button" class="btn btn-primary">
          <i class="bi bi-play-circle"></i> Aplicar filtros
        </button>
      </div>
    </form>
  </div>

  <div class="row g-4">
    {{-- Tabla --}}
    <div class="col-12 col-lg-8">
      <div class="card card-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">   Resultados</h5>
          <span class="chip" text-align="center">        Total: <span id="totalSpan">{{ $total }}</span></span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>id</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Sexo</th>
                  <th>Edad</th>
                  <th>Fecha de registro</th>
                </tr>
              </thead>
              <tbody id="tbodyResultados">
                @forelse ($pacientes as $row)
                  @php $edad = $row->edad; @endphp
                  <tr>
                    <td>{{ $row->idUsuario }}</td>
                    <td>{{ $row->nombre }} {{ $row->apellido }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->sexo ? ucfirst($row->sexo) : '—' }}</td>
                    <td>{{ is_null($edad) ? '—' : $edad }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->fechaRegistro)->format('d/m/Y H:i') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-4">Sin resultados.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Gráfica --}}
    <div class="col-12 col-lg-4">
      <div class="card card-soft h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">Distribución por género</h5>
            <div class="legend-mini small text-muted mt-1">
              <span><span class="dot" style="background:#5B8DEF;"></span> Masculino</span>
              <span><span class="dot" style="background:#FF6FB1;"></span> Femenino</span>
              <span><span class="dot" style="background:#C6A7FE;"></span> Otro</span>
            </div>
          </div>
          <button id="btnDownloadPng" class="btn btn-sm btn-outline-primary" type="button" title="Descargar PNG">
            <i class="bi bi-download"></i> Decargar imágen de la gráfica en PNG
          </button>
        </div>
        <div class="card-body d-flex flex-column">
          <div class="flex-grow-1 d-flex align-items-center justify-content-center">
            <canvas id="donutGenero" class="chart-border" width="320" height="320" aria-label="Gráfica de dona por género" role="img"></canvas>
          </div>
          <div class="mt-3 small text-muted">
            <div>Masculino: <strong id="mCount">{{ $dataDonut[0] }}</strong></div>
            <div>Femenino: <strong id="fCount">{{ $dataDonut[1] }}</strong></div>
            <div>Otro: <strong id="oCount">{{ $dataDonut[2] }}</strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function(){
  // Obtener colores desde CSS variables
  const css = getComputedStyle(document.documentElement);
  const cMale = (css.getPropertyValue('--c-male') || '#5B8DEF').trim();
  const cFem  = (css.getPropertyValue('--c-fem')  || '#FF6FB1').trim();
  const cOth  = (css.getPropertyValue('--c-oth')  || '#C6A7FE').trim();

  const ctx = document.getElementById('donutGenero');
  const chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Masculino','Femenino','Otro'],
      datasets: [{
        data: @json($dataDonut),
        backgroundColor: [cMale, cFem, cOth],
        hoverOffset: 6,
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
      cutout: '58%'
    }
  });

  // Descargar PNG
  document.getElementById('btnDownloadPng')?.addEventListener('click', () => {
    chart.update();
    const qs = new URLSearchParams(new FormData(document.getElementById('formFiltros')));
    const sexo = qs.get('sexo') || 'todos';
    const d = new Date(), y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0'), dd=String(d.getDate()).padStart(2,'0');
    const a = document.createElement('a');
    a.download = `reporte_genero_${sexo}_${y}-${m}-${dd}.png`;
    a.href = chart.toBase64Image('image/png', 1.0);
    a.click();
  });

  // AJAX filtros
  const btn = document.getElementById('btnAplicar');
  const form = document.getElementById('formFiltros');
  const tbody = document.getElementById('tbodyResultados');
  const totalSpan = document.getElementById('totalSpan');
  const mCount = document.getElementById('mCount');
  const fCount = document.getElementById('fCount');
  const oCount = document.getElementById('oCount');

  btn?.addEventListener('click', fetchData);
  form?.addEventListener('keydown', (ev) => { if (ev.key === 'Enter') { ev.preventDefault(); fetchData(); } });

  async function fetchData() {
    const params = new URLSearchParams(new FormData(form));
    const url = "{{ route('admin.reportes.pacientes.genero.data') }}?" + params.toString();

    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Aplicando...';

    try {
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const json = await res.json();

      totalSpan.textContent = json.total ?? 0;

      const arr = json.dataDonut || [0,0,0];
      mCount.textContent = arr[0] ?? 0;
      fCount.textContent = arr[1] ?? 0;
      oCount.textContent = arr[2] ?? 0;
      chart.data.datasets[0].data = arr;
      chart.update();

      if (!json.rows || json.rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Sin resultados.</td></tr>`;
      } else {
        tbody.innerHTML = json.rows.map(r => `
          <tr>
            <td>${r.id}</td>
            <td>${escapeHtml(r.nombre)}</td>
            <td>${escapeHtml(r.email)}</td>
            <td>${capitalize(r.sexo)}</td>
            <td>${r.edad}</td>
            <td>${r.fecha}</td>
          </tr>
        `).join('');
      }
    } catch (e) {
      console.error(e);
      alert('No se pudo actualizar el reporte. Intenta de nuevo.');
    } finally {
      btn.disabled = false; btn.innerHTML = '<i class="bi bi-play-circle"></i> Aplicar filtros';
    }
  }

  function escapeHtml(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  function capitalize(s){ s = String(s ?? ''); return s ? s.charAt(0).toUpperCase() + s.slice(1) : '—'; }
})();
</script>
@endpush
