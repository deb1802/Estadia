@extends('layouts.landing')
@section('title', 'Bienvenido a MindWare')

@section('content')
<!-- ============== HERO ============== -->
<section class="mw-hero position-relative overflow-hidden">
  <div class="container py-5 py-lg-6">
    <div class="row align-items-center g-4 g-lg-5">
      <div class="col-lg-6">
        <span class="chip mb-3">Bienestar · Acompañamiento · Emociones</span>

        <h1 class="display-4 fw-bold text-ink mb-3">
          Bienvenido a <span class="brand-accent">MindWare</span>
        </h1>

        <p class="lead text-ink-2 mb-4 pe-lg-5">
          La salud mental no es un destino: es un camino que no tienes que recorrer solo.
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

      <div class="col-lg-6">
        <div class="hero-art shadow-soft" data-aos="zoom-in">
          <img src="{{ asset('img/emoticons.png') }}" alt="Emociones MindWare"
               class="img-fluid emo-float">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============== POR QUÉ CUIDAR LA SALUD MENTAL ============== -->
<section id="por-que" class="section-pad bg-soft">
  <div class="container text-center">
    <h2 class="h1 fw-bold text-ink mb-3" data-aos="fade-up">¿Por qué es importante cuidar tu salud mental?</h2>
    <p class="text-ink-2 mx-auto mb-5" style="max-width: 760px;" data-aos="fade-up" data-aos-delay="100">
      Afecta cómo pensamos, sentimos y actuamos. Promover hábitos saludables mejora el rendimiento académico y laboral,
      la regulación emocional y la calidad de las relaciones interpersonales.
    </p>

    <div class="row g-4 justify-content-center">
      @foreach ([
        ['img'=>'calma.png','titulo'=>'Reducción del estrés','texto'=>'La práctica regular de atención plena se asocia con menor estrés percibido y mejor autorregulación.'],
        ['img'=>'energia.png','titulo'=>'Mayor energía y vitalidad','texto'=>'Dormir mejor, moverse y gestionar emociones incrementa la sensación de vitalidad.'],
        ['img'=>'enfoque.png','titulo'=>'Claridad y enfoque','texto'=>'Mejora de la atención sostenida y de las funciones ejecutivas en actividades diarias.']
      ] as $i => $card)
        <div class="col-md-4 col-sm-10" data-aos="fade-up" data-aos-delay="{{ 100 + ($i*100) }}">
          <article class="visual-card rounded-4 shadow-soft">
            <img class="card-bg-img" src="{{ asset('img/'.$card['img']) }}" alt="{{ $card['titulo'] }}">
            <div class="overlay-gradient"></div>
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

<!-- ============== LO QUE DICE LA EVIDENCIA (con íconos) ============== -->
<section class="section-pad">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-lg-5" data-aos="fade-right">
        <h2 class="h1 fw-bold text-ink mb-3">Lo que dice la evidencia</h2>
        <p class="text-ink-2 mb-3">
          Programas de entrenamiento en atención plena como MBSR reportan disminuciones
          significativas en estrés y síntomas de ansiedad en población general y estudiantes.
        </p>
        <p class="text-ink-2 mb-3">
          Metaanálisis en adultos muestran efectos pequeños a moderados en reducción de ansiedad,
          depresión y dolor, y mejoras en bienestar subjetivo.
        </p>
        <p class="text-ink-2 mb-0">
          Organismos internacionales recomiendan intervenciones psicoeducativas para fortalecer
          habilidades socioemocionales y favorecer climas escolares saludables.
        </p>
        <div class="small text-ink-3 mt-3">
          Referencias abreviadas: Kabat-Zinn (MBSR); metaanálisis de mindfulness en adultos; OMS – promoción de salud mental.
        </div>
      </div>

      <div class="col-lg-7" data-aos="fade-left">
        <div class="evidence-grid">
          <div class="ev-card">
            <div class="ev-icon"><i class="bi bi-heart-pulse"></i></div>
            <div class="ev-kpi">–25%</div>
            <div class="ev-text">Estrés percibido tras 8 semanas de práctica guiada (promedios reportados en MBSR).</div>
          </div>
          <div class="ev-card">
            <div class="ev-icon"><i class="bi bi-bullseye"></i></div>
            <div class="ev-kpi">+18%</div>
            <div class="ev-text">Mejora en atención sostenida y funciones ejecutivas en intervenciones escolares.</div>
          </div>
          <div class="ev-card">
            <div class="ev-icon"><i class="bi bi-emoji-smile"></i></div>
            <div class="ev-kpi">–20%</div>
            <div class="ev-text">Reducción de ansiedad y estado de ánimo negativo en metaanálisis de mindfulness.</div>
          </div>
          <div class="ev-card">
            <div class="ev-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="ev-kpi">+22%</div>
            <div class="ev-text">Incremento en bienestar/autocompasión tras programas breves basados en atención plena.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============== CÓMO FUNCIONA MINDWARE (flujo real) ============== -->
