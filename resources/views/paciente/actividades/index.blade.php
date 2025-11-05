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
    <a href="{{ route('paciente.dashboard') }}" class="btn-ghost" data-action="back-dashboard">
      <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>
  </div>

  {{-- Filtros --}}
  <div class="card filters-card">
    <div class="filters-row">
      <span class="filter-label">Filtrar por estado:</span>

      <a href="{{ route('paciente.actividades.index') }}"
         class="chip {{ $estado ? 'chip-outline' : 'chip-primary' }}"
         data-chip> Todas </a>

      <a href="{{ route('paciente.actividades.index', ['estado'=>'pendiente']) }}"
         class="chip {{ $estado==='pendiente' ? 'chip-primary' : 'chip-outline' }}"
         data-chip> Pendientes </a>

      <a href="{{ route('paciente.actividades.index', ['estado'=>'completada']) }}"
         class="chip {{ $estado==='completada' ? 'chip-primary' : 'chip-outline' }}"
         data-chip> Completadas </a>

      @if($estado)
        <a href="{{ route('paciente.actividades.index') }}" class="chip chip-light" data-chip-clear>
          <i class="fas fa-times"></i> Limpiar
        </a>
      @endif
    </div>
  </div>

  {{-- ✅ Mensaje Dinámico Suave --}}
  @if(session('success'))
      <div id="flash-msg" class="flash-msg flash-success">{{ session('success') }}</div>
  @endif
  @if(session('warning'))
      <div id="flash-msg" class="flash-msg flash-warn">{{ session('warning') }}</div>
  @endif
  @if($errors->any())
      <div id="flash-msg" class="flash-msg flash-error">{{ $errors->first() }}</div>
  @endif

  {{-- Listado --}}
  @forelse($asignaciones as $a)
    @php
      $titulo     = $a->titulo ?? $a->nombreActividad ?? 'Actividad terapéutica';
      $tipo       = $a->tipoContenido ?? 'N/D';
      $categoria  = $a->categoriaTerapeutica ?? 'N/D';
      $diag       = $a->diagnosticoDirigido ?? 'N/D';
      $severidad  = $a->nivelSeveridad ?? 'N/D';

      $url       = recurso_url_local($a->recurso ?? '');
      $fechaAsig = \Carbon\Carbon::parse($a->fechaAsignacion)->format('d/m/Y');
      $fechaLim  = $a->fechaFinalizacion ? \Carbon\Carbon::parse($a->fechaFinalizacion)->format('d/m/Y') : null;

      $lower       = $url ? Str::lower($url) : '';
      $isVideoLink = $url && Str::contains($lower, ['youtube.com','youtu.be','vimeo.com']);
      $isVideoFile = $url && Str::endsWith($lower, ['.mp4','.webm','.ogg']);
      $isPdf       = $url && Str::endsWith($lower, ['.pdf']);
      $isImage     = $url && Str::endsWith($lower, ['.png','.jpg','.jpeg','.gif','.webp']);

      $indicaciones = $a->indicaciones ?? $a->indicacionesMedicas ?? $a->observaciones ?? null;

      $cardId     = 'act-'.($a->idAsignacionActividad ?? Str::random(6));
      $previewId  = $cardId.'-preview';
      $collapseId = $cardId.'-ind';
    @endphp

    <div class="card activity-card" id="{{ $cardId }}" tabindex="0" data-estado="{{ $a->estado }}">
      <div class="card-head">
        <div class="title-col">
          <div class="title-row">
            <h3 class="activity-title">{{ $titulo }}</h3>
            <span class="badge {{ $a->estado==='pendiente' ? 'badge-pending' : 'badge-done' }}">
              {{ ucfirst($a->estado) }}
            </span>
          </div>

          <div class="meta">
            <span class="pill" title="Tipo"><i class="fas fa-tag"></i> {{ ucfirst($tipo) }}</span>
            <span class="pill" title="Categoría"><i class="fas fa-layer-group"></i> {{ $categoria }}</span>
            <span class="pill" title="Diagnóstico"><i class="fas fa-stethoscope"></i> {{ $diag }}</span>
            <span class="pill" title="Severidad"><i class="fas fa-thermometer-half"></i> {{ $severidad }}</span>
          </div>

          <div class="dates">
            <span title="Fecha de asignación"><i class="far fa-calendar-check"></i> {{ $fechaAsig }}</span>
            @if($fechaLim)
              <span class="dot">•</span>
              <span title="Fecha límite">
                <i class="far fa-calendar-times"></i> <b>{{ $fechaLim }}</b>
              </span>
            @endif
          </div>
        </div>
      </div>

      @if($indicaciones)
        <details class="note" id="{{ $collapseId }}">
          <summary class="note-title">
            <i class="fas fa-sticky-note"></i> Indicaciones del médico
            <span class="chev"><i class="fas fa-chevron-down"></i></span>
          </summary>
          <div class="note-body">{!! nl2br(e($indicaciones)) !!}</div>
        </details>
      @endif

      <div class="resource">
        @if($url)
          <div class="resource-actions">
            @if($isVideoLink || $isPdf)
              <a href="{{ $url }}" target="_blank" class="btn-link">
                <i class="fas fa-external-link-alt"></i> Abrir recurso
              </a>
            @endif

            @if($isVideoFile || $isImage)
              <button class="btn-link" type="button" data-toggle-preview="#{{ $previewId }}">
                <i class="far fa-eye"></i> Previsualizar
              </button>
              <a href="{{ $url }}" target="_blank" class="btn-link">
                <i class="fas fa-external-link-alt"></i> Abrir en pestaña
              </a>
            @endif

            @unless($isVideoFile || $isImage || $isVideoLink || $isPdf)
              <a href="{{ $url }}" target="_blank" class="btn-link">
                <i class="fas fa-link"></i> Abrir recurso
              </a>
            @endunless

            <button class="btn-link" type="button" data-copy-url="{{ $url }}">
              <i class="far fa-copy"></i> Copiar enlace
            </button>
          </div>

          <div id="{{ $previewId }}" class="preview hidden">
            @if($isVideoFile)
              <video src="{{ $url }}" controls class="media"></video>
            @elseif($isImage)
              <img src="{{ $url }}" class="media img" alt="Recurso">
            @elseif($isPdf)
              <iframe class="media iframe" src="{{ $url }}" title="PDF"></iframe>
            @elseif($isVideoLink)
              <div class="embed-wrap">
                <a href="{{ $url }}" target="_blank" class="btn-link">
                  <i class="fas fa-external-link-alt"></i> Ver video
                </a>
              </div>
            @endif
          </div>
        @else
          <span class="muted">Sin recurso adjunto.</span>
        @endif
      </div>

      <div class="actions">
        @if($a->estado === 'pendiente')
          <button class="btn-primary" type="button"
                  data-open-complete="#confirm-{{ $cardId }}">
            <i class="fas fa-check-circle"></i> Marcar como completada
          </button>

          <div class="light-modal" id="confirm-{{ $cardId }}" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="light-modal__backdrop" data-close></div>
            <div class="light-modal__dialog" role="document">
              <div class="light-modal__head">
                <h3 class="light-modal__title">Confirmar</h3>
                <button class="light-modal__close" type="button" data-close aria-label="Cerrar">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              <div class="light-modal__body">
                ¿Deseas marcar la actividad "<strong>{{ $titulo }}</strong>" como completada?
              </div>
              <div class="light-modal__foot">
                <form method="POST" action="{{ route('paciente.actividades.completar', $a->idAsignacionActividad) }}">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Sí, completar
                  </button>
                </form>
                <button class="btn-ghost" type="button" data-close>
                  Cancelar
                </button>
              </div>
            </div>
          </div>
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

  @if(method_exists($asignaciones,'links'))
    <div class="pagination-wrap">
      {{ $asignaciones->links() }}
    </div>
  @endif
</div>

{{-- ✅ Estilo para el mensaje dinámico --}}
<style>
.flash-msg {
    max-width: 550px;
    margin: 16px auto;
    padding: 12px 18px;
    border-radius: 14px;
    font-weight: 600;
    text-align: center;
    opacity: 1;
    transition: opacity 1.2s ease-out;
}
.flash-success { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; }
.flash-warn    { background:#fffbeb; border:1px solid #fde68a; color:#92400e; }
.flash-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
</style>

{{-- ✅ Fade-out automático --}}
<script>
setTimeout(() => {
    const msg = document.getElementById('flash-msg');
    if(msg){
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 1300);
    }
}, 3000);
</script>

@include('paciente.bottom-nabvar')
@endsection
