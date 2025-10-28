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
    --dash-bg:#ffffff;   /* Dashboard en blanco */
    --home-bg:#d7dfe9;   /* Inicio bienestar */
  }

  .page-wrap{ position:relative; min-height:calc(100vh - var(--navbar-h,56px)); overflow:hidden; }
  .bg-layer{ position:absolute; inset:0; z-index:-1; transition:background .6s ease, opacity .6s ease; }

  .theme-day .bg-layer{
    background:
      radial-gradient(1200px 800px at 12% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%),
      radial-gradient(900px 600px at 95% 120%, #eaf3ff 0%, #ffffff 60%);
  }
  .theme-night .bg-layer{
    background:
      radial-gradient(1300px 900px at 15% -15%, #1f234a 0%, #272c5d 45%, #12152d 100%),
      radial-gradient(900px 600px at 110% 120%, rgba(139,128,249,.18) 0%, transparent 60%);
  }

  /* ===== A) INICIO DE BIENESTAR ===== */
  #wellnessHome{
    position:relative;
    padding:44px 18px 32px;
    isolation:isolate;
    background: var(--home-bg) !important;
    background-image:none !important;
  }
  #wellnessHome .wellness-surface,
  #wellnessHome .container-fluid{
    background: var(--home-bg) !important;
    background-image:none !important;
  }

  .welcome-head{ text-align:center; margin-bottom:22px; }
  .welcome-title{ font-weight:950; color:#2b3e6f; font-size:clamp(32px,4.2vw,46px); letter-spacing:.2px; }
  .welcome-sub{ color:#23324e; font-size:1.08rem; margin-top:6px; }
  .day-chip{ display:inline-flex; align-items:center; gap:10px; padding:10px 16px; border-radius:999px; margin-top:12px; background:rgba(255,255,255,.35); color:#1f2937; font-weight:800; border:1px solid rgba(255,255,255,.55); }
  .day-chip i{ font-size:1.25rem; }

  .widgets{ max-width:1200px; margin:26px auto 30px; display:grid; gap:22px; grid-template-columns:repeat(12,1fr); }
  .w-mind{ grid-column:span 12; } .w-quote{ grid-column:span 12; } .w-next{ grid-column:span 12; }
  @media (min-width:992px){ .w-mind{ grid-column:span 7; } .w-quote{ grid-column:span 5; } }

  .glass{ position:relative; border-radius:26px; overflow:hidden; background:var(--glass-bg); border:1px solid var(--glass-brd); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); box-shadow:var(--glass-shadow); }
  .glass::before{ content:""; position:absolute; inset:0 0 auto 0; height:55%; background:linear-gradient(180deg,rgba(255,255,255,.38),rgba(255,255,255,0)); pointer-events:none; }
  .glass .inner{ padding:26px; }
  .glass h3{ font-weight:950; color:#1f2937; margin-bottom:10px; font-size:1.35rem; }

  /* Mindfulness */
  .breath-hero{ display:flex; align-items:center; gap:22px; flex-wrap:wrap; }
  .breath-anim{
    width:140px; height:140px; border-radius:50%;
    background:radial-gradient(circle, var(--violet-2) 0%, var(--violet) 60%, var(--mint) 100%);
    box-shadow:0 24px 48px rgba(139,128,249,.38);
    transition: transform 4s ease-in-out, box-shadow 4s ease-in-out, opacity .6s ease;
    transform: scale(1);
  }
  .breath-anim.glow{ box-shadow:0 28px 70px rgba(139,128,249,.48), 0 0 0 10px rgba(139,128,249,.10); }
  .mini-meta{ color:#23324e; font-size:1rem; }

  .coach-wrap{ display:flex; flex-direction:column; gap:6px; margin-top:8px; text-align:center; }
  .coach-emoji{
    font-size:2.8rem; line-height:1; filter: drop-shadow(0 6px 14px rgba(0,0,0,.15));
    opacity:0; transform: scale(.92);
    transition: opacity .35s ease, transform .35s ease;
  }
  .coach-emoji.show{ opacity:1; transform: scale(1); }
  .coach-step{ font-weight:900; color:#102036; font-size:1.18rem; letter-spacing:.2px; }
  .coach-count{ font-weight:800; color:#223a5a; font-size:1.02rem; opacity:.9; }

  .audio-select{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:10px; }
  .audio-select select{ max-width:460px; font-weight:600; }
  .audio-box{ margin-top:12px; padding:12px; border-radius:16px; background:rgba(255,255,255,.30); border:1px solid rgba(255,255,255,.42); box-shadow:0 10px 32px rgba(0,0,0,.08); }

  /* Frase del día */
  .quote-big{ display:flex; align-items:flex-start; gap:14px; }
  .quote-icon{ display:inline-flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.35); border:1px solid rgba(255,255,255,.55); }
  .quote-icon i{ font-size:1.4rem; color:#0ea97a; }
  .quote-text{ font-weight:950; line-height:1.22; font-size:clamp(24px,2.8vw,28px); color:#172033; letter-spacing:.1px; }
  .quote-actions{ margin-top:10px; }

  /* Botones suaves bajo la frase */
  .quote-cta{ margin-top:8px; color:#22324b; font-weight:600; opacity:.9; }
  .btn-soft{
    --b:#2f6de0;
    display:inline-flex; align-items:center; gap:8px;
    border:1px solid rgba(47,109,224,.25);
    background:rgba(47,109,224,.08);
    color:#1b2f53; font-weight:800;
    border-radius:12px; padding:10px 14px; text-decoration:none;
    transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .btn-soft:hover{ transform:translateY(-1px); background:rgba(47,109,224,.12); box-shadow:0 6px 16px rgba(0,0,0,.08); }
  .btn-soft.secondary{
    border-color:rgba(27,43,83,.22);
    background:rgba(27,43,83,.06);
  }

  /* ===== B) DASHBOARD (blanco) ===== */
  #patientDashboard{
    position:relative;
    isolation:isolate;
    min-height:calc(100vh - var(--navbar-h,56px));
    background: var(--dash-bg) !important;
    background-image:none !important;
    padding-bottom:28px;
  }
  #patientDashboard .dashboard-surface,
  #patientDashboard .container-fluid,
  #patientDashboard .content-header,
  #patientDashboard .min-h-screen{
    background: var(--dash-bg) !important;
    background-image:none !important;
  }
  .dash-title{ font-size:clamp(32px,4.2vw,46px); color:#0f2a40; font-weight:950; }

  .dashboard-grid{ display:flex; flex-wrap:wrap; justify-content:center; gap:38px 48px; padding:8px 10px 28px; }

  .gestion-card{
    display:block; text-align:center;
    background:rgba(255,255,255,.62);
    border:1px solid rgba(0,0,0,.06);
    backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px);
    border-radius:22px;
    padding:34px 22px;
    width:300px;
    margin:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    text-decoration:none; color:#1a2a3c;
    transition:transform .22s ease, box-shadow .22s ease;
  }
  .gestion-card:hover{ transform:translateY(-8px); box-shadow:0 16px 40px rgba(0,0,0,0.16); }
  .icon-box{
    width:100px; height:100px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    margin:0 auto 18px; font-size:2.9rem; color:#fff;
    transition:transform .32s ease, box-shadow .32s ease;
    box-shadow:0 0 20px rgba(108,99,255,.30);
  }
  .gestion-card:hover .icon-box{ transform:scale(1.12) rotate(8deg); box-shadow:0 0 32px rgba(108,99,255,.50); }
  .gestion-card h4{ font-weight:950; color:#0f2a40; font-size:1.28rem; margin-bottom:6px; letter-spacing:.2px; }
  .gestion-card p{ font-size:1.02rem; color:#1b3b52; }

  .mw-hidden{ display:none !important; }
  /* Centrar el contenedor del CTA en cualquier tamaño */
.w-next .cta-wrap{
  display:flex;
  justify-content:center;
  align-items:center;
  width:100%;
  margin-top:12px;
}

/* Forzar color/gradiente del botón y su tipografía */
.w-next .cta-wrap .cta-primary{
  background:linear-gradient(135deg,#2f6de0,#2956b3) !important;
  color:#ffffff !important;
  font-weight:950;
  letter-spacing:.35px;
  padding:16px 22px;
  border-radius:16px;
  border:none;
  box-shadow:0 14px 36px rgba(41,86,179,.35);
  display:inline-flex;
  align-items:center;
  gap:12px;
}

/* Hover consistente (por si otro estilo lo pisa) */
.w-next .cta-wrap .cta-primary:hover{
  transform:translateY(-2px);
  box-shadow:0 18px 44px rgba(41,86,179,.45);
}

</style>

<div id="appTheme" class="page-wrap theme-day">
  <div class="bg-layer"></div>

  {{-- ===== A) INICIO DE BIENESTAR ===== --}}
  <section id="wellnessHome">
    <div class="wellness-surface"></div>

    <div class="welcome-head">
      <h1 class="welcome-title">Bienvenido(a), {{ ucfirst(Auth::user()->nombre) }}</h1>
      <div class="welcome-sub">
        <span id="dateNow">—</span>
        <div class="day-chip" id="dayChip">
          <i id="dayIcon" class="bi bi-sun"></i>
          <span id="dayLabel">Día luminoso</span>
        </div>
      </div>
    </div>

    <div class="widgets">
      <!-- Widget Mindfulness -->
      <div class="glass w-mind">
        <div class="inner">
          <h3>Momento Mindfulness</h3>

          <div class="breath-hero">
            <div id="breathBall" class="breath-anim" aria-hidden="true"></div>

            <div>
              <div class="fw-semibold mb-1" style="font-size:1.08rem;">Toma 60 segundos para respirar</div>
              <div class="mini-meta">Respiración 4-4-4-4 (Box Breathing)</div>

              <div class="coach-wrap" aria-live="polite">
                <div id="coachEmoji" class="coach-emoji" aria-hidden="true"></div>
                <div id="coachStep" class="coach-step">Inhala lentamente</div>
                <div id="coachCount" class="coach-count">4</div>
              </div>

              <div class="audio-select">
                <label for="audioSelect" class="form-label mb-0">Audio:</label>
                <select id="audioSelect" class="form-select form-select-sm">
                  <option value="breathe_60s.mp3">Respiración guiada 60s</option>
                  <option value="mar_olas.mp3">Olas del mar con respiración</option>
                  <option value="body_scan_corto.mp3">Consciencia corporal breve</option>
                  <option value="campanas_tibetanas_suaves.mp3">Campanas tibetanas suaves</option>
                  <option value="respira_circulo.mp3">Respira con ritmo visual</option>
                  <option value="musica_ambiental_relajante.mp3">Música ambiental relajante</option>
                  <option value="afirmacion_positiva_1min.mp3">Afirmación positiva 1 min</option>
                </select>
              </div>

              <div class="audio-box">
                <audio id="mindAudio" controls preload="none" style="width:100%">
                  <source id="audioSource" src="" type="audio/mpeg">
                  Tu navegador no soporta audio HTML5.
                </audio>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Widget Frase del día -->
      <div class="glass w-quote">
        <div class="inner">
          <h3>Frase del día</h3>
          <div class="quote-big">
            <div class="quote-icon"><i class="bi bi-stars" aria-hidden="true"></i></div>
            <div class="quote-text" id="quoteText">—</div>
          </div>

          <div class="quote-actions">
            <button type="button" id="shuffleQuote" class="btn btn-link p-0">Quiero otra</button>
          </div>
          <div class="quote-cta">
            💬 <strong>Compártenos cómo te has sentido</strong> y descubre lo que otros pacientes han escrito.
          </div>
          <div class="d-flex gap-2 flex-wrap mt-2">
            <a href="{{ url('/paciente/testimonios') }}" class="btn-soft">
              <i class="bi bi-people"></i> Compartir mi experiencia
            </a>
          </div>
        </div>
      </div>

      <!-- CTA a dashboard -->
      <div class="w-next">
        <div class="cta-wrap">
          <button id="goToDashboardBtn" class="cta-primary">
            <i class="bi bi-grid-1x2"></i> Gestionar mis recursos
          </button>
        </div>
      </div>
    </div>
  </section>

  {{-- ===== B) DASHBOARD ===== --}}
  <section id="patientDashboard" class="mw-hidden">
    <div class="dashboard-surface"></div>

    <section class="content-header py-4 position-relative w-100 text-center" style="background:transparent;">
      <h1 class="dash-title">
        Aquí puedes gestionar los recursos,
        <span style="font-weight:950;">{{ ucfirst(Auth::user()->nombre) }}</span>
      </h1>
    </section>

    <div class="min-h-screen d-flex flex-column align-items-center justify-content-start pt-2">
      <div class="dashboard-grid">

        {{-- 1. Perfil --}}
        <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="100">
          <div class="icon-box" style="background:linear-gradient(135deg,#7a9cc6,#8b80f9);">
            <i class="fas fa-user-circle" aria-hidden="true"></i>
          </div>
          <h4>Perfil</h4>
          <p>Consulta y actualiza tu información personal.</p>
        </a>

        {{-- 2. Tests Psicológicos --}}
        <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="150">
          <div class="icon-box" style="background:linear-gradient(135deg,#74b9ff,#a29bfe);">
            <i class="fas fa-brain" aria-hidden="true"></i>
          </div>
          <h4>Tests Psicológicos</h4>
          <p>Realiza tus pruebas psicológicas asignadas.</p>
        </a>

        {{-- 3. Actividades Terapéuticas --}}
        <a href="{{ route('paciente.actividades.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="200">
          <div class="icon-box" style="background:linear-gradient(135deg,#6c5ce7,#00cec9);">
            <i class="fas fa-heartbeat" aria-hidden="true"></i>
          </div>
          <h4>Actividades Terapéuticas</h4>
          <p>Participa en tus terapias y actividades recomendadas.</p>
        </a>

        {{-- 4. Citas --}}
        <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="250">
          <div class="icon-box" style="background:linear-gradient(135deg,#74b9ff,#6c63ff);">
            <i class="fas fa-calendar-check" aria-hidden="true"></i>
          </div>
          <h4>Citas</h4>
          <p>Consulta tus próximas citas médicas y terapias.</p>
        </a>

        {{-- 5. Emociones --}}
        <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="300">
          <div class="icon-box" style="background:linear-gradient(135deg,#81ecec,#a29bfe);">
            <i class="fas fa-smile-beam" aria-hidden="true"></i>
          </div>
          <h4>Emociones</h4>
          <p>Registra y reflexiona sobre tu estado emocional.</p>
        </a>

        {{-- 6. Testimonios --}}
       <a href="{{ route('paciente.testimonios.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="350">
          <div class="icon-box" style="background:linear-gradient(135deg,#b388ff,#82b1ff);">
            <i class="fas fa-comment-dots" aria-hidden="true"></i>
          </div>
          <h4>Testimonios</h4>
          <p>Comparte tus experiencias y avances personales.</p>
        </a>

      </div>

      <div class="mt-2">
        <button type="button" id="backToWellnessBtn" class="btn btn-primary btn-sm">
          Volver al inicio de bienestar
        </button>
      </div>
    </div>
  </section>
</div>

<script>
  function formatFechaMX(d){
    const dias=["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
    const meses=["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    return `${dias[d.getDay()]}, ${d.getDate()} de ${meses[d.getMonth()]} de ${d.getFullYear()}`;
  }

  (function configureTheme(){
    const h=new Date().getHours();
    const dayIcon=document.getElementById('dayIcon');
    const dayLabel=document.getElementById('dayLabel');
    document.getElementById('dateNow').textContent=formatFechaMX(new Date());
    if(h>=6 && h<19){ dayIcon.className='bi bi-sun'; dayLabel.textContent='Día luminoso'; }
    else{ dayIcon.className='bi bi-moon-stars'; dayLabel.textContent='Noche relajada'; }
  })();

  const FRASES=[
    'Hoy basta con intentarlo.',
    'Respira, observa y acepta.',
    'Un paso pequeño también es avance.',
    'Eres más fuerte de lo que crees.',
    'Sigue a tu propio ritmo.',
    'Tu calma también es productividad.',
    'Te mereces un respiro consciente.'
  ];
  (function setQuote(){
    const txt=document.getElementById('quoteText');
    const idx=(new Date().getDate())%FRASES.length;
    txt.textContent=FRASES[idx];
    document.getElementById('shuffleQuote').addEventListener('click',()=>{
      const r=Math.floor(Math.random()*FRASES.length);
      txt.textContent=FRASES[r];
    });
  })();

  const well=document.getElementById('wellnessHome');
  const dash=document.getElementById('patientDashboard');
  document.getElementById('goToDashboardBtn').addEventListener('click',()=>{
    well.classList.add('mw-hidden'); dash.classList.remove('mw-hidden');
    if(window.AOS) AOS.init({ duration:900, once:true, easing:'ease-out-back' });
  });
  document.getElementById('backToWellnessBtn').addEventListener('click',()=>{
    dash.classList.add('mw-hidden'); well.classList.remove('mw-hidden');
  });

  const AUDIO_BASE=@json(asset('audio'));
  const selectAudio=document.getElementById('audioSelect');
  const audioEl=document.getElementById('mindAudio');
  const audioSrc=document.getElementById('audioSource');
  function setAudio(file){ audioEl.pause(); audioSrc.src=AUDIO_BASE+'/'+file; audioEl.load(); }
  setAudio(selectAudio.value);
  selectAudio.addEventListener('change', function(){ setAudio(this.value); audioEl.play().catch(()=>{}); });

  const ball=document.getElementById('breathBall');
  const coachEmoji=document.getElementById('coachEmoji');
  const coachStep=document.getElementById('coachStep');
  const coachCount=document.getElementById('coachCount');

  const EMOJI = {
    INHALA:  "\uD83C\uDF2C\uFE0F", // 🌬️
    MANTEN:  "\uD83D\uDE0C",       // 😌
    EXHALA:  "\uD83D\uDCA8",       // 💨
    PAUSA:   "\uD83E\uDDE7"        // 🫧
  };

  const STEPS=[
    {label:'Inhala lentamente',  emoji:EMOJI.INHALA, scale:1.16, glow:true},
    {label:'Mantén el aire',     emoji:EMOJI.MANTEN, scale:1.16, glow:true},
    {label:'Exhala suavemente',  emoji:EMOJI.EXHALA, scale:0.88, glow:false},
    {label:'Descansa un momento',emoji:EMOJI.PAUSA,  scale:0.88, glow:false},
  ];

  let stepIndex=0, count=4, tick=null;

  function renderStep(){
    const s=STEPS[stepIndex];
    coachEmoji.classList.remove('show');
    setTimeout(()=>{
      coachEmoji.textContent=s.emoji;
      coachEmoji.classList.add('show');
    }, 120);

    coachStep.textContent=s.label;
    coachCount.textContent=String(count);

    ball.style.transform=`scale(${s.scale})`;
    ball.classList.toggle('glow', !!s.glow);
  }

  function nextTick(){
    count--;
    if(count<=0){
      stepIndex=(stepIndex+1)%STEPS.length;
      count=4;
      renderStep();
    }else{
      coachCount.textContent=String(count);
    }
  }

  renderStep();
  tick=setInterval(nextTick, 1000);

  document.querySelectorAll('.gestion-card').forEach(card=>{
    card.addEventListener('mouseenter',()=>{
      const icon=card.querySelector('.icon-box i');
      if(!icon || !window.anime) return;
      anime({ targets:icon, scale:[1,1.18,1], rotate:'1turn', duration:900, easing:'easeInOutElastic(1,.7)' });
    });
  });
</script>

@include('paciente.bottom-nabvar')
@endsection
