@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow p-4">
        <h3 class="mb-4 text-warning">
            <i class="fas fa-edit"></i> Editar expediente clínico
        </h3>

        {!! Form::model($expediente, ['route' => ['medico.expedientes.update', $expediente->idExpediente], 'method' => 'PUT']) !!}

        {{-- Paciente (solo lectura) --}}
        <div class="form-group mb-3">
            {!! Form::label('fkPaciente', 'Paciente', ['class' => 'form-label fw-bold']) !!}
            {!! Form::select('fkPaciente', $pacientes, $expediente->fkPaciente, ['class' => 'form-control', 'disabled']) !!}
        </div>

        {{-- Antecedentes --}}
        <div class="form-group mb-3">
            {!! Form::label('antecedentes', 'Antecedentes Médicos y Psicológicos', ['class' => 'form-label fw-bold']) !!}
            {!! Form::textarea('antecedentes', null, ['class' => 'form-control', 'rows' => 3]) !!}
        </div>

        {{-- Diagnóstico --}}
        <div class="form-group mb-3">
            {!! Form::label('diagnosticos', 'Diagnóstico Clínico', ['class' => 'form-label fw-bold']) !!}
            {!! Form::textarea('diagnosticos', null, ['class' => 'form-control', 'rows' => 3]) !!}
        </div>

        {{-- Notas clínicas --}}
        <div class="form-group mb-3">
            {!! Form::label('notasClinicas', 'Notas Clínicas', ['class' => 'form-label fw-bold']) !!}
            {!! Form::textarea('notasClinicas', null, ['class' => 'form-control', 'rows' => 3]) !!}
        </div>

        {{-- Observaciones --}}
        <div class="form-group mb-3">
            {!! Form::label('observaciones', 'Observaciones Generales', ['class' => 'form-label fw-bold']) !!}
            {!! Form::textarea('observaciones', null, ['class' => 'form-control', 'rows' => 3]) !!}
        </div>

        <div class="text-end">
            <a href="{{ route('medico.expedientes.show', $expediente->idExpediente) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Actualizar expediente
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection
