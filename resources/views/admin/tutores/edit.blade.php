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
        {!! Form::model($tutor, ['route' => ['admin.tutores.update', $tutor->idTutor], 'method' => 'patch']) !!}

        <div class="card-body">
            <div class="row">
                <!-- Nombre -->
                <div class="form-group col-sm-6">
                    {!! Form::label('nombre', 'Nombre:') !!}
                    {!! Form::text('nombre', old('nombre', $tutor->nombre ?? ''), [
                        'class' => 'form-control',
                        'maxlength' => 50,
                        'required' => true
                    ]) !!}
                    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Apellido -->
                <div class="form-group col-sm-6">
                    {!! Form::label('apellido', 'Apellido:') !!}
                    {!! Form::text('apellido', old('apellido', $tutor->apellido ?? ''), [
                        'class' => 'form-control',
                        'maxlength' => 50,
                        'required' => true
                    ]) !!}
                    @error('apellido') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Parentesco -->
                <div class="form-group col-sm-6">
                    {!! Form::label('parentesco', 'Parentesco:') !!}
                    {!! Form::text('parentesco', null, ['class' => 'form-control', 'maxlength' => 50]) !!}
                    @error('parentesco') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Teléfono -->
                <div class="form-group col-sm-6">
                    {!! Form::label('telefono', 'Teléfono:') !!}
                    {!! Form::text('telefono', null, ['class' => 'form-control', 'maxlength' => 20]) !!}
                    @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Correo -->
                <div class="form-group col-sm-6">
                    {!! Form::label('correo', 'Correo electrónico:') !!}
                    {!! Form::email('correo', null, ['class' => 'form-control', 'maxlength' => 100]) !!}
                    @error('correo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Dirección -->
                <div class="form-group col-sm-12">
                    {!! Form::label('direccion', 'Dirección:') !!}
                    {!! Form::textarea('direccion', null, ['class' => 'form-control', 'rows' => 2]) !!}
                    @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Observaciones -->
                <div class="form-group col-sm-12">
                    {!! Form::label('observaciones', 'Observaciones:') !!}
                    {!! Form::textarea('observaciones', null, ['class' => 'form-control', 'rows' => 2]) !!}
                    @error('observaciones') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Paciente asignado -->
                <div class="form-group col-sm-6">
                    {!! Form::label('fkPaciente', 'Paciente asignado:') !!}
                    <select name="fkPaciente" id="fkPaciente" class="form-control" required>
                        <option value="">Seleccione un paciente...</option>
                        @foreach($pacientes as $pac)
                            <option value="{{ $pac->paciente_id }}"
                                {{ old('fkPaciente', $tutor->fkPaciente ?? '') == $pac->paciente_id ? 'selected' : '' }}>
                                {{ $pac->display_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('fkPaciente') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            {!! Form::submit('Actualizar', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('admin.tutores.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection
