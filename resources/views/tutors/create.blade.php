@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                    Create Tutors
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['url' => route(Auth::user()->tipoUsuario === 'medico' ? 'medico.tutores.store' : 'admin.tutores.store')]) !!}

            <div class="card-body">

                <div class="row">
                    @include('tutors.fields')
                </div>

            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route(Auth::user()->tipoUsuario === 'medico' ? 'medico.tutores.index' : 'admin.tutores.index') }}" class="btn btn-default"> Cancel </a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
