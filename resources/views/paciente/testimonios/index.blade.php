@extends('layouts.app')

@section('content')
<style>
  /* ===== Fondo general (lila → azul) ===== */
  body {
    background: linear-gradient(180deg, #e8d9f3ff, #d6e1f1ff);
    min-height: 100vh;
  }

  .foro-wrapper { display:flex; justify-content:center; padding:3rem 1rem; }
  .foro-container {
    width:100%; max-width:820px; background:#fff; border-radius:18px;
    padding:1.25rem 1.25rem 1rem; box-shadow:0 10px 24px rgba(0,0,0,.07);
  }

  /* Encabezado + barra superior */
  .foro-topbar{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom: .75rem;
  }
  .foro-header { font-weight:800; color:#1b3b6f; font-size:1.8rem; margin:0; }

  /* Botón suave */
  .btn-soft {
    background:#fff; border:1px solid #ccd6e5; color:#1b2a4a;
    border-radius:999px; padding:.5rem 1.25rem; font-weight:600;
    transition:.2s ease; box-shadow:0 2px 8px rgba(0,0,0,.04);
  }
  .btn-soft:hover { background:#f2f6fc; color:#0f2442; }

  /* Filtro select */
  .filter-wrap{ display:flex; align-items:center; gap:.5rem; }
  .filter-select{
    border:1px solid #e0e8f5; border-radius:12px; padding:.45rem .7rem;
    background:#f8fbff; color:#1b2a4a; font-weight:600;
  }

  /* Composer (nueva publicación) */
  .composer {
    background:#f5f9ff; border-radius:14px; padding:1rem; display:flex;
    align-items:flex-start; gap:1rem; margin-bottom:1rem;
    border:1px solid #e6effc;
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
    border-radius:25px; font-weight:700; transition:.25s; white-space:nowrap;
  }
  .composer button:hover { background:#0f2c56; }

  /* Cards de testimonios */
  .testimonio { border-top:1px solid #e7eef7; padding-top:1.1rem; margin-top:1.1rem; position:relative; }
  .user-row { display:flex; gap:.9rem; align-items:flex-start; }
  .user-name { font-weight:700; color:#1b3b6f; text-transform: lowercase; }
  .user-name::first-letter { text-transform: uppercase; }
  .user-date { font-size:.86rem; color:#7b8da6; }
  .contenido { margin-top:.45rem; color:#2b2b2b; line-height:1.6; }

  /* Micro destello (feedback) */
  .micro-flash{
    position:absolute; right:12px; top:6px;
    background:#eef6ff; border:1px solid #d9e6fb;
    color:#1b2a4a; font-weight:700; font-size:.85rem;
    padding:.25rem .6rem; border-radius:10px;
    box-shadow:0 6px 16px rgba(10,30,60,.08);
    animation: fadeFlash .2s ease;
  }
  @keyframes fadeFlash { from{opacity:0; transform:translateY(-6px)} to{opacity:1; transform:none} }

  /* Respuestas */
  .respuestas { margin-left:3.2rem; margin-top:.6rem; }
  .respuesta {
    background:#f6faff; border-radius:12px; padding:.7rem .9rem; margin-bottom:.55rem;
    border:1px solid #e6effc;
  }
  .respuesta .r-name { font-weight:700; color:#1b3b6f; }
  .respuesta .r-body { margin:.2rem 0 0; }

  .meta-row {
    display:flex; align-items:center; gap:.75rem; color:#6f86a6; font-size:.92rem;
    margin-left:3.2rem; margin-top:.6rem;
  }
  .btn-link.foro { padding:0; font-weight:700; text-decoration:none; color:#1b3b6f; }
  .btn-link.foro:hover { text-decoration:underline; }

  .reply-panel { margin-left:3.2rem; margin-top:.6rem; }
  .reply-card {
    background:#f6faff; border-radius:12px; padding:.75rem .9rem; max-width:640px;
    border:1px solid #e6effc;
  }
  .reply-card textarea { min-height:70px; }
  .d-none { display:none !important; }

  @keyframes bob { 0%{transform:translateY(0)} 50%{transform:translateY(-3px)} 100%{transform:translateY(0)} }
  .icon-user img, .user-avatar img { animation:bob 2.4s ease-in-out infinite; }

  .foro-empty { text-align:center; color:#7b8da6; font-style:italic; margin-top:1.5rem; }

  /* ===== Mascota flotante + celebraciones ===== */
  .mascot-float {
    position: fixed;
    right: 18px; bottom: 18px;
    width: 160px; height: 160px;
    z-index: 1050;
    filter: drop-shadow(0 10px 18px rgba(17,35,61,.16));
    pointer-events: none;
  }
  .mascot-float svg { width: 100%; height: 100%; }
  .mascot-bob { animation:bob 3.2s ease-in-out infinite; }

  /* Corazón y brillitos */
  .celebrations { position:absolute; inset:0; pointer-events:none; }
  .heart, .spark { position:absolute; opacity:0; transform: translate(-50%, -50%) scale(.6); }

  .heart {
    left: 30%; top: 20%;
    width: 28px; height: 28px;
    background: radial-gradient(circle at 30% 30%, #ff7aa7, #ff4f8a);
    clip-path: path("M14 4 C14 -1, 6 -1, 6 4 C6 7, 9 9, 14 13 C19 9, 22 7, 22 4 C22 -1, 14 -1, 14 4 Z");
    filter: drop-shadow(0 4px 8px rgba(255,79,138,.35));
  }
  .spark { width: 10px; height: 10px; border-radius: 50%; background: radial-gradient(#fff, rgba(255,255,255,.2)); }
  .spark.s1 { left: 65%; top: 18%; }
  .spark.s2 { left: 78%; top: 40%; width:12px; height:12px; }
  .spark.s3 { left: 58%; top: 70%; width:8px;  height:8px; }

  .mascot-float.celebrate .heart { animation: popHeart .9s ease forwards; }
  .mascot-float.celebrate .spark.s1 { animation: sparkle 1.2s .05s ease forwards; }
  .mascot-float.celebrate .spark.s2 { animation: sparkle 1.2s .15s ease forwards; }
  .mascot-float.celebrate .spark.s3 { animation: sparkle 1.2s .25s ease forwards; }

  @keyframes popHeart {
    0%   { opacity:0; transform: translate(-50%, -50%) scale(.4); }
    40%  { opacity:1; transform: translate(-50%, -70%) scale(1.15); }
    100% { opacity:0; transform: translate(-50%, -100%) scale(0.9); }
  }
  @keyframes sparkle {
    0%   { opacity:0; transform: translate(-50%, -50%) scale(.3) rotate(0deg); }
    40%  { opacity:1; transform: translate(-30%, -80%) scale(1) rotate(20deg); }
    100% { opacity:0; transform: translate(20%, -110%) scale(.8) rotate(-20deg); }
  }

  /* ===== Globito de diálogo ===== */
  .mascot-toast {
    position: fixed;
    right: 195px; bottom: 55px;
    max-width: 360px;
    background: #ffffffee;
    border: 1px solid #e6effc;
    border-radius: 16px;
    padding: .8rem 1rem;
    color:#1b2a4a;
    box-shadow: 0 10px 24px rgba(15,36,66,.12);
    z-index: 1050;
    display: none;
  }
  .mascot-toast.show { display:block; animation: fadeIn .25s ease; }
  .mascot-toast::after {
    content:"";
    position:absolute;
    right:-10px; bottom:18px;
    width: 0; height: 0;
    border-left: 10px solid #e6effc;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
  }
  .mascot-toast::before {
    content:"";
    position:absolute;
    right:-8px; bottom:19px;
    width: 0; height: 0;
    border-left: 10px solid #ffffffee;
    border-top: 9px solid transparent;
    border-bottom: 9px solid transparent;
    z-index: 1;
  }
  @keyframes fadeIn { from{opacity:0; transform: translateY(6px)} to{opacity:1; transform:none} }
</style>

@php
  $sexoActual = auth()->user()?->sexo;
  $imgActual = ($sexoActual && strtolower($sexoActual) === 'femenino')
      ? asset('img/USER-MUJER.png')
      : asset('img/USER-HOMBRE.png');

  // Mensaje para la mascotita según el contexto de éxito
  $toastMsg = null;
  if(session('success')) {
    $context = session('success_context'); // 'testimonio' | 'respuesta'
    if($context === 'respuesta'){
      $toastMsg = 'Se ha publicado tu respuesta.';
    } elseif($context === 'testimonio') {
      $toastMsg = 'Gracias por compartir tu experiencia.';
    } else {
      $toastMsg = session('success'); // fallback
    }
  }

  $flashId = session('flash_testimonio_id'); // si lo mandas desde el controlador
@endphp

<div class="foro-wrapper">
  <div class="foro-container">

    {{-- Barra superior: Título + Filtro + Volver --}}
    <div class="foro-topbar">
      <h2 class="foro-header">Testimonios</h2>

      <div class="d-flex align-items-center gap-2">
        <div class="filter-wrap">
          <label for="filtroLectura" class="text-muted small fw-bold me-1">Filtrar:</label>
          <select id="filtroLectura" class="filter-select">
            <option value="all">Ver todo</option>
            <option value="mine">Solo mis testimonios</option>
            <option value="withreplies">Con respuestas</option>
            <option value="last7">Últimos 7 días</option>
          </select>
        </div>

        <button type="button" class="btn btn-soft"
                onclick="window.location='{{ route('paciente.dashboard') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver al dashboard
        </button>
      </div>
    </div>

    {{-- Composer sólo para pacientes --}}
    @if(auth()->user()?->tipoUsuario === 'paciente')
      <form action="{{ route('paciente.testimonios.store') }}" method="POST">
        @csrf
        <div class="composer">
          <div class="icon-user">
            <img src="{{ $imgActual }}" alt="Avatar" class="avatar-img">
          </div>
          <textarea name="contenido" rows="2" placeholder="Comparte tu experiencia de cómo te has sentido en las citas, al hacer las actividades, etc..."></textarea>
          <button type="submit">Publicar</button>
        </div>
      </form>
      @error('contenido')
        <div class="text-danger small mb-3">{{ $message }}</div>
      @enderror
      @if(session('success') && !session('success_context'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
      @endif
    @endif

    {{-- Listado de testimonios --}}
    @forelse($testimonios as $t)
      @php
        $countRes = $t->respuestas->count();
        $autorUsuario = optional($t->paciente)->usuario;
        $nombrePaciente = $autorUsuario?->nombre
          ? ($autorUsuario->nombre.' '.$autorUsuario->apellido)
          : 'Paciente';

        $imgAutor = ($autorUsuario && strtolower($autorUsuario->sexo ?? '') === 'femenino')
          ? asset('img/USER-MUJER.png')
          : asset('img/USER-HOMBRE.png');

        // ¿Es mío?
        $isMine = ($autorUsuario?->idUsuario ?? null) === (auth()->user()->idUsuario ?? null);
        // Fecha ISO para filtro last7
        $isoDate = \Carbon\Carbon::parse($t->fecha)->format('YYYY-MM-DD');
      @endphp

      <div id="test-{{ $t->idTestimonio }}"
           class="testimonio"
           data-owner="{{ $isMine ? 'me' : 'other' }}"
           data-has-responses="{{ $countRes > 0 ? 'true' : 'false' }}"
           data-date="{{ \Carbon\Carbon::parse($t->fecha)->toDateString() }}">
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

        {{-- Micro destello en el post recién publicado --}}
        @if(session('success') && ($flashId === $t->idTestimonio || (!$flashId && $loop->first)))
          <div class="micro-flash" data-auto-hide="true">Tu voz importa. Gracias por abrirte.</div>
        @endif

        {{-- Contador + botones --}}
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

        {{-- Respuestas (ocultas por defecto) --}}
        <div id="res-{{ $t->idTestimonio }}" class="respuestas d-none">
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

        {{-- Formulario de respuesta --}}
        @if(auth()->user()?->tipoUsuario === 'paciente' && $countRes < 3)
          <div id="reply-{{ $t->idTestimonio }}" class="reply-panel d-none">
            <form action="{{ route('paciente.testimonios.respuestas.store', $t->idTestimonio) }}" method="POST" class="reply-card">
              @csrf
              <label class="form-label fw-semibold mb-1">Tu respuesta</label>
              <textarea name="contenido" class="form-control mb-2" maxlength="800"
                        placeholder="Respuesta breve y respetuosa (máx. 800 caracteres)"></textarea>
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

{{-- ===== Mascota flotante (happy) + celebraciones ===== --}}
<div id="mwMascotBox" class="mascot-float" aria-hidden="true">
  <svg id="mwMascotFloat" viewBox="0 0 120 120" class="mascot-bob">
    <defs>
      <linearGradient id="mwMascotFloatG" x1="0" y1="0" x2="1" y2="1">
        <stop class="mw-gstop-1" offset="0%" stop-color="#bea4d2"/>
        <stop class="mw-gstop-2" offset="100%" stop-color="#b5c8e1"/>
      </linearGradient>
    </defs>
    <g class="blob">
      <path d="M60 15c16 0 31 8 38 19 7 11 6 24 0 36-6 12-19 23-35 24-16 2-34-5-41-17-7-12-3-28 6-41 9-13 16-21 32-21z" fill="url(#mwMascotFloatG)"></path>
    </g>
    <g class="eyes">
      <circle class="mw-eye-left"  cx="48" cy="55" r="5" fill="#1f2d3d"/>
      <circle class="mw-eye-right" cx="72" cy="55" r="5" fill="#1f2d3d"/>
    </g>
    <path class="mw-mouth" d="M46 70c6 10 22 10 28 0" stroke="#1f2d3d" stroke-width="3" stroke-linecap="round" fill="none"/>
  </svg>

  <div class="celebrations">
    <div class="heart"></div>
    <div class="spark s1"></div>
    <div class="spark s2"></div>
    <div class="spark s3"></div>
  </div>
</div>

{{-- Globito de diálogo (toast) --}}
<div id="mascotToast" class="mascot-toast">
  <strong id="mascotToastText">{{ $toastMsg ?? '' }}</strong>
</div>

<script>
  document.addEventListener('click', function (e) {
    // Toggle formulario de respuesta
    const btnReply = e.target.closest('.js-toggle-reply');
    if (btnReply) {
      const id = btnReply.dataset.target;
      const panel = document.getElementById(id);
      if (!panel) return;
      document.querySelectorAll('.reply-panel').forEach(p => {
        if (p.id !== id) p.classList.add('d-none');
      });
      panel.classList.toggle('d-none');
      if (!panel.classList.contains('d-none')) {
        const ta = panel.querySelector('textarea');
        if (ta) setTimeout(() => ta.focus(), 50);
      }
    }

    // Toggle de respuestas (mostrar/ocultar)
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

  // Globito + celebración 7s si hay mensaje
  (function(){
    const toast = document.getElementById('mascotToast');
    const text  = document.getElementById('mascotToastText');
    const mascotBox = document.getElementById('mwMascotBox');
    const hasMsg = text && text.textContent.trim().length > 0;

    if(hasMsg){
      toast.classList.add('show');
      mascotBox.classList.add('celebrate');
      setTimeout(()=>{
        toast.classList.remove('show');
        mascotBox.classList.remove('celebrate');
      }, 7000);
    }
  })();

  // Auto-ocultar micro destello (2s)
  (function(){
    document.querySelectorAll('.micro-flash[data-auto-hide="true"]').forEach(el=>{
      setTimeout(()=> el.remove(), 2000);
    });
  })();

  // ===== Filtro de lectura consciente (cliente) =====
  (function(){
    const select = document.getElementById('filtroLectura');
    if(!select) return;
    const items = Array.from(document.querySelectorAll('.testimonio'));
    function apply(){
      const val = select.value;
      const now = new Date();
      items.forEach(it=>{
        const owner = it.dataset.owner;                // 'me' | 'other'
        const has   = it.dataset.hasResponses === 'true';
        const dStr  = it.dataset.date;                 // 'YYYY-MM-DD'
        let show = true;
        if(val === 'mine') show = owner === 'me';
        else if(val === 'withreplies') show = has;     // (respuestas de pacientes)
        else if(val === 'last7'){
          const d = new Date(dStr+'T00:00:00');
          const diff = (now - d)/(1000*60*60*24);
          show = diff <= 7;
        } // else 'all'
        it.style.display = show ? '' : 'none';
      });
    }
    select.addEventListener('change', apply);
    apply(); // inicial
  })();
</script>

@include('paciente.bottom-nabvar')
@endsection

@push('styles')
<style>
  /* Si reutilizas btn-soft en otros lados */
  .btn-soft {
    background: #fff;
    border: 1px solid #ccc;
    color: #333;
    border-radius: 50px;
    padding: .5rem 1.25rem;
    transition: .2s ease;
  }
  .btn-soft:hover {
    background: #f2f2f2;
    color: #000;
  }
</style>
@endpush
