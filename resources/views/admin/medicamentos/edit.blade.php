@php
  // Detecta si la URL pertenece a médico o admin
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Editar medicamento</h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card">
        @if(isset($medicamento))
            {!! Form::model($medicamento, [
                'route' => [$routeArea . 'medicamentos.update', $medicamento],
                'method' => 'patch',
                'files' => true
            ]) !!}
        @else
            {!! Form::open([
                'route' => [$routeArea . 'medicamentos.update', request()->route('medicamento')],
                'method' => 'patch',
                'files' => true
            ]) !!}
        @endif

        <div class="card-body">
            <div class="row">
                @include('admin.medicamentos.fields')
            </div>
        </div>

        <div class="card-footer">
            {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route($routeArea . 'medicamentos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection
