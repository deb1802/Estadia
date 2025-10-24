@extends('layouts.app') {{-- si usas AdminLTE, cambia por @extends('adminlte::page') --}}

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    // Helper para resolver URL del recurso
    function recurso_url_local($raw) {
        $raw = trim((string)($raw ?? ''));
        if ($raw === '') return null;
        if (Str::startsWith($raw, ['http://','https://','//'])) return $raw;
        if (Storage::disk('public')->exists($raw)) return Storage::url($raw);
        return asset(ltrim($raw,'/'));
    }

    $estado = $estado ?? request('estado');
@endphp

@section('content')
<div class="activities-wrapper">
  {{-- Header --}}
  <div class="header-row">
    <div>
      <h1 class="page-title">Mis actividades</h1>
      <p class="page-subtitle">Consulta, revisa el recurso y marca como completadas.</p>
    </div>
    <a href="{{ route('paciente.dashboard') }}" class="btn-ghost">
      <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>
  </div>

  {{-- Filtros --}}
  <div class="card filters-card">
    <div class="filters-row">
      <span class="filter-label">Filtrar por estado:</span>

      <a href="{{ route('paciente.actividades.index') }}"
         class="chip {{ $estado ? 'chip-outline' : 'chip-primary' }}">Todas</a>

      <a href="{{ route('paciente.actividades.index', ['estado'=>'pendiente']) }}"
         class="chip {{ $estado==='pendiente' ? 'chip-primary' : 'chip-outline' }}">Pendientes</a>

      <a href="{{ route('paciente.actividades.index', ['estado'=>'completada']) }}"
         class="chip {{ $estado==='completada' ? 'chip-primary' : 'chip-outline' }}">Completadas</a>

      @if($estado)
        <a href="{{ route('paciente.actividades.index') }}" class="chip chip-light">
          <i class="fas fa-times"></i> Limpiar
        </a>
      @endif
    </div>
  </div>

  {{-- Mensajes --}}
  @if(session('success'))  <div class="alert ok">{{ session('success') }}</div> @endif
  @if(session('warning'))  <div class="alert warn">{{ session('warning') }}</div> @endif
  @if($errors->any())      <div class="alert err">{{ $errors->first() }}</div> @endif

  {{-- Listado --}}
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

      // Si el controller trajo observaciones/indicaciones con alias:
      $indicaciones = $a->indicacionesMedicas
                        ?? $a->observaciones
                        ?? null;
    @endphp

    <div class="card activity-card">
      <div class="card-head">
        <div class="title-col">
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

      {{-- Indicaciones del médico (si hay) --}}
      @if($indicaciones)
        <div class="note">
          <div class="note-title"><i class="fas fa-sticky-note"></i> Indicaciones del médico</div>
          <div class="note-body">{!! nl2br(e($indicaciones)) !!}</div>
        </div>
      @endif

      {{-- Recurso --}}
      <div class="resource">
        @if($url)
          @if($isVideoLink)
            <a href="{{ $url }}" target="_blank" class="btn-link">
              <i class="fas fa-external-link-alt"></i> Ver video
            </a>
          @elseif($isVideoFile)
            <video src="{{ $url }}" controls class="media"></video>
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

      {{-- Acciones --}}
      <div class="actions">
        @if($a->estado === 'pendiente')
          <form method="POST" action="{{ route('paciente.actividades.completar', $a->idAsignacionActividad) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn-primary">
              <i class="fas fa-check-circle"></i> Marcar como completada
            </button>
          </form>
        @else
          <span class="muted"><i class="fas fa-check-circle"></i> Completada</span>
        @endif
      </div>
    </div>
  @empty
    <div class="card empty">
      <div class="empty-body">
        <i class="fas fa-clipboard-list"></i>
        <p>Aún no tienes actividades asignadas.</p>
        <a href="{{ route('paciente.dashboard') }}" class="btn-ghost">
          <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
      </div>
    </div>
  @endforelse

  {{-- Paginación --}}
  @if(method_exists($asignaciones,'links'))
    <div class="pagination-wrap">
      {{ $asignaciones->links() }}
    </div>
  @endif
</div>

{{-- ===== Estilos embebidos (centrado y mejor tipografía) ===== --}}
<style>
  :root{
    --bg:#f8fafc; --card:#ffffff; --border:#e6eaf0; --text:#1f2937; --muted:#6b7280;
    --primary:#2563eb; --primary-600:#1d4ed8; --ok:#16a34a; --warn:#f59e0b;
  }
  body{ background:var(--bg); }
  .activities-wrapper{ max-width: 960px; margin: 0 auto; padding: 24px 16px; }
  .header-row{ display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
  .page-title{ font-size: 1.5rem; margin:0; color:var(--text); }
  .page-subtitle{ margin:2px 0 0; color:var(--muted); }

  .btn-ghost{
    display:inline-flex; align-items:center; gap:8px;
    padding:10px 14px; border:1px solid var(--border); border-radius:12px;
    background:#fff; color:var(--text); text-decoration:none;
  }
  .btn-ghost:hover{ background:#f3f4f6; }

  .card{ background:var(--card); border:1px solid var(--border); border-radius:14px; box-shadow:0 4px 12px rgba(0,0,0,0.04); }
  .filters-card{ margin-top:16px; }
  .filters-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:12px 16px; }
  .filter-label{ color:var(--muted); margin-right:4px; }

  .chip{ padding:6px 10px; border-radius:999px; text-decoration:none; font-size:.875rem; border:1px solid transparent; }
  .chip-primary{ background:var(--primary); color:#fff; }
  .chip-outline{ border-color:var(--border); color:var(--text); background:#fff; }
  .chip-light{ background:#f3f4f6; color:var(--text); }

  .alert{ margin:12px 0; padding:10px 12px; border-radius:12px; font-size:.95rem; }
  .alert.ok{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .alert.warn{ background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
  .alert.err{ background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

  .activity-card{ padding:16px; margin-bottom:14px; }
  .card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap; }
  .activity-title{ margin:0 0 4px; color:var(--text); font-size:1.15rem; }
  .meta{ color:var(--muted); display:flex; flex-wrap:wrap; gap:8px; }
  .dates{ color:var(--muted); margin-top:4px; }

  .badge{ padding:6px 10px; border-radius:999px; font-size:.83rem; }
  .badge-pending{ background:#fff7ed; color:#7c2d12; border:1px solid #fed7aa; }
  .badge-done{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }

  .note{ margin-top:12px; background:#f8fafc; border:1px dashed var(--border); border-radius:12px; }
  .note-title{ font-weight:600; padding:10px 12px 4px; color:#0f172a; }
  .note-body{ padding:0 12px 10px; color:#374151; line-height:1.5; white-space:pre-line; }

  .resource{ margin-top:12px; }
  .btn-link{ display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid var(--border); border-radius:10px; text-decoration:none; color:var(--text); background:#fff; }
  .btn-link:hover{ background:#f3f4f6; }
  .media{ max-width:100%; height:auto; border-radius:12px; border:1px solid var(--border); }
  .img{ display:block; }

  .actions{ margin-top:14px; display:flex; justify-content:flex-end; }
  .btn-primary{
    display:inline-flex; align-items:center; gap:8px; padding:10px 16px;
    background:var(--primary); color:#fff; border:none; border-radius:12px; cursor:pointer;
  }
  .btn-primary:hover{ background:var(--primary-600); }
  .muted{ color:var(--muted); }

  .empty{ margin-top:10px; text-align:center; }
  .empty .empty-body{ padding:36px 16px; }
  .empty i{ font-size:28px; color:var(--muted); }

  .pagination-wrap{ margin-top:16px; display:flex; justify-content:center; }
</style>
@endsection
