{{-- resources/views/admin/testimonios/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  body { background: #e9f4ff; }

  .foro-wrapper { display:flex; justify-content:center; padding:3rem 1rem; }
  .foro-container {
    width:100%; max-width:980px; background:#fff; border-radius:18px;
    padding:2rem 2rem 1rem; box-shadow:0 8px 20px rgba(0,0,0,.05);
  }
  .foro-header { font-weight:700; color:#1b3b6f; font-size:1.8rem; margin-bottom:1.5rem; }

  .testimonio { border-top:1px solid #e0e8f5; padding-top:1.3rem; margin-top:1.3rem; }
  .user-row { display:flex; gap:.9rem; }
  .user-avatar { width:45px; height:45px; border-radius:50%; overflow:hidden; background:#cfe2ff; }
  .avatar-img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .user-name { font-weight:600; color:#1b3b6f; text-transform: lowercase; }
  .user-name::first-letter { text-transform: uppercase; }
  .user-date { font-size:.85rem; color:#7b8da6; }
  .contenido { margin-top:.4rem; color:#2b2b2b; line-height:1.55; }

  .respuestas { margin-left:3.2rem; margin-top:.6rem; }
  .respuesta {
    background:#f5f9ff; border-radius:12px; padding:.65rem .9rem; margin-bottom:.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }
  .respuesta .r-name { font-weight:600; color:#1b3b6f; }
  .respuesta .r-body { margin:.2rem 0 0; }

  .meta-row {
    display:flex; align-items:center; gap:.75rem; color:#6f86a6; font-size:.9rem;
    margin-left:3.2rem; margin-top:.6rem;
  }
  .btn-link.foro { padding:0; font-weight:600; text-decoration:none; }
  .btn-link.foro:hover { text-decoration:underline; }

  .d-none { display:none !important; }

  @keyframes bob { 0%{transform:translateY(0)} 50%{transform:translateY(-3px)} 100%{transform:translateY(0)} }
  .user-avatar img { animation:bob 2.2s ease-in-out infinite; }

  .foro-empty { text-align:center; color:#7b8da6; font-style:italic; margin-top:1.5rem; }
</style>

@php
  $imgH = asset('img/USER-HOMBRE.png');
  $imgM = asset('img/USER-MUJER.png');
@endphp

<div class="foro-wrapper">
  <div class="foro-container">
    <h2 class="foro-header">
      Testimonios <small class="text-muted">· Moderación</small>
    </h2>

    @forelse($testimonios as $t)
      @php
        $countRes = $t->respuestas->count();
        $autorUsuario = optional($t->paciente)->usuario;
        $nombrePaciente = $autorUsuario?->nombre
          ? trim(($autorUsuario->nombre ?? '').' '.($autorUsuario->apellido ?? ''))
          : 'Paciente';

        $imgAutor = ($autorUsuario && strtolower($autorUsuario->sexo ?? '') === 'femenino') ? $imgM : $imgH;
      @endphp

      <div class="testimonio">
        <div class="user-row">
          <div class="user-avatar">
            <img src="{{ $imgAutor }}" alt="Avatar" class="avatar-img">
          </div>
          <div>
            <div class="user-name">{{ strtolower($nombrePaciente) }}</div>
            <div class="user-date">
              {{ \Carbon\Carbon::parse($t->fecha)->locale('es')->translatedFormat('d \\de F \\de Y') }}
            </div>
          </div>
        </div>

        <div class="contenido">{!! nl2br(e($t->contenido)) !!}</div>

        {{-- Meta / Acciones (toggle respuestas + eliminar solo admin) --}}
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
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                Eliminar testimonio
              </button>
            </form>
          @endcan
        </div>

        {{-- Respuestas (ocultas por defecto) --}}
        <div id="res-{{ $t->idTestimonio }}" class="respuestas d-none">
          @foreach($t->respuestas as $r)
            @php
              $respUsuario = optional($r->paciente)->usuario;
              $nombreResp  = $respUsuario?->nombre
                ? trim(($respUsuario->nombre ?? '').' '.($respUsuario->apellido ?? ''))
                : 'Paciente';

              $imgResp = ($respUsuario && strtolower($respUsuario->sexo ?? '') === 'femenino') ? $imgM : $imgH;
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
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
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

{{-- Toggle respuestas --}}
<script>
  document.addEventListener('click', function (e) {
    const btnRes = e.target.closest('.js-toggle-respuestas');
    if (btnRes) {
      const id = btnRes.dataset.target;
      const cont = document.getElementById(id);
      if (!cont) return;
      const visible = !cont.classList.contains('d-none');
      cont.classList.toggle('d-none');
      btnRes.textContent = visible ? 'Ver respuestas' : 'Ocultar respuestas';
    }
  });
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      if (!form) return;
      Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
});
</script>
@endpush
