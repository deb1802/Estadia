@extends('layouts.app')

@section('title','Reporte de tests asignados')

@section('content')
<div class="report-scope">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12">
          <div class="d-grid d-sm-flex align-items-center gap-2">
            <h1 class="m-0 d-flex align-items-center gap-2">
              <i class="bi bi-clipboard2-pulse"></i>
              Reporte de tests asignados
            </h1>
            <div class="ms-sm-auto">
              <a href="{{ route('admin.dashboard') }}" class="btn btn-volver">
                <i class="bi bi-arrow-90deg-left me-1"></i> Volver al dashboard
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <main class="report-wrap">
    <div class="card-surface p-3 p-lg-4">

      {{-- Encabezado con mini KPIs --}}
      <div class="report-head">
        <div>
          <div class="text-muted small">Resumen general</div>
          <div class="fw-semibold">Filtros y resultados</div>
        </div>
        <div class="kpis">
          <div class="kpi">
            <div class="kpi-title">Registros</div>
            <div class="kpi-val">{{ number_format($rows->total()) }}</div>
          </div>
          <div class="kpi">
            <div class="kpi-title">Página</div>
            <div class="kpi-val">{{ $rows->currentPage() }} / {{ max(1,$rows->lastPage()) }}</div>
          </div>
        </div>
      </div>

      {{-- ===== Toolbar de filtros ===== --}}
      <div class="toolbar mb-3">
        <div class="toolbar-head mb-2">
          <div class="toolbar-title">
            <i class="bi bi-funnel-fill"></i> Filtros del reporte
          </div>
        </div>

        <form method="GET" action="{{ route('admin.reportes.tests.index') }}">
          <div class="row g-2 align-items-end">
            {{-- Tipo de test --}}
            <div class="col-12 col-md-4">
              <label class="form-label mb-1">Tipo de test</label>
              <div class="input-group pretty">
                <span class="input-group-text"><i class="bi bi-card-checklist"></i></span>
                <input type="text" name="tipo"
                       value="{{ $filtros['tipo'] ?? '' }}"
                       class="form-control form-control-lg"
                       placeholder="Nombre del test o trastorno">
              </div>
            </div>

            {{-- Estado --}}
            <div class="col-12 col-md-3">
              <label class="form-label mb-1">Estado</label>
              <div class="input-group pretty">
                <span class="input-group-text"><i class="bi bi-activity"></i></span>
                <select name="estado" class="form-select form-select-lg">
                  <option value="">Todos</option>
                  <option value="pendiente"  @selected(($filtros['estado'] ?? '')==='pendiente')>Pendiente</option>
                  <option value="completado" @selected(($filtros['estado'] ?? '')==='completado')>Completado</option>
                  <option value="vencido"    @selected(($filtros['estado'] ?? '')==='vencido')>Vencido</option>
                </select>
              </div>
            </div>

            {{-- Diagnóstico preliminar --}}
            <div class="col-12 col-md-5">
              <label class="form-label mb-1">Diagnóstico preliminar</label>
              <div class="input-group pretty">
                <span class="input-group-text"><i class="bi bi-heart-pulse-fill"></i></span>
                <input type="text" name="diagnostico"
                       value="{{ $filtros['diagnostico'] ?? '' }}"
                       class="form-control form-control-lg"
                       placeholder="Ej. leve, moderado, alto...">
              </div>
            </div>

            {{-- Rango de fechas --}}
            <div class="col-6 col-md-3">
              <label class="form-label mb-1">Desde</label>
              <div class="input-group pretty">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="form-control form-control-lg">
              </div>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label mb-1">Hasta</label>
              <div class="input-group pretty">
                <span class="input-group-text"><i class="bi bi-calendar2-event"></i></span>
                <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="form-control form-control-lg">
              </div>
            </div>

            <div class="col-12 col-md-6 d-flex gap-2 justify-content-md-end mt-1">
              <button type="submit" class="btn btn-accent btn-lg">
                <i class="bi bi-search me-1"></i> Aplicar filtros
              </button>
              <a class="btn btn-reset btn-lg"
                 href="{{ route('admin.reportes.tests.index') }}">
                Limpiar
              </a>
            </div>
          </div>
        </form>
      </div>

      {{-- Chips de filtros activos --}}
      <div class="chips">
        @if(!empty($filtros['tipo']))        <span class="chip"><i class="bi bi-tag-fill"></i> {{ $filtros['tipo'] }}</span>@endif
        @if(!empty($filtros['estado']))      <span class="chip"><i class="bi bi-activity"></i> {{ ucfirst($filtros['estado']) }}</span>@endif
        @if(!empty($filtros['diagnostico'])) <span class="chip"><i class="bi bi-heart-pulse-fill"></i> {{ $filtros['diagnostico'] }}</span>@endif
        @if(!empty($filtros['desde']))       <span class="chip"><i class="bi bi-calendar-event"></i> Desde {{ $filtros['desde'] }}</span>@endif
        @if(!empty($filtros['hasta']))       <span class="chip"><i class="bi bi-calendar2-event"></i> Hasta {{ $filtros['hasta'] }}</span>@endif
        <span class="chip"><i class="bi bi-list-ol"></i> {{ number_format($rows->total()) }} registros</span>
      </div>

      {{-- ===== Tabla ===== --}}
      <div class="table-card">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th><i class="bi bi-person-badge me-1"></i>Doctor que asignó</th>
                <th><i class="bi bi-person-heart me-1"></i>Paciente</th>
                <th><i class="bi bi-clock-history me-1"></i>Fecha</th>
                <th><i class="bi bi-clipboard-check me-1"></i>Tipo de test</th>
                <th><i class="bi bi-flag me-1"></i>Estado</th>
                <th><i class="bi bi-stethoscope me-1"></i>Diagnóstico preliminar</th>
              </tr>
            </thead>
            <tbody>
              @once
                @php
                  if (!function_exists('rr_val')) {
                    function rr_val($obj, array $keys, $default = null) {
                      foreach ($keys as $k) {
                        if (is_object($obj) && isset($obj->$k) && $obj->$k !== '' && $obj->$k !== null) return $obj->$k;
                        if (is_array($obj)  && array_key_exists($k,$obj) && $obj[$k] !== '' && $obj[$k] !== null) return $obj[$k];
                      }
                      return $default;
                    }
                  }
                @endphp
              @endonce

              @forelse($rows as $r)
                @php
                  $doctor = rr_val($r, ['doctor','medico','doctor_nombre','medico_nombre','nombre_medico','m_nombre'],'—');
                  $pac    = rr_val($r, ['paciente','paciente_nombre','nombre_paciente','p_nombre'],'—');
                  $fechaR = rr_val($r, ['fechaAsignacion','fecha_asignacion','fecha','created_at','asignado_en'], null);
                  $fecha  = $fechaR ? \Illuminate\Support\Str::of($fechaR)->replace('T',' ') : '—';
                  $tipo   = rr_val($r, ['tipo_test','test','tipo','nombre_test'],'—');
                  $estadoRaw = strtolower(rr_val($r, ['estado','status','estatus'],'pendiente'));
                  $estadoLbl = ucfirst($estadoRaw);
                  $diag   = rr_val($r, ['diagnostico_preliminar','diagnostico','resultado_preliminar'], '—');
                @endphp
                <tr>
                  <td>{{ $doctor }}</td>
                  <td>{{ $pac }}</td>
                  <td>{{ $fecha }}</td>
                  <td>{{ $tipo }}</td>
                  <td>
                    <span class="badge-state {{ $estadoRaw==='completado' ? 'st-completado' : ($estadoRaw==='vencido' ? 'st-vencido' : 'st-pendiente') }}">
                      {{ $estadoLbl }}
                    </span>
                  </td>
                  <td>{{ $diag }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    No se encontraron resultados con los filtros aplicados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- ===== Footer: Export + Paginación ===== --}}
        <div class="sticky-footer">
          <a href="{{ route('admin.reportes.tests.export', request()->query()) }}" class="btn btn-excel">
            <span class="excel-icon"><i class="bi bi-file-earmark-excel-fill"></i></span>
            <span class="fw-semibold">Descargar Excel</span>
          </a>

          <div>
            {{ $rows->onEachSide(1)->links() }}
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
@endsection

