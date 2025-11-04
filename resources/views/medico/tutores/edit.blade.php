@extends('layouts.app')

@section('content')
<section class="content-header text-center mb-3">
    <div class="container-fluid">
        <h1 class="fw-semibold text-primary">Editar Tutor</h1>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card shadow-sm">
        {!! Form::model($tutor, ['route' => ['medico.tutores.update', $tutor->idTutor], 'method' => 'patch']) !!}

        <div class="card-body">
            <div class="row">
                @include('medico.tutores.fields')
            </div>
        </div>

        <div class="card-footer text-end">
            {!! Form::submit('Actualizar', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('medico.tutores.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection
