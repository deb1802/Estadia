@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{ --ink:#1b3b6f; --bd:#e6eefc; --muted:#64748b; --card:#fff; }
  .wrap{ background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%); }
  .page{ max-width: 920px; margin-inline:auto; padding: 20px 14px 40px; }
  .card-soft{ border:1px solid var(--bd); background: var(--card); box-shadow:0 10px 30px rgba(27,59,111,.08); border-radius:16px; }
  .title{ font-weight:800; color:var(--ink); }
  .label{ font-size:.85rem; color:var(--muted); }
  .hr{ height:1px; background:var(--bd); margin:.75rem 0 1rem; }
  .logo{ height: 44px; width: auto; }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-center gap-3">
        <img class="logo" src="{{ asset('img/logo.png') }}" alt="Mindware">
        <h2 class="title mb-0">Receta médica #{{ $receta->idReceta }}</h2>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('medico.recetas.pdf', ['idReceta'=>$receta->idReceta]) }}" class="btn btn-outline-secondary">
          <i class="bi bi-filetype-pdf"></i> PDF
        </a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Volver
        </a>
      </div>
    </div>

    <div class="card card-soft mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="label">Paciente</div>
            <div class="fw-semibold">{{ $receta->paciente_nombre }} {{ $receta->paciente_apellido }}</div>
          </div>
          <div class="col-md-3">
            <div class="label">Fecha</div>
            <div class="fw-semibold">{{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}</div>
          </div>
          <div class="col-md-3">
            <div class="label">Médico</div>
            <div class="fw-semibold">
              {{ $receta->medico_nombre }} {{ $receta->medico_apellido }}
            </div>
            @if($receta->especialidad)
              <div class="small text-muted">{{ $receta->especialidad }}</div>
            @endif
            @if($receta->cedulaProfesional)
              <div class="small text-muted">Cédula: {{ $receta->cedulaProfesional }}</div>
            @endif
          </div>
        </div>
        <div class="hr"></div>
        <div class="label">Observaciones</div>
        <div>{{ $receta->observaciones ?: 'Sin observaciones' }}</div>
      </div>
    </div>

    <div class="card card-soft">
      <div class="card-body">
        <h5 class="mb-3">Medicamentos</h5>
        @if($detalles->isEmpty())
          <div class="text-muted">No hay medicamentos en esta receta.</div>
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
                </tr>
              </thead>
              <tbody>
                @foreach($detalles as $d)
                <tr>
                  <td>{{ $d->nombre }}</td>
                  <td>{{ $d->presentacion }}</td>
                  <td>{{ $d->dosis }}</td>
                  <td>{{ $d->frecuencia }}</td>
                  <td>{{ $d->duracion }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection
