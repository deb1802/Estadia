@extends('layouts.app')

@section('content')
<section class="content-header mb-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h1 class="fw-semibold text-primary m-0">
                <i class="fas fa-calendar-plus me-2"></i> Programar nueva cita
            </h1>
        </div>
        <div>
            <a href="{{ route('medico.citas.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card shadow-sm border-0 rounded-4 p-4">
        {!! Form::open(['route' => 'medico.citas.store', 'method' => 'POST', 'id' => 'form-cita']) !!}

        <div class="row g-4">
            {{-- 🔹 Médico autenticado --}}
            <div class="form-group col-12">
                <label class="fw-semibold text-secondary">Médico:</label>
                <p class="fs-5 text-dark mb-1">
                    {{ Auth::user()->nombre ?? Auth::user()->name }} {{ Auth::user()->apellido ?? '' }}
                </p>
                {!! Form::hidden('fkMedico', $medico->id) !!}

            </div>

            {{-- 🔹 Paciente --}}
            <div class="form-group col-md-6">
                {!! Form::label('fkPaciente', 'Paciente:', ['class' => 'fw-semibold text-secondary']) !!}
                <select name="fkPaciente" id="fkPaciente" class="form-select shadow-sm" required>
                    <option value="">Seleccione un paciente...</option>
                    @foreach($pacientes as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 🔹 Fecha --}}
            <div class="form-group col-md-3">
                {!! Form::label('fecha', 'Fecha de la cita:', ['class' => 'fw-semibold text-secondary']) !!}
                <input type="date" id="fecha" class="form-control shadow-sm" required min="{{ date('Y-m-d') }}">
            </div>

            {{-- 🔹 Hora --}}
            <div class="form-group col-md-3">
                {!! Form::label('hora', 'Hora de la cita:', ['class' => 'fw-semibold text-secondary']) !!}
                <input type="time" id="hora" class="form-control shadow-sm" required>
            </div>

            {{-- 🔹 Motivo --}}
            <div class="form-group col-12">
                {!! Form::label('motivo', 'Motivo de la cita:', ['class' => 'fw-semibold text-secondary']) !!}
                {!! Form::textarea('motivo', null, [
                    'class' => 'form-control shadow-sm',
                    'rows' => 3,
                    'required' => true,
                    'placeholder' => 'Describa brevemente el motivo de la cita...'
                ]) !!}
            </div>

            {{-- 🔹 Ubicación --}}
            <div class="form-group col-12">
                {!! Form::label('ubicacion', 'Ubicación:', ['class' => 'fw-semibold text-secondary']) !!}
                {!! Form::text('ubicacion', null, [
                    'class' => 'form-control shadow-sm',
                    'maxlength' => 150,
                    'required' => true,
                    'placeholder' => 'Ejemplo: Consultorio 3, Edificio A'
                ]) !!}
            </div>

            {!! Form::hidden('fechaHora', null, ['id' => 'fechaHora']) !!}
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i> Guardar cita
            </button>
            <a href="{{ route('medico.citas.index') }}" class="btn btn-outline-secondary ms-2">
                Cancelar
            </a>
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

{{-- ✅ Barra inferior de notificaciones --}}
@include('medico.bottom-navbar')
@endsection
