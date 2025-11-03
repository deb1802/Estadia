@extends('layouts.landing')
@section('title', 'Bienvenido a MindWare')

@section('content')

<!-- ============== HERO ============== -->
<section class="mw-hero position-relative overflow-hidden">
  <div class="container py-5 py-lg-6 position-relative">
    <div class="row align-items-center g-4 g-lg-5">
      <!-- ==== Texto ==== -->
      <div class="col-lg-6 position-relative z-2">
        <span class="chip mb-3">Bienestar · Terapia · Acompañamiento</span>

        <h1 class="display-4 fw-bold text-ink mb-3">
          Bienvenido a <span class="brand-accent">MindWare</span>
        </h1>

        <p class="lead text-ink-2 mb-4 pe-lg-5">
          Tu bienestar emocional comienza con un paso: conocerte, acompañarte y cuidar de tu mente.
          En MindWare te ayudamos con herramientas terapéuticas, tests y actividades diseñadas para ti.
        </p>

        <div class="d-flex flex-wrap gap-3">
          <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-door-open me-2"></i> Iniciar sesión
          </a>
          <a href="#por-que" class="btn btn-outline-primary btn-lg px-4">
            Descubre más
          </a>
        </div>
      </div>

      <!-- ==== Mascotas flotantes ==== -->
      <div class="col-lg-6 position-relative">
        <div class="mascot-free-stage position-relative">
          @foreach ([
            ['id'=>'mwMascotA','g1'=>'#b3d9d2','g2'=>'#90aacc','face'=>'happy','class'=>'orbit-a big'],
            ['id'=>'mwMascotB','g1'=>'#f7c7de','g2'=>'#b5c8e1','face'=>'neutral','class'=>'orbit-b big'],
            ['id'=>'mwMascotC','g1'=>'#c9d7ff','g2'=>'#90aacc','face'=>'focus','class'=>'orbit-c big'],
            ['id'=>'mwMascotD','g1'=>'#e3e1ff','g2'=>'#b5c8e1','face'=>'happy','class'=>'orbit-d med'],
            ['id'=>'mwMascotE','g1'=>'#c7f0f2','g2'=>'#b3d9d2','face'=>'neutral','class'=>'orbit-e med'],
            ['id'=>'mwMascotF','g1'=>'#f9d7e9','g2'=>'#b5c8e1','face'=>'focus','class'=>'orbit-f med'],
          ] as $m)
            <div class="mascot-free {{ $m['class'] }}">
              @include('partials.mascot-svg', [
                'id' => $m['id'],
                'g1' => $m['g1'],
                'g2' => $m['g2'],
                'face' => $m['face']
              ])
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============== POR QUÉ CUIDAR TU SALUD MENTAL ============== -->
<section id="por-que" class="section-pad bg-soft">
  <div class="container text-center">
    <h2 class="h1 fw-bold text-ink mb-3" data-aos="fade-up">
      ¿Por qué es importante cuidar tu salud mental?
    </h2>
    <p class="text-ink-2 mx-auto mb-5" style="max-width: 760px;" data-aos="fade-up" data-aos-delay="100">
      La salud mental influye en cómo pensamos, sentimos y actuamos. Fortalecerla mejora nuestra estabilidad emocional,
      la concentración y las relaciones con los demás.
    </p>

    <div class="row g-4 justify-content-center">
      @foreach ([
        ['img'=>'calma.png','titulo'=>'Reducción del estrés','texto'=>'La práctica de atención plena y la autorreflexión reducen el estrés y la ansiedad.'],
        ['img'=>'energia.png','titulo'=>'Mayor energía y vitalidad','texto'=>'Dormir bien, moverse y trabajar en tus emociones mejora tu energía y enfoque.'],
        ['img'=>'enfoque.png','titulo'=>'Claridad mental','texto'=>'Aprende a reconocer tus emociones y tomar decisiones con mayor serenidad.']
      ] as $i => $card)
        <div class="col-md-4 col-sm-10" data-aos="fade-up" data-aos-delay="{{ 100 + ($i*100) }}">
          <article class="visual-card rounded-4 shadow-soft">
            <img class="card-bg-img" src="{{ asset('img/'.$card['img']) }}" alt="{{ $card['titulo'] }}">
            <div class="text-overlay">
              <h5 class="fw-bold text-white mb-1">{{ $card['titulo'] }}</h5>
              <p class="text-white-50 small mb-0">{{ $card['texto'] }}</p>
            </div>
          </article>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ============== ACOMPAÑAMIENTO TERAPÉUTICO ============== -->
