@php
  // Detecta si la URL pertenece a médico o admin
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Medicamentos</h1>
      </div>
      <div class="col-sm-6 text-right">
        {{-- Botón Volver al dashboard (según área) --}}
        <a class="btn btn-default mr-2"
           href="{{ route( ($routeArea === 'medico.' ? 'medico.dashboard' : 'admin.dashboard') ) }}">
          <i class="fa-solid fa-arrow-left"></i> Volver
        </a>

        {{-- Botón de crear (solo si tiene permiso según policy) --}}
        @can('create', App\Models\Medicamento::class)
          <a class="btn btn-primary" href="{{ route($routeArea . 'medicamentos.create') }}">
            <i class="fas fa-plus"></i> Nuevo Medicamento
          </a>
        @endcan
      </div>
    </div>
  </div>
</section>


<div class="content px-3">
  @include('flash::message')

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped" id="medicamentos-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Presentación</th>
              <th>Indicaciones</th>
              <th>Efectos Secundarios</th>
              <th>Imagen</th>
              <th style="width: 180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($medicamentos as $medicamento)
              <tr>
                <td>{{ $medicamento->nombre }}</td>
                <td>{{ $medicamento->presentacion }}</td>
                <td>{{ Str::limit($medicamento->indicaciones, 60) }}</td>
                <td>{{ Str::limit($medicamento->efectosSecundarios, 60) }}</td>
                <td>
                  @if($medicamento->imagenMedicamento)
                    <img src="{{ asset('storage/' . $medicamento->imagenMedicamento) }}" alt="Imagen" width="60" height="60" class="rounded">
                  @else
                    <span class="text-muted">Sin imagen</span>
                  @endif
                </td>

                <td>
                  {{-- Ver --}}
                  @can('view', $medicamento)
                    <a href="{{ route($routeArea . 'medicamentos.show', $medicamento->idMedicamento) }}"
                       class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                  @endcan

                  {{-- Editar --}}
                  @can('update', $medicamento)
                    <a href="{{ route($routeArea . 'medicamentos.edit', $medicamento->idMedicamento) }}"
                       class="btn btn-sm btn-warning text-white">
                      <i class="fas fa-edit"></i>
                    </a>
                  @endcan

                  {{-- Eliminar --}}
                  @can('delete', $medicamento)
                    {!! Form::open([
                      'route' => [$routeArea . 'medicamentos.destroy', $medicamento->idMedicamento],
                      'method' => 'delete',
                      'style' => 'display:inline'
                    ]) !!}
                      {!! Form::button('<i class="fas fa-trash-alt"></i>', [
                        'type' => 'submit',
                        'class' => 'btn btn-sm btn-danger',
                        'onclick' => "return confirm('¿Eliminar este medicamento?')"
                      ]) !!}
                    {!! Form::close() !!}
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
