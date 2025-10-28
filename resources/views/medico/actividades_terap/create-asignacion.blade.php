@extends('layouts.app')

@section('content')
@php
  use Illuminate\Support\Str;

  // Imagen fallback en public/img/
  $imgUrl = asset('img/actividad.png');

  // Normalizar campos por si en la BD se llaman distinto
  $tituloActividad   = $actividad->titulo ?? $actividad->nombreActividad ?? 'Actividad terapéutica';
  $categoriaAct      = $actividad->categoriaTerapeutica ?? $actividad->categoria ?? 'No definida';
  $tipoContenido     = $actividad->tipoContenido ?? null;
  $descripcionAct    = $actividad->descripcion ?? null;

  // Recurso (puede ser nombre de archivo en storage/app/public o URL)
  $recurso    = $actividad->recurso ?? null;
  $tipo       = strtolower($tipoContenido ?? '');
  $isUrl      = $recurso && (Str::startsWith($recurso, 'http://') || Str::startsWith($recurso, 'https://'));
  $recursoUrl = $recurso ? ($isUrl ? $recurso : asset('storage/' . ltrim($recurso, '/'))) : null;
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
    --danger:#ef4444;
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
    max-height: 240px;
    overflow: hidden;
  }
  .cc-media img, .cc-media video, .cc-media iframe{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display:block;
  }
  .cc-media img{
    transform: scale(1);
    transition: transform .9s ease;
  }
  .card-compact:hover .cc-media img{ transform: scale(1.04); }

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
    z-index: 2;
  }

  .cc-body{ padding: 16px 18px; }
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

  .cc-help{ color: var(--muted); font-size: .9rem; margin: 6px 0 0; }
  .cc-desc{ color: #334155; font-size: .94rem; margin: 8px 0 0; }

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
  .card-form .card-body{ padding: 18px; }

  label{ font-weight: 600; color: var(--ink); }
  .form-control, .form-select, textarea.form-control{
    border-radius: 12px;
    border-color: #cbd5e1;
    transition: border-color .2s ease, box-shadow .2s ease, transform .06s ease;
  }
  .form-control:focus, .form-select:focus, textarea.form-control:focus{
    border-color: var(--brand);
    box-shadow: 0 0 0 .2rem rgba(37,99,235,.15);
  }
  .form-control:active, .form-select:active, textarea.form-control:active{ transform: scale(.998); }

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
  .help{ color: var(--muted); font-size: .86rem; margin-top: 6px; }

  /* Errores */
  .is-invalid{ border-color: var(--danger) !important; }
  .invalid-feedback{ display:block; color: var(--danger); font-size:.86rem; margin-top:6px; }

  /* Hover card form */
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

  @if($errors->any())
    <div class="alert alert-danger">
      <strong>Revisa los campos:</strong>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ===== Tarjeta compacta con recurso dinámico ===== --}}
  <div class="card-compact">
    <div class="cc-grid">
      <div class="cc-media">
        @if($recursoUrl)
          {{-- VIDEO --}}
          @if(Str::contains($tipo, 'video') || Str::endsWith($recursoUrl, ['.mp4','.webm','.ogv']))
            <video src="{{ $recursoUrl }}" controls playsinline></video>

          {{-- AUDIO --}}
          @elseif(Str::contains($tipo, 'audio') || Str::endsWith($recursoUrl, ['.mp3','.wav','.ogg']))
            <div class="d-flex flex-column justify-content-center align-items-center p-3" style="height:160px;background:#f1f5f9;">
              <i class="fas fa-headphones fa-2x text-primary mb-2"></i>
              <audio controls style="width:90%;">
                <source src="{{ $recursoUrl }}">
                Tu navegador no soporta el elemento de audio.
              </audio>
            </div>

          {{-- PDF --}}
          @elseif(Str::contains($tipo, 'pdf') || Str::endsWith($recursoUrl, '.pdf'))
            <iframe src="{{ $recursoUrl }}" style="width:100%;height:100%;border:none;"></iframe>

          {{-- IMAGEN --}}
          @elseif(Str::contains($tipo, ['imagen','image','foto']) || Str::endsWith($recursoUrl, ['.jpg','.jpeg','.png','.gif','.webp']))
            <img src="{{ $recursoUrl }}" alt="Recurso de actividad">

          {{-- OTRO: mostrar link al recurso --}}
          @else
            <div class="d-flex justify-content-center align-items-center p-3" style="height:160px;background:#f8fafc;">
              <a href="{{ $recursoUrl }}" target="_blank" class="text-primary fw-semibold">
                <i class="fas fa-external-link-alt me-2"></i> Ver recurso
              </a>
            </div>
          @endif
        @else
          {{-- Fallback: imagen por defecto --}}
          <img src="{{ $imgUrl }}" alt="Actividad terapéutica">
        @endif

        @if(!empty($tipoContenido))
          <span class="cc-badge" title="Tipo de contenido">{{ ucfirst($tipoContenido) }}</span>
        @endif
      </div>

      <div class="cc-body">
        <div class="cc-title">{{ $tituloActividad }}</div>
        <div class="cc-meta">
          <span class="chip" title="Categoría terapéutica">
            <i class="fas fa-layer-group me-1"></i>{{ $categoriaAct }}
          </span>
          <span class="sep"></span>
          <span class="text-muted"><i class="far fa-calendar-check me-1"></i>Asignable hoy</span>
        </div>

        @if($descripcionAct)
          <p class="cc-desc">{{ $descripcionAct }}</p>
        @endif

        <p class="cc-help">
          Revisa los datos y procede a asignarla a un paciente. Puedes definir una fecha de finalización y agregar <strong>indicaciones</strong> específicas.
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
            <select name="fkPaciente" id="fkPaciente" class="form-select @error('fkPaciente') is-invalid @enderror" required>
              <option value="" disabled {{ old('fkPaciente') ? '' : 'selected' }}>Selecciona un paciente…</option>
              @foreach($pacientes as $p)
                <option value="{{ $p->id }}" {{ (string)old('fkPaciente')===(string)$p->id ? 'selected' : '' }}>
                  {{ $p->display_name }}
                </option>
              @endforeach
            </select>
            @error('fkPaciente')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="help"><i class="far fa-user me-1"></i> Solo aparecen tus pacientes asignados.</div>
          </div>

          <div class="col-md-6">
            <label for="fechaFinalizacion" class="form-label">Fecha de finalización (opcional)</label>
            <input
              type="date"
              name="fechaFinalizacion"
              id="fechaFinalizacion"
              class="form-control @error('fechaFinalizacion') is-invalid @enderror"
              value="{{ old('fechaFinalizacion') }}"
            >
            @error('fechaFinalizacion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="help"><i class="far fa-calendar-alt me-1"></i> Define un límite para concluir la actividad.</div>
          </div>

          {{-- NUEVO: Indicaciones --}}
          <div class="col-12">
            <label for="indicaciones" class="form-label">Indicaciones (opcional)</label>
            <textarea
              name="indicaciones"
              id="indicaciones"
              rows="4"
              maxlength="10000"
              class="form-control @error('indicaciones') is-invalid @enderror"
              placeholder="Ejemplos: 
• Escucha el audio de respiración consciente (enlace) 10 minutos antes de dormir. 
• Lee la lectura ‘Atención plena al cuerpo’ y redacta 3 ideas clave. 
• Haz 3 series de respiración 4-7-8 por la mañana y por la noche.">{{ old('indicaciones') }}</textarea>
            @error('indicaciones')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="help"><i class="far fa-lightbulb me-1"></i> Puedes pegar enlaces, pasos numerados o recordatorios específicos para el paciente.</div>
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
