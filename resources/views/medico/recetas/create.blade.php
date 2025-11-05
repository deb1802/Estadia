@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    /* Colores base del admin bonito */
    --ink:#1b3b6f;
    --muted:#6b7280;
    --stroke:#e6eefc;
    --soft:#f8fafc;
    --chip:#eef2ff;
    --chip-b:#c7d2fe;

    /* Botones suaves (grises) */
    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-hover:#f3f4f6;
  }

  /* Fondo suave tipo admin */
  .wrap{
    background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%);
  }

  .page{ max-width: 980px; margin-inline:auto; padding: 20px 14px 40px; color:var(--ink); }

  /* Encabezado “banner” como admin */
  .rx-header{
    background: #b5c8e1;      /* azul admin */
    color:#fff;
    border-radius:18px;
    padding: 1.25rem 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
    margin-bottom: 1rem;
  }
  .rx-title{ font-weight:800; margin:0; }
  .rx-meta{ color:#eef2f7; font-size:.95rem; }

  /* Chips */
  .chip{
    display:inline-block; background:var(--chip); border:1px solid var(--chip-b);
    color:#3730a3; padding:4px 12px; border-radius:999px; font-size:.8rem; font-weight:600;
  }

  /* Tarjeta base */
  .card-soft{
    border:1px solid var(--stroke);
    background: #fff;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(27,59,111,.08);
  }

  /* Etiquetas y separadores */
  .section-title{ font-weight:800; color:#fff; }
  .label{ font-size:.9rem; color:var(--muted); }
  .req::after{ content:"*"; color:#dc3545; margin-left:4px; }
  .hr{ height:1px; background:var(--stroke); margin:.75rem 0 1rem; }

  /* Botón suave reutilizable (volver) */
  .btn-soft{
    background: var(--g-bg);
    border: 1px solid var(--g-borde);
    color: var(--g-text);
    border-radius: 50px;
    font-weight: 500;
    padding: .5rem 1.25rem;
    transition: all .25s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,.04);
  }
  .btn-soft:hover{
    background: var(--g-bg-hover);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
  }

  /* Inputs más marcados */
  .form-control, .form-select, textarea{
    border-color: var(--stroke);
    border-radius: 12px;
  }
  .form-control:focus, .form-select:focus, textarea:focus{
    border-color:#9fb5dd;
    box-shadow: 0 0 0 .2rem rgba(181,200,225,.25);
  }

  /* Pie de ayuda */
  .hint{ color:var(--muted); font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page">

    {{-- Encabezado tipo admin --}}
    <div class="rx-header d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h2 class="rx-title">Generar receta médica</h2>
        <div class="rx-meta">
          <span class="chip me-1"><i class="bi bi-person-badge me-1"></i>{{ $paciente->nombre }} {{ $paciente->apellido }}</span>
          <span class="chip"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($hoy)->format('d/m/Y') }}</span>
        </div>
      </div>

      <div>
        <button type="button"
                class="btn btn-soft"
                onclick="window.location='{{ route('medico.pacientes.index') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </button>
      </div>
    </div>

    {{-- Alerts --}}
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <strong>Revisa los campos:</strong>
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    {{-- Formulario --}}
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="mb-3" style="font-weight:700; color:var(--ink);">Datos de la receta</h5>

        <form method="POST" action="{{ route('medico.recetas.store') }}">
          @csrf
          <input type="hidden" name="fkPaciente" value="{{ $paciente->idPaciente }}">

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Paciente</label>
              <input type="text" class="form-control" value="{{ $paciente->nombre }} {{ $paciente->apellido }}" disabled>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label req">Fecha</label>
              <input type="date" name="fecha" class="form-control" value="{{ $hoy }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Observaciones (opcional)</label>
              <textarea name="observaciones" class="form-control" rows="4" placeholder="Indicaciones generales...">{{ old('observaciones') }}</textarea>
            </div>
          </div>

          <div class="hr"></div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
              Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-file-earmark-plus me-1"></i> Crear receta
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="mt-3 hint">
      Después de crear, pasarás a agregar los medicamentos (dosis, frecuencia, duración) a esta receta.
    </div>

  </div>
</div>

@include('medico.bottom-navbar')
@endsection
