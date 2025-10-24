@extends('layouts.app')

@section('content')
<section class="content-header py-3">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h1 class="fw-bold text-primary mb-0">
      <i class="bi bi-file-medical me-2"></i> Receta #{{ $receta->idReceta }}
    </h1>
    <div class="d-flex gap-2">
      <a href="{{ route('paciente.recetas.pdf', ['idReceta'=>$receta->idReceta]) }}" class="btn btn-outline-secondary">
        <i class="bi bi-filetype-pdf"></i> PDF
      </a>
      <a href="{{ route('paciente.recetas.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>
</section>

<div class="content px-4">
  <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body">

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="text-muted small">Paciente</div>
          <div class="fw-semibold">{{ $receta->paciente_nombre }} {{ $receta->paciente_apellido }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted small">Fecha</div>
          <div class="fw-semibold">{{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted small">Médico</div>
          <div class="fw-semibold">{{ $receta->medico_nombre }} {{ $receta->medico_apellido }}</div>
          @if($receta->especialidad)
            <div class="small text-muted">{{ $receta->especialidad }}</div>
          @endif
          @if($receta->cedulaProfesional)
            <div class="small text-muted">Cédula: {{ $receta->cedulaProfesional }}</div>
          @endif
        </div>
      </div>

      <div class="mb-3">
        <div class="text-muted small">Observaciones</div>
        <div>{{ $receta->observaciones ?: 'Sin observaciones' }}</div>
      </div>

      <h5 class="mb-2">Medicamentos</h5>
      @if($detalles->isEmpty())
        <div class="alert alert-light border">Sin medicamentos en esta receta.</div>
      @else
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Foto</th>
                <th>Medicamento</th>
                <th>Presentación</th>
                <th>Dosis</th>
                <th>Frecuencia</th>
                <th>Duración</th>
              </tr>
            </thead>
            <tbody>
              @foreach($detalles as $d)
                @php
                  $raw = trim((string) $d->imagenMedicamento);
                  $url = $raw !== ''
                      ? asset( (str_starts_with($raw, 'storage/') ? $raw : ('storage/medicamentos/'.$raw)) )
                      : null;
                @endphp
                <tr>
                  <td>
                    @if($url)
                      <img src="{{ $url }}" style="width:42px; height:42px; object-fit:contain; border:1px solid #e5e7eb; border-radius:6px;">
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
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
@endsection
