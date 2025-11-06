@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    function recurso_url_local($raw) {
        $raw = trim((string)($raw ?? ''));
        if ($raw === '') return null;
        if (Str::startsWith($raw, ['http://','https://','//'])) return $raw;
        if (Storage::disk('public')->exists($raw)) return Storage::url($raw);
        return asset(ltrim($raw,'/'));
    }

    $estado = request('estado');
    $f      = request('f', '');
    $q      = request('q', '');
@endphp

@section('content')
{{-- ===== Encabezado sin contenedor; botón IZQUIERDA y títulos CENTRADOS ===== --}}
<section class="content-header no-container">
  <div class="head-actions">
    <button type="button" class="btn btn-soft"
      onclick="window.location='{{ route('medico.dashboard') }}'">
      <i class="bi bi-arrow-90deg-left me-1"></i> Volver
    </button>
  </div>

  <div class="head-titles text-center">
    <h1 class="title">
      <i class="bi bi-clipboard2-check me-2"></i>
      Actividades asignadas a los pacientes
    </h1>
    <p class="subtitle">Consulta el historial de actividades que has asignado a tus pacientes.</p>
  </div>
</section>

<div class="content px-3">
  @includeWhen(View::exists('flash::message'), 'flash::message')

  {{-- ===== FILTROS ===== --}}
  <div class="card card-body shadow-sm mb-3 card-search">
    <form id="search-form" method="GET" action="{{ route('medico.actividades_terap.asignadas') }}" class="w-100">

      {{-- Chips de estado (colores solicitados) --}}
      <div class="mb-3 d-flex align-items-center flex-wrap gap-2">
        <span class="filter-label me-1">Estado:</span>

        @php $activeAll = empty($estado); @endphp
        <a href="{{ route('medico.actividades_terap.asignadas', array_filter(['f'=>$f,'q'=>$q])) }}"
           class="chip chip-all {{ $activeAll ? 'is-active' : 'is-outline' }}">
          Todas
        </a>

        @php $activePend = $estado==='pendiente'; @endphp
        <a href="{{ route('medico.actividades_terap.asignadas', array_filter(['estado'=>'pendiente','f'=>$f,'q'=>$q])) }}"
           class="chip chip-pending {{ $activePend ? 'is-active' : 'is-outline' }}">
          Pendientes
        </a>

        @php $activeDone = $estado==='completada'; @endphp
        <a href="{{ route('medico.actividades_terap.asignadas', array_filter(['estado'=>'completada','f'=>$f,'q'=>$q])) }}"
           class="chip chip-done {{ $activeDone ? 'is-active' : 'is-outline' }}">
          Completadas
        </a>
      </div>

      {{-- 🔍 Barra de búsqueda --}}
      <div class="search-bar">
        <div class="search-input-group">
          <input
            type="text"
            id="search-input"
            name="q"
            class="form-control"
            value="{{ $q }}"
            placeholder="@switch($f)
              @case('paciente') Buscar por nombre/apellido del paciente… @break
              @case('diagnostico') Buscar por diagnóstico (p. ej. ansiedad)… @break
              @case('tipo') Escribe: audio, video o lectura… @break
              @default Buscar por paciente, diagnóstico o tipo…
            @endswitch"
            autocomplete="off"
          >

          <select id="search-type" name="f" class="form-select">
              <option value=""             {{ $f==='' ? 'selected' : '' }}>Buscar en todo</option>
              <option value="paciente"     {{ $f==='paciente' ? 'selected' : '' }}>Por nombre del paciente</option>
              <option value="diagnostico"  {{ $f==='diagnostico' ? 'selected' : '' }}>Por diagnóstico</option>
              <option value="tipo"         {{ $f==='tipo' ? 'selected' : '' }}>Por tipo de recurso</option>
          </select>
        </div>

        <div class="d-flex align-items-center gap-2">
          @if($estado)
            <input type="hidden" name="estado" value="{{ $estado }}">
          @endif

          <button type="submit" class="btn btn-outline-secondary">Buscar</button>

          @if($q || $f || $estado)
            <a href="{{ route('medico.actividades_terap.asignadas') }}" class="btn btn-outline-secondary" title="Limpiar">
              <i class="fas fa-times"></i>
            </a>
          @endif
        </div>
      </div>
    </form>
  </div>

  {{-- ===== MENSAJES ===== --}}
  @if(session('success'))  <div class="alert ok">{{ session('success') }}</div> @endif
  @if(session('warning'))  <div class="alert warn">{{ session('warning') }}</div> @endif
  @if($errors->any())      <div class="alert err">{{ $errors->first() }}</div> @endif

  {{-- ===== LISTADO ===== --}}
  @php
      $tienePaginacion = is_object($asignaciones) && method_exists($asignaciones,'links');
  @endphp

  @forelse($asignaciones as $a)
    @php
      $url       = recurso_url_local($a->recurso ?? '');
      $fechaAsig = \Carbon\Carbon::parse($a->fechaAsignacion)->format('d/m/Y');
      $fechaLim  = $a->fechaFinalizacion ? \Carbon\Carbon::parse($a->fechaFinalizacion)->format('d/m/Y') : null;

      $lower       = $url ? Str::lower($url) : '';
      $isVideoLink = $url && Str::contains($lower, ['youtube.com','youtu.be','vimeo.com']);
      $isVideoFile = $url && Str::endsWith($lower, ['.mp4','.webm','.ogg']);
      $isPdf       = $url && Str::endsWith($lower, ['.pdf']);
      $isImage     = $url && Str::endsWith($lower, ['.png','.jpg','.jpeg','.gif','.webp']);
      $indicaciones = $a->indicacionesMedicas ?? null;
    @endphp

    <div class="card activity-card">
      <div class="card-head">
        <div class="title-col">
          <div class="paciente-chip">
            <i class="fas fa-user-injured"></i>
            <span class="txt">{{ $a->pacienteNombre ?: ('Paciente #' . $a->paciente_id) }}</span>
          </div>

          <h3 class="activity-title">{{ $a->titulo }}</h3>
          <div class="meta">
            <span>Tipo: <b class="strong">{{ ucfirst($a->tipoContenido) }}</b></span>
            <span>· Categoría: {{ $a->categoriaTerapeutica ?? 'N/D' }}</span>
            <span>· Diagnóstico: {{ $a->diagnosticoDirigido ?? 'N/D' }}</span>
            <span>· Severidad: {{ $a->nivelSeveridad ?? 'N/D' }}</span>
          </div>
          <div class="dates">
            Asignada: <b class="strong">{{ $fechaAsig }}</b>
            @if($fechaLim) · Límite: <b class="strong">{{ $fechaLim }}</b>@endif
          </div>
        </div>

        @php
          $badgeClass = $a->estado==='pendiente' ? 'badge-pending' : 'badge-done';
          $badgeText  = ucfirst($a->estado);
        @endphp
        <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
      </div>

      @if($indicaciones)
        <div class="note">
          <div class="note-title"><i class="fas fa-sticky-note"></i> Indicaciones</div>
          <div class="note-body">{!! nl2br(e($indicaciones)) !!}</div>
        </div>
      @endif

      <div class="resource">
        @if($url)
          @if($isVideoLink || $isVideoFile)
            <a href="{{ $url }}" target="_blank" class="btn-link">
              <i class="fas fa-external-link-alt"></i> Ver video
            </a>
          @elseif($isPdf)
            <a href="{{ $url }}" target="_blank" class="btn-link">
              <i class="fas fa-file-pdf"></i> Abrir PDF
            </a>
          @elseif($isImage)
            <img src="{{ $url }}" class="media img" alt="Recurso">
          @else
            <a href="{{ $url }}" target="_blank" class="btn-link">
              <i class="fas fa-link"></i> Abrir recurso
            </a>
          @endif
        @else
          <span class="muted">Sin recurso adjunto.</span>
        @endif
      </div>
    </div>
  @empty
    <div class="card empty">
      <div class="empty-body">
        <i class="fas fa-clipboard-list"></i>
        <p>No has asignado actividades o no hay resultados con los filtros actuales.</p>
        <a href="{{ route('medico.actividades_terap.asignadas') }}" class="btn-ghost">Limpiar filtros</a>
      </div>
    </div>
  @endforelse

  @if($tienePaginacion)
    <div class="pagination-wrap">
      {{ $asignaciones->links() }}
    </div>
  @endif
