@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Editar Cita</h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card shadow-sm">
        {!! Form::model($cita, ['route' => ['medico.citas.update', $cita->idCita], 'method' => 'patch']) !!}

        <div class="card-body">
            <div class="row">

                {{-- 🔹 Fecha y hora --}}
                <div class="form-group col-sm-6">
                    {!! Form::label('fechaHora', 'Fecha y hora:') !!}
                    {!! Form::datetimeLocal('fechaHora', $cita->fechaHora, ['class' => 'form-control', 'required']) !!}
                </div>

                {{-- 🔹 Motivo --}}
                <div class="form-group col-sm-12">
                    {!! Form::label('motivo', 'Motivo:') !!}
                    {!! Form::textarea('motivo', $cita->motivo, ['class' => 'form-control', 'rows' => 3, 'required']) !!}
                </div>

                {{-- 🔹 Ubicación --}}
                <div class="form-group col-sm-12">
                    {!! Form::label('ubicacion', 'Ubicación:') !!}
                    {!! Form::text('ubicacion', $cita->ubicacion, ['class' => 'form-control', 'required']) !!}
                </div>

                {{-- 🔹 Estado (solo editable por médico) --}}
                <div class="form-group col-sm-4">
                    {!! Form::label('estado', 'Estado de la cita:') !!}
                    {!! Form::select('estado', [
                        'programada' => 'Programada',
                        'realizada' => 'Realizada',
                        'cancelada' => 'Cancelada'
                    ], $cita->estado, ['class' => 'form-control']) !!}
                </div>

                {{-- 🔹 Campos ocultos (FKs) --}}
                {!! Form::hidden('fkMedico', $cita->fkMedico) !!}
                {!! Form::hidden('fkPaciente', $cita->fkPaciente) !!}

            </div>
        </div>

        <div class="card-footer">
            {!! Form::submit('Guardar cambios', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('medico.citas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection
