@extends('layouts.app')
@section('title', 'Responder test')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --soft:#b5c8e1; --accent:#90aacc;
    --ink:#1b2a4a; --muted:#6b7280; --stroke:#e7eef7; --card:#fff;
    --g1:#bea4d2; --g2:#b5c8e1;
  }
  body{ background:linear-gradient(180deg,var(--bg),#eef3f9); color:var(--ink); }
  .shell{ max-width:980px; margin:0 auto; padding:16px 12px 24px; }

  /* ===== Hero compacto ===== */
  .hero{
    background:#f7fbff; border:1px solid var(--stroke); border-radius:16px;
    padding:12px 14px; margin-bottom:12px;
    display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
  }
  .test-title{ margin:0; font-weight:900; letter-spacing:.2px; }
  .pill{ background:#eef6ff; border:1px solid var(--stroke); border-radius:999px; padding:.2rem .6rem; font-size:.85rem; }

  /* ===== Wizard ===== */
  .wizard{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 10px 22px rgba(25,55,100,.06); overflow:hidden;
  }
  .wiz-head{
    padding:12px 16px; border-bottom:1px solid var(--stroke); background:#fbfdff;
    display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
  }
  .progress-wrap{ display:flex; align-items:center; gap:.6rem; }
  .progress{ height:10px; background:#e9eef7; border-radius:999px; width:220px; overflow:hidden; }
  .progress>span{ display:block; height:100%; background:linear-gradient(90deg,var(--g1),var(--g2)); width:0%; transition:width .25s ease; }

  /* ===== Tarjeta completa con degradado ===== */
  .q-card{
    min-height: 400px;
    display:none;
    padding: 22px 20px;
    background: linear-gradient(180deg, var(--g1) 0%, var(--g2) 100%);
    color:#0f2442;
  }
  .q-card.active{ display:block; animation:fadeIn .22s ease; }
  @keyframes fadeIn{ from{opacity:0; transform:translateY(6px)} to{opacity:1; transform:none} }

  /* Transiciones entre pasos */
  .slide-in-right{ animation: slideInRight .28s ease both; }
  .slide-out-left{ animation: slideOutLeft .24s ease both; }
  @keyframes slideInRight{ from{opacity:0; transform:translateX(28px)} to{opacity:1; transform:none} }
  @keyframes slideOutLeft{ from{opacity:1; transform:none} to{opacity:0; transform:translateX(-28px)} }

  /* Interior de la tarjeta */
  .q-inner{
    background: rgba(255,255,255,0.78);
    backdrop-filter: blur(3px);
    border:1px solid rgba(255,255,255,0.45);
    border-radius:18px;
    padding:16px 14px;
    box-shadow:0 8px 20px rgba(20,40,70,.08);
  }

  /* Mascota SVG animada */
  .mascot{
    width:110px; height:110px; margin:0 auto 8px; position:relative;
  }
  .blob{ transform-origin:50% 50%; animation: blobPulse 3.5s ease-in-out infinite; }
  @keyframes blobPulse{ 0%,100%{transform:scale(1)} 50%{transform:scale(1.06)} }
  .eyes{ animation: blink 4.2s infinite; transform-origin:center; }
  @keyframes blink{ 0%,92%,100%{transform:scaleY(1)} 96%{transform:scaleY(.2)} }
  .mascot .spark{
    position:absolute; top:-6px; right:-6px; width:16px; height:16px; border-radius:50%;
    background:linear-gradient(90deg,#ffd166,#ffe38a); box-shadow:0 0 10px rgba(255,209,102,.8);
    animation: pop 2.6s ease-in-out infinite;
  }
  @keyframes pop{ 0%,100%{transform:scale(.6); opacity:.8} 50%{transform:scale(1); opacity:1} }

  /* Partículas/emojis de feedback */
  .emoji-float{
    position:absolute; left:50%; top:10%;
    transform:translateX(-50%); font-size:20px; pointer-events:none;
    animation: floatUp .9s ease-out forwards;
  }
  @keyframes floatUp{ from{opacity:0; transform:translate(-50%, 6px)} to{opacity:1; transform:translate(-50%, -20px)} }

  /* Texto y opciones */
  .q-title{
    font-weight:900; font-size:1.1rem;
    text-align:center; margin:0 0 .75rem;
    color:#0f2442;
  }
  .opts{ display:flex; flex-direction:column; gap:8px; }
  .opt{
    background: rgba(255,255,255,0.92);
    border:1px solid rgba(255,255,255,0.6);
    border-radius:12px;
    padding:.55rem .75rem;
    display:flex; gap:.55rem; align-items:center;
    transition: all .15s ease;
  }
  .opt:hover{
    transform:translateY(-1px);
    box-shadow:0 8px 16px rgba(0,0,0,.08);
    border-color:#e0e7f5;
  }

  .textarea{
    width:100%; min-height:110px;
    background:rgba(255,255,255,0.92);
    border:1px solid rgba(255,255,255,0.6);
    border-radius:12px;
    padding:.65rem .8rem;
  }

  .req{ color:#b91c1c; font-size:.85rem; display:none; }

  .wiz-foot{
    display:flex; align-items:center; justify-content:space-between; gap:8px;
    padding:12px 16px; border-top:1px solid var(--stroke); background:#fbfdff;
  }
  .btn{
    border-radius:12px; padding:.6rem 1rem; font-weight:800;
    display:inline-flex; align-items:center; gap:.45rem;
  }
  .btn-ghost{ background:#fff; border:1px solid var(--stroke); color:#273a56; }
  .btn-primary{ background:#2563eb; color:#fff; border:0; box-shadow:0 8px 18px rgba(37,99,235,.22); }
  .btn-primary:disabled{ opacity:.55; box-shadow:none; }

  .badge-type{
    font-size:.8rem; font-weight:700; color:#243a58;
    background:#eef6ff; border:1px solid var(--stroke);
    border-radius:999px; padding:.18rem .55rem;
  }
</style>
@endpush

@section('content')
<div class="shell">
  {{-- Encabezado --}}
  <div class="hero">
    <div>
      <h1 class="test-title h4">{{ $asignacion->nombreTest }}</h1>
      <div class="text-muted small">
        Asignado: {{ \Carbon\Carbon::parse($asignacion->fechaAsignacion)->format('d/m/Y H:i') }}
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="pill">Paciente</span>
      <a class="btn btn-ghost" href="{{ route('paciente.tests.index') }}"><i class="bi bi-arrow-left"></i> Mis tests</a>
    </div>
  </div>

  {{-- Descripción --}}
  @if(!empty($asignacion->descripcionTest))
    <div class="alert alert-info border-0 shadow-sm" style="border-radius:12px">
      <i class="bi bi-info-circle me-1"></i> {{ $asignacion->descripcionTest }}
    </div>
  @endif

  {{-- Formulario + Wizard --}}
  <form id="formTest" method="POST" action="{{ route('paciente.tests.guardar', $asignacion->idAsignacionTest) }}">
    @csrf

    <div class="wizard">
      <div class="wiz-head">
        <div class="fw-bold d-flex align-items-center gap-2 flex-wrap">
          <span>Cuestionario</span>
          <span class="badge-type" id="tipoBadge"></span>
        </div>
        <div class="progress-wrap">
          <small class="text-muted"><span id="doneCount">0</span>/<span id="totalCount">{{ $preguntas->count() }}</span> respondidas</small>
          <div class="progress"><span id="bar"></span></div>
        </div>
      </div>

      {{-- Tarjetas (una por pregunta) --}}
      @foreach($preguntas as $idx => $p)
        @php
          $tipo = $p->tipo; // opcion_unica | opcion_multiple | abierta
          $ops  = $opcionesPorPregunta[$p->idPregunta] ?? [];
          $nameBase = "respuestas[{$p->idPregunta}]";
        @endphp
        <section class="q-card {{ $idx===0 ? 'active':'' }}" data-step="{{ $idx }}" data-qid="{{ $p->idPregunta }}" data-tipo="{{ $tipo }}">
          <div class="q-inner position-relative">
            {{-- Mascota animada --}}
            <div class="mascot" data-face="neutral">
              <div class="spark"></div>
              <svg viewBox="0 0 120 120" width="120" height="120" aria-hidden="true">
                <defs>
                  <linearGradient id="g{{$idx}}" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#bea4d2"/>
                    <stop offset="100%" stop-color="#b5c8e1"/>
                  </linearGradient>
                </defs>
                <g class="blob">
                  <path d="M60 15c16 0 31 8 38 19 7 11 6 24 0 36-6 12-19 23-35 24-16 2-34-5-41-17-7-12-3-28 6-41 9-13 16-21 32-21z" fill="url(#g{{$idx}})"></path>
                </g>
                <g class="eyes">
                  <circle class="eye-left" cx="48" cy="55" r="5" fill="#1f2d3d"/>
                  <circle class="eye-right" cx="72" cy="55" r="5" fill="#1f2d3d"/>
                </g>
                <path class="mouth" d="M46 73c6 6 22 6 28 0" stroke="#1f2d3d" stroke-width="3" stroke-linecap="round" fill="none"/>
              </svg>
            </div>

            <h3 class="q-title">{{ $p->orden }}. {{ $p->texto }} <span class="req">*</span></h3>

            <div class="opts">
              @if($tipo === 'opcion_unica')
                @foreach($ops as $op)
                  <label class="opt">
                    <input type="radio" name="{{ $nameBase }}" value="{{ $op->idOpcion }}"> <span>{{ $op->etiqueta }}</span>
                  </label>
                @endforeach

              @elseif($tipo === 'opcion_multiple')
                @foreach($ops as $op)
                  <label class="opt">
                    <input type="checkbox" name="{{ $nameBase }}[]" value="{{ $op->idOpcion }}"> <span>{{ $op->etiqueta }}</span>
                  </label>
                @endforeach

              @elseif($tipo === 'abierta')
                <textarea class="textarea" name="{{ $nameBase }}" placeholder="Escribe tu respuesta..."></textarea>
              @endif
            </div>
          </div>
        </section>
      @endforeach

      {{-- Navegación --}}
      <div class="wiz-foot">
        <button type="button" class="btn btn-ghost" id="btnPrev" disabled><i class="bi bi-arrow-left"></i> Atrás</button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary" id="btnNext">Siguiente <i class="bi bi-arrow-right"></i></button>
          <button type="submit" class="btn btn-primary d-none" id="btnSubmit"><i class="bi bi-send"></i> Enviar respuestas</button>
        </div>
      </div>
    </div>
  </form>
</div>

@include('paciente.bottom-nabvar')
@endsection

@push('scripts')
<script>
(function(){
  /* ==============================
     Config "emocional"
     ============================== */
  const VALENCE_DICT = {
    positivo: ['bien','feliz','content','tranquil','calm','excelente','muy bien','optimista','satisfecho','seguro','alegr','entusiasm','motivad'],
    negativo: ['mal','triste','ansios','estres','enoja','enojad','frustr','miedo','preocup','cansad','agotad','fatal','terribl'],
    neutro:   ['normal','ok','regular','neutral','indiferente']
  };

  /* ==============================
     Helpers: audio + vibración
     ============================== */
  let audioCtx = null;
  function ping(){
    try{
      if(!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const o = audioCtx.createOscillator();
      const g = audioCtx.createGain();
      o.type='sine'; o.frequency.setValueAtTime(740, audioCtx.currentTime);
      g.gain.setValueAtTime(0.0001, audioCtx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.15, audioCtx.currentTime + 0.02);
      g.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.18);
      o.connect(g); g.connect(audioCtx.destination); o.start(); o.stop(audioCtx.currentTime+0.2);
    }catch(_e){}
  }
  function vibrate(ms=20){ if('vibrate' in navigator) navigator.vibrate(ms); }

  /* ==============================
     Selección de nodos principales
     ============================== */
  const steps = Array.from(document.querySelectorAll('.q-card'));
  const total = steps.length;
  const bar   = document.getElementById('bar');
  const doneEl= document.getElementById('doneCount');
  const totalEl=document.getElementById('totalCount');
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const btnSubmit = document.getElementById('btnSubmit');
  const tipoBadge = document.getElementById('tipoBadge');
  totalEl.textContent = total;

  let curr = 0;                 // índice visible
  let doneCount = 0;            // respondidas

  /* ==============================
     Utilidades
     ============================== */
  function normalize(txt){ return (txt||'').toString().toLowerCase(); }
  function inferValenceFromText(txt){
    const t = normalize(txt);
    if(VALENCE_DICT.positivo.some(w => t.includes(w))) return +1;
    if(VALENCE_DICT.negativo.some(w => t.includes(w))) return -1;
    if(VALENCE_DICT.neutro.some(w => t.includes(w)))   return 0;
    // fallback: si contiene "si" / "no" / escalas
    if(/\b(s[ií]|yes)\b/.test(t)) return +1;
    if(/\b(no)\b/.test(t)) return -1;
    if(/\b(1|2|3|4|5)\b/.test(t)){ const n = Number(t.match(/\b[1-5]\b/)?.[0]||3); return n>=4?+1:(n<=2?-1:0); }
    return 0;
  }

  function tipoNice(t){
    if(t==='opcion_unica') return 'Opción única';
    if(t==='opcion_multiple') return 'Opción múltiple';
    return 'Respuesta abierta';
  }

  function computeDone(){
    let n=0;
    steps.forEach(s=> n += getValidFor(s)?1:0 );
    doneCount = n;
    doneEl.textContent = n;
    const pct = total ? Math.round(n*100/total) : 0;
    bar.style.width = pct+'%';
    // asterisco rojo
    steps.forEach(s=>{
      s.querySelector('.req')?.style.setProperty('display', getValidFor(s)?'none':'inline');
    });
  }

  function getValidFor(stepEl){
    const tipo = stepEl.dataset.tipo;
    if (tipo==='abierta'){
      const t = stepEl.querySelector('textarea');
      return t && t.value.trim()!==''; 
    }else if (tipo==='opcion_unica'){
      return !!stepEl.querySelector('input[type=radio]:checked');
    }else if (tipo==='opcion_multiple'){
      return stepEl.querySelectorAll('input[type=checkbox]:checked').length>0;
    }
    return false;
  }

  /* ==============================
     Mascota: expresiones + color
     ============================== */
  function setMascotExpression(stepEl, val){
    const mascot = stepEl.querySelector('.mascot');
    if(!mascot) return;
    const mouth = mascot.querySelector('.mouth');
    const leftEye = mascot.querySelector('.eye-left');
    const rightEye = mascot.querySelector('.eye-right');

    // expresión
    if(val > 0){
      mascot.dataset.face = 'happy';
      mouth.setAttribute('d','M46 70c6 10 22 10 28 0');     // sonrisa amplia
    }else if(val < 0){
      mascot.dataset.face = 'sad';
      mouth.setAttribute('d','M46 78c6 -6 22 -6 28 0');     // boca triste
    }else{
      mascot.dataset.face = 'neutral';
      mouth.setAttribute('d','M46 73c6 6 22 6 28 0');       // sonrisa leve
    }

    // ojos (ligero ajuste de tamaño)
    const r = (val>0)?5.5 : (val<0?4.2:5);
    leftEye.setAttribute('r', r);
    rightEye.setAttribute('r', r);

    // partículas/emoji
    const em = document.createElement('div');
    em.className = 'emoji-float';
    em.textContent = val>0 ? '💖' : (val<0 ? '💧' : '✨');
    mascot.appendChild(em);
    setTimeout(()=> em.remove(), 900);
  }

  function updateCardGradient(stepEl, val){
    // Cambia sutilmente el degradado de la tarjeta según valencia
    // positivo -> más turquesa; negativo -> más púrpura
    const base1 = getComputedStyle(document.documentElement).getPropertyValue('--g1').trim() || '#bea4d2';
    const base2 = getComputedStyle(document.documentElement).getPropertyValue('--g2').trim() || '#b5c8e1';
    let g1 = base1, g2 = base2;
    if(val>0){ g1 = '#b3d9d2'; g2 = '#b5c8e1'; }
    if(val<0){ g1 = '#b98fd6'; g2 = '#b9c0df'; }
    stepEl.style.background = `linear-gradient(180deg, ${g1} 0%, ${g2} 100%)`;
  }

  /* ==============================
     Mostrar step con transiciones
     ============================== */
  function show(i){
    const current = steps[curr];
    const next = steps[i];

    steps.forEach(s => s.classList.remove('active','slide-in-right','slide-out-left'));
    if(current && current!==next){
      // salida
      current.classList.add('slide-out-left');
    }
    // entrada
    next.classList.add('active','slide-in-right');

    curr = i;

    // estado botones
    btnPrev.disabled = (i===0);
    const isLast = (i===total-1);
    btnNext.classList.toggle('d-none', isLast);
    btnSubmit.classList.toggle('d-none', !isLast);

    // badge tipo
    const tipo = next.dataset.tipo;
    tipoBadge.textContent = '· ' + tipoNice(tipo);

    // radio auto-siguiente
    const autoInputs = next.querySelectorAll('input[type=radio]');
    autoInputs.forEach(r=>{
      r.addEventListener('change', () => {
        // feedback emocional inmediato
        const labelTxt = r.closest('label')?.innerText || '';
        const v = inferValenceFromText(labelTxt);
        setMascotExpression(next, v);
        updateCardGradient(next, v);
        computeDone();
        // sonido/vibración
        ping(); vibrate(15);
        setTimeout(()=> nextStep(), 200);
      }, { once:false });
    });

    // checkboxes / textarea: habilitar next solo si hay algo
    gateNext();
  }

  function gateNext(){
    const valid = getValidFor(steps[curr]);
    btnNext.disabled = !valid && curr<total-1;
  }

  function nextStep(){
    if (curr < total-1){
      if (!getValidFor(steps[curr])) { gateNext(); return; }
      // antes de pasar, si es múltiple/abierta intentar inferir valencia por el contenido
      const s = steps[curr];
      const tipo = s.dataset.tipo;
      let v = 0;
      if(tipo==='opcion_multiple'){
        s.querySelectorAll('input[type=checkbox]:checked').forEach(ch=>{
          const txt = ch.closest('label')?.innerText || '';
          v += inferValenceFromText(txt);
        });
        v = Math.sign(v);
      }else if(tipo==='abierta'){
        const t = s.querySelector('textarea')?.value || '';
        v = inferValenceFromText(t);
      }
      setMascotExpression(s, v);
      updateCardGradient(s, v);
      computeDone();
      ping(); vibrate(15);

      show(curr+1);
    }
  }
  function prevStep(){
    if (curr>0) show(curr-1);
  }

  // Listeners generales
  btnPrev.addEventListener('click', prevStep);
  btnNext.addEventListener('click', nextStep);

  document.addEventListener('input', e=>{
    if (e.target.closest('.q-card')){
      computeDone();
      gateNext();
    }
  });

  // Enviar: validamos todo
  document.getElementById('formTest').addEventListener('submit', function(e){
    computeDone();
    if (doneCount !== total){
      e.preventDefault();
      // llevar al primero incompleto
      const idx = steps.findIndex(s=>!getValidFor(s));
      if (idx>=0) show(idx);
      alert('Por favor, responde todas las preguntas antes de enviar.');
      return;
    }
    // ping final suave
    ping(); vibrate(25);
  });

  // init
  show(0);
  computeDone();
})();
</script>
@endpush
