@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{ --ink:#1b3b6f; --bd:#e6eefc; }
  .wrap{ background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%); }
  .page{ max-width: 900px; margin-inline:auto; padding: 20px 14px 40px; }
  .card-soft{ border:1px solid var(--bd); box-shadow:0 10px 30px rgba(27,59,111,.08); border-radius:16px; }
  .section-title{ font-weight:800; color:var(--ink); }
  .req::after{ content:"*"; color:#dc3545; margin-left:4px; }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="section-title mb-0">Gnerar receta médica</h2>
      
      <div class="mb-3">
          <button type="button"
              class="btn btn-soft"
              onclick="window.location='{{ route('medico.pacientes.index') }}'">
              <i class="bi bi-arrow-90deg-left me-1"></i> Volver
          </button>
      </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <strong>Revisa los campos:</strong>
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="card card-soft">
      <div class="card-body">
        <h5 class="mb-3">Datos de la receta</h5>

        <form method="POST" action="{{ route('medico.recetas.store') }}">
          @csrf

          <input type="hidden" name="fkPaciente" value="{{ $paciente->idPaciente }}">

          <div class="mb-3">
            <label class="form-label">Paciente</label>
            <input type="text" class="form-control" value="{{ $paciente->nombre }} {{ $paciente->apellido }}" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label req">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="{{ $hoy }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Observaciones (opcional)</label>
            <textarea name="observaciones" class="form-control" rows="3" placeholder="Indicaciones generales...">{{ old('observaciones') }}</textarea>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-file-earmark-plus"></i> Crear receta</button>
          </div>
        </form>
      </div>
    </div>

    <div class="mt-3 text-muted small">
      Después de crear, pasarás a agregar los medicamentos (dosis, frecuencia, duración) a esta receta.
    </div>
  </div>
</div>
@include('medico.bottom-navbar')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --g-text:#374151;       /* gris oscuro */
    --g-text-strong:#111827;
    --g-borde:#d1d5db;      /* gris claro borde */
    --g-borde-2:#9ca3af;    /* gris medio hover */
    --g-bg:#ffffff;         /* fondo blanco */
    --g-bg-hover:#f3f4f6;   /* gris claro hover */
  }

  /* ===== Botón suave reutilizable (Volver) ===== */
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

  .btn-soft:active{
    transform: scale(.98);
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
  }

  .btn-soft i{
    font-size: 1rem;
    vertical-align: middle;
  }
</style>

@endpush