</div>

{{-- ===== Estilos ===== --}}
<style>
  :root{
    /* Paleta base */
    --bg:#f8fafc; --card:#ffffff; --border:#e6eaf0; --text:#1f2937; --muted:#6b7280;

    /* Azul MindWare */
    --mw:#2563eb;           /* azul principal */
    --mw-600:#1d4ed8;
    --mw-50:#eff6ff;

    /* Estado (chips/badges) */
    --ok:#16a34a;
    --ok-50:#ecfdf5;
    --ok-b:#a7f3d0;

    --warn:#f59e0b;
    --warn-50:#fffbeb;
    --warn-b:#fde68a;

    /* Grises botón suave */
    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-hover:#f3f4f6;
  }

  body{ background:var(--bg); }

  /* ===== Encabezado sin contenedor ni tarjeta ===== */
  section.content-header.no-container{
    margin-top:-6px!important; padding-top:0!important;
  }
  .head-actions{
    display:flex; justify-content:flex-start; align-items:center;
    padding:6px 0 2px 0;
  }
  .head-titles .title{
    font-size:2.05rem; line-height:1.2; margin:.25rem 0 0;
    color:#0f172a; letter-spacing:.2px;
  }
  .head-titles .subtitle{ margin:6px 0 4px; color:var(--muted); }

  /* ===== Botón suave (Volver) ===== */
  .btn-soft{
    background: var(--g-bg);
    border: 1px solid var(--g-borde);
    color: var(--g-text);
    border-radius: 999px;
    font-weight: 600;
    padding: .6rem 1.25rem;
    transition: all .25s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,.05);
  }
  .btn-soft:hover{
    background: var(--g-bg-hover);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(0,0,0,.08);
  }
  .btn-soft:active{ transform: scale(.98); }

  /* ===== Buscador / filtros ===== */
  .card-search{ border-radius:14px; padding:16px 18px; }
  .search-bar{ display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; width:100%; }
  .search-input-group{ display:flex; align-items:center; gap:8px; flex:1; min-width:280px; }
  .search-input-group input{ flex:1; border-radius:10px; font-size:1rem; }
  .search-input-group select{ width:240px; border-radius:10px; }
  .filter-label{ color:#64748b; font-weight:600; }

  /* ===== Chips con color por estado ===== */
  .chip{
    display:inline-flex; align-items:center; gap:8px;
    padding:8px 14px; border-radius:999px; text-decoration:none;
    font-size:.9rem; border:1px solid transparent; font-weight:600;
    transition: all .2s ease;
  }
  /* Todas → Azul MindWare */
  .chip-all.is-active{ background:var(--mw); color:#fff; }
  .chip-all.is-outline{ background:#fff; color:var(--mw); border-color:var(--mw); }
  .chip-all:is(:hover,:focus){ filter:brightness(0.97); }
  /* Pendientes → Amarillo */
  .chip-pending.is-active{ background:var(--warn); color:#0b0b0b; }
  .chip-pending.is-outline{ background:#fff; color:var(--warn); border-color:var(--warn); }
  .chip-pending:is(:hover,:focus){ filter:brightness(0.98); }
  /* Completadas → Verde */
  .chip-done.is-active{ background:var(--ok); color:#f8fff9; }
  .chip-done.is-outline{ background:#fff; color:var(--ok); border-color:var(--ok); }
  .chip-done:is(:hover,:focus){ filter:brightness(0.98); }

  /* ===== Alerts ===== */
  .alert{ margin:12px 0; padding:10px 12px; border-radius:12px; font-size:.95rem; }
  .alert.ok{ background:var(--ok-50); color:#065f46; border:1px solid var(--ok-b); }
  .alert.warn{ background:var(--warn-50); color:#92400e; border:1px solid var(--warn-b); }
  .alert.err{ background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

  /* ===== Cards de actividades ===== */
  .activity-card{
    background:var(--card); border:1px solid var(--border); border-radius:14px;
    box-shadow:0 6px 16px rgba(0,0,0,.05); padding:16px; margin-bottom:14px;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .activity-card:hover{ transform: translateY(-1px); box-shadow:0 10px 22px rgba(0,0,0,.08); }
  .card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap; }
  .activity-title{ margin:8px 0 6px; color:var(--text); font-size:1.18rem; font-weight:800; letter-spacing:.2px; }
  .meta{ color:var(--muted); display:flex; flex-wrap:wrap; gap:8px; }
  .dates{ color:var(--muted); margin-top:6px; }
  .strong{ color:#0f172a; }

  /* Badges de estado */
  .badge{ padding:7px 12px; border-radius:999px; font-size:.86rem; font-weight:700; }
  .badge-pending{ background:var(--warn-50); color:#7c2d12; border:1px solid var(--warn-b); }
  .badge-done{ background:var(--ok-50); color:#065f46; border:1px solid var(--ok-b); }

  .paciente-chip{
    display:inline-flex; align-items:center; gap:8px;
    padding:6px 10px; border-radius:999px; background:var(--mw-50); color:#1e2a78; font-size:.85rem;
    border:1px dashed #dbeafe;
  }
  .paciente-chip .txt{ font-weight:700; }

  .note{ margin-top:12px; background:#f8fafc; border:1px dashed var(--border); border-radius:12px; }
  .note-title{ font-weight:700; padding:10px 12px 4px; color:#0f172a; }
  .note-body{ padding:0 12px 10px; color:#374151; line-height:1.55; white-space:pre-line; }

  .resource{ margin-top:12px; }
  .btn-link{
    display:inline-flex; align-items:center; gap:8px; padding:9px 14px;
    border:1px solid var(--border); border-radius:10px; text-decoration:none; color:#1f2937; background:#fff;
    font-weight:600;
  }
  .btn-link:hover{ background:#f3f4f6; }
  .media{ max-width:100%; height:auto; border-radius:12px; border:1px solid #e6eaf0; }
  .img{ display:block; }
  .muted{ color:#6b7280; }

  .empty{ margin-top:10px; text-align:center; border-radius:14px; }
  .empty .empty-body{ padding:36px 16px; }
  .empty i{ font-size:28px; color:#6b7280; }
  .btn-ghost{
    display:inline-block; margin-top:8px; padding:8px 14px; border-radius:999px;
    border:1px dashed var(--mw); color:var(--mw); text-decoration:none; font-weight:700;
  }
  .btn-ghost:hover{ background:#eef2ff; }

  .pagination-wrap{ margin-top:16px; display:flex; justify-content:center; }
</style>

{{-- ===== Búsqueda dinámica (autosubmit con debounce) ===== --}}
<script>
  (function(){
    const form   = document.getElementById('search-form');
    if(!form) return;

    const input  = document.getElementById('search-input');
    const select = document.getElementById('search-type');

    const placeholders = {
      '': 'Buscar por paciente, diagnóstico o tipo…',
      'paciente': 'Buscar por nombre/apellido del paciente…',
      'diagnostico': 'Buscar por diagnóstico (p. ej. ansiedad)…',
      'tipo': 'Escribe: audio, video o lectura…'
    };

    const setPlaceholder = () => {
      const val = (select?.value || '');
      input.placeholder = placeholders[val] || placeholders[''];
    };
    setPlaceholder();
    select?.addEventListener('change', setPlaceholder);

    const debounce = (fn, delay = 450) => {
      let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
    };

    const autoSubmit = debounce(() => {
      if (input.value.trim() === '' && (select.value || '') === '') return;
      form.requestSubmit();
    }, 450);

    input?.addEventListener('keyup', autoSubmit);
    select?.addEventListener('change', () => form.requestSubmit());

    input?.addEventListener('keydown', function(e){
      if(e.key === 'Enter'){ e.preventDefault(); form.requestSubmit(); }
    });
  })();
</script>
 @include('medico.bottom-navbar')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
