<!-- Fkmedico Field -->
<div class="col-sm-12">
    {!! Form::label('fkMedico', 'Fkmedico:') !!}
    <p>{{ $cita->fkMedico }}</p>
</div>

<!-- Fkpaciente Field -->
<div class="col-sm-12">
    {!! Form::label('fkPaciente', 'Fkpaciente:') !!}
    <p>{{ $cita->fkPaciente }}</p>
</div>

<!-- Fechahora Field -->
<div class="col-sm-12">
    {!! Form::label('fechaHora', 'Fechahora:') !!}
    <p>{{ $cita->fechaHora }}</p>
</div>

<!-- Motivo Field -->
<div class="col-sm-12">
    {!! Form::label('motivo', 'Motivo:') !!}
    <p>{{ $cita->motivo }}</p>
</div>

<!-- Ubicacion Field -->
<div class="col-sm-12">
    {!! Form::label('ubicacion', 'Ubicacion:') !!}
    <p>{{ $cita->ubicacion }}</p>
</div>

<!-- Estado Field -->
<div class="col-sm-12">
    {!! Form::label('estado', 'Estado:') !!}
    <p>{{ $cita->estado }}</p>
</div>

