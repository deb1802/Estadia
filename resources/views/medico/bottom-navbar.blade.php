{{-- resources/views/medico/bottom-navbar.blade.php --}}
@php
  use App\Models\Notificacion;
  use Illuminate\Support\Facades\Auth;

  $__uid    = Auth::id();
  $__unread = Notificacion::where('fkUsuario', $__uid)->where('leida', 0)->count();
  $__items  = Notificacion::where('fkUsuario', $__uid)->orderBy('fecha','desc')->limit(10)->get();

  // Activo por ruta (para resaltar el ítem actual)
  $__isNoti   = request()->is('medico/notificaciones*');
  $__isHome   = request()->routeIs('medico.dashboard') || request()->is('medico') || request()->is('medico/');
  $__isCitas  = request()->routeIs('medico.citas.*');
  $__isTests  = request()->is('medico/tests*');
@endphp

<style>
  /* ===== Barra inferior médico ===== */
  .doctor-bottom-navbar {
    position: fixed; left: 0; right: 0; bottom: 0; z-index: 1040;
    background: #fff; border-top: 1px solid #e5e7eb;
    box-shadow: 0 -8px 24px rgba(2,6,23,.06);
    padding-bottom: env(safe-area-inset-bottom);
  }
  .doctor-bottom-navbar .dbn-wrap {
    max-width: 820px; margin: 0 auto; height: 58px;
    display: flex; align-items: center; justify-content: space-around;
  }
  .doctor-bottom-navbar .dbn-link {
    position: relative; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 2px; color: #64748b; text-decoration: none;
    font-size: .83rem; padding: 6px 10px;
    transition: color .18s ease, transform .18s ease;
  }
  .doctor-bottom-navbar .dbn-link:hover { color: #111827; transform: translateY(-2px); }
  .doctor-bottom-navbar .dbn-link.active { color: #1d4ed8; } /* azul activo */
  .doctor-bottom-navbar .dbn-badge {
    position: absolute; top: -4px; right: -2px;
    min-width: 18px; height: 18px; padding: 0 5px;
    background: #ef4444; color: #fff;
    border-radius: 999px; font-size: .72rem; font-weight: 700;
    display: {{ $__unread > 0 ? 'flex' : 'none' }};
    align-items: center; justify-content: center;
  }
  .dbn-spacer { height: calc(62px + env(safe-area-inset-bottom)); }

  /* ===== Modal Notificaciones ===== */
  .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050; display: none; }
  .modal.show { display: block; }
  .modal-dialog {
    position: absolute; left: 0; right: 0; bottom: 14px; margin: 0 auto;
    pointer-events: none; max-width: 480px; width: calc(100% - 24px);
  }
  .modal-content {
    pointer-events: auto; background: #fff; border: 0; border-radius: 16px;
    box-shadow: 0 24px 60px rgba(2,6,23,.22); overflow: hidden;
  }
  .modal-header {
    display: flex; align-items: center; gap: 8px;
    background: linear-gradient(180deg, #eaf2ff, #ffffff 70%);
    border-bottom: 1px solid #e6efff; padding: .6rem .85rem;
  }
  .modal-title { margin: 0; font-size: 1rem; font-weight: 800; color: #1e40af; }
  .close { padding: .25rem .5rem; background: transparent; border: 0; font-size: 1.25rem; line-height: 1; color: #334155; }
  .modal-body { padding: 0; }
  .list-group { list-style: none; margin: 0; padding: 0; }
  .list-group-item { padding: .75rem .95rem; border-bottom: 1px solid #eff2f8; background: #ffffff; }
  .list-group-item:last-child { border-bottom: 0; }
  .font-weight-bold { font-weight: 800; color: #0f172a; }
  .text-muted { color: #6b7280; }
  .small { font-size: .86rem; }
  .list-group-item:hover { background: #f8fbff; }
  .badge-unread {
    display: inline-block; margin-left: .5rem; font-size: .68rem; font-weight: 800;
    color: #1d4ed8; background: #e8f0ff; padding: .12rem .4rem; border-radius: 999px;
  }
  .btn { display:inline-block; font-weight:600; padding:.28rem .65rem; border:1px solid #cbd5e1;
        border-radius:.5rem; background:#fff; color:#374151; font-size:.85rem; }
  .btn-outline-secondary{ border-color:#cbd5e1; color:#334155; }
  .btn-outline-secondary:hover{ background:#f3f4f6; }
  .btn-outline-primary{ border-color:#3b82f6; color:#1d4ed8; }
  .btn-outline-primary:hover{ background:#eff6ff; }
</style>

<div class="doctor-bottom-navbar" role="navigation" aria-label="Barra inferior de médico">
  <div class="dbn-wrap">

    {{-- Notificaciones --}}
    <a href="#" class="dbn-link {{ $__isNoti ? 'active' : '' }}" data-toggle="modal" data-target="#dbnNotiModal" aria-label="Notificaciones">
      <i class="fas fa-bell fa-lg"></i>
      <span>Notificaciones</span>
      <span class="dbn-badge" id="dbn-noti-badge">{{ $__unread > 99 ? '99+' : $__unread }}</span>
    </a>

    {{-- Inicio --}}
    <a href="{{ route('medico.dashboard') }}" class="dbn-link {{ $__isHome ? 'active' : '' }}" aria-label="Inicio">
      <i class="fas fa-home fa-lg"></i>
      <span>Inicio</span>
    </a>

    {{-- Citas --}}
    <a href="{{ route('medico.citas.index') }}" class="dbn-link {{ $__isCitas ? 'active' : '' }}" aria-label="Citas">
      <i class="fas fa-calendar-check fa-lg"></i>
      <span>Citas</span>
    </a>

    {{-- Tests --}}
    <a href="{{ route('medico.tests.index') }}" class="dbn-link {{ $__isTests ? 'active' : '' }}" aria-label="Tests">
      <i class="fas fa-brain fa-lg"></i>
      <span>Tests</span>
    </a>

  </div>
</div>

<div class="dbn-spacer" aria-hidden="true"></div>

{{-- Modal de notificaciones --}}
<div class="modal fade" id="dbnNotiModal" tabindex="-1" role="dialog" aria-labelledby="dbnNotiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dbnNotiLabel">
          Notificaciones
          @if($__unread > 0)
            <span class="badge-unread">{{ $__unread > 99 ? '99+' : $__unread }}</span>
          @endif
        </h5>

        {{-- Marcar todas --}}
        <form method="POST" action="{{ url('/medico/notificaciones/leertodas') }}" class="ml-auto">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar todas</button>
        </form>

        <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <ul class="list-group list-group-flush">
          @if($__items->isEmpty())
            <li class="list-group-item text-muted">Sin notificaciones.</li>
          @else
            @foreach($__items as $n)
              @php
                // Extraer AID=### del mensaje (si existe)
                $aid = null;
                if (preg_match('/AID=(\d+)/', (string)$n->mensaje, $m)) {
                    $aid = (int)$m[1];
                }
                // Mensaje sin el marcador AID
                $mensajeLimpio = preg_replace('/AID=\d+/', '', (string)$n->mensaje);
              @endphp

              <li class="list-group-item {{ $n->leida ? '' : 'font-weight-bold' }}">
                <div class="small">{{ $n->titulo ?? 'Notificación' }}</div>
                <div class="text-muted small">{{ $mensajeLimpio }}</div>

                <div class="d-flex justify-content-between align-items-center mt-1">
                  <span class="text-muted small">{{ optional($n->fecha)->format('d/m/Y H:i') }}</span>

                  <div class="d-flex gap-2">
                    @if($aid)
                        <a href="{{ route('medico.tests.asignaciones.show', $aid) }}"
                            class="btn btn-sm btn-outline-primary">
                            Ver detalle
                        </a>
                    @endif

                    @unless($n->leida)
                      <form method="POST" action="{{ url('/medico/notificaciones/'.$n->idNotificacion.'/leer') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar leída</button>
                      </form>
                    @endunless
                  </div>
                </div>
              </li>
            @endforeach
          @endif
        </ul>
      </div>

    </div>
  </div>
</div>

{{-- Modal de DETALLE (se llenará en el siguiente paso) --}}
<div class="modal fade" id="dbnDetalleModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document" style="max-width:640px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle del test</h5>
        <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="dbnDetalleBody">
        {{-- Aquí inyectaremos el detalle vía AJAX en el siguiente paso --}}
        <div class="text-muted">Cargando detalle…</div>
      </div>
    </div>
  </div>
</div>

<script>
  async function medVerDetalleTest(btn){
    const aid = btn.getAttribute('data-aid');
    if(!aid) return;

    if (window.$) { $('#dbnDetalleModal').modal('show'); }
    const body = document.getElementById('dbnDetalleBody');
    body.innerHTML = '<div class="text-muted">Cargando detalle…</div>';

    try{
      const url = "{{ route('medico.tests.asignaciones.show', ['idAsignacionTest' => 'AID']) }}".replace('AID', aid);
      const resp = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
      const text = await resp.text();
      if(!resp.ok){
        body.innerHTML = `<div class="text-danger">Error ${resp.status}: ${text}</div>`;
        return;
      }
      body.innerHTML = text;
    }catch(e){
      body.innerHTML = '<div class="text-danger">No se pudo cargar el detalle (conexión).</div>';
      console.error(e);
    }
  }
</script>

<script>
  function limpiarBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
  }

  function abrirDetalleAsignacion(idAsignacion) {
    const url = "{{ route('medico.tests.asignaciones.show', '__ID__') }}".replace('__ID__', idAsignacion);

    const noti = $('#dbnNotiModal');
    if (noti.length && noti.is(':visible')) {
      noti.one('hidden.bs.modal', function () {
        limpiarBackdrops();
        cargarYMostrarDetalle(url);
      }).modal('hide');
    } else {
      cargarYMostrarDetalle(url);
    }
  }

  function cargarYMostrarDetalle(url) {
    const $detalle = $('#dbnDetalleModal');
    $detalle.find('.modal-body').html('<div class="p-3">Cargando…</div>');
    $detalle.modal({backdrop: true, keyboard: true});
    $detalle.modal('show');

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
      .then(r => r.text())
      .then(html => { $detalle.find('.modal-body').html(html); })
      .catch(() => { $detalle.find('.modal-body').html('<div class="p-3 text-danger">Error al cargar el detalle.</div>'); });
  }
</script>

@include('medico.partials.modal-detalle')



