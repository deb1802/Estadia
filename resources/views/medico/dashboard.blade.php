@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crud-style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<style>
  /* 🌈 Fondo animado suave */
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

  /* 🧩 Tarjetas de botones */
  .gestion-card {
    display: block;
    text-align: center;
    background: white;
    border-radius: 16px;
    padding: 25px 15px;
    width: 220px;
    margin: 15px;
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
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px auto;
    font-size: 2.2rem;
    background: linear-gradient(135deg, #6c63ff, #00bcd4);
    color: white;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    box-shadow: 0 0 15px rgba(108,99,255,0.4);
    animation: pulseGlow 3s ease-in-out infinite;
  }

  .gestion-card:hover .icon-box {
    transform: scale(1.2) rotate(10deg);
    box-shadow: 0 0 30px rgba(108,99,255,0.6);
  }

  .gestion-card h4 {
    font-weight: bold;
    color: #374151;
    font-size: 1.1rem;
  }

  .gestion-card p {
    font-size: 0.85rem;
    color: #6b7280;
  }

  /* ✨ Efecto de brillo diagonal */
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

  /* 🧠 Acomodo responsive */
  .dashboard-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px 40px;
  }

  /* 💫 Brillo continuo de íconos */
  @keyframes pulseGlow {
    0% {
      box-shadow: 0 0 15px rgba(108,99,255,0.4), 0 0 25px rgba(0,188,212,0.3);
      transform: scale(1);
    }
    50% {
      box-shadow: 0 0 30px rgba(139,128,249,0.6), 0 0 45px rgba(0,188,212,0.5);
      transform: scale(1.05);
    }
    100% {
      box-shadow: 0 0 15px rgba(108,99,255,0.4), 0 0 25px rgba(0,188,212,0.3);
      transform: scale(1);
    }
  }
</style>

<section class="content-header py-6 position-relative w-100 text-center" style="background: transparent;">
  <div class="dashboard-bg"></div>

  <!-- 💬 Bienvenida al médico -->
  <h1 class="fw-semibold mt-3 mb-0"
      style="font-size: 2.7rem; color: #5c6ac4; text-align: center; width: 100%;">
    Bienvenido(a),
    <span style="color: #8b80f9; font-weight: 800;">
      Dr(a). {{ ucfirst(Auth::user()->nombre) }}
    </span>
  </h1>
</section>

<div class="min-h-screen flex flex-col items-center justify-start pt-16">
  <div class="dashboard-grid">

   {{-- 1. Pacientes --}}
  <a href="{{ route('medico.pacientes.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="100">
    <div class="icon-box" style="background: linear-gradient(135deg, #7a9cc6, #8b80f9);">
      <i class="fas fa-user-injured"></i>
    </div>
    <h4>Pacientes</h4>
    <p>Gestiona la información de los pacientes asignados.</p>
  </a>

    {{-- 2. Tutores --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="150">
      <div class="icon-box" style="background: linear-gradient(135deg, #74b9ff, #a29bfe);">
        <i class="fas fa-user-friends"></i>
      </div>
      <h4>Tutores</h4>
      <p>Consulta los tutores responsables de cada paciente.</p>
    </a>

    {{-- 3. Medicamentos --}}
    <a href="{{ route('medico.medicamentos.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="200">
      <div class="icon-box" style="background: linear-gradient(135deg, #00bcd4, #6c63ff);">
        <i class="fas fa-pills"></i>
      </div>
      <h4>Medicamentos</h4>
      <p>Controla los tratamientos y dosis asignadas.</p>
    </a>

    {{-- 4. Tests Psicológicos --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="250">
      <div class="icon-box" style="background: linear-gradient(135deg, #a29bfe, #74b9ff);">
        <i class="fas fa-brain"></i>
      </div>
      <h4>Tests Psicológicos</h4>
      <p>Aplica y analiza pruebas psicológicas.</p>
    </a>

    {{-- 5. Actividades Terapéuticas --}}
   <a href="{{ route('medico.actividades_terap.index') }}" class="gestion-card" data-aos="zoom-in" data-aos-delay="200">
      <div class="icon-box" style="background: linear-gradient(135deg, #6c5ce7, #00cec9);">
        <i class="fas fa-heartbeat"></i>
      </div>
      <h4>Actividades Terapéuticas</h4>
      <p>Supervisa y registra terapias personalizadas.</p>
    </a>

    {{-- 6. Citas --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="350">
      <div class="icon-box" style="background: linear-gradient(135deg, #74b9ff, #6c63ff);">
        <i class="fas fa-calendar-check"></i>
      </div>
      <h4>Citas</h4>
      <p>Agenda, modifica o consulta las citas programadas.</p>
    </a>

    {{-- 7. Emociones --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="400">
      <div class="icon-box" style="background: linear-gradient(135deg, #81ecec, #a29bfe);">
        <i class="fas fa-smile-beam"></i>
      </div>
      <h4>Emociones</h4>
      <p>Analiza el estado emocional de los pacientes.</p>
    </a>

    {{-- 8. Expediente Clínico --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="450">
      <div class="icon-box" style="background: linear-gradient(135deg, #00cec9, #6c5ce7);">
        <i class="fas fa-file-medical-alt"></i>
      </div>
      <h4>Expediente Clínico</h4>
      <p>Consulta y administra expedientes clínicos.</p>
    </a>

    {{-- 9. Seguimiento de Pacientes --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="500">
      <div class="icon-box" style="background: linear-gradient(135deg, #6c63ff, #00bcd4);">
        <i class="fas fa-stethoscope"></i>
      </div>
      <h4>Seguimiento de Pacientes</h4>
      <p>Monitorea la evolución y progreso terapéutico.</p>
    </a>

    {{-- 10. Asignación de Actividades --}}
    <a href="#" class="gestion-card" data-aos="zoom-in" data-aos-delay="550">
      <div class="icon-box" style="background: linear-gradient(135deg, #b388ff, #82b1ff);">
        <i class="fas fa-tasks"></i>
      </div>
      <h4>Asignación de Actividades</h4>
      <p>Asigna terapias o ejercicios personalizados.</p>
    </a>

  </div>
</div>

<script>
  // Inicializa animaciones AOS
  AOS.init({
    duration: 900,
    once: true,
    easing: 'ease-out-back'
  });

  // 💫 Animación de íconos con anime.js
  document.querySelectorAll('.gestion-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      anime({
        targets: card.querySelector('.icon-box i'),
        scale: [1, 1.3, 1],
        rotate: '1turn',
        duration: 900,
        easing: 'easeInOutElastic(1, .7)'
      });
    });
  });
</script>
@endsection
