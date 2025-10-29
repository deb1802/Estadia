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
<section class="content-header text-center mb-2">
  <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div class="text-start">
          <h1 class="fw-semibold text-primary" style="font-size:2.2rem;">Actividades asignadas</h1>
          <p class="text-muted mb-0">Consulta el historial de actividades que has asignado a tus pacientes.</p>
      </div>

      {{-- 🔙 Botón para volver al dashboard --}}
      <a href="{{ route('medico.dashboard') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
          <i class="fas fa-home"></i> Volver al Dashboard
      </a>
  </div>
</section>

<div class="content px-3">
  @includeWhen(View::exists('flash::message'), 'flash::message')

  {{-- ===== FILTROS ===== --}}
  <div class="card card-body shadow-sm mb-3 card-search">
    <form method="GET" action="{{ route('medico.actividades_terap.asignadas') }}" class="w-100">

      {{-- Chips de estado --}}
      <div class="mb-2 d-flex align-items-center flex-wrap gap-2">
        <span class="filter-label me-1">Estado:</span>

        <a href="{{ route('medico.actividades_terap.asignadas', array_filter(['f'=>$f,'q'=>$q])) }}"
           class="chip {{ $estado ? 'chip-outline' : 'chip-primary' }}">Todas</a>

        <a href="{{ route('medico.actividades_terap.asignadas', array_filter(['estado'=>'pendiente','f'=>$f,'q'=>$q])) }}"
           class="chip {{ $estado==='pendiente' ? 'chip-primary' : 'chip-outline' }}">Pendientes</a>

        <a href="{{ route('medico.actividades_terap.asignadas', array_filter(['estado'=>'completada','f'=>$f,'q'=>$q])) }}"
           class="chip {{ $estado==='completada' ? 'chip-primary' : 'chip-outline' }}">Completadas</a>
      </div>

      {{-- 🔍 Barra de búsqueda (idéntica a “Gestión de Tutores”) --}}
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
            <span>Tipo: <b>{{ ucfirst($a->tipoContenido) }}</b></span>
            <span>· Categoría: {{ $a->categoriaTerapeutica ?? 'N/D' }}</span>
            <span>· Diagnóstico: {{ $a->diagnosticoDirigido ?? 'N/D' }}</span>
            <span>· Severidad: {{ $a->nivelSeveridad ?? 'N/D' }}</span>
          </div>
          <div class="dates">
            Asignada: <b>{{ $fechaAsig }}</b>
            @if($fechaLim) · Límite: <b>{{ $fechaLim }}</b>@endif
          </div>
        </div>

        <span class="badge {{ $a->estado==='pendiente' ? 'badge-pending' : 'badge-done' }}">
          {{ ucfirst($a->estado) }}
        </span>
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
  section.content-header { margin-top:-10px!important; padding-top:5px!important; }
  .search-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; width:100%; }
  .search-input-group { display:flex; align-items:center; gap:8px; flex:1; }
  .search-input-group input { flex:1; max-width:980px; border-radius:10px; font-size:1rem; }
  .search-input-group select { width:220px; border-radius:10px; }
  .card-search { border-radius:12px; padding:15px 20px; }
  .chip{ padding:6px 10px; border-radius:999px; text-decoration:none; font-size:.875rem; border:1px solid transparent; }
  .chip-primary{ background:#2563eb; color:#fff; }
  .chip-outline{ border-color:#e6eaf0; color:#1f2937; background:#fff; }
  .filter-label{ color:#6b7280; }

  :root{
    --bg:#f8fafc; --card:#ffffff; --border:#e6eaf0; --text:#1f2937; --muted:#6b7280;
    --primary:#2563eb; --primary-600:#1d4ed8; --ok:#16a34a;
  }
  body{ background:var(--bg); }
  .alert{ margin:12px 0; padding:10px 12px; border-radius:12px; font-size:.95rem; }
  .alert.ok{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .alert.warn{ background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
  .alert.err{ background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

  .activity-card{ background:var(--card); border:1px solid var(--border); border-radius:14px; box-shadow:0 4px 12px rgba(0,0,0,0.04); padding:16px; margin-bottom:14px; }
  .card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap; }
  .activity-title{ margin:6px 0 4px; color:var(--text); font-size:1.15rem; }
  .meta{ color:var(--muted); display:flex; flex-wrap:wrap; gap:8px; }
  .dates{ color:var(--muted); margin-top:4px; }
  .badge{ padding:6px 10px; border-radius:999px; font-size:.83rem; }
  .badge-pending{ background:#fff7ed; color:#7c2d12; border:1px solid #fed7aa; }
  .badge-done{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .paciente-chip{ display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; background:#eef2ff; color:#3730a3; font-size:.85rem; }
  .paciente-chip .txt{ font-weight:600; }
  .note{ margin-top:12px; background:#f8fafc; border:1px dashed var(--border); border-radius:12px; }
  .note-title{ font-weight:600; padding:10px 12px 4px; color:#0f172a; }
  .note-body{ padding:0 12px 10px; color:#374151; line-height:1.5; white-space:pre-line; }
  .resource{ margin-top:12px; }
  .btn-link{ display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid var(--border); border-radius:10px; text-decoration:none; color:#1f2937; background:#fff; }
  .btn-link:hover{ background:#f3f4f6; }
  .media{ max-width:100%; height:auto; border-radius:12px; border:1px solid #e6eaf0; }
  .img{ display:block; }
  .muted{ color:#6b7280; }
  .empty{ margin-top:10px; text-align:center; }
  .empty .empty-body{ padding:36px 16px; }
  .empty i{ font-size:28px; color:#6b7280; }
  .pagination-wrap{ margin-top:16px; display:flex; justify-content:center; }
</style>

<script>
  document.getElementById('search-input')?.addEventListener('keydown', function(e){
    if(e.key === 'Enter'){ this.form.submit(); }
  });
</script>
@endsection