<section class="section-pad bg-soft">
  <div class="container">
    <h2 class="h1 fw-bold text-ink text-center mb-4" data-aos="fade-up">Cómo funciona MindWare</h2>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up">
        <div class="step-card">
          <div class="step-icon"><i class="bi bi-person-vcard"></i></div>
          <h6 class="fw-bold text-ink mb-1">Alta por el médico</h6>
          <p class="text-ink-2 small mb-0">El paciente acude con un profesional, quien crea el perfil y define objetivos iniciales.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="step-card">
          <div class="step-icon"><i class="bi bi-calendar-check"></i></div>
          <h6 class="fw-bold text-ink mb-1">Citas y evaluación</h6>
          <p class="text-ink-2 small mb-0">Se agenda la cita inicial y se realiza la valoración clínica de línea base.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="step-card">
          <div class="step-icon"><i class="bi bi-ui-checks-grid"></i></div>
          <h6 class="fw-bold text-ink mb-1">Tests psicológicos</h6>
          <p class="text-ink-2 small mb-0">El médico asigna instrumentos validados para medir síntomas y bienestar.</p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="step-card">
          <div class="step-icon"><i class="bi bi-journal-medical"></i></div>
          <h6 class="fw-bold text-ink mb-1">Actividades terapéuticas</h6>
          <p class="text-ink-2 small mb-0">Prácticas guiadas (p. ej., mindfulness) y tareas entre sesiones con seguimiento.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
        <div class="step-card">
          <div class="step-icon"><i class="bi bi-people"></i></div>
          <h6 class="fw-bold text-ink mb-1">Acompañamiento profesional</h6>
          <p class="text-ink-2 small mb-0">Retroalimentación periódica, ajustes de plan y alertas tempranas.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
        <div class="step-card">
          <div class="step-icon"><i class="bi bi-prescription2"></i></div>
          <h6 class="fw-bold text-ink mb-1">Recetas e indicaciones</h6>
          <p class="text-ink-2 small mb-0">Emisión de recetas, registro de indicaciones y documentación para el paciente.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============== CTA ============== -->
<section class="section-pad">
  <div class="container">
    <div class="cta-box shadow-soft" data-aos="fade-up">
      <h3 class="fw-bold text-ink mb-2">Da el siguiente paso</h3>
      <p class="mb-0 text-ink-2">Empieza con una cita de valoración. La constancia y el acompañamiento marcan la diferencia.</p>
    </div>
  </div>
</section>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<style>
  /* ===== Paleta ===== */
  :root{
    --c1:#d7dfe9;  --c2:#b5c8e1;  --c3:#90aacc;
    --ink:#21374f; --ink-2:#3a536d; --ink-3:#5c7288; --stroke:#d7e1ed;
  }

  .mw-hero{ background: linear-gradient(180deg, var(--c1), #ffffff); }
  .chip{ display:inline-block; padding:.35rem .8rem; border-radius:999px; background: rgba(144,170,204,.18); color: var(--ink); border:1px solid rgba(144,170,204,.35); font-weight:600; font-size:.9rem; }
  .brand-accent{ color: var(--c3); }
  .hero-art{ background:#fff; border:1px solid var(--stroke); border-radius:20px; padding:24px; text-align:center; }
  .shadow-soft{ box-shadow:0 12px 28px rgba(33,55,79,.08); }
  .emo-float{ animation: emoFloat 4s ease-in-out infinite; }
  @keyframes emoFloat{ 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
  .section-pad{ padding:64px 0; }
  .bg-soft{ background: linear-gradient(180deg, #fff, var(--c1)); }

  /* Visual cards full-cover */
  .visual-card{ position:relative; min-height:220px; overflow:hidden; border:1px solid var(--stroke); border-radius:16px; }
  .visual-card .card-bg-img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1.05); transition:transform .6s ease; filter:saturate(1.05); }
  .visual-card:hover .card-bg-img{ transform:scale(1.12); }
  .overlay-gradient{ position:absolute; inset:0; background: linear-gradient(0deg, rgba(33,55,79,.55), rgba(33,55,79,.05)); }
  .text-overlay{ position:absolute; left:0; right:0; bottom:0; padding:1.25rem; }

  /* Evidencia con íconos */
  .evidence-grid{ display:grid; gap:16px; grid-template-columns:repeat(2,1fr); }
  .ev-card{ background:#fff; border:1px solid var(--stroke); border-radius:14px; padding:16px; display:grid; grid-template-columns:auto auto 1fr; align-items:center; gap:12px; }
  .ev-icon{ width:38px; height:38px; border-radius:10px; background: var(--c1); color:var(--ink); display:grid; place-items:center; font-size:18px; }
  .ev-kpi{ color:var(--c3); font-weight:800; font-size:1.4rem; min-width:70px; }
  .ev-text{ color:var(--ink-2); }

  /* Steps con íconos */
  .step-card{ background:#fff; border:1px solid var(--stroke); border-radius:14px; padding:18px; height:100%; }
  .step-icon{ width:40px; height:40px; border-radius:12px; background:var(--c1); color:var(--ink); display:grid; place-items:center; font-size:20px; margin-bottom:.5rem; }

  /* CTA */
  .cta-box{ background: linear-gradient(180deg, #fff, var(--c1)); border:1px solid var(--stroke); border-radius:18px; padding:28px; }

  /* Botones */
  .btn-primary{ background: var(--c3); border-color: var(--c3); }
  .btn-primary:hover{ background:#7897b6; border-color:#7897b6; }
  .btn-outline-primary{ color: var(--c3); border-color: var(--c3); }
  .btn-outline-primary:hover{ background: var(--c3); color:#fff; }

  /* Footer del layout */
  .footer-lite{ background: var(--c3) !important; color:#fff !important; border-top:none !important; }
  .footer-lite a{ color:#fff !important; text-decoration: underline; text-decoration-color: rgba(255,255,255,.5); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script> if (window.AOS) AOS.init({ once:true, duration:700 }); </script>
@endpush
