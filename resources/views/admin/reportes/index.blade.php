@extends('layouts.app')

@section('title', 'Reportes del Administrador')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<style>
  :root{
    --c1:#d7dfe9; --c2:#b5c8e1; --c3:#90aacc; --ink:#1e2e4a;
  }
  body{
    background: radial-gradient(1100px 600px at 50% 0%, #eef4fb 0%, #dfe8f3 35%, #d7dfe9 60%, #cbd9eb 100%);
    min-height:100vh;
  }

  /* Contenedor principal centrado */
  .wrap{
    max-width: 1200px; margin: 0 auto; padding: 2rem 1rem 3rem;
    text-align:center;
  }

  /* Encabezado */
  .titulo-seccion{
    font-weight: 800; color: var(--ink); letter-spacing:.3px;
    display:inline-flex; align-items:center; gap:.6rem;
  }
  .title-badge{
    display:inline-flex; align-items:center; justify-content:center;
    width:40px; height:40px; border-radius:12px;
    background: linear-gradient(135deg, var(--c3), var(--c2)); color:#fff;
    box-shadow: 0 6px 16px rgba(144,170,204,.35);
  }

  /* Botones centrados */
  .toolbar-actions{
    display:flex; justify-content:center; gap:.75rem; flex-wrap:wrap; margin:1rem 0 1.25rem;
  }
  .btn-soft{
    border: 1px solid rgba(144,170,204,.45);
    background: linear-gradient(180deg, #ffffff, #f6f9ff);
    color: var(--ink); font-weight:600;
    padding:.6rem 1rem; border-radius:12px;
    box-shadow: 0 6px 18px rgba(23,40,79,.08);
    transition: transform .18s ease, box-shadow .18s ease, background .25s ease;
  }
  .btn-soft:hover{ transform: translateY(-2px); box-shadow: 0 10px 24px rgba(23,40,79,.12); background:#fff; }
  .btn-primary-glass{ background: linear-gradient(135deg, var(--c3), var(--c2)); color:#fff; border:none; }
  .btn-primary-glass:hover{ filter:brightness(1.05); }

  /* Grid de tarjetas centrada */
  .reportes-container{
    display:grid; gap:1.6rem; justify-content:center;
    grid-template-columns: repeat(auto-fit, minmax(260px, 320px));
  }

  .reporte-card{
    background: rgba(255, 255, 255, 0.45);
    backdrop-filter: blur(10px);
    border-radius: 18px;
    padding: 1.4rem 1.2rem;
    text-align: center;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.10);
    transition: all 0.25s ease;
    border: 1px solid rgba(255, 255, 255, 0.55);
    cursor:pointer;
    position:relative; overflow:hidden;
  }
  .reporte-card:hover{ transform: translateY(-6px); box-shadow: 0 22px 48px rgba(0,0,0,.16); }
  .reporte-card::after{
    content:""; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%;
    background: radial-gradient(closest-side, rgba(144,170,204,.35), transparent 70%);
  }

  .reporte-icon{
    width: 64px; height: 64px; margin: 0 auto .75rem;
    border-radius: 50%; display:flex; align-items:center; justify-content:center;
    background: radial-gradient(circle at 30% 30%, var(--c3), var(--c2)); color:#fff; font-size:1.35rem;
    box-shadow: 0 10px 20px rgba(144,170,204,.35); border:2px solid #fff;
  }
  .reporte-titulo{ margin:0; color:#2b3a55; font-weight:700; font-size:1.05rem; line-height:1.35; }
  .reporte-desc{ margin:.35rem 0 0; color:#455a7a; font-size:.92rem; }
</style>
@endpush

@section('content')
<main class="wrap" data-aos="fade-up" data-aos-duration="650">
  <h1 class="titulo-seccion mb-2">
    <span class="title-badge"><i class="bi bi-bar-chart-line"></i></span>
    <span>Panel de Reportes del Administrador</span>
  </h1>

  <div class="toolbar-actions">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-soft btn-primary-glass">
      <i class="bi bi-speedometer2 me-1"></i> Ir al Dashboard
    </a>
  </div>

  <div class="reportes-container">
    <!-- Pon tus rutas reales en data-href -->
    <div class="reporte-card" data-title="Reporte 1: Tests asignados"
         data-desc="Listado y conteo de tests asignados por periodo, cohorte o usuario."
         data-href="#">
      <div class="reporte-icon"><i class="bi bi-clipboard-data"></i></div>
      <h3 class="reporte-titulo">Reporte 1: Tests asignados</h3>
      <p class="reporte-desc">Resumen por fechas, docente y grupos.</p>
    </div>

    <div class="reporte-card" data-title="Reporte 2: Seguimiento de pacientes"
         data-desc="Avances y estatus de cada paciente por periodo."
         data-href="#">
      <div class="reporte-icon"><i class="bi bi-people-fill"></i></div>
      <h3 class="reporte-titulo">Reporte 2: Seguimiento de pacientes</h3>
      <p class="reporte-desc">Estatus, progreso y tendencias.</p>
    </div>

    <div class="reporte-card" data-title="Reporte 3: Número y porcentaje de citas por mes"
         data-desc="Conteo y porcentaje mensual de citas programadas y atendidas."
         data-href="#">
      <div class="reporte-icon"><i class="bi bi-calendar2-week"></i></div>
      <h3 class="reporte-titulo">Reporte 3: Número y porcentaje de citas por mes</h3>
      <p class="reporte-desc">Distribución mensual y comparación.</p>
    </div>

    <div class="reporte-card" data-title="Reporte 4: Pacientes registrados por género"
         data-desc="Distribución por género con porcentajes."
         data-href="{{ route('admin.reportes.pacientes.genero') }}">
      <div class="reporte-icon"><i class="bi bi-gender-ambiguous"></i></div>
      <h3 class="reporte-titulo">Reporte 4: Pacientes registrados por género</h3>
      <p class="reporte-desc">Gráfica y tabla.</p>
    </div>

    <div class="reporte-card" data-title="Reporte 5: Estado emocional"
         data-desc="Distribución de emociones registradas por periodo."
         data-href="#">
      <div class="reporte-icon"><i class="bi bi-activity"></i></div>
      <h3 class="reporte-titulo">Reporte 5: Estado emocional</h3>
      <p class="reporte-desc">Tendencias e intensidad.</p>
    </div>

    <div class="reporte-card"
        data-title="Reporte 6: Recetas médicas"
        data-desc="Histórico, visualización y descarga de recetas por paciente."
        data-href="{{ route('admin.recetas.index') }}">
    <div class="reporte-icon">
        <i class="bi bi-file-earmark-medical"></i>
    </div>
    <h3 class="reporte-titulo">Reporte 6: Recetas médicas</h3>
    <p class="reporte-desc">Consulta y exporta en PDF.</p>
    </div>

  </div>
</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  AOS.init({ once:true });

  // Sin filtros: solo confirmación para ir al reporte
  document.querySelectorAll('.reporte-card').forEach(card=>{
    card.addEventListener('click', async ()=>{
      const title = card.getAttribute('data-title') || 'Reporte';
      const desc  = card.getAttribute('data-desc')  || '';
      const href  = card.getAttribute('data-href')  || '#';

      const res = await Swal.fire({
        title: title,
        html: `<p style="margin:.5rem 0 0; color:#41597c;">${desc}</p>`,
        showCancelButton: true,
        confirmButtonText: 'Ir al reporte',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#90aacc',
        background: '#f6f9ff',
        color: '#1e2e4a'
      });

      if (res.isConfirmed && href && href !== '#') window.location.href = href;
    });
  });
</script>
@endpush
