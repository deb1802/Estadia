@extends('layouts.app')

@section('title', 'Bienvenido a MindWare')

@section('content')

{{-- ========================= HERO ========================= --}}
<section class="hero-mw py-5 py-lg-6 position-relative overflow-hidden">
  <div class="container">
    <div class="row align-items-center g-5">
      {{-- ===== Texto principal ===== --}}
      <div class="col-lg-6">
        <span class="badge rounded-pill text-bg-primary-subtle mb-3 fw-semibold">
          Bienestar • Acompañamiento • Emociones
        </span>

        <h1 class="display-4 fw-bold lh-1 mb-3">
          <span class="text-primary">Bienvenido a</span><br>
          <span class="mw-title-gradient">MindWare</span>
        </h1>

        <p class="lead text-secondary-emphasis mb-4">
          <em>La salud mental no es un destino, es un camino que no tienes que recorrer solo.</em>
        </p>

        {{-- ✨ Botón de iniciar sesión mejorado --}}
        <a href="{{ route('login') }}"
           class="btn btn-mw-login btn-lg fw-semibold px-4 shadow-sm mb-3">
           <i class="bi bi-door-open me-2"></i> Iniciar sesión
        </a>

        <div>
          <a class="btn btn-outline-primary btn-sm px-4 rounded-pill mt-2"
             href="#salud-mental">
            Descubre más
          </a>
        </div>
      </div>

      {{-- ===== Imagen hero ===== --}}
      <div class="col-lg-6 text-center position-relative">
        <figure class="mw-hero-illust m-0">
          <img src="{{ asset('img/emoticons.png') }}" alt="Emociones MindWare"
               class="img-fluid mw-emo-img floating">
        </figure>
        <div class="mw-hero-circle position-absolute top-50 start-50 translate-middle"></div>
      </div>
    </div>
  </div>
</section>


{{-- ========================= SECCIÓN: IMPORTANCIA SALUD MENTAL ========================= --}}
<section id="salud-mental" class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="fw-bold mb-4 text-primary">¿Por qué es importante cuidar tu salud mental?</h2>
    <p class="text-secondary mx-auto mb-5" style="max-width: 720px;">
      La salud mental influye en cómo pensamos, sentimos y actuamos.  
      Cuidarla te permite mantener equilibrio emocional, tomar mejores decisiones  
      y disfrutar de relaciones más plenas.
    </p>

    <div class="row g-4 justify-content-center">
      {{-- Tarjetas visuales --}}
      @foreach ([
        ['img'=>'calma.png','titulo'=>'Reducción del estrés','texto'=>'Aprende a gestionar la presión diaria y mantener la calma interior.'],
        ['img'=>'energia.png','titulo'=>'Mayor energía','texto'=>'La mente en equilibrio impulsa tu bienestar físico y emocional.'],
        ['img'=>'enfoque.png','titulo'=>'Claridad y enfoque','texto'=>'Conecta con tus objetivos y toma decisiones con serenidad.']
      ] as $card)
        <div class="col-md-4 col-sm-10">
          <div class="visual-card full-bg-card rounded-4 shadow-lg position-relative overflow-hidden">
            <img src="{{ asset('img/'.$card['img']) }}" alt="{{ $card['titulo'] }}" class="card-bg-img">
            <div class="overlay-gradient"></div>
            <div class="text-overlay">
              <h4 class="fw-bold text-white mb-2">{{ $card['titulo'] }}</h4>
              <p class="text-light small">{{ $card['texto'] }}</p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>


