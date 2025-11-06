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
    <div class="head-left">
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

{{-- ===== Mascotita flotante (toast) ===== --}}
<div id="mwMascotToast" class="mw-mascot-toast" aria-live="polite" aria-atomic="true">
  <div class="mw-mascot-bubble">
    <div class="mw-mascot-svg" aria-hidden="true">
      <svg viewBox="0 0 120 120">
        <defs>
          <linearGradient id="mwGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%"  stop-color="#cdbaf0"/>
            <stop offset="100%" stop-color="#bfcad8"/>
          </linearGradient>
        </defs>
        <path d="M60 15c16 0 31 8 38 19 7 11 6 24 0 36-6 12-19 23-35 24-16 2-34-5-41-17-7-12-3-28 6-41 9-13 16-21 32-21z"
              fill="url(#mwGrad)"></path>
        <circle cx="48" cy="55" r="5" fill="#1f2d3d"/>
        <circle cx="72" cy="55" r="5" fill="#1f2d3d"/>
        <path d="M46 70c6 10 22 10 28 0" stroke="#1f2d3d" stroke-width="3" stroke-linecap="round" fill="none"/>
      </svg>
    </div>
    <div class="mw-mascot-text">
      <strong>¡Gracias por completar tu actividad!</strong>
      <span class="d-block small">Sigue así, estás avanzando muy bien ✨</span>
    </div>
    <button type="button" class="mw-mascot-close" aria-label="Cerrar">&times;</button>
  </div>
</div>

{{-- ===== JS mínimo ===== --}}
<script>
(function(){
  // Toggle preview
  document.querySelectorAll('[data-toggle-preview]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const sel = btn.getAttribute('data-toggle-preview');
      const el  = document.querySelector(sel);
      if(!el) return;
      el.classList.toggle('hidden');
      btn.blur();
    });
  });

  // Copiar enlace
  document.querySelectorAll('[data-copy-url]').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      try{
        await navigator.clipboard.writeText(btn.getAttribute('data-copy-url'));
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado';
        setTimeout(()=>btn.innerHTML = '<i class="far fa-copy"></i> Copiar enlace', 1200);
      }catch{}
    });
  });

  // Modal abrir/cerrar
  document.querySelectorAll('[data-open-complete]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const target = document.querySelector(btn.getAttribute('data-open-complete'));
      if(target) target.classList.add('is-open');
    });
  });
  document.addEventListener('click', e=>{
    const close = e.target.closest('[data-close]');
    if(close){
      const modal = close.closest('.light-modal');
      if(modal) modal.classList.remove('is-open');
    }
  });

  // Mascotita: mostrar si viene de sesión success
  const toast = document.getElementById('mwMascotToast');
  const closeBtn = toast.querySelector('.mw-mascot-close');
  const showMascot = () => {
    toast.classList.add('show');
    // Autocerrar a los 10s
    const t = setTimeout(()=> toast.classList.remove('show'), 10000);
    closeBtn.onclick = () => { clearTimeout(t); toast.classList.remove('show'); };
  };

  @if(session('success'))
    showMascot();
  @endif

  // También puedes llamar manualmente: window.dispatchEvent(new Event('mw:mascot'))
  window.addEventListener('mw:mascot', showMascot);
})();
</script>

{{-- ===== ESTILOS MindWare (dos colores base) ===== --}}
<style>
:root{
  /* Paleta solicitada */
  --mw-lila: #f0e2f8;   /* lila bajito principal */
  --mw-azul: #d7dfe9;   /* azul suave */
  /* Derivados sutiles */
  --mw-lila-strong:#e1c9f1;
  --mw-lila-border:#d9c3ee;
  --mw-azul-strong:#c6d1de;
  --ink:#0f172a;
  --muted:#5b6472;
  --white:#ffffff;
}

body{ background:linear-gradient(180deg, var(--mw-azul), var(--mw-lila)); }

.activities-wrapper{ max-width:980px; margin:0 auto; padding:8px 12px 24px; }

/* Header */
.header-row{
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px;
  margin:10px 0 14px;
}
.page-title{ margin:0; font-size:2rem; font-weight:900; color:var(--ink); letter-spacing:.2px; }
.page-subtitle{ margin:6px 0 0; color:var(--muted); }