@push('styles')
<style>
  :root{
    --bg:#eaf1f8; --bg2:#f6f9ff; --ink:#1b2a4a; --muted:#6b7280;
    --white:#ffffff; --stroke:#e5e7eb; --stroke-2:#d9e3ef;
    --accent:#4067b4; --success:#16a34a; --excel:#107c41;
  }

  .report-scope{ color:var(--ink); }
  .report-wrap{ max-width:1200px; margin:0 auto; padding:1rem .75rem 2rem; }

  .card-surface{
    background:linear-gradient(180deg,#d7dfe9 0%, var(--bg) 100%);
    border:1px solid #cbd7eb; border-radius:20px;
    box-shadow:0 16px 40px rgba(27,42,74,.08);
  }

  .report-head{ display:grid; grid-template-columns:1fr auto; gap:16px; align-items:center; margin-bottom:.5rem; }
  @media (max-width:576px){ .report-head{ grid-template-columns:1fr; } }
  .kpis{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
  .kpi{ background:var(--white); border:1px solid var(--stroke); padding:.55rem .75rem; border-radius:12px; min-width:120px; }
  .kpi .kpi-title{ font-size:.78rem; color:var(--muted); margin-bottom:2px; }
  .kpi .kpi-val{ font-weight:700; font-size:1.05rem; }

  .toolbar{ background:var(--white); border:1px solid var(--stroke); border-radius:16px; padding:.9rem; }
  .toolbar-title{ font-weight:600; display:flex; align-items:center; gap:.5rem; color:#0f172a; }

  /* Inputs bonitos */
  .input-group.pretty .input-group-text{
    background:#eef4ff; border-color:var(--stroke-2);
  }
  .input-group.pretty .form-control,
  .input-group.pretty .form-select{
    border-color:var(--stroke-2);
    border-radius:12px;
  }
  .form-control.form-control-lg,
  .form-select.form-select-lg{
    padding-top:.7rem; padding-bottom:.7rem;
    border-radius:12px;
  }
  .form-control:focus, .form-select:focus{
    box-shadow:0 0 0 .2rem rgba(64,103,180,.12);
    border-color:#b7c4e6;
  }

  .chips{ display:flex; gap:.5rem; flex-wrap:wrap; margin:.5rem 0 1rem; }
  .chip{ display:inline-flex; align-items:center; gap:.45rem; background:var(--white); border:1px solid var(--stroke);
         padding:.3rem .6rem; border-radius:999px; color:var(--muted); font-size:.85rem; box-shadow:0 3px 8px rgba(2,6,23,.05); }

  .table-card{ background:var(--white); border:1px solid var(--stroke); border-radius:16px; overflow:hidden; }
  .table thead th{ background:#f5f7fb; color:#334155; font-weight:600; border-bottom:1px solid var(--stroke); white-space:nowrap; }
  .table tbody td{ vertical-align:middle; }

  .badge-state{ font-weight:700; padding:.4rem .7rem; border-radius:999px; letter-spacing:.2px; }
  .st-pendiente{ background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
  .st-completado{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .st-vencido{ background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

  .sticky-footer{ position:sticky; bottom:0; background:var(--white); border-top:1px solid var(--stroke);
                  padding:.9rem; border-radius:0 0 16px 16px; display:flex; flex-direction:column; gap:.75rem; }
  @media (min-width:992px){ .sticky-footer{ flex-direction:row; align-items:center; justify-content:space-between; } }

  .btn-excel{ display:inline-flex; align-items:center; gap:.6rem;
              background:linear-gradient(180deg, #18a956, var(--excel));
              border:1px solid #0b5e2b; color:#fff; border-radius:12px; padding:.6rem 1rem;
              box-shadow:0 8px 18px rgba(16,124,65,.25); }
  .btn-excel:hover{ filter:brightness(.95); }
  .excel-icon{ width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; background:#fff; color:var(--excel); border-radius:8px; font-size:20px; line-height:1; }

  .btn-volver{ border:1px solid var(--stroke); background:var(--white); }
  .btn-accent{ background:var(--accent); color:#fff; border:1px solid rgba(0,0,0,.04); }
  .btn-accent:hover{ filter:brightness(.95); }
  .btn-reset{ background:var(--bg2); border:1px solid var(--stroke); color:#0f172a; }
</style>
@endpush
