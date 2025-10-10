@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crud-style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<style>
  /* 🔹 Fondo animado suave */
  .dashboard-bg {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 20%, #f0f4ff 0%, transparent 70%),
                radial-gradient(circle at 80% 80%, #e8f7ff 0%, transparent 70%);
    z-index: -1;
    animation: float 10s ease-in-out infinite alternate;
  }

  @keyframes float {
    0% { background-position: 0% 0%, 100% 100%; }
    100% { background-position: 50% 50%, 50% 50%; }
  }

  /* 🔹 Tarjetas con efecto 3D */
  .gestion-card {
    display: block;
    text-align: center;
    background: white;
    border-radius: 16px;
    padding: 30px 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    transform-style: preserve-3d;
    perspective: 800px;
  }

  .gestion-card:hover {
    transform: translateY(-8px) rotateX(4deg) rotateY(-4deg);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  }

  .icon-box {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px auto;
    font-size: 2.3rem;
    background: linear-gradient(135deg, #6c63ff, #00bcd4);
    color: white;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    box-shadow: 0 0 15px rgba(108,99,255,0.4);
  }

  .gestion-card:hover .icon-box {
    transform: scale(1.2) rotate(10deg);
    box-shadow: 0 0 30px rgba(108,99,255,0.6);
  }

  .gestion-card h4 {
    font-weight: bold;
    color: #374151;
  }

  .gestion-card p {
    font-size: 0.9rem;
    color: #6b7280;
  }

  /* 🔹 Brillo al pasar el cursor */
  .gestion-card::after {
    content: "";
    position: absolute;
    top: -75%;
    left: -75%;
    width: 50%;
    height: 200%;
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(25deg);
    transition: 0.6s;
  }

  .gestion-card:hover::after {
    left: 125%;
    transition: 0.6s;
  }
</style>

<section class="content-header py-4 text-center position-relative">
  <div class="dashboard-bg"></div>
  <div class="container">
    <h1 class="fw-bold text-primary mb-3">
      <i class="fas fa-crown me-2"></i>Panel principal del administrador
    </h1>
    <p class="text-muted">Gestiona usuarios, tutores, actividades, reportes y más desde un solo lugar.</p>
  </div>
</section>

<div class="container py-5">
  <div class="row g-4 justify-content-center">

    {{-- 1. Usuarios --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
      <a href="{{ route('admin.usuarios.index') }}" class="gestion-card">
        <div class="icon-box"><i class="fas fa-users"></i></div>
        <h4>Usuarios</h4>
        <p>Administra todas las cuentas registradas.</p>
      </a>
    </div>

    {{-- 2. Tutores --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="150">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-user-graduate"></i></div>
        <h4>Tutores</h4>
        <p>Gestiona la información de tutores asignados.</p>
      </a>
    </div>

    {{-- 3. Medicamentos --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-pills"></i></div>
        <h4>Medicamentos</h4>
        <p>Controla el catálogo de tratamientos.</p>
      </a>
    </div>

    {{-- 4. Tests Psicológicos --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="250">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-brain"></i></div>
        <h4>Tests Psicológicos</h4>
        <p>Administra los tests aplicados a pacientes.</p>
      </a>
    </div>

    {{-- 5. Actividades Terapéuticas --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-heartbeat"></i></div>
        <h4>Actividades Terapéuticas</h4>
        <p>Registra y supervisa terapias personalizadas.</p>
      </a>
    </div>

    {{-- 6. Citas --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="350">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-calendar-check"></i></div>
        <h4>Citas</h4>
        <p>Agenda, modifica o cancela citas médicas.</p>
      </a>
    </div>

    {{-- 7. Emociones --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="400">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-smile"></i></div>
        <h4>Emociones</h4>
        <p>Analiza los registros emocionales de los pacientes.</p>
      </a>
    </div>

    {{-- 8. Expediente Clínico --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="450">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-file-medical"></i></div>
        <h4>Expediente Clínico</h4>
        <p>Consulta y administra expedientes de pacientes.</p>
      </a>
    </div>

    {{-- 9. Reportes --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="500">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-chart-line"></i></div>
        <h4>Reportes</h4>
        <p>Visualiza estadísticas y reportes del sistema.</p>
      </a>
    </div>

    {{-- 10. Configuración --}}
    <div class="col-md-5 col-lg-3" data-aos="zoom-in" data-aos-delay="550">
      <a href="#" class="gestion-card">
        <div class="icon-box"><i class="fas fa-cogs"></i></div>
        <h4>Configuración</h4>
        <p>Administra opciones y ajustes del sistema.</p>
      </a>
    </div>

  </div>
</div>

<script>
  // Inicializa animaciones AOS
  AOS.init({
    duration: 900,
    once: true,
    easing: 'ease-out-back'
  });

  // 💫 Animación interactiva con anime.js
  document.querySelectorAll('.gestion-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      anime({
        targets: card.querySelector('.icon-box i'),
        scale: [1, 1.3, 1],
        rotate: '1turn',
        duration: 1000,
        easing: 'easeInOutElastic(1, .6)'
      });
    });
  });
</script>
@endsection