/* Tarjetas y filtros */
.card{
  background:var(--white); border:1px solid var(--mw-azul-strong); border-radius:16px;
  box-shadow:0 8px 24px rgba(51,64,92,.08);
}
.filters-card{ padding:12px 16px; margin-bottom:14px; background: #fff; }
.filters-row{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.filter-label{ color:#394150; font-weight:800; }

/* Chips */
.chip{
  padding:8px 14px; border-radius:999px; text-decoration:none; border:1px solid transparent;
  font-weight:800; font-size:.92rem; transition:.2s;
}
.chip-primary{ background:var(--mw-lila); color:#3a2c53; border:1px solid var(--mw-lila-border); }
.chip-outline{ background:#fff; color:#3a2c53; border:1px solid var(--mw-lila-border); }
.chip-light{ background:#f8fafc; color:#374151; }
.chip:hover{ filter:saturate(1.05) brightness(1.01); }

/* Botones */
.btn-ghost{
  background:#fff; color:#2b2b40; border:1px solid var(--mw-azul-strong); border-radius:12px;
  padding:.6rem 1rem; text-decoration:none; display:inline-flex; gap:8px; align-items:center;
  box-shadow:0 2px 8px rgba(0,0,0,.05); font-weight:800;
}
.btn-ghost:hover{ background:var(--mw-azul); }

.btn-primary{
  background:var(--mw-lila); color:#3a2c53; border:1px solid var(--mw-lila-border); border-radius:12px;
  padding:.65rem 1.05rem; font-weight:900;
}
.btn-primary:hover{ background:var(--mw-lila-strong); }

.btn-link{
  display:inline-flex; align-items:center; gap:8px; padding:9px 14px;
  border:1px solid var(--mw-azul-strong); border-radius:10px; text-decoration:none; color:#2b2b40; background:#fff;
  font-weight:800;
}
.btn-link:hover{ background:var(--mw-azul); }

/* Cards de actividad */
.activity-card{ padding:16px; margin-bottom:14px; transition:.15s; }
.activity-card:hover{ transform:translateY(-1px); box-shadow:0 12px 28px rgba(0,0,0,.10); }
.card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; }
.title-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.activity-title{ margin:6px 0 4px; color:var(--ink); font-size:1.15rem; font-weight:900; }
.badge{ padding:6px 10px; border-radius:999px; font-size:.83rem; font-weight:900; }
.badge-pending{ background:#fff5d1; color:#7c2d12; border:1px solid #f6df97; }
.badge-done{ background:#e7ffe9; color:#065f46; border:1px solid #b9f3c6; }

.meta{ display:flex; flex-wrap:wrap; gap:8px; margin-top:4px; }
.pill{
  background:var(--mw-azul); color:#18223a; border:1px dashed var(--mw-azul-strong);
  padding:6px 10px; border-radius:999px; font-size:.86rem; font-weight:800;
}
.dates{ color:var(--muted); margin-top:6px; display:flex; gap:10px; align-items:center; }
.dates .dot{ opacity:.6; }

/* Indicaciones */
.note{ margin-top:10px; border:1px dashed var(--mw-azul-strong); border-radius:12px; background:#f9fbff; }
.note[open]{ background:#f6f8ff; }
.note-title{ list-style:none; cursor:pointer; padding:10px 12px; font-weight:900; color:#0f172a; display:flex; align-items:center; gap:8px; }
.note-title::-webkit-details-marker{ display:none; }
.note-body{ padding:0 12px 10px; color:#374151; line-height:1.55; white-space:pre-line; }

/* Recurso */
.resource{ margin-top:12px; }
.resource-actions{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.media{ max-width:100%; width:100%; height:auto; border-radius:12px; border:1px solid var(--mw-azul-strong); }
.iframe{ min-height:400px; }
.preview.hidden{ display:none; }

/* Acciones */
.actions{ display:flex; align-items:center; gap:10px; margin-top:12px; }
.muted{ color:#6b7280; }

/* Modal ligero */
.light-modal{ position:fixed; inset:0; display:none; z-index:1070; }
.light-modal.is-open{ display:block; }
.light-modal__backdrop{ position:absolute; inset:0; background:rgba(31,41,55,.45); }
.light-modal__dialog{ position:relative; z-index:2; margin:8vh auto; max-width:520px; background:#fff; border-radius:16px; border:1px solid var(--mw-azul-strong); box-shadow:0 25px 60px rgba(0,0,0,.2); }
.light-modal__head{ display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--mw-azul-strong); background:linear-gradient(135deg, #cbb6ff, #9f7aea); color:#fff; }
.light-modal__title{ margin:0; font-weight:900; }
.light-modal__close{ background:#fff; border:1px solid var(--mw-azul-strong); border-radius:10px; padding:6px 10px; }
.light-modal__body{ padding:16px; color:#111827; }
.light-modal__foot{ padding:12px 16px 16px; display:flex; gap:10px; }

/* Empty & paginación */
.empty{ text-align:center; }
.empty .empty-body{ padding:36px 16px; }
.empty i{ font-size:28px; color:#6b7280; }
.pagination-wrap{ margin:12px 0 6px; display:flex; justify-content:center; }

/* Mascotita flotante */
.mw-mascot-toast{
  position:fixed; right:24px; bottom:24px; z-index:1080; pointer-events:none; opacity:0; transform:translateY(16px);
  transition:opacity .35s ease, transform .35s ease;
}
.mw-mascot-toast.show{ opacity:1; transform:translateY(0); }
.mw-mascot-bubble{
  display:flex; align-items:center; gap:14px; background:#fff; border:1px solid var(--mw-azul-strong);
  border-radius:16px; box-shadow:0 14px 30px rgba(33,55,79,.18); padding:12px 14px 12px 10px; pointer-events:auto;
}
.mw-mascot-svg{ width:64px; height:64px; filter:drop-shadow(0 8px 14px rgba(17,35,61,.18)); animation:floaty 3s ease-in-out infinite; }
@keyframes floaty { 0%,100%{ transform:translateY(0) } 50%{ transform:translateY(-6px) } }
.mw-mascot-text{ color:#1f2d3d; }
.mw-mascot-text strong{ font-weight:900; display:block; }
.mw-mascot-close{
  background:transparent; border:0; font-size:24px; line-height:1; color:#6b7280; padding:0 4px;
}
.mw-mascot-close:hover{ color:#111827; }

/* Accesibilidad: foco visible */
.btn-primary:focus, .btn-ghost:focus, .btn-link:focus, .chip:focus, .light-modal__close:focus{
  outline: 3px solid var(--mw-azul-strong);
  outline-offset: 2px;
}
</style>

@include('paciente.bottom-nabvar')
@endsection
 