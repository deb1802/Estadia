@extends('layouts.app')

@section('content')
@php
  // Imagen decorativa fija en public/img/
  $imgUrl = asset('img/actividad.png');
@endphp

@push('styles')
<style>
  :root{
    --ink:#0f172a;
    --muted:#64748b;
    --brand:#2563eb;
    --brand-dark:#1d4ed8;
    --accent:#16a34a;
    --bg-soft:#f8fafc;
    --bd:#e2e8f0;
    --shadow: 0 10px 26px rgba(2,6,23,.08);
    --shadow-lg: 0 18px 46px rgba(2,6,23,.10);
  }

  .wrap-asignar{
    background: radial-gradient(1100px 700px at -10% -30%, #eef5ff 0%, #f6fbff 55%, #ffffff 100%);
    padding: 20px 16px 40px;
    border-radius: 14px;
  }

  /* ===== Tarjeta compacta de actividad ===== */
  .card-compact{
    max-width: 760px;
    margin: 0 auto 20px;
    border: 1px solid var(--bd);
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    box-shadow: var(--shadow);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .card-compact:hover{
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: #d5e1ff;
  }

  .cc-grid{
    display: grid;
    grid-template-columns: 180px 1fr;
  }
  @media (max-width: 640px){
    .cc-grid{ grid-template-columns: 1fr; }
  }

  .cc-media{
    position: relative;
    background: #eef3ff;
    min-height: 160px;
    overflow: hidden;
  }
  .cc-media img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display:block;
    transform: scale(1);
    transition: transform .9s ease;
  }
  .card-compact:hover .cc-media img{
    transform: scale(1.04);
  }

  .cc-badge{
    position:absolute;
    top:12px; left:12px;
    background: rgba(22,163,74,.96);
    color:#fff;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: .78rem;
    font-weight: 600;
    box-shadow: 0 8px 20px rgba(22,163,74,.25);
  }

  .cc-body{
    padding: 16px 18px;
  }
  .cc-title{
    color: var(--ink);
    font-weight: 800;
    font-size: clamp(1.05rem, 1.1vw + .8rem, 1.35rem);
    line-height: 1.2;
    margin: 0 0 6px;
    letter-spacing: .1px;
  }

  .cc-meta{
    display: flex; flex-wrap: wrap; gap:8px 14px;
    align-items: center;
    margin-bottom: 10px;
    font-size: .92rem;
    color: var(--muted);
  }
  .chip{
    background: #f0fdf4;
    color: #065f46;
    border: 1px solid #bbf7d0;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 600;
  }
  .sep{ width:6px; height:6px; border-radius:50%; background:#cbd5e1; display:inline-block; }

  .cc-help{
    color: var(--muted);
    font-size: .9rem;
    margin: 6px 0 0;
  }

  /* ===== Card formulario ===== */
  .card-form{
    max-width: 760px;
    margin: 0 auto;
    border: 1px solid var(--bd);
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--shadow);
    overflow: hidden;
  }
  .card-form .card-header{
    background: linear-gradient(180deg, #eef5ff, #fafcff);
    border-bottom: 1px solid var(--bd);
    padding: 14px 18px;
    color: var(--ink);
    font-weight: 700;
  }
  .card-form .card-body{
    padding: 18px;
  }

  label{
    font-weight: 600;
    color: var(--ink);
  }
  .form-control, .form-select{
    border-radius: 12px;
    border-color: #cbd5e1;
    transition: border-color .2s ease, box-shadow .2s ease, transform .06s ease;
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--brand);
    box-shadow: 0 0 0 .2rem rgba(37,99,235,.15);
  }
  .form-control:active, .form-select:active{ transform: scale(.998); }

  /* Botones */
  .btn-brand{
    background: var(--brand);
    border-color: var(--brand);
    color:#fff;
    border-radius: 12px;
    padding: 10px 14px;
    font-weight: 700;
    box-shadow: 0 8px 16px rgba(37,99,235,.18);
    transition: transform .08s ease, box-shadow .2s ease, background .2s ease;
  }
  .btn-brand:hover{ background: var(--brand-dark); border-color: var(--brand-dark); box-shadow: 0 12px 22px rgba(29,78,216,.22); }
  .btn-brand:active{ transform: translateY(1px); }

  /* Tip para inputs */
  .help{
    color: var(--muted);
    font-size: .86rem;
    margin-top: 6px;
  }

  /* Sutil borde activo al pasar el mouse por la card del form */
  .card-form:hover{ border-color:#d5e1ff; box-shadow: var(--shadow-lg); }
</style>
@endpush

<section class="content-header">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h1 class="mb-0">Asignar actividad</h1>
    <a href="{{ route('medico.actividades_terap.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left"></i> Regresar
    </a>
  </div>
</section>

<div class="content px-3 wrap-asignar">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- ===== Tarjeta compacta con imagen y detalles ===== --}}
  <div class="card-compact">
    <div class="cc-grid">
      <div class="cc-media">
        <img src="{{ $imgUrl }}" alt="Actividad terapéutica">
        @if(!empty($actividad->tipoContenido))
          <span class="cc-badge" title="Tipo de contenido">{{ ucfirst($actividad->tipoContenido) }}</span>
        @endif
      </div>
      <div class="cc-body">
        <div class="cc-title">{{ $actividad->titulo }}</div>
        <div class="cc-meta">
          <span class="chip" title="Categoría terapéutica">
            <i class="fas fa-layer-group me-1"></i>{{ $actividad->categoriaTerapeutica ?? 'No definida' }}
          </span>
          <span class="sep"></span>
          <span class="text-muted"><i class="far fa-calendar-check me-1"></i>Asignable hoy</span>
        </div>
        <p class="cc-help">
          Revisa los datos y procede a asignarla a un paciente. Puedes definir una fecha de finalización opcional.
        </p>
      </div>
    </div>
  </div>

  {{-- ===== Formulario ===== --}}
  <div class="card-form">
    <div class="card-header">
      <strong>Datos de asignación</strong>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('medico.actividades_terap.asignar.store') }}">
        @csrf
        <input type="hidden" name="fkActividad" value="{{ $actividad->idActividad }}">
        <input type="hidden" name="fkMedico" value="{{ $medicoId }}">

        <div class="row g-3">
          <div class="col-md-6">
            <label for="fkPaciente" class="form-label">Paciente</label>
            <select name="fkPaciente" id="fkPaciente" class="form-select" required>
              <option value="" selected disabled>Selecciona un paciente…</option>
              @foreach($pacientes as $p)
                <option value="{{ $p->id }}">{{ $p->display_name }}</option>
              @endforeach
            </select>
            <div class="help"><i class="far fa-user me-1"></i> Solo aparecen tus pacientes asignados.</div>
          </div>

          <div class="col-md-6">
            <label for="fechaFinalizacion" class="form-label">Fecha de finalización (opcional)</label>
            <input type="date" name="fechaFinalizacion" id="fechaFinalizacion" class="form-control">
            <div class="help"><i class="far fa-calendar-alt me-1"></i> Define un límite para concluir la actividad.</div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-brand">
            <i class="fas fa-check me-1"></i> Asignar actividad
          </button>
          <a href="{{ route('medico.actividades_terap.index') }}" class="btn btn-outline-secondary">
            Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
