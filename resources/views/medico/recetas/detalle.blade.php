@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{ --ink:#1b3b6f; --bd:#e6eefc; }
  .wrap{ background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%); }
  .page{ max-width: 1100px; margin-inline:auto; padding: 20px 14px 40px; }
  .card-soft{ border:1px solid var(--bd); box-shadow:0 10px 30px rgba(27,59,111,.08); border-radius:16px; }
  .section-title{ font-weight:800; color:var(--ink); }
  .req::after{ content:"*"; color:#dc3545; margin-left:4px; }

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

</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="section-title mb-0">Detalle de receta #{{ $receta->idReceta }}</h2>
      <div class="mb-3">
        <button type="button"
                class="btn btn-soft"
                onclick="window.history.back()">
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

    {{-- Cabecera resumen --}}
    <div class="card card-soft mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12 col-md-4">
            <div class="small text-muted">Paciente</div>
            <div class="fw-semibold">{{ $receta->nombre }} {{ $receta->apellido }}</div>
          </div>
          <div class="col-6 col-md-3">
            <div class="small text-muted">Fecha</div>
            <div class="fw-semibold">{{ $receta->fecha }}</div>
          </div>
          <div class="col-12">
            <div class="small text-muted">Observaciones</div>
            <div class="fw-normal">{{ $receta->observaciones ?: 'Sin observaciones' }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Form para agregar una línea --}}
    <div class="card card-soft mb-4">
      <div class="card-body">
        <h5 class="mb-3">Agregar medicamento</h5>
        <form method="POST" action="{{ route('medico.recetas.detalle.agregar', ['idReceta' => $receta->idReceta]) }}">
          @csrf
          <div class="row g-3">
            <div class="col-12 col-lg-4">
              <label class="form-label req">Medicamento</label>
              <select name="fkMedicamento" class="form-select" required>
                <option value="" selected disabled>Selecciona…</option>
                @foreach($medicamentos as $m)
                  <option value="{{ $m->idMedicamento }}" @selected(old('fkMedicamento')==$m->idMedicamento)>
                    {{ $m->nombre }} @if($m->presentacion) - {{ $m->presentacion }} @endif
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-lg-2">
              <label class="form-label req">Dosis</label>
              <input type="text" name="dosis" class="form-control" maxlength="100" value="{{ old('dosis') }}" placeholder="Ej. Tomar 2 pastillas" required>
            </div>
            <div class="col-12 col-lg-3">
              <label class="form-label req">Frecuencia</label>
              <input type="text" name="frecuencia" class="form-control" maxlength="100" value="{{ old('frecuencia') }}" placeholder="Ej. cada 8 h" required>
            </div>
            <div class="col-12 col-lg-3">
              <label class="form-label req">Duración</label>
              <input type="text" name="duracion" class="form-control" maxlength="100" value="{{ old('duracion') }}" placeholder="Ej. Por 2 meses" required>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Agregar</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Lista de líneas agregadas --}}
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="mb-3">Medicamentos en la receta</h5>
        @if($detalles->isEmpty())
          <div class="text-muted">Aún no hay medicamentos agregados.</div>
        @else
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Medicamento</th>
                  <th>Presentación</th>
                  <th>Dosis</th>
                  <th>Frecuencia</th>
                  <th>Duración</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @foreach($detalles as $d)
                  <tr>
                    <td>{{ $d->medicamento }}</td>
                    <td>{{ $d->presentacion }}</td>
                    <td>{{ $d->dosis }}</td>
                    <td>{{ $d->frecuencia }}</td>
                    <td>{{ $d->duracion }}</td>
                    <td class="text-end">
                      <form method="POST" action="{{ route('medico.recetas.detalle.borrar', ['idReceta'=>$receta->idReceta, 'idDetalle'=>$d->idDetalleMedicamento]) }}" onsubmit="return confirm('¿Eliminar este medicamento de la receta?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-trash"></i> Eliminar
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <div class="d-flex justify-content-end gap-2 mt-3">
          <a href="{{ url('medico/pacientes/'.$receta->idPaciente) }}" class="btn btn-outline-secondary">Finalizar</a>
          {{-- O podrías redirigir a una vista de impresión de receta --}}
        </div>
      </div>
    </div>

  </div>
</div>
@include('medico.bottom-navbar')
@endsection
