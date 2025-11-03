{{-- resources/views/paciente/bottom-navbar.blade.php --}}
@php
  use App\Models\Notificacion;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Str;

  $__uid    = Auth::id();
  $__unread = Notificacion::where('fkUsuario', $__uid)->where('leida', 0)->count();
  $__items  = Notificacion::where('fkUsuario', $__uid)->orderBy('fecha','desc')->limit(10)->get();

  // Activo por ruta (para resaltar el ítem actual)
  $__isNoti     = request()->is('paciente/notificaciones*');
  $__isHome     = request()->routeIs('paciente.dashboard') || request()->is('paciente') || request()->is('paciente/');
  $__isRecetas  = request()->routeIs('paciente.recetas.*');
  $__isActs     = request()->is('paciente/actividades*') || request()->routeIs('paciente.actividades_terap.*');
@endphp

<style>
  /* ===== Bottom navbar fijo ===== */
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
  .patient-bottom-navbar .pbn-link.active{ color:#1d4ed8; } /* azul activo */
  .patient-bottom-navbar .pbn-badge{
    position:absolute; top:-4px; right:-2px; min-width:18px; height:18px; padding:0 5px;
    background:#ef4444; color:#fff; border-radius:999px; font-size:.72rem; font-weight:700;
    display: {{ $__unread > 0 ? 'flex' : 'none' }}; align-items:center; justify-content:center;
  }
  .pbn-spacer{ height: calc(62px + env(safe-area-inset-bottom)); }

  /* ===== Modal “bottom sheet” azul (sin depender de Bootstrap CSS) ===== */
  .modal{ position:fixed; top:0; left:0; width:100%; height:100%; z-index:1050; display:none; }
  .modal.show{ display:block; }
  .modal-dialog{
    position:absolute; left:0; right:0; bottom:14px; margin:0 auto; pointer-events:none;
    max-width:480px; width: calc(100% - 24px);
  }
  .modal-dialog.modal-sm{ max-width:480px; }
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
  .modal-body{ padding:0; }
  .modal-dialog-scrollable .modal-body{ max-height:42vh; overflow-y:auto; }
  .modal-backdrop{ position:fixed; inset:0; z-index:1040; background:#0f172a; opacity:.42; }

  .btn{ display:inline-block; font-weight:600; padding:.28rem .65rem; border:1px solid #cbd5e1;
        border-radius:.5rem; background:#fff; color:#374151; font-size:.85rem; }
  .btn-sm{ padding:.22rem .55rem; font-size:.82rem; }
  .btn-outline-secondary{ border-color:#cbd5e1; color:#334155; }
  .btn-outline-secondary:hover{ background:#f3f4f6; }
  .btn-outline-primary{ border-color:#3b82f6; color:#1d4ed8; }
  .btn-outline-primary:hover{ background:#eff6ff; }

  .list-group{ list-style:none; margin:0; padding:0; }
  .list-group-item{ padding:.75rem .95rem; border-bottom:1px solid #eff2f8; background:#ffffff; }
  .list-group-item:last-child{ border-bottom:0; }
  .font-weight-bold{ font-weight:800; color:#0f172a; }
  .text-muted{ color:#6b7280; }
  .small{ font-size:.86rem; }
  .list-group-item:hover{ background:#f8fbff; }
  .badge-unread{
    display:inline-block; margin-left:.5rem; font-size:.68rem; font-weight:800;
    color:#1d4ed8; background:#e8f0ff; padding:.12rem .4rem; border-radius:999px;
  }

  .modal.fade .modal-dialog{ transform: translateY(12px); transition: transform .18s ease-out; }
  .modal.show .modal-dialog{ transform: translateY(0); }
</style>

<div class="patient-bottom-navbar" role="navigation" aria-label="Barra inferior de paciente">
  <div class="pbn-wrap">

    {{-- Notificaciones (modal) --}}
    <a href="#" class="pbn-link {{ $__isNoti ? 'active' : '' }}"
       data-toggle="modal" data-target="#pbnNotiModal" aria-label="Notificaciones">
      <i class="fas fa-bell fa-lg" aria-hidden="true"></i>
      <span>Notificaciones</span>
      <span class="pbn-badge" id="pbn-noti-badge">{{ $__unread > 99 ? '99+' : $__unread }}</span>
    </a>

    {{-- Inicio --}}
    <a href="{{ route('paciente.dashboard') }}" class="pbn-link {{ $__isHome ? 'active' : '' }}" aria-label="Inicio">
      <i class="fas fa-home fa-lg" aria-hidden="true"></i>
      <span>Inicio</span>
    </a>

    {{-- Mis Recetas --}}
    <a href="{{ route('paciente.recetas.index') }}" class="pbn-link {{ $__isRecetas ? 'active' : '' }}" aria-label="Mis Recetas">
      <i class="fas fa-file-medical fa-lg" aria-hidden="true"></i>
      <span>Mis Recetas</span>
    </a>

    {{-- Actividades / Actividades terapéuticas --}}
    <a href="{{ Route::has('paciente.actividades_terap.index') ? route('paciente.actividades_terap.index') : route('paciente.actividades.index') }}"
       class="pbn-link {{ $__isActs ? 'active' : '' }}"
       aria-label="Actividades">
      <i class="fas fa-clipboard-list fa-lg" aria-hidden="true"></i>
      <span>Actividades</span>
    </a>

  </div>
</div>

<div class="pbn-spacer" aria-hidden="true"></div>

{{-- Modal (SSR) --}}
<div class="modal fade" id="pbnNotiModal" tabindex="-1" role="dialog" aria-labelledby="pbnNotiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">

        <h5 class="modal-title" id="pbnNotiLabel">
          Notificaciones
          @if($__unread > 0)
            <span class="badge-unread">{{ $__unread > 99 ? '99+' : $__unread }}</span>
          @endif
        </h5>

        {{-- Marcar todas (POST clásico) --}}
        <form method="POST" action="{{ url('/paciente/notificaciones/leertodas') }}" class="ml-auto" aria-label="Marcar todas como leídas">
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
                // Detecta "asignación de actividad" por tipo o por texto
                $textoNoti   = Str::lower(trim(($n->titulo ?? '').' '.($n->mensaje ?? '')));
                $esAsignAct  = ($n->tipo ?? null) === 'asignacion_actividad'
                               || Str::contains($textoNoti, ['asignación de actividad', 'actividad asignada', 'nueva actividad']);

                // Ruta destino: prioriza actividades_terap si existe; si no, cae a actividades
                $rutaActiv   = Route::has('paciente.actividades_terap.index')
                                    ? route('paciente.actividades_terap.index')
                                    : (Route::has('paciente.actividades.index') ? route('paciente.actividades.index') : '#');
              @endphp

              <li class="list-group-item {{ $n->leida ? '' : 'font-weight-bold' }}">
                <div class="small">{{ $n->titulo ?? 'Notificación' }}</div>
                <div class="text-muted small">{{ $n->mensaje ?? '' }}</div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-1">
                  <span class="text-muted small">{{ optional($n->fecha)->format('d/m/Y H:i') }}</span>

                  <div class="d-flex gap-2">
                    @if($esAsignAct && $rutaActiv !== '#')
                      <a href="{{ $rutaActiv }}" class="btn btn-sm btn-outline-primary" title="Ver actividades">
                        Ir a actividades
                      </a>
                    @endif

                    @unless($n->leida)
                      <form method="POST" action="{{ url('/paciente/notificaciones/'.$n->idNotificacion.'/leer') }}" aria-label="Marcar notificación como leída">
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
