<!-- Fkmedico Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fkMedico', 'Fkmedico:') !!}
    {!! Form::number('fkMedico', null, ['class' => 'form-control']) !!}
</div>

<!-- Fkpaciente Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fkPaciente', 'Fkpaciente:') !!}
    {!! Form::number('fkPaciente', null, ['class' => 'form-control']) !!}
</div>

<!-- Fechahora Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fechaHora', 'Fechahora:') !!}
    {!! Form::text('fechaHora', null, ['class' => 'form-control','id'=>'fechaHora']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#fechaHora').datepicker()
    </script>
@endpush

<!-- Motivo Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('motivo', 'Motivo:') !!}
    {!! Form::textarea('motivo', null, ['class' => 'form-control', 'required', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Ubicacion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ubicacion', 'Ubicacion:') !!}
    {!! Form::text('ubicacion', null, ['class' => 'form-control', 'required', 'maxlength' => 150, 'maxlength' => 150]) !!}
</div>

<!-- Estado Field -->
<div class="form-group col-sm-6">
    {!! Form::label('estado', 'Estado:') !!}
    {!! Form::text('estado', null, ['class' => 'form-control']) !!}
</div>