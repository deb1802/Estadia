@php
  // Detecta si la URL pertenece a médico o admin
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 d-flex align-items-center">
                <h1 class="mb-0">
                    <i class="fa-solid fa-capsules text-info me-2"></i>
                    Detalles del medicamento
                </h1>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-default float-right"
                   href="{{ route($routeArea . 'medicamentos.index') }}">
                    <i class="fa-solid fa-list me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @include('admin.medicamentos.show_fields')
            </div>
        </div>

        {{-- FOOTER opcional: acciones solo de lectura para médico --}}
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                <a href="{{ route($routeArea . 'medicamentos.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i> Volver al listado de medicamentos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
