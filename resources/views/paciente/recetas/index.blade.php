@extends('layouts.app')

@section('content')
<section class="content-header py-3">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h1 class="fw-bold text-primary mb-0">
      <i class="bi bi-file-medical me-2"></i> Mis recetas
    </h1>
    <a href="{{ route('paciente.dashboard') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
  </div>
</section>

<div class="content px-4">
  <div class="card shadow-sm">
    <div class="card-body">
      @if($recetas->isEmpty())
        <div class="alert alert-info mb-0">Aún no tienes recetas registradas.</div>
      @else
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Médico</th>
               <th>Observaciones</th> 
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recetas as $r)
                <tr>
                  <td>#{{ $r->idReceta }}</td>
                  <td>{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</td>
                  <td>{{ $r->medico_nombre }} {{ $r->medico_apellido }}</td>
                  <td>{{ \Illuminate\Support\Str::limit($r->observaciones, 60) }}</td>
                  <td class="text-end">
                    <a href="{{ route('paciente.recetas.show', ['idReceta'=>$r->idReceta]) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i> Ver
                    </a>
                    <a href="{{ route('paciente.recetas.pdf', ['idReceta'=>$r->idReceta]) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-filetype-pdf"></i> PDF
                    </a>
                  </td>
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
