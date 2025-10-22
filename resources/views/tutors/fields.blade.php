<!-- NombreCompleto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombreCompleto', 'Nombre completo:') !!}
    {!! Form::text('nombreCompleto', null, ['class' => 'form-control', 'maxlength' => 100]) !!}
    @error('nombreCompleto') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- Parentesco Field -->
<div class="form-group col-sm-6">
    {!! Form::label('parentesco', 'Parentesco:') !!}
    {!! Form::text('parentesco', null, ['class' => 'form-control', 'maxlength' => 50]) !!}
    @error('parentesco') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- Telefono Field -->
<div class="form-group col-sm-6">
    {!! Form::label('telefono', 'Teléfono:') !!}
    {!! Form::text('telefono', null, ['class' => 'form-control', 'maxlength' => 20]) !!}
    @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- Correo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('correo', 'Correo:') !!}
    {!! Form::email('correo', null, ['class' => 'form-control', 'maxlength' => 100]) !!}
    @error('correo') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- Direccion Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('direccion', 'Dirección:') !!}
    {!! Form::textarea('direccion', null, ['class' => 'form-control']) !!}
    @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- Observaciones Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('observaciones', 'Observaciones:') !!}
    {!! Form::textarea('observaciones', null, ['class' => 'form-control']) !!}
    @error('observaciones') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Paciente asignado (fkPaciente) --}}
<div class="form-group col-sm-6">
    {!! Form::label('fkPaciente', 'Paciente asignado:') !!}
    <select name="fkPaciente" id="fkPaciente" class="form-control" required>
        <option value="">Seleccione un paciente...</option>
        @foreach($pacientes as $pac)
            <option value="{{ $pac->paciente_id }}"
                {{ (old('fkPaciente', isset($tutor) ? $tutor->fkPaciente : null) == $pac->paciente_id) ? 'selected' : '' }}>
                {{ $pac->display_name }}
            </option>
        @endforeach
    </select>
    @error('fkPaciente') <small class="text-danger">{{ $message }}</small> @enderror
</div>
