<!-- Fkpaciente Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fkPaciente', 'Fkpaciente:') !!}
    {!! Form::number('fkPaciente', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Antecedentes Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('antecedentes', 'Antecedentes:') !!}
    {!! Form::textarea('antecedentes', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Diagnosticos Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('diagnosticos', 'Diagnosticos:') !!}
    {!! Form::textarea('diagnosticos', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Notasclinicas Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('notasClinicas', 'Notasclinicas:') !!}
    {!! Form::textarea('notasClinicas', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Historialcitas Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('historialCitas', 'Historialcitas:') !!}
    {!! Form::textarea('historialCitas', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Testsaplicados Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('testsAplicados', 'Testsaplicados:') !!}
    {!! Form::textarea('testsAplicados', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Actividadesasignadas Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('actividadesAsignadas', 'Actividadesasignadas:') !!}
    {!! Form::textarea('actividadesAsignadas', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Respuestasemocionales Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('respuestasEmocionales', 'Respuestasemocionales:') !!}
    {!! Form::textarea('respuestasEmocionales', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Medicamentosprescritos Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('medicamentosPrescritos', 'Medicamentosprescritos:') !!}
    {!! Form::textarea('medicamentosPrescritos', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Observaciones Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('observaciones', 'Observaciones:') !!}
    {!! Form::textarea('observaciones', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!-- Fechaactualizacion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fechaActualizacion', 'Fechaactualizacion:') !!}
    {!! Form::text('fechaActualizacion', null, ['class' => 'form-control','id'=>'fechaActualizacion']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#fechaActualizacion').datepicker()
    </script>
@endpush