@extends('layouts.app') 

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

  {{-- Mensajes --}}
  @if(session('success'))  <div class="alert ok">{{ session('success') }}</div> @endif
  @if(session('warning'))  <div class="alert warn">{{ session('warning') }}</div> @endif
  @if($errors->any())      <div class="alert err">{{ $errors->first() }}</div> @endif

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

      {{-- Indicaciones del médico (colapsable) --}}
      @if($indicaciones)
        <details class="note" id="{{ $collapseId }}">
          <summary class="note-title">
            <i class="fas fa-sticky-note"></i> Indicaciones del médico
            <span class="chev"><i class="fas fa-chevron-down"></i></span>
          </summary>
          <div class="note-body">{!! nl2br(e($indicaciones)) !!}</div>
        </details>
      @endif

      {{-- Recurso con previsualización opcional --}}
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

      {{-- Acciones --}}
      <div class="actions">
        @if($a->estado === 'pendiente')
          <button class="btn-primary" type="button"
                  data-open-complete="#confirm-{{ $cardId }}">
            <i class="fas fa-check-circle"></i> Marcar como completada
          </button>

          {{-- Modal ligero de confirmación (no depende de Bootstrap) --}}
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

  {{-- Paginación --}}
  @if(method_exists($asignaciones,'links'))
    <div class="pagination-wrap">
      {{ $asignaciones->links() }}
    </div>
  @endif
</div>

{{-- ===== Estilos embebidos: paleta personalizada e interactividad ===== --}}
<style>
  :root{
    /* Paleta solicitada */
    --mind-bg:#d7dfe9;   /* base suave */
    --mind-soft:#b5c8e1; /* acento suave */
    --mind-pop:#f0e2f8;  /* highlight lila */

    /* Derivados y semánticos */
    --bg:var(--mind-bg);
    --card:#ffffff;
    --border:#e5eaf0;
    --text:#1f2937;
    --muted:#5b6b84;

    --primary:#6b7fb3;         /* derivado azulado para contraste con la paleta */
    --primary-600:#556a9a;
    --accent:var(--mind-soft);  /* acento */
    --accent-600:#96b1cf;
    --pop:var(--mind-pop);      /* pop lila */
    --pop-600:#e5cff2;

    --ok:#15956a;
    --warn:#c78326;

    --ring:rgba(107,127,179,.35);
  }

  body{ background:
    radial-gradient(1200px 400px at 10% -10%, var(--pop) 0%, transparent 50%),
    radial-gradient(800px 320px at 120% 0%, var(--accent) 0%, transparent 45%),
    var(--bg);
  }

  .activities-wrapper{ max-width: 1020px; margin: 0 auto; padding: 26px 16px; }
  .header-row{ display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
  .page-title{ font-size: 1.6rem; margin:0; color:var(--text); font-weight:800; letter-spacing:.2px; }
  .page-subtitle{ margin:4px 0 0; color:var(--muted); }

  .btn-ghost{
    display:inline-flex; align-items:center; gap:8px;
    padding:10px 14px; border:1px solid var(--border); border-radius:12px;
    background:linear-gradient(180deg,#fff, #fbfdff);
    color:var(--text); text-decoration:none; transition: transform .18s ease, box-shadow .18s ease;
    box-shadow:0 6px 20px rgba(15,23,42,.05);
  }
  .btn-ghost:hover{ transform: translateY(-1px); box-shadow:0 10px 24px rgba(15,23,42,.08); }

  .card{
    background:linear-gradient(180deg,#fff, #fdfdff);
    border:1px solid var(--border); border-radius:16px;
    box-shadow:0 10px 26px rgba(14,31,56,.05);
  }

  .filters-card{ margin-top:16px; }
  .filters-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:12px 16px; }
  .filter-label{ color:var(--muted); margin-right:4px; }

  .chip{
    padding:7px 12px; border-radius:999px; text-decoration:none; font-size:.9rem;
    border:1px solid transparent; transition: transform .12s ease, box-shadow .12s ease;
  }
  .chip-primary{ background:var(--accent); color:#0e2a43; border-color:#a9c2dc; box-shadow:0 6px 16px rgba(34,64,104,.08); }
  .chip-outline{ border-color:#d9e3ef; color:#20324f; background:#fff; }
  .chip-light{ background:#eef3fb; color:#20324f; }
  .chip:hover{ transform: translateY(-1px); box-shadow:0 8px 20px rgba(20,40,70,.08); }

  .alert{ margin:12px 0; padding:10px 12px; border-radius:12px; font-size:.95rem; }
  .alert.ok{ background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .alert.warn{ background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
  .alert.err{ background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

  .activity-card{ padding:16px; margin-top:14px; transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
  .activity-card:focus{
    outline: none;
    box-shadow: 0 0 0 4px var(--ring);
  }
  .activity-card:hover{
    transform: translateY(-2px);
    box-shadow:0 16px 36px rgba(14,31,56,.09);
    border-color:#d4deea;
  }

  .card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap; }
  .title-col{ flex:1; min-width: 260px; }
  .title-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .activity-title{ margin:0 0 4px; color:#142844; font-size:1.18rem; font-weight:800; }
  .meta{ color:#2e4366; display:flex; flex-wrap:wrap; gap:8px; margin-top:4px; }
  .pill{
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 10px; border-radius:999px; font-size:.83rem;
    background:linear-gradient(180deg, #f7f9ff, #eef4ff); border:1px solid #d6e2fb;
  }
  .dates{ color:#2f466a; margin-top:8px; font-size:.92rem; display:flex; align-items:center; gap:8px; }
  .dot{ opacity:.55; }

  .badge{ padding:6px 10px; border-radius:999px; font-size:.83rem; border:1px solid transparent; }
  .badge-pending{ background:var(--pop); color:#502e66; border-color:#e2c9f0; }
  .badge-done{ background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }

  /* Indicaciones */
  .note{ margin-top:12px; background:#fbfdff; border:1px dashed #cfd9ea; border-radius:12px; overflow:hidden; }
  .note-title{
    list-style:none; cursor:pointer; font-weight:700; padding:12px 14px; color:#0f172a;
    display:flex; align-items:center; justify-content:space-between; background:linear-gradient(180deg,#eef5ff,#ffffff);
  }
  .note[open] .chev{ transform: rotate(180deg); }
  .note-body{ padding:12px 14px; color:#374151; line-height:1.55; white-space:pre-line; background:#fff; }

  /* Recurso */
  .resource{ margin-top:14px; }
  .resource-actions{ display:flex; flex-wrap:wrap; gap:10px; }
  .btn-link{
    display:inline-flex; align-items:center; gap:8px; padding:9px 12px;
    border:1px solid #dbe4f0; border-radius:10px; text-decoration:none; color:#1c2f4d; background:linear-gradient(180deg,#fff,#fbfcff);
    transition: transform .12s ease, box-shadow .12s ease;
  }
  .btn-link:hover{ transform: translateY(-1px); box-shadow:0 10px 24px rgba(20,40,70,.08); }
  .media{ max-width:100%; height:auto; border-radius:12px; border:1px solid #dbe4f0; }
  .img{ display:block; }
  .iframe{ width:100%; height:480px; border:0; border-radius:12px; }
  .embed-wrap{ padding:10px 0; }

  .preview{ margin-top:12px; }
  .hidden{ display:none; }

  .actions{ margin-top:16px; display:flex; justify-content:flex-end; }
  .btn-primary{
    display:inline-flex; align-items:center; gap:8px; padding:10px 16px;
    background:linear-gradient(180deg, var(--primary), var(--primary-600));
    color:#fff; border:none; border-radius:12px; cursor:pointer;
    box-shadow:0 10px 24px rgba(14,31,56,.12);
    transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
  }
  .btn-primary:hover{ transform: translateY(-1px); filter: brightness(1.02); box-shadow:0 14px 30px rgba(14,31,56,.16); }
  .muted{ color:#5b6b84; }

  .empty{ margin-top:10px; text-align:center; }
  .empty .empty-body{ padding:36px 16px; }
  .empty i{ font-size:28px; color:#5b6b84; }

  .pagination-wrap{ margin-top:16px; display:flex; justify-content:center; }

  /* Modal ligero */
  .light-modal{ position:fixed; inset:0; z-index:1050; display:none; }
  .light-modal[aria-hidden="false"]{ display:block; }
  .light-modal__backdrop{ position:absolute; inset:0; background:#0f172a; opacity:.42; }
  .light-modal__dialog{
    position:absolute; left:0; right:0; bottom:16px; margin:0 auto; max-width:480px; width:calc(100% - 24px);
    background:#fff; border-radius:16px; box-shadow:0 24px 60px rgba(2,6,23,.22);
    transform: translateY(12px); transition: transform .18s ease-out;
  }
  .light-modal[aria-hidden="false"] .light-modal__dialog{ transform: translateY(0); }
  .light-modal__head{ display:flex; align-items:center; justify-content:space-between; gap:8px; padding:10px 12px; background:linear-gradient(180deg,#eaf2ff,#ffffff 70%); border-bottom:1px solid #e6efff; }
  .light-modal__title{ margin:0; font-size:1rem; font-weight:800; color:#1e40af; }
  .light-modal__close{ padding:.25rem .5rem; background:transparent; border:0; font-size:1.25rem; line-height:1; color:#334155; cursor:pointer; }
  .light-modal__body{ padding:14px 12px; color:#1f2937; }
  .light-modal__foot{ display:flex; gap:10px; align-items:center; justify-content:flex-end; padding:12px; border-top:1px solid #eef2f7; }

  /* Estados visuales por estado */
  .activity-card[data-estado="pendiente"]{ border-color:#e9d8ff; background:
    linear-gradient(180deg,#ffffff, #ffffff),
    radial-gradient(600px 120px at 100% -20%, var(--pop) 0%, transparent 60%);
    background-blend-mode: normal, screen;
  }
  .activity-card[data-estado="completada"]{ border-color:#cfe9da; background:
    linear-gradient(180deg,#ffffff, #ffffff),
    radial-gradient(600px 120px at -10% -20%, #d8f5e8 0%, transparent 60%);
    background-blend-mode: normal, screen;
  }

  /* Accesibilidad foco en botones y chips */
  .btn-link:focus, .btn-primary:focus, .btn-ghost:focus, .chip:focus{
    outline: none; box-shadow: 0 0 0 4px var(--ring);
  }
</style>

{{-- ===== Interactividad ligera (vanilla JS) ===== --}}
<script>
  (function() {
    // Toggle de previsualización
    document.querySelectorAll('[data-toggle-preview]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var sel = btn.getAttribute('data-toggle-preview');
        var el = document.querySelector(sel);
        if(!el) return;
        el.classList.toggle('hidden');
        if(!el.classList.contains('hidden')) {
          // Scroll suave hacia la previsualización
          el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    });

    // Copiar URL al portapapeles
    document.querySelectorAll('[data-copy-url]').forEach(function(btn){
      btn.addEventListener('click', async function(){
        var url = btn.getAttribute('data-copy-url');
        try {
          await navigator.clipboard.writeText(url);
          flashToast('Enlace copiado al portapapeles');
        } catch(e) {
          flashToast('No se pudo copiar el enlace');
        }
      });
    });

    // Apertura y cierre de modal ligero
    document.querySelectorAll('[data-open-complete]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var sel = btn.getAttribute('data-open-complete');
        var modal = document.querySelector(sel);
        if(!modal) return;
        modal.setAttribute('aria-hidden','false');
      });
    });
    document.querySelectorAll('.light-modal [data-close], .light-modal__backdrop').forEach(function(el){
      el.addEventListener('click', function(){
        var modal = el.closest('.light-modal') || document.querySelector('.light-modal');
        if(modal) modal.setAttribute('aria-hidden','true');
      });
    });

    // Teclado: Enter en tarjeta enfocada abre confirmar (si está pendiente)
    document.querySelectorAll('.activity-card').forEach(function(card){
      card.addEventListener('keydown', function(ev){
        if(ev.key === 'Enter') {
          var btn = card.querySelector('[data-open-complete]');
          if(btn) { ev.preventDefault(); btn.click(); }
        }
      });
    });

    // Micro-toast básico
    function flashToast(text) {
      var t = document.createElement('div');
      t.className = 'mw-toast';
      t.textContent = text;
      document.body.appendChild(t);
      requestAnimationFrame(function(){
        t.classList.add('show');
      });
      setTimeout(function(){
        t.classList.remove('show');
        setTimeout(function(){ t.remove(); }, 200);
      }, 1800);
    }

    // Estilo dinámico para el toast
    var toastCss = document.createElement('style');
    toastCss.textContent = `
      .mw-toast{
        position: fixed; left: 50%; bottom: 24px; transform: translateX(-50%) translateY(8px);
        background: linear-gradient(180deg, var(--accent), var(--accent-600));
        color:#0e2a43; padding:10px 14px; border-radius:12px; font-weight:700; font-size:.92rem;
        box-shadow:0 12px 30px rgba(30,50,90,.2); opacity:0; transition:.2s ease;
        z-index: 1100;
      }
      .mw-toast.show{ opacity:1; transform: translateX(-50%) translateY(0); }
    `;
    document.head.appendChild(toastCss);
  })();
</script>
@include('paciente.bottom-nabvar')
@endsection
