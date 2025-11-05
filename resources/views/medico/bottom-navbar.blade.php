{{-- resources/views/medico/bottom-navbar.blade.php --}}
@php
  use App\Models\Notificacion;
  use Illuminate\Support\Facades\Auth;

  $__uid    = Auth::id();
  $__unread = Notificacion::where('fkUsuario', $__uid)->where('leida', 0)->count();
  $__items  = Notificacion::where('fkUsuario', $__uid)->orderBy('fecha','desc')->limit(10)->get();

  $__isNoti   = request()->is('medico/notificaciones*');
  $__isHome   = request()->routeIs('medico.dashboard') || request()->is('medico') || request()->is('medico/');
  $__isCitas  = request()->routeIs('medico.citas.*');
  $__isTests  = request()->is('medico/tests*');
@endphp

<style>
  .dbn-scope .doctor-bottom-navbar {
    position: fixed; left: 0; right: 0; bottom: 0; z-index: 1040;
    background: #fff; border-top: 1px solid #e5e7eb;
    box-shadow: 0 -8px 24px rgba(2,6,23,.06);
    padding-bottom: env(safe-area-inset-bottom);
  }
  .dbn-scope .doctor-bottom-navbar .dbn-wrap {
    max-width: 820px; margin: 0 auto; height: 58px;
    display: flex; align-items: center; justify-content: space-around;
  }
  .dbn-scope .doctor-bottom-navbar .dbn-link {
    position: relative; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 2px; color: #64748b; text-decoration: none;
    font-size: .83rem; padding: 6px 10px;
    transition: color .18s ease, transform .18s ease;
  }
  .dbn-scope .doctor-bottom-navbar .dbn-link:hover { color: #111827; transform: translateY(-2px); }
  .dbn-scope .doctor-bottom-navbar .dbn-link.active { color: #1d4ed8; }
  .dbn-scope .doctor-bottom-navbar .dbn-badge {
    position: absolute; top: -4px; right: -2px;
    min-width: 18px; height: 18px; padding: 0 5px;
    background: #ef4444; color: #fff;
    border-radius: 999px; font-size: .72rem; font-weight: 700;
    display: {{ $__unread > 0 ? 'flex' : 'none' }};
    align-items: center; justify-content: center;
  }
  .dbn-scope .dbn-spacer { height: calc(62px + env(safe-area-inset-bottom)); }

  /* === Modal notificaciones mejorado === */
  #dbnNotiModal.modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050; display: none; }
  #dbnNotiModal.modal.show { display: block; }
  #dbnNotiModal .modal-dialog {
    position: absolute; left: 0; right: 0; bottom: 14px; margin: 0 auto;
    pointer-events: none; max-width: 480px; width: calc(100% - 24px);
    max-height: 70vh;
  }
  #dbnNotiModal .modal-content {
    pointer-events: auto; background: #fff; border: 0; border-radius: 16px;
    box-shadow: 0 24px 60px rgba(2,6,23,.22);
    display: flex; flex-direction: column; overflow: hidden;
  }
  #dbnNotiModal .modal-header {
    display: flex; align-items: center; gap: 8px;
    background: linear-gradient(180deg, #eaf2ff, #ffffff 70%);
    border-bottom: 1px solid #e6efff; padding: .6rem .85rem;
  }
  #dbnNotiModal .modal-title { margin: 0; font-size: 1rem; font-weight: 800; color: #1e40af; }
  #dbnNotiModal .close { padding: .25rem .5rem; background: transparent; border: 0; font-size: 1.25rem; line-height: 1; color: #334155; }

  #dbnNotiModal .modal-body {
    padding: 0;
    overflow-y: auto;
    max-height: 60vh;
    scroll-behavior: smooth;
    position: relative;
  }
  #dbnNotiModal .modal-body::before,
  #dbnNotiModal .modal-body::after {
    content: "";
    position: sticky;
    left: 0; right: 0;
    height: 20px;
    z-index: 2;
    pointer-events: none;
  }
  #dbnNotiModal .modal-body::before {
    top: 0;
    background: linear-gradient(to bottom, rgba(255,255,255,0.95), transparent);
  }
  #dbnNotiModal .modal-body::after {
    bottom: 0;
    background: linear-gradient(to top, rgba(255,255,255,0.95), transparent);
  }

  #dbnNotiModal .list-group { list-style: none; margin: 0; padding: 0; }
  #dbnNotiModal .list-group-item { padding: .75rem .95rem; border-bottom: 1px solid #eff2f8; background: #ffffff; }
  #dbnNotiModal .list-group-item:last-child { border-bottom: 0; }
  #dbnNotiModal .font-weight-bold { font-weight: 800; color: #0f172a; }
  #dbnNotiModal .text-muted { color: #6b7280; }
  #dbnNotiModal .small { font-size: .86rem; }
  #dbnNotiModal .list-group-item:hover { background: #f8fbff; }
  #dbnNotiModal .badge-unread {
    display: inline-block; margin-left: .5rem; font-size: .68rem; font-weight: 800;
    color: #1d4ed8; background: #e8f0ff; padding: .12rem .4rem; border-radius: 999px;
  }
  #dbnNotiModal .btn { display:inline-block; font-weight:600; padding:.28rem .65rem; border:1px solid #cbd5e1;
        border-radius:.5rem; background:#fff; color:#374151; font-size:.85rem; }
</style>

<div class="dbn-scope">
  <div class="doctor-bottom-navbar">
    <div class="dbn-wrap">
      <a href="#" class="dbn-link {{ $__isNoti ? 'active' : '' }}" data-toggle="modal" data-target="#dbnNotiModal">
        <i class="fas fa-bell fa-lg"></i>
        <span>Notificaciones</span>
        <span class="dbn-badge" id="dbn-noti-badge">{{ $__unread > 99 ? '99+' : $__unread }}</span>
      </a>
      <a href="{{ route('medico.dashboard') }}" class="dbn-link {{ $__isHome ? 'active' : '' }}">
        <i class="fas fa-home fa-lg"></i><span>Inicio</span>
      </a>
      <a href="{{ route('medico.citas.index') }}" class="dbn-link {{ $__isCitas ? 'active' : '' }}">
        <i class="fas fa-calendar-check fa-lg"></i><span>Citas</span>
      </a>
      <a href="{{ route('medico.tests.index') }}" class="dbn-link {{ $__isTests ? 'active' : '' }}">
        <i class="fas fa-brain fa-lg"></i><span>Tests</span>
      </a>
    </div>
  </div>

  <div class="dbn-spacer"></div>

  {{-- Modal de notificaciones --}}
  <div class="modal fade" id="dbnNotiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Notificaciones
            @if($__unread > 0)
              <span class="badge-unread">{{ $__unread > 99 ? '99+' : $__unread }}</span>
            @endif
          </h5>
          <form method="POST" action="{{ url('/medico/notificaciones/leertodas') }}" class="ml-auto">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar todas como leída</button>
          </form>
          <button type="button" class="close ml-2" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <ul class="list-group list-group-flush">
            @forelse($__items as $n)
              <li class="list-group-item {{ $n->leida ? '' : 'font-weight-bold' }}">
                <div class="small">{{ $n->titulo ?? 'Notificación' }}</div>
                <div class="text-muted small">{{ $n->mensaje }}</div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                  <span class="text-muted small">{{ optional($n->fecha)->format('d/m/Y H:i') }}</span>
                  @unless($n->leida)
                    <form method="POST" action="{{ url('/medico/notificaciones/'.$n->idNotificacion.'/leer') }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar leída</button>
                    </form>
                  @endunless
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted">Sin notificaciones.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
