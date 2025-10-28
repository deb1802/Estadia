@extends('layouts.app')

@php
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')
  <section class="content-header">
    <a href="{{ route($routeArea.'actividades_terap.index') }}" class="btn btn-default float-right">
      <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
  </section>

  <div class="content px-3">
    <div class="card">
      <div class="card-body">
        @include('medico.actividades_terap.show_fields')
      </div>
    </div>
  </div>

  <div class="card-footer bg-white border-top py-3 mt-auto">
    <div class="d-flex justify-content-center">
      <a href="{{ route($routeArea.'actividades_terap.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-list me-1"></i> Volver al listado
      </a>
    </div>
  </div>
@endsection
