{{-- resources/views/paciente/bottom-nabvar.blade.php --}}
@php
  use App\Models\Notificacion;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Str;

  $__uid    = Auth::id();
  $__unread = Notificacion::where('fkUsuario', $__uid)->where('leida', 0)->count();
  $__items  = Notificacion::where('fkUsuario', $__uid)->orderBy('fecha','desc')->limit(10)->get();

  $__isNoti     = request()->is('paciente/notificaciones*');
  $__isHome     = request()->routeIs('paciente.dashboard') || request()->is('paciente') || request()->is('paciente/');
  $__isRecetas  = request()->routeIs('paciente.recetas.*');
  $__isActs     = request()->is('paciente/actividades*') || request()->routeIs('paciente.actividades_terap.*');
@endphp

<style>
  .patient-bottom-navbar{
    position: fixed; left:0; right:0; bottom:0; z-index:1040;
    background:#fff; border-top:1px solid #e5e7eb;
    box-shadow:0 -8px 24px rgba(2,6,23,.06);
    padding-bottom: env(safe-area-inset-bottom);
  }
  .patient-bottom-navbar .pbn-wrap{
    max-width:820px; margin:0 auto; height:58px;
    display:flex; align-items:center; justify-content:space-around;
  }
  .patient-bottom-navbar .pbn-link{
    position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:2px; color:#64748b; text-decoration:none; font-size:.83rem; padding:6px 10px;
    transition: color .18s ease, transform .18s ease;
  }
  .patient-bottom-navbar .pbn-link:hover{ color:#111827; transform: translateY(-2px); }
  .patient-bottom-navbar .pbn-link.active{ color:#1d4ed8; }
  .patient-bottom-navbar .pbn-badge{
    position:absolute; top:-4px; right:-2px; min-width:18px; height:18px; padding:0 5px;
    background:#ef4444; color:#fff; border-radius:999px; font-size:.72rem; font-weight:700;
    display: {{ $__unread > 0 ? 'flex' : 'none' }}; align-items:center; justify-content:center;
  }
  .pbn-spacer{ height: calc(62px + env(safe-area-inset-bottom)); }

  .modal{ position:fixed; top:0; left:0; width:100%; height:100%; z-index:1050; display:none; }
  .modal.show{ display:block; }
  .modal-dialog{
    position:absolute; left:0; right:0; bottom:14px; margin:0 auto; pointer-events:none;
    max-width:480px; width: calc(100% - 24px);
  }
  .modal-dialog-scrollable .modal-body {
    max-height: 70vh;
    overflow-y: auto;
    scroll-behavior: smooth;
    position: relative;
  }
  .modal-dialog-scrollable .modal-body::before,
  .modal-dialog-scrollable .modal-body::after {
    content: "";
    position: sticky;
    left: 0; right: 0;
    height: 20px;
    z-index: 2;
    pointer-events: none;
  }
  .modal-dialog-scrollable .modal-body::before {
    top: 0;
    background: linear-gradient(to bottom, rgba(255,255,255,0.95), transparent);
  }
  .modal-dialog-scrollable .modal-body::after {
    bottom: 0;
    background: linear-gradient(to top, rgba(255,255,255,0.95), transparent);
  }
  .modal-content{
    pointer-events:auto; background:#ffffff; border:0; border-radius:16px;
    box-shadow:0 24px 60px rgba(2,6,23,.22); overflow:hidden;
  }
  .modal-header{
    display:flex; align-items:center; gap:8px;
    background: linear-gradient(180deg, #eaf2ff, #ffffff 70%);
    border-bottom:1px solid #e6efff; padding:.6rem .85rem;
  }
  .modal-title{ margin:0; font-size:1rem; font-weight:800; color:#1e40af; }
  .close{ padding:.25rem .5rem; background:transparent; border:0; font-size:1.25rem; line-height:1; color:#334155; }

  .btn{ display:inline-block; font-weight:600; padding:.28rem .65rem; border:1px solid #cbd5e1;
        border-radius:.5rem; background:#fff; color:#374151; font-size:.85rem; }
  .btn-sm{ padding:.22rem .55rem; font-size:.82rem; }
  .btn-outline-secondary{ border-color:#cbd5e1; color:#334155; }
  .btn-outline-secondary:hover{ background:#f3f4f6; }
  .btn-outline-primary{ border-color:#3b82f6; color:#1d4ed8; }
  .btn-outline-primary:hover{ background:#eff6ff; }

  .list-group{ list-style:none; margin:0; padding:0; }
  .list-group-item{ padding:.75rem .95rem; border-bottom:1px solid #eff2f8; background:#ffffff; }
  .font-weight-bold{ font-weight:800; color:#0f172a; }
  .text-muted{ color:#6b7280; }
</style>

<div class="patient-bottom-navbar">
  <div class="pbn-wrap">
    <a href="#" class="pbn-link {{ $__isNoti ? 'active' : '' }}" data-toggle="modal" data-target="#pbnNotiModal">
      <i class="fas fa-bell fa-lg"></i><span>Notificaciones</span>
      <span class="pbn-badge" id="pbn-noti-badge">{{ $__unread > 99 ? '99+' : $__unread }}</span>
    </a>
    <a href="{{ route('paciente.dashboard') }}" class="pbn-link {{ $__isHome ? 'active' : '' }}">
      <i class="fas fa-home fa-lg"></i><span>Inicio</span>
    </a>
    <a href="{{ route('paciente.recetas.index') }}" class="pbn-link {{ $__isRecetas ? 'active' : '' }}">
      <i class="fas fa-file-medical fa-lg"></i><span>Mis Recetas</span>
    </a>
    <a href="{{ Route::has('paciente.actividades_terap.index') ? route('paciente.actividades_terap.index') : route('paciente.actividades.index') }}" class="pbn-link {{ $__isActs ? 'active' : '' }}">
      <i class="fas fa-clipboard-list fa-lg"></i><span>Actividades</span>
    </a>
  </div>
</div>

<div class="pbn-spacer"></div>

<div class="modal fade" id="pbnNotiModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Notificaciones
          @if($__unread > 0)
            <span class="badge-unread">{{ $__unread > 99 ? '99+' : $__unread }}</span>
          @endif
        </h5>
        <form method="POST" action="{{ url('/paciente/notificaciones/leertodas') }}" class="ml-auto">
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
              <div class="text-muted small">{{ $n->mensaje ?? '' }}</div>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-muted small">{{ optional($n->fecha)->format('d/m/Y H:i') }}</span>
                @unless($n->leida)
                  <form method="POST" action="{{ url('/paciente/notificaciones/'.$n->idNotificacion.'/leer') }}">
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
