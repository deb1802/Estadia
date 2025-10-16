@extends('layouts.app')

@section('content')
<style>
  body { background: #e9f4ff; }

  .foro-wrapper { display:flex; justify-content:center; padding:3rem 1rem; }
  .foro-container {
    width:100%; max-width:800px; background:#fff; border-radius:18px;
    padding:2rem 2rem 1rem; box-shadow:0 8px 20px rgba(0,0,0,.05);
  }
  .foro-header { font-weight:700; color:#1b3b6f; font-size:1.8rem; margin-bottom:1.5rem; }

  /* Composer (publicar) */
  .composer {
    background:#f0f7ff; border-radius:14px; padding:1rem; display:flex;
    align-items:flex-start; gap:1rem; margin-bottom:2rem;
    box-shadow: inset 0 0 4px rgba(0,0,0,.05);
  }
  .icon-user, .user-avatar {
    width:45px; height:45px; border-radius:50%; background:#cfe2ff;
    display:flex; justify-content:center; align-items:center; overflow:hidden;
  }
  .avatar-img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

  .composer textarea {
    flex:1; resize:none; border:none; background:transparent; outline:none; font-size:1rem;
  }
  .composer button {
    border:none; background:#1b3b6f; color:#fff; padding:.55rem 1.2rem;
    border-radius:25px; font-weight:600; transition:.3s;
  }
  .composer button:hover { background:#0043a7; }

  /* Listado */
  .testimonio { border-top:1px solid #e0e8f5; padding-top:1.3rem; margin-top:1.3rem; }
  .user-row { display:flex; gap:.9rem; }
  .user-name { font-weight:600; color:#1b3b6f; text-transform: lowercase; }
  .user-name::first-letter { text-transform: uppercase; }
  .user-date { font-size:.85rem; color:#7b8da6; }
  .contenido { margin-top:.4rem; color:#2b2b2b; line-height:1.55; }

  /* Respuestas */
  .respuestas { margin-left:3.2rem; margin-top:.6rem; }
  .respuesta {
    background:#f5f9ff; border-radius:12px; padding:.65rem .9rem; margin-bottom:.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }
  .respuesta .r-name { font-weight:600; color:#1b3b6f; }
  .respuesta .r-body { margin:.2rem 0 0; }

  /* Meta / responder */
  .meta-row {
    display:flex; align-items:center; gap:.75rem; color:#6f86a6; font-size:.9rem;
    margin-left:3.2rem; margin-top:.6rem;
  }
  .btn-link.foro { padding:0; font-weight:600; text-decoration:none; }
  .btn-link.foro:hover { text-decoration:underline; }

  /* Form de respuesta compacto */
  .reply-panel { margin-left:3.2rem; margin-top:.6rem; }
  .reply-card {
    background:#f6faff; border-radius:12px; padding:.75rem .9rem; max-width:640px;
    border:1px solid #e6effc;
  }
  .reply-card textarea { min-height:70px; }

  /* Utilidades */
  .d-none { display:none !important; }

  /* Animación leve del avatar */
  @keyframes bob { 0%{transform:translateY(0)} 50%{transform:translateY(-3px)} 100%{transform:translateY(0)} }
  .icon-user img, .user-avatar img { animation:bob 2.2s ease-in-out infinite; }

  .foro-empty { text-align:center; color:#7b8da6; font-style:italic; margin-top:1.5rem; }
</style>

@php
  // Avatar del usuario autenticado (composer)
  $sexoActual = auth()->user()?->sexo;
  $imgActual = ($sexoActual && strtolower($sexoActual) === 'femenino')
      ? asset('img/USER-MUJER.png')
      : asset('img/USER-HOMBRE.png');
@endphp

<div class="foro-wrapper">
  <div class="foro-container">
    <h2 class="foro-header">Testimonios</h2>

    {{-- Publicar (misma vista) --}}
    @if(auth()->user()?->tipoUsuario === 'paciente')
      <form action="{{ route('paciente.testimonios.store') }}" method="POST">
        @csrf
        <div class="composer">
          <div class="icon-user">
            <img src="{{ $imgActual }}" alt="Avatar" class="avatar-img">
          </div>
          <textarea name="contenido" rows="2" placeholder="Comparte tu experiencia..."></textarea>
          <button type="submit">Publicar</button>
        </div>
      </form>
      @error('contenido')
        <div class="text-danger small mb-3">{{ $message }}</div>
      @enderror
      @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
      @endif
    @endif

    {{-- Listado --}}
    @forelse($testimonios as $t)
      @php
        $countRes = $t->respuestas->count();

        // Autor (Paciente -> Usuario)
        $autorUsuario = optional($t->paciente)->usuario;
        $nombrePaciente = $autorUsuario?->nombre
          ? ($autorUsuario->nombre.' '.$autorUsuario->apellido)
          : 'Paciente';

        $imgAutor = ($autorUsuario && strtolower($autorUsuario->sexo ?? '') === 'femenino')
          ? asset('img/USER-MUJER.png')
          : asset('img/USER-HOMBRE.png');
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

        {{-- Contador + botón responder --}}
        <div class="meta-row">
          <span>{{ $countRes }} / 3 respuestas</span>
          @if(auth()->user()?->tipoUsuario === 'paciente' && $countRes < 3)
            <span>•</span>
            <button
              type="button"
              class="btn btn-link foro js-toggle-reply"
              data-target="reply-{{ $t->idTestimonio }}"
            >
              Responder
            </button>
          @endif
        </div>

        {{-- Respuestas existentes (visibles) --}}
        <div class="respuestas">
          @foreach($t->respuestas as $r)
            @php
              $respUsuario = optional($r->paciente)->usuario;
              $nombreResp  = $respUsuario?->nombre
                ? ($respUsuario->nombre.' '.$respUsuario->apellido)
                : 'Paciente';

              $imgResp = ($respUsuario && strtolower($respUsuario->sexo ?? '') === 'femenino')
                ? asset('img/USER-MUJER.png')
                : asset('img/USER-HOMBRE.png');
            @endphp

            <div class="respuesta">
              <div class="d-flex gap-2">
                <div class="user-avatar" style="width:36px;height:36px;">
                  <img src="{{ $imgResp }}" alt="Avatar respuesta" class="avatar-img">
                </div>
                <div class="w-100">
                  <div class="d-flex justify-content-between">
                    <span class="r-name">{{ $nombreResp }}</span>
                    <small class="text-secondary">
                      {{ \Carbon\Carbon::parse($r->fecha)->locale('es')->translatedFormat('d/M/Y H:i') }}
                    </small>
                  </div>
                  <div class="r-body">{!! nl2br(e($r->contenido)) !!}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        {{-- Formulario de respuesta (toggle propio, compacto) --}}
        @if(auth()->user()?->tipoUsuario === 'paciente' && $countRes < 3)
          <div id="reply-{{ $t->idTestimonio }}" class="reply-panel d-none">
            <form action="{{ route('paciente.testimonios.respuestas.store', $t->idTestimonio) }}" method="POST" class="reply-card">
              @csrf
              <label class="form-label fw-semibold mb-1">Tu respuesta</label>
              <textarea name="contenido" class="form-control mb-2" maxlength="800"
                        placeholder="Respuesta breve y respetuosa (máx. 800 caracteres)"></textarea>
              @error('contenido')
                <div class="text-danger small mb-2">{{ $message }}</div>
              @enderror
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-primary">Enviar</button>
              </div>
            </form>
          </div>
        @endif
      </div>
    @empty
      <div class="foro-empty">Aún no hay testimonios. ¡Sé la primera persona en compartir!</div>
    @endforelse

    @if(method_exists($testimonios, 'links'))
      <div class="mt-3">{{ $testimonios->links() }}</div>
    @endif
  </div>
</div>

<script>
  // Toggle simple: abre/cierra el panel del testimonio clicado
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-toggle-reply');
    if (!btn) return;
    const id = btn.getAttribute('data-target');
    const panel = document.getElementById(id);
    if (!panel) return;

    // Cerrar otros paneles abiertos
    document.querySelectorAll('.reply-panel').forEach(p => {
      if (p.id !== id) p.classList.add('d-none');
    });

    panel.classList.toggle('d-none');
    if (!panel.classList.contains('d-none')) {
      // Llevar foco al textarea
      const ta = panel.querySelector('textarea');
      if (ta) setTimeout(() => ta.focus(), 50);
    }
  });
</script>
@endsection