{{-- ========================= SECCIÓN: BENEFICIOS ========================= --}}
<section class="py-5 position-relative" style="background: linear-gradient(135deg, #efb5f7ff, #bdcef1ff);">
  <div class="container text-center text-white">
    <h2 class="fw-bold mb-4">Beneficios de fortalecer tu bienestar mental</h2>
    <p class="mx-auto mb-5" style="max-width: 700px;">
      Practicar mindfulness y hábitos saludables transforma tu vida.  
      Con MindWare puedes desarrollar resiliencia, empatía y equilibrio emocional.
    </p>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="benefit-card p-4 bg-white text-dark rounded-4 shadow-sm h-100">
          <h5 class="fw-bold mb-2">✨ Mayor autoestima</h5>
          <p class="text-secondary">Conecta contigo y aprende a valorar cada pequeño logro.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="benefit-card p-4 bg-white text-dark rounded-4 shadow-sm h-100">
          <h5 class="fw-bold mb-2">🧘‍♀️ Paz interior</h5>
          <p class="text-secondary">Encuentra calma incluso en los días más difíciles.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="benefit-card p-4 bg-white text-dark rounded-4 shadow-sm h-100">
          <h5 class="fw-bold mb-2">💬 Mejores relaciones</h5>
          <p class="text-secondary">La empatía y la serenidad fortalecen tus vínculos personales.</p>
        </div>
      </div>
    </div>
  </div>
</section>


{{-- ========================= SECCIÓN: MENSAJE MOTIVACIONAL ========================= --}}
<section class="py-5 text-center">
  <div class="container">
    <div class="p-5 rounded-4 shadow-lg mx-auto" 
         style="max-width: 700px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
      <h3 class="fw-bold mb-3">Recuerda que no estás solo</h3>
      <p class="lead mb-4">Pedir ayuda es un acto de valentía.  
        En MindWare te acompañamos paso a paso hacia tu bienestar emocional.</p>
    </div>
  </div>
</section>

{{-- ========================= FOOTER ========================= --}}
<footer class="mw-footer text-center text-lg-start text-white mt-auto">
  <div class="container py-5">
    <div class="row gy-4 justify-content-center">
      
      {{-- Columna 1: Información --}}
      <div class="col-md-4 text-md-start">
        <h5 class="fw-bold text-uppercase mb-3">MindWare</h5>
        <p class="small mb-2">
          <em>Plataforma digital de acompañamiento emocional y bienestar mental.</em>
        </p>
        <p class="small mb-0">"La salud mental no es un destino, es un camino que se recorre acompañado."</p>
      </div>

      {{-- Columna 2: Contacto --}}
      <div class="col-md-4">
        <h5 class="fw-bold text-uppercase mb-3">Contacto</h5>
        <ul class="list-unstyled small">
          <li><i class="bi bi-envelope me-2"></i> mindwaremental@gmail.com</li>
          <li><i class="bi bi-telephone me-2"></i> +52 777 123 4567</li>
          <li><i class="bi bi-geo-alt me-2"></i> Cuernavaca, Morelos, México</li>
        </ul>
      </div>

      {{-- Columna 3: Enlaces rápidos --}}
      <div class="col-md-4 text-md-end">
        <h5 class="fw-bold text-uppercase mb-3">Síguenos</h5>
        <div class="d-flex justify-content-center justify-content-md-end gap-3">
          <a href="#" class="text-white-50 fs-4 hover-link"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white-50 fs-4 hover-link"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-white-50 fs-4 hover-link"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>

    </div>
  </div>

  <div class="footer-bottom text-center py-3 small border-top border-light-subtle">
    © {{ date('Y') }} MindWare. Todos los derechos reservados.
  </div>
</footer>


@endsection


@push('styles')
<style>
  /* ===== Estilos personalizados MindWare ===== */
  .mw-title-gradient {
    background: linear-gradient(90deg, #6c63ff, #b388eb);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .btn-mw-login {
    background: linear-gradient(135deg, #6c63ff, #b388eb);
    color: #fff;
    border: none;
    transition: 0.3s ease;
  }
  .btn-mw-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 99, 255, 0.4);
  }

  .mw-emo-img.floating {
    animation: float 4s ease-in-out infinite;
  }
  @keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
  }

  .mw-hero-circle {
    width: 360px;
    height: 360px;
    background: radial-gradient(circle at 30% 30%, rgba(188, 154, 255, 0.3), transparent 70%);
    z-index: -1;
    border-radius: 50%;
  }

  .visual-card .card-bg-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0; left: 0;
  }

  .overlay-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.6), transparent 70%);
  }

  .text-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    padding: 1.5rem;
    text-align: left;
  }
</style>
@endpush
