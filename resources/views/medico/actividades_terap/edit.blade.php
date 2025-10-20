@extends('layouts.app')

@php
  // Detecta si estás en /medico/* o /admin/*
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Editar Actividad Terapéutica</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('adminlte-templates::common.errors')

        <div class="card">
            {!! Form::model($actividadesTerap, [
                'route'  => [$routeArea . 'actividades_terap.update', $actividadesTerap],
                'method' => 'patch',
                'files'  => true
            ]) !!}

            <div class="card-body">
                <div class="row">
                    @include('medico.actividades_terap.fields')
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                {!! Form::submit('Guardar cambios', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route($routeArea . 'actividades_terap.index') }}" class="btn btn-default">
                    Cancelar
                </a>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection
