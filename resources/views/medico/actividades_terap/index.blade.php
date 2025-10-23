{{-- resources/views/medico/actividades_terap/index.blade.php --}}
@extends('layouts.app')

@php
    // Detecta si estás en /medico/* o /admin/*
    $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')
    {{-- Mensajes flash y errores --}}
    @include('flash::message')
    @include('adminlte-templates::common.errors')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Actividades Terapéuticas</h1>
                </div>
                <div class="col-sm-6">
                    {{-- 🔒 Solo los médicos pueden crear nuevas actividades --}}
                    @can('create', App\Models\ActividadesTerap::class)
                        <a class="btn btn-primary float-right"
                           href="{{ route($routeArea . 'actividades_terap.create') }}">
                            <i class="fas fa-plus"></i> Agregar nueva actividad
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            {{-- Pasa $routeArea para que los enlaces apunten a /admin o /medico según corresponda --}}
            @include('medico.actividades_terap.table', ['routeArea' => $routeArea])
        </div>
    </div>
@endsection
