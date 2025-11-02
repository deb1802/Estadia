@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Programar nueva cita</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('medico.citas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card shadow-sm p-3">
        {{-- ⚙️ Formulario principal --}}
        {!! Form::open(['route' => 'medico.citas.store', 'method' => 'POST', 'id' => 'form-cita']) !!}

        <div class="row">
            {{-- 🔹 Médico autenticado --}}
            <div class="form-group col-sm-12">
                <label><strong>Médico:</strong></label>
                <p>{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
                {!! Form::hidden('fkMedico', $medicoId) !!}
            </div>

            {{-- 🔹 Paciente --}}
            <div class="form-group col-sm-12 col-md-6">
                {!! Form::label('fkPaciente', 'Paciente:') !!}
                <select name="fkPaciente" id="fkPaciente" class="form-control" required>
                    <option value="">Seleccione un paciente...</option>
                    @foreach($pacientes as $paciente)
                        <option value="{{ $paciente->id }}">
                            {{ $paciente->nombre }} {{ $paciente->apellido }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔹 Fecha --}}
            <div class="form-group col-sm-12 col-md-3">
                {!! Form::label('fecha', 'Fecha de la cita:') !!}
                <input type="date" id="fecha" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>

            {{-- 🔹 Hora --}}
            <div class="form-group col-sm-12 col-md-3">
                {!! Form::label('hora', 'Hora de la cita:') !!}
                <input type="time" id="hora" class="form-control" required>
            </div>

            {{-- 🔹 Motivo --}}
            <div class="form-group col-sm-12">
                {!! Form::label('motivo', 'Motivo de la cita:') !!}
                {!! Form::textarea('motivo', null, ['class' => 'form-control', 'rows' => 3, 'required' => true, 'placeholder' => 'Describa brevemente el motivo de la cita...']) !!}
            </div>

            {{-- 🔹 Ubicación --}}
            <div class="form-group col-sm-12">
                {!! Form::label('ubicacion', 'Ubicación:') !!}
                {!! Form::text('ubicacion', null, ['class' => 'form-control', 'maxlength' => 150, 'required' => true]) !!}
            </div>

            {{-- 🔹 Campo oculto para almacenar fechaHora combinada --}}
            {!! Form::hidden('fechaHora', null, ['id' => 'fechaHora']) !!}
        </div>

        <div class="card-footer text-right">
            {!! Form::submit('Guardar cita', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('medico.citas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

        {!! Form::close() !!}
    </div>
</div>

{{-- 🔹 Script para combinar fecha y hora antes del envío --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-cita');
    const fecha = document.getElementById('fecha');
    const hora = document.getElementById('hora');
    const fechaHora = document.getElementById('fechaHora');

    form.addEventListener('submit', function(e) {
        const selectedDate = fecha.value;
        const selectedTime = hora.value;

        if (!selectedDate || !selectedTime) {
            e.preventDefault();
            alert('Debe seleccionar una fecha y hora válidas.');
            return;
        }

        const now = new Date();
        const selectedDateTime = new Date(`${selectedDate}T${selectedTime}`);

        if (selectedDateTime < now) {
            e.preventDefault();
            alert('La fecha y hora seleccionadas no pueden ser anteriores al momento actual.');
            return;
        }

        fechaHora.value = `${selectedDate} ${selectedTime}:00`;
    });
});
</script>
@endsection
