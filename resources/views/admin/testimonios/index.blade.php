{{-- resources/views/admin/testimonios/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Fondo lila → azul */
  body{
    background: linear-gradient(180deg, #e8d9f3ff, #d6e1f1ff);
    min-height: 100vh;
  }

  .foro-wrapper { display:flex; justify-content:center; padding:3rem 1rem; }
  .foro-container {
    width:100%; max-width:980px; background:#fff; border-radius:18px;
    padding:1.25rem 1.25rem 1rem; box-shadow:0 10px 24px rgba(0,0,0,.07);
  }

  .foro-topbar{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:.75rem;
  }

  .left-head{
    display:flex; align-items:center; gap:14px; flex-wrap:wrap;
  }

  .foro-header{ font-weight:800; color:#1b3b6f; font-size:1.8rem; margin:0; }

  /* Botón suave volver */
  .btn-soft{
    background:#fff; border:1px solid #ccd6e5; color:#1b2a4a;
    border-radius:999px; padding:.5rem 1.25rem; font-weight:600;
    transition:.2s ease; box-shadow:0 2px 8px rgba(0,0,0,.04);
  }
  .btn-soft:hover{ background:#f2f6fc; color:#0f2442; }

  /* Select de filtros */
  .filter-select{
    border:1px solid #d7e1ed; border-radius:999px; padding:.45rem .9rem;
    color:#1b2a4a; background:#fff; font-weight:600;
  }

  .testimonio{ border-top:1px solid #e0e8f5; padding-top:1.1rem; margin-top:1.1rem; }
  .user-row{ display:flex; gap:.9rem; }
  .user-avatar{ width:45px; height:45px; border-radius:50%; overflow:hidden; background:#cfe2ff; }
  .avatar-img{ width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .user-name{ font-weight:700; color:#1b3b6f; text-transform: lowercase; }
  .user-name::first-letter{ text-transform: uppercase; }
  .user-date{ font-size:.86rem; color:#7b8da6; }
  .contenido{ margin-top:.45rem; color:#2b2b2b; line-height:1.55; }

  .respuestas{ margin-left:3.2rem; margin-top:.6rem; }
  .respuesta{
    background:#f6faff; border-radius:12px; padding:.65rem .9rem; margin-bottom:.5rem;
    border:1px solid #e6effc;
  }
  .respuesta .r-name{ font-weight:700; color:#1b3b6f; }
  .respuesta .r-body{ margin:.2rem 0 0; }

  .meta-row{
    display:flex; align-items:center; gap:.75rem; color:#6f86a6; font-size:.9rem;
    margin-left:3.2rem; margin-top:.6rem;
  }
  .btn-link.foro{ padding:0; font-weight:700; text-decoration:none; color:#1b3b6f; }
  .btn-link.foro:hover{ text-decoration:underline; }

  .d-none{ display:none !important; }

  @keyframes bob { 0%{transform:translateY(0)} 50%{transform:translateY(-3px)} 100%{transform:translateY(0)} }
  .user-avatar img{ animation:bob 2.2s ease-in-out infinite; }

  .foro-empty{ text-align:center; color:#7b8da6; font-style:italic; margin-top:1.5rem; }
</style>

@php
  $imgH = asset('img/USER-HOMBRE.png');
  $imgM = asset('img/USER-MUJER.png');
@endphp

<div class="foro-wrapper">
  <div class="foro-container">

    {{-- Topbar: título + filtros + volver --}}
    <div class="foro-topbar">
      <div class="left-head">
        <h2 class="foro-header">Testimonios <small class="text-muted">· Moderación</small></h2>

        {{-- Filtros (client-side) --}}
        <div class="d-flex align-items-center gap-2">
          <label for="filterSelect" class="text-secondary small fw-semibold">Filtrar:</label>
          <select id="filterSelect" class="filter-select">
            <option value="all">Ver todo</option>
            <option value="withResponses">Con respuestas de pacientes</option>
            <option value="last7">Últimos 7 días</option>
          </select>
        </div>
      </div>

      <button type="button" class="btn btn-soft"
              onclick="window.location='{{ route('admin.dashboard') }}'">
        <i class="bi bi-arrow-90deg-left me-1"></i> Volver al dashboard
      </button>
    </div>

    {{-- Alert de eliminación (flash) --}}
    @if(session('deleted') || request()->boolean('deleted'))
      <div id="alertDeleted" class="alert alert-success py-2 mb-3" role="alert">
        {{ session('deleted') ?? 'Eliminado correctamente' }}
      </div>
    @endif

    @forelse($testimonios as $t)
      @php
        $countRes    = $t->respuestas->count();
        $autorUsuario= optional($t->paciente)->usuario;
        $nombrePac   = $autorUsuario?->nombre
                        ? trim(($autorUsuario->nombre ?? '').' '.($autorUsuario->apellido ?? ''))
                        : 'Paciente';
        $imgAutor    = ($autorUsuario && strtolower($autorUsuario->sexo ?? '') === 'femenino') ? $imgM : $imgH;
        $fechaISO    = \Carbon\Carbon::parse($t->fecha)->format('YYYY-MM-DD'); // data attr seguro
      @endphp

      <div class="testimonio"
           data-respuestas="{{ $countRes }}"
           data-date="{{ \Carbon\Carbon::parse($t->fecha)->format('Y-m-d') }}">
        <div class="user-row">
          <div class="user-avatar">
            <img src="{{ $imgAutor }}" alt="Avatar" class="avatar-img">
          </div>
          <div>
            <div class="user-name">{{ strtolower($nombrePac) }}</div>
            <div class="user-date">
              {{ \Carbon\Carbon::parse($t->fecha)->locale('es')->translatedFormat('d \\de F \\de Y') }}
            </div>
          </div>
        </div>

        <div class="contenido">{!! nl2br(e($t->contenido)) !!}</div>

        {{-- Meta / Acciones --}}
        <div class="meta-row">
          <span>{{ $countRes }} / 3 respuestas</span>

          @if($countRes > 0)
            <span>•</span>
            <button
              type="button"
              class="btn btn-link foro js-toggle-respuestas"
              data-target="res-{{ $t->idTestimonio }}"
            >
              Ver respuestas
            </button>
          @endif

          {{-- Eliminar testimonio: SOLO admin (policy) --}}
          @can('delete', $t)
            <form action="{{ route('admin.testimonios.destroy', $t->idTestimonio) }}"
                  method="POST" class="ms-auto form-delete d-inline">
              @csrf @method('DELETE')
              <button type="button" class="btn btn-sm btn-danger btn-delete">
                Eliminar testimonio
              </button>
            </form>
          @endcan
        </div>

        {{-- Respuestas --}}
        <div id="res-{{ $t->idTestimonio }}" class="respuestas d-none">
          @foreach($t->respuestas as $r)
            @php
              $respUsuario = optional($r->paciente)->usuario;
              $nombreResp  = $respUsuario?->nombre
                            ? trim(($respUsuario->nombre ?? '').' '.($respUsuario->apellido ?? ''))
                            : 'Paciente';
              $imgResp     = ($respUsuario && strtolower($respUsuario->sexo ?? '') === 'femenino') ? $imgM : $imgH;
            @endphp
            <div class="respuesta">
              <div class="d-flex gap-2">
                <div class="user-avatar" style="width:36px;height:36px;">
                  <img src="{{ $imgResp }}" alt="Avatar respuesta" class="avatar-img">
                </div>

                <div class="w-100">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <span class="r-name">{{ $nombreResp }}</span>
                      <small class="text-secondary ms-2">
                        {{ \Carbon\Carbon::parse($r->fecha)->locale('es')->translatedFormat('d/M/Y H:i') }}
                      </small>
                    </div>

                    {{-- Eliminar respuesta: SOLO admin (policy) --}}
                    @can('delete', $r)
                      <form action="{{ route('admin.testimonios.respuestas.destroy', [$t->idTestimonio, $r->idRespuesta]) }}"
                            method="POST" class="ms-2 form-delete d-inline">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete">
                          Eliminar
                        </button>
                      </form>
                    @endcan
                  </div>

                  <div class="r-body">{!! nl2br(e($r->contenido)) !!}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="foro-empty">No hay testimonios por ahora.</div>
    @endforelse

    {{-- Paginación --}}
    @if(method_exists($testimonios, 'links'))
      <div class="mt-3">{{ $testimonios->links() }}</div>
    @endif
  </div>
</div>

{{-- JS: toggle respuestas + filtros + alert --}}
<script>
  // Toggle respuestas
  document.addEventListener('click', function (e) {
    const btnRes = e.target.closest('.js-toggle-respuestas');
    if (!btnRes) return;
    const id = btnRes.dataset.target;
    const cont = document.getElementById(id);
    if (!cont) return;
    const visible = !cont.classList.contains('d-none');
    cont.classList.toggle('d-none');
    btnRes.textContent = visible ? 'Ver respuestas' : 'Ocultar respuestas';
  });

  // Filtros client-side
  (function(){
    const select = document.getElementById('filterSelect');
    const cards  = Array.from(document.querySelectorAll('.testimonio'));

    function applyFilter() {
      const val = select.value;
      const now = new Date();

      cards.forEach(card => {
        const respuestas = parseInt(card.dataset.respuestas || '0', 10);
        const dateStr    = card.dataset.date; // YYYY-MM-DD
        const d          = dateStr ? new Date(dateStr + 'T00:00:00') : null;

        let show = true;
        if (val === 'withResponses') {
          show = respuestas > 0;
        } else if (val === 'last7') {
          if (!d) { show = false; }
          else {
            const diff = (now - d) / (1000*60*60*24); // días
            show = diff <= 7 && diff >= 0;
          }
        }
        card.style.display = show ? '' : 'none';
      });
    }

    if (select) {
      select.addEventListener('change', applyFilter);
    }
  })();

  // Alert de eliminado: desaparecer a los 6s, con fade
  (function(){
    const alertDeleted = document.getElementById('alertDeleted');
    if (!alertDeleted) return;
    setTimeout(() => {
      alertDeleted.style.transition = 'opacity .5s ease';
      alertDeleted.style.opacity = '0';
      setTimeout(() => alertDeleted.remove(), 600);
    }, 6000);
  })();
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Confirmación de borrado con SweetAlert (botones rojos)
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      if (!form) return;
      Swal.fire({
        title: '¿Eliminar este contenido?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
});
</script>
@endpush