<section class="section-pad">
  <div class="container">
    <h2 class="h1 fw-bold text-center text-ink mb-4" data-aos="fade-up">
      Un acompañamiento terapéutico pensado para ti
    </h2>

    <div class="row g-4 align-items-center">
      <div class="col-lg-6" data-aos="fade-right">
        <img src="{{ asset('img/terapia.png') }}" alt="Acompañamiento terapéutico" class="img-fluid rounded-4 shadow-soft">
      </div>

      <div class="col-lg-6" data-aos="fade-left">
        <p class="text-ink-2 mb-3">
          En MindWare, cada persona es única. Por eso, nuestros profesionales crean un plan de acompañamiento adaptado a tus necesidades.
          A través de <strong>tests personalizados</strong> y <strong>actividades terapéuticas guiadas</strong>, ayudamos a explorar emociones, hábitos y pensamientos.
        </p>
        <ul class="text-ink-2 small ps-3">
          <li>Evaluaciones emocionales personalizadas.</li>
          <li>Seguimiento profesional y retroalimentación continua.</li>
          <li>Actividades terapéuticas diseñadas para tu bienestar.</li>
          <li>Espacios digitales seguros para reflexionar y mejorar día a día.</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ============== TESTS Y ACTIVIDADES ============== -->
<section class="section-pad bg-soft">
  <div class="container text-center">
    <h2 class="fw-bold text-ink mb-4" data-aos="fade-up">Explora, reflexiona y crece</h2>
    <p class="text-ink-2 mb-5" data-aos="fade-up" data-aos-delay="100">
      Los tests y actividades terapéuticas te ayudan a comprender tu estado emocional y a trabajar tu bienestar de forma activa.
    </p>

    <div class="row g-4 justify-content-center">
      @foreach ([
        ['icon'=>'bi-journal-check','titulo'=>'Tests personalizados','texto'=>'Cuestionarios diseñados por profesionales para comprender tus emociones y patrones de pensamiento.'],
        ['icon'=>'bi-lightbulb','titulo'=>'Actividades terapéuticas','texto'=>'Ejercicios guiados que te permiten practicar mindfulness, autocuidado y manejo del estrés.'],
        ['icon'=>'bi-calendar-heart','titulo'=>'Citas y seguimiento','texto'=>'Sesiones de acompañamiento con profesionales que te ayudan a crecer y mantener tu equilibrio.']
      ] as $item)
        <div class="col-md-4 col-sm-6" data-aos="fade-up">
          <div class="how-card p-4 rounded-4 shadow-sm h-100">
            <i class="bi {{ $item['icon'] }} fs-1 text-accent mb-3"></i>
            <h5 class="fw-semibold text-ink mb-2">{{ $item['titulo'] }}</h5>
            <p class="text-ink-3 small">{{ $item['texto'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>




@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<style>
:root{
  --c1:#d7dfe9; --c2:#b5c8e1; --c3:#90aacc;
  --ink:#21374f; --ink-2:#3a536d; --ink-3:#5c7288; --accent:#7fa3c8;
}

/* ==== HERO ==== */
.mw-hero{ background:linear-gradient(180deg,var(--c1),#ffffff);}
.chip{ display:inline-block; padding:.35rem .8rem; border-radius:999px; background:rgba(144,170,204,.18); border:1px solid rgba(144,170,204,.35);}
.brand-accent{ color:var(--c3); }

/* ==== MASCOTAS ==== */
.mascot-free-stage{ position:relative; min-height:500px;}
.mascot-free{ position:absolute; filter:drop-shadow(0 10px 16px rgba(0,0,0,0.12)); transition:transform .3s ease;}
.mascot-free.big{ width:230px; height:230px;}
.mascot-free.med{ width:180px; height:180px;}
.mascot-free:hover{ transform:scale(1.05) translateY(-4px); }

/* Distribución natural (no en línea recta) */
.orbit-a{ left:8%; top:5%; animation:floatA 9s ease-in-out infinite;}
.orbit-b{ right:10%; top:20%; animation:floatB 10s ease-in-out infinite;}
.orbit-c{ left:35%; bottom:5%; animation:floatC 11s ease-in-out infinite;}
.orbit-d{ left:60%; top:10%; animation:floatD 8s ease-in-out infinite;}
.orbit-e{ right:15%; bottom:12%; animation:floatE 12s ease-in-out infinite;}
.orbit-f{ left:20%; bottom:25%; animation:floatF 9s ease-in-out infinite;}

@keyframes floatA{0%,100%{transform:translateY(0)}50%{transform:translateY(-18px)}}
@keyframes floatB{0%,100%{transform:translateY(0)}50%{transform:translateY(20px)}}
@keyframes floatC{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
@keyframes floatD{0%,100%{transform:translateY(0)}50%{transform:translateY(-16px)}}
@keyframes floatE{0%,100%{transform:translateY(0)}50%{transform:translateY(22px)}}
@keyframes floatF{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}

/* ==== Tarjetas visuales ==== */
.visual-card{ position:relative; overflow:hidden; border-radius:18px; min-height:320px; box-shadow:0 10px 20px rgba(33,55,79,.08);}
.visual-card:hover{ transform:translateY(-8px);}
.visual-card .card-bg-img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:brightness(0.8);}
.text-overlay{ position:absolute; bottom:0; width:100%; background:linear-gradient(0deg,rgba(0,0,0,.45),rgba(0,0,0,0)); padding:1.2rem 1rem; color:white;}

/* ==== How / CTA ==== */
.how-card{ background:#fff; transition:all .3s ease;}
.how-card:hover{ transform:translateY(-6px); box-shadow:0 8px 20px rgba(0,0,0,0.08);}
.cta-section{ background:linear-gradient(135deg,var(--c3),var(--c2)); color:white; }

.text-accent{ color:var(--accent)!important; }
.section-pad{ padding:64px 0;}
.bg-soft{ background:linear-gradient(180deg,#fff,var(--c1)); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script> if(window.AOS) AOS.init({once:true,duration:700}); </script>
<script>
(function(){
  const mascots=[
    {id:'mwMascotA',faces:['happy','neutral','focus'],palettes:[['#bea4d2','#b5c8e1'],['#b3d9d2','#90aacc'],['#c9d7ff','#90aacc']]},
    {id:'mwMascotB',faces:['focus','happy','neutral'],palettes:[['#b3d9d2','#90aacc'],['#f7c7de','#b5c8e1'],['#bea4d2','#b5c8e1']]},
    {id:'mwMascotC',faces:['neutral','focus','happy'],palettes:[['#f7c7de','#b5c8e1'],['#c9d7ff','#b5c8e1'],['#b3d9d2','#90aacc']]},
    {id:'mwMascotD',faces:['happy','focus','neutral'],palettes:[['#e3e1ff','#b5c8e1'],['#c7f0f2','#b3d9d2'],['#f9d7e9','#b5c8e1']]},
    {id:'mwMascotE',faces:['focus','happy','neutral'],palettes:[['#b5c8e1','#c9d7ff'],['#c7f0f2','#b3d9d2'],['#e3e1ff','#b5c8e1']]},
    {id:'mwMascotF',faces:['neutral','happy','focus'],palettes:[['#f7c7de','#b5c8e1'],['#b3d9d2','#90aacc'],['#bea4d2','#b5c8e1']]}
  ];
  let step=0;
  setInterval(()=>{
    mascots.forEach(m=>{
      const svg=document.getElementById(m.id);
      if(!svg)return;
      const f=m.faces[step%m.faces.length];
      const [c1,c2]=m.palettes[step%m.palettes.length];
      const mouth=svg.querySelector('.mw-mouth');
      if(mouth){
        if(f==='happy') mouth.setAttribute('d','M46 70c6 10 22 10 28 0');
        else if(f==='focus') mouth.setAttribute('d','M46 73c10 0 18 0 28 0');
        else mouth.setAttribute('d','M46 73c6 6 22 6 28 0');
      }
      const s1=svg.querySelector('.mw-gstop-1'),s2=svg.querySelector('.mw-gstop-2');
      if(s1) s1.setAttribute('stop-color',c1);
      if(s2) s2.setAttribute('stop-color',c2);
    });
    step++;
  },2800);
})();
</script>
@endpush
