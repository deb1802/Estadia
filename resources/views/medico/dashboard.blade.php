@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crud-style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>

<style>
  :root{
    --violet:#8b80f9; --violet-2:#a29bfe; --mint:#7be3d5; --ink:#1b3b6f;
    --glass-bg: rgba(255,255,255,0.22);
    --glass-brd: rgba(255,255,255,0.35);
    --glass-shadow: 0 22px 70px rgba(31,38,135,0.22);
    --home-bg:#dce6f6;  /* portada bienestar médico */
    --dash-bg:#ffffff;  /* dashboard tarjetas */
  }

  .page-wrap{ position:relative; min-height:calc(100vh - var(--navbar-h,56px)); overflow:hidden; }
  .mw-hidden{ display:none !important; }

  /* ======= PORTADA BIENESTAR (médico) ======= */
  #wellnessDoctor{
    background: var(--home-bg) !important;
    padding: 40px 18px 28px;
  }
  .hero-wrap{ text-align:center; max-width:1100px; margin:0 auto 16px; }
  .hero-title{ font-weight:950; color:#2c3f73; font-size:clamp(30px,4vw,44px); }
  .subline{ color:#22324e; font-size:1.05rem; margin-top:4px; }
  .chip{
    display:inline-flex; align-items:center; gap:10px; margin-top:12px;
    padding:10px 16px; border-radius:999px; background:rgba(255,255,255,.38);
    border:1px solid rgba(255,255,255,.6); font-weight:800; color:#17324e;
  }

  .widgets{ max-width:1100px; margin:22px auto; display:grid; gap:18px; grid-template-columns:repeat(12,1fr); }
  .w-quick{ grid-column:span 12; }
  .w-vision{ grid-column:span 12; }
  .w-stretch{ grid-column:span 12; }
  .w-quote{ grid-column:span 12; }
  @media(min-width:992px){
    .w-quick{ grid-column:span 7; }
    .w-vision{ grid-column:span 5; }
  }

  .glass{
    position:relative; border-radius:26px; overflow:hidden;
    background:var(--glass-bg); border:1px solid var(--glass-brd);
    backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px);
    box-shadow:var(--glass-shadow);
  }
  .glass::before{ content:""; position:absolute; inset:0 0 auto 0; height:55%;
    background:linear-gradient(180deg,rgba(255,255,255,.34),rgba(255,255,255,0)); }
  .inside{ padding:24px; }
  .block-title{ font-weight:900; color:#13233d; font-size:1.25rem; margin-bottom:12px; }

  /* Atajos rápidos */
  .quick-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
  @media(min-width:768px){ .quick-grid{ grid-template-columns:repeat(3,1fr);} }
  .quick{
    display:flex; align-items:center; gap:10px; padding:14px; border-radius:16px;
    background:rgba(255,255,255,.55); border:1px solid rgba(0,0,0,.06);
    font-weight:800; color:#163055; text-decoration:none;
  }
  .quick i{ font-size:1.1rem; }

  /* Pausa ocular 20-20-20 */
  .vision-wrap{ display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
  .eye{
    width:120px; height:120px; border-radius:50%;
    background:radial-gradient(circle, var(--violet-2) 0%, var(--violet) 60%, var(--mint) 100%);
    display:flex; align-items:center; justify-content:center;
    box-shadow:0 24px 48px rgba(139,128,249,.36);
    transition: transform .4s ease;
  }
  .eye-emoji{ font-size:3rem; }
  .vision-legend{ font-weight:800; color:#0f2440; }
  .vision-note{ font-size:.92rem; color:#28415f; opacity:.9; }
  .vision-controls{ display:flex; gap:8px; margin-top:8px; flex-wrap:wrap; }
  .btn-soft{
    display:inline-flex; align-items:center; gap:8px;
    border:1px solid rgba(47,109,224,.25); background:rgba(47,109,224,.08);
    color:#1b2f53; font-weight:800; border-radius:12px; padding:10px 14px; text-decoration:none;
  }

  /* Rutina express de estiramiento (1 min) */
  .stretch-wrap{ display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
  .stretch-badge{
    width:120px; height:120px; border-radius:26px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.55); border:1px solid rgba(0,0,0,.06);
    font-size:3rem; box-shadow:0 20px 40px rgba(0,0,0,.08);
  }
  .stretch-title{ font-weight:800; color:#0f2440; }
  .stretch-sub{ font-size:.95rem; color:#28415f; opacity:.9; margin-top:4px; }
  .stretch-controls{ display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }

  /* ======= DASHBOARD TARJETAS (estilo original restaurado) ======= */
  #doctorDashboard{ background: var(--dash-bg) !important; padding: 22px 10px 32px; }
  .dashboard-grid{ display:flex; flex-wrap:wrap; justify-content:center; gap:30px 40px; }

  .gestion-card{
    display:block; text-align:center;
    background:#fff;
    border-radius:16px;
    padding:25px 15px;
    width:220px; margin:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    text-decoration:none; color:#333;
    transition: all 0.3s ease; position:relative; overflow:hidden;
    transform-style:preserve-3d; perspective:800px;
  }
  .gestion-card:hover{ transform: translateY(-8px) rotateX(4deg) rotateY(-4deg); box-shadow:0 8px 25px rgba(0,0,0,0.2); }
  .gestion-card::after{
    content:""; position:absolute; top:-75%; left:-75%; width:50%; height:200%;
    background:rgba(255,255,255,0.3); transform:rotate(25deg); transition:0.6s;
  }
  .gestion-card:hover::after{ left:125%; }

  .icon-box{
    width:80px; height:80px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    margin:0 auto 15px; font-size:2.2rem; color:#fff;
    box-shadow:0 0 18px rgba(108,99,255,0.4); animation:pulseGlow 3s ease-in-out infinite;
  }
  @keyframes pulseGlow{
    0%{ box-shadow:0 0 15px rgba(108,99,255,.4), 0 0 25px rgba(0,188,212,.3); transform:scale(1);}
    50%{ box-shadow:0 0 30px rgba(139,128,249,.6), 0 0 45px rgba(0,188,212,.5); transform:scale(1.05);}
    100%{ box-shadow:0 0 15px rgba(108,99,255,.4), 0 0 25px rgba(0,188,212,.3); transform:scale(1);}
  }
  .gestion-card h4{ font-weight:bold; color:#374151; font-size:1.1rem; }
  .gestion-card p{ font-size:.85rem; color:#6b7280; }
</style>

<div class="page-wrap">
  {{-- ====== PORTADA BIENESTAR ====== --}}
  <section id="wellnessDoctor">
    <div class="hero-wrap">
      <h1 class="hero-title">Bienvenido(a), Dr(a). {{ ucfirst(Auth::user()->nombre) }}</h1>
      <div class="subline">
        <span id="todayText">—</span>
        <span class="chip" id="dayChip">
          <i id="dayIcon" class="bi bi-sun"></i>
          <span id="dayLabel">Día luminoso</span>
        </span>
      </div>
    </div>

    <div class="widgets">
      {{-- Atajos rápidos --}}
      <div class="glass w-quick" data-aos="fade-up">
        <div class="inside">
          <div class="block-title">Atajos rápidos</div>
          <div class="quick-grid">
            <a class="quick" href="{{ route('medico.citas.index') }}"><i class="bi bi-calendar2-event"></i> Citas del día</a>
            <a class="quick" href="{{ route('medico.pacientes.index') }}"><i class="bi bi-people"></i> Pacientes</a>
            <a class="quick" href="{{ route('medico.medicamentos.index') }}"><i class="bi bi-capsule"></i> Medicamentos</a>
            <a class="quick" href="{{ route('medico.tutores.index') }}"><i class="bi bi-person-lines-fill"></i> Tutores</a>
            <a class="quick" href="#"><i class="bi bi-clipboard2-check"></i> Notas clínicas</a>
            <a class="quick" href="#"><i class="bi bi-chat-text"></i> Mensajes</a>
          </div>

          <div class="text-center mt-3">
            <button id="goDashboard" class="btn btn-primary px-4 py-2 fw-bold rounded-3">
              <i class="bi bi-grid-1x2 me-2"></i>Ir a mis gestiones
            </button>
          </div>
        </div>
      </div>

      {{-- Pausa ocular 20-20-20 (con nota) --}}
      <div class="glass w-vision" data-aos="fade-left">
        <div class="inside">
          <div class="block-title">Pausa ocular 20-20-20</div>
          <div class="vision-wrap">
            <div class="eye" id="eyeBall"><div class="eye-emoji" id="eyeEmoji">👁️</div></div>
            <div>
              <div class="vision-legend">Mira a 6 metros durante <span id="eyeCount">20</span>s</div>
              <div class="vision-controls">
                <button id="eyeStart" class="btn-soft"><i class="bi bi-play-fill"></i> Iniciar</button>
                <button id="eyePause" class="btn-soft"><i class="bi bi-pause-fill"></i> Pausar</button>
                <button id="eyeReset" class="btn-soft"><i class="bi bi-arrow-repeat"></i> Reiniciar</button>
              </div>
              <div class="vision-note mt-2">
                <strong>Recomendación:</strong> cada <b>20 minutos</b>, mira algo a <b>6 m (20 ft)</b> durante <b>20 s</b>. Parpadea suave y relaja mandíbula y hombros para reducir fatiga visual.
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Rutina express de estiramiento (1 min) con instrucciones y propósito --}}
      <div class="glass w-stretch" data-aos="fade-up">
        <div class="inside">
          <div class="block-title">Rutina express de estiramiento (1 min)</div>
          <div class="stretch-wrap">
            <div class="stretch-badge" id="strBadge">🤲</div>
            <div>
              <div class="stretch-title" id="strStep">Abre y cierra las manos lentamente</div>
              <div class="stretch-sub" id="strSub">Ritmo: <b>Inhala</b> al abrir, <b>Exhala</b> al cerrar. Hombros relajados.</div>
              <div class="mt-2 fw-bold">Tiempo restante: <span id="strCount">20</span>s</div>
              <div class="stretch-controls">
                <button id="strStart" class="btn-soft"><i class="bi bi-play-fill"></i> Iniciar</button>
                <button id="strPause" class="btn-soft"><i class="bi bi-pause-fill"></i> Pausar</button>
                <button id="strReset" class="btn-soft"><i class="bi bi-arrow-repeat"></i> Reiniciar</button>
              </div>
              <div class="mt-2" style="font-size:.9rem;color:#2d405a;opacity:.9;">
                <strong>¿Para qué sirve?</strong> Reduce tensión en manos, cuello y hombros, mejora la circulación y ayuda a sostener la concentración clínica.
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Frase para profesionales --}}
      <div class="glass w-quote" data-aos="fade-up">
        <div class="inside">
          <div class="block-title">Frase para profesionales</div>
          <div class="d-flex align-items-start gap-3">
            <div class="d-inline-flex align-items-center justify-content-center"
                 style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.35);border:1px solid rgba(255,255,255,.55);">
              <i class="bi bi-stars" style="color:#0ea97a;font-size:1.4rem;"></i>
            </div>
            <div id="quoteText" style="font-weight:950;line-height:1.22;font-size:clamp(24px,2.4vw,28px);color:#172033;">—</div>
          </div>
          <button type="button" id="shuffleQuote" class="btn btn-link p-0 mt-1">Quiero otra</button>
        </div>
      </div>
    </div>
  </section>

  {{-- ====== DASHBOARD DE GESTIONES (estilo original restaurado) ====== --}}
  <section id="doctorDashboard" class="mw-hidden">
    <section class="content-header py-4 text-center" style="background:transparent;">
      <h2 style="font-weight:950; color:#0f2a40;">Panel de gestiones</h2>
    </section>

    <div class="dashboard-grid">
      {{-- 1. Pacientes --}}
      <a href="{{ route('medico.pacientes.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="100">
        <div class="icon-box" style="background:linear-gradient(135deg,#7a9cc6,#8b80f9);">
          <i class="fas fa-user-injured"></i>
        </div>
        <h4>Pacientes</h4>
        <p>Gestiona la información de los pacientes asignados.</p>
      </a>

      {{-- 2. Tutores --}}
      <a href="{{ route('medico.tutores.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="150">
        <div class="icon-box" style="background:linear-gradient(135deg,#74b9ff,#a29bfe);">
          <i class="fas fa-user-friends"></i>
        </div>
        <h4>Tutores</h4>
        <p>Consulta los tutores responsables de cada paciente.</p>
      </a>

      {{-- 3. Medicamentos --}}
      <a href="{{ route('medico.medicamentos.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="200">
        <div class="icon-box" style="background:linear-gradient(135deg,#00bcd4,#6c63ff);">
          <i class="fas fa-pills"></i>
        </div>
        <h4>Medicamentos</h4>
        <p>Controla los tratamientos y dosis asignadas.</p>
      </a>

      {{-- 4. Tests Psicológicos --}}
      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="250">
        <div class="icon-box" style="background:linear-gradient(135deg,#a29bfe,#74b9ff);">
          <i class="fas fa-brain"></i>
        </div>
        <h4>Tests Psicológicos</h4>
        <p>Aplica y analiza pruebas psicológicas.</p>
      </a>

      {{-- 5. Actividades Terapéuticas --}}
      <a href="{{ route('medico.actividades_terap.asignadas') }}"  class="gestion-card" data-aos="zoom-in" data-aos-delay="300">
        <div class="icon-box" style="background:linear-gradient(135deg,#6c5ce7,#00cec9);">
          <i class="fas fa-heartbeat"></i>
        </div>
        <h4>Actividades Terapéuticas</h4>
        <p>Supervisa y registra terapias personalizadas.</p>
      </a>

      {{-- 6. Citas --}}
      <a href="{{ route('medico.citas.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="350">
        <div class="icon-box" style="background:linear-gradient(135deg,#74b9ff,#6c63ff);">
          <i class="fas fa-calendar-check"></i>
        </div>
        <h4>Citas</h4>
        <p>Agenda, modifica o consulta las citas programadas.</p>
      </a>

      {{-- 7. Emociones --}}
      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="400">
        <div class="icon-box" style="background:linear-gradient(135deg,#81ecec,#a29bfe);">
          <i class="fas fa-smile-beam"></i>
        </div>
        <h4>Emociones</h4>
        <p>Analiza el estado emocional de los pacientes.</p>
      </a>

      {{-- 8. Expediente Clínico --}}
      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="450">
        <div class="icon-box" style="background:linear-gradient(135deg,#00cec9,#6c5ce7);">
          <i class="fas fa-file-medical-alt"></i>
        </div>
        <h4>Expediente Clínico</h4>
        <p>Consulta y administra expedientes clínicos.</p>
      </a>

      {{-- 9. Seguimiento de Pacientes --}}
      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="500">
        <div class="icon-box" style="background:linear-gradient(135deg,#6c63ff,#00bcd4);">
          <i class="fas fa-stethoscope"></i>
        </div>
        <h4>Seguimiento de Pacientes</h4>
        <p>Monitorea la evolución y progreso terapéutico.</p>
      </a>

      {{-- 10. Asignación de Actividades --}}
      <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="550">
        <div class="icon-box" style="background:linear-gradient(135deg,#b388ff,#82b1ff);">
          <i class="fas fa-tasks"></i>
        </div>
        <h4>Asignación de Actividades</h4>
        <p>Asigna terapias o ejercicios personalizados.</p>
      </a>
    </div>

    <div class="text-center mt-3">
      <button id="backToWellness" class="btn btn-outline-primary btn-sm">
        Volver a portada de bienestar
      </button>
    </div>
  </section>
</div>

<script>
  // Utilidades
  function formatFechaMX(d){
    const dias=["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
    const meses=["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    return `${dias[d.getDay()]}, ${d.getDate()} de ${meses[d.getMonth()]} de ${d.getFullYear()}`;
  }

  // Saludo + chip día/noche
  (function(){
    const h=new Date().getHours();
    document.getElementById('todayText').textContent=formatFechaMX(new Date());
    const dayIcon=document.getElementById('dayIcon');
    const dayLabel=document.getElementById('dayLabel');
    if(h>=6 && h<19){ dayIcon.className='bi bi-sun'; dayLabel.textContent='Día luminoso'; }
    else{ dayIcon.className='bi bi-moon-stars'; dayLabel.textContent='Noche serena'; }
  })();

  // Frases para profesionales
  const PRO_QUOTES=[
    ' “Escuchar con calma también es una forma de sanar.”',
    ' “Pequeños avances constantes construyen grandes recuperaciones.”',
    ' “Tu presencia serena es parte del tratamiento.”',
    '“Respira tú también: la empatía necesita oxígeno.”',
    '“Cuidarte es cuidar mejor.”'
  ];
  (function(){
    const t=document.getElementById('quoteText');
    const i=(new Date().getDate())%PRO_QUOTES.length;
    t.textContent=PRO_QUOTES[i];
    document.getElementById('shuffleQuote').addEventListener('click',()=>{
      const r=Math.floor(Math.random()*PRO_QUOTES.length);
      t.textContent=PRO_QUOTES[r];
    });
  })();

  // Toggle portada <-> dashboard
  const wel=document.getElementById('wellnessDoctor');
  const dash=document.getElementById('doctorDashboard');
  document.getElementById('goDashboard').addEventListener('click', ()=>{ wel.classList.add('mw-hidden'); dash.classList.remove('mw-hidden'); });
  document.getElementById('backToWellness').addEventListener('click', ()=>{ dash.classList.add('mw-hidden'); wel.classList.remove('mw-hidden'); });

  // AOS
  AOS.init({ duration:900, once:true, easing:'ease-out-back' });

  // Pausa ocular 20-20-20
  let eyeTimer=null, eyeCount=20, eyeRunning=false;
  const eyeCountEl=document.getElementById('eyeCount');
  const eyeBall=document.getElementById('eyeBall');
  const eyeEmoji=document.getElementById('eyeEmoji');

  function renderEye(){
    eyeCountEl.textContent=eyeCount;
    eyeEmoji.textContent = (eyeCount % 2 === 0) ? '👁️' : '😉';
    eyeBall.style.transform = (eyeCount % 2 === 0) ? 'scale(1)' : 'scale(1.04)';
  }
  function tickEye(){ eyeCount--; if(eyeCount<=0){ eyeCount=20; } renderEye(); }
  function startEye(){ if(eyeRunning) return; eyeRunning=true; renderEye(); eyeTimer=setInterval(tickEye, 1000); }
  function pauseEye(){ eyeRunning=false; clearInterval(eyeTimer); }
  function resetEye(){ pauseEye(); eyeCount=20; renderEye(); }
  document.getElementById('eyeStart').addEventListener('click', startEye);
  document.getElementById('eyePause').addEventListener('click', pauseEye);
  document.getElementById('eyeReset').addEventListener('click', resetEye);
  renderEye();

  // Rutina express de estiramiento (3 pasos × 20s) con instrucciones
  const STR_STEPS=[
    {e:'🤲', t:'Abre y cierra las manos lentamente', s:'Ritmo: Inhala al abrir • Exhala al cerrar. Hombros relajados.'},
    {e:'🧑‍⚕️', t:'Inclina el cuello hacia los lados', s:'Lleva oreja hacia hombro (suave) 10s por lado. Respira sin forzar.'},
    {e:'💪', t:'Rota los hombros hacia atrás', s:'Círculos amplios, lentos. Inhala al subir • Exhala al bajar.'}
  ];
  let idx=0, sec=20, timer=null, active=false;
  const badge=document.getElementById('strBadge');
  const step=document.getElementById('strStep');
  const sub=document.getElementById('strSub');
  const count=document.getElementById('strCount');
  function paint(){
    const s=STR_STEPS[idx];
    badge.textContent=s.e;
    step.textContent=s.t;
    sub.textContent=s.s;
    count.textContent=sec;
  }
  function tick(){
    sec--;
    if(sec<=0){
      idx=(idx+1)%STR_STEPS.length;
      sec=20;
      anime({targets:badge,scale:[1,1.3,1],duration:600,easing:'easeInOutQuad'});
      paint();
    }else{
      count.textContent=sec;
    }
  }
  function start(){ if(active) return; active=true; paint(); timer=setInterval(tick,1000); }
  function pause(){ active=false; clearInterval(timer); }
  function reset(){ pause(); idx=0; sec=20; paint(); }
  document.getElementById('strStart').addEventListener('click', start);
  document.getElementById('strPause').addEventListener('click', pause);
  document.getElementById('strReset').addEventListener('click', reset);
  paint();

  // Animación hover íconos dashboard (anime.js)
  document.querySelectorAll('.gestion-card').forEach(card=>{
    card.addEventListener('mouseenter',()=>{
      const icon=card.querySelector('.icon-box i');
      if(!icon || !window.anime) return;
      anime({ targets:icon, scale:[1,1.3,1], rotate:'1turn', duration:900, easing:'easeInOutElastic(1,.7)' });
    });
  });
</script>
@endsection
