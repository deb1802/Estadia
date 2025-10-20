@include('flash::message')
@include('adminlte-templates::common.errors')

@extends('layouts.app')

@php
    // Detecta automáticamente si estás en /medico/* o /admin/*
    $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')
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
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            {{-- La tabla interna también usará $routeArea --}}
            @include('medico.actividades_terap.table')
        </div>
    </div>
@endsection
