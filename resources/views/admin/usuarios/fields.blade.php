@php
    use Illuminate\Support\Carbon;
@endphp

{{-- Nombre --}}
<div class="form-group col-sm-6">
    {!! Form::label('nombre', 'Nombre:') !!}
    {!! Form::text('nombre', old('nombre', $usuario->nombre ?? null), [
        'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
        'maxlength' => 50,
        'autocomplete' => 'given-name',
    ]) !!}
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Apellido --}}
<div class="form-group col-sm-6">
    {!! Form::label('apellido', 'Apellido:') !!}
    {!! Form::text('apellido', old('apellido', $usuario->apellido ?? null), [
        'class' => 'form-control' . ($errors->has('apellido') ? ' is-invalid' : ''),
        'maxlength' => 50,
        'autocomplete' => 'family-name',
    ]) !!}
    @error('apellido')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Email Field (antes era Correo) -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Contraseña Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contrasena', 'Contraseña:') !!}
    {!! Form::password('contrasena', ['class' => 'form-control', 'required', 'minlength' => 6]) !!}
</div>

<!-- Fecha de Nacimiento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fechaNacimiento', 'Fecha de Nacimiento:') !!}
    {!! Form::text('fechaNacimiento', null, ['class' => 'form-control', 'id' => 'fechaNacimiento']) !!}
</div>

{{-- Fecha de nacimiento --}}
<div class="form-group col-sm-6">
    {!! Form::label('fechaNacimiento', 'Fecha de nacimiento:') !!}
    {!! Form::date(
        'fechaNacimiento',
        old(
            'fechaNacimiento',
            isset($usuario) && $usuario->fechaNacimiento
                ? Carbon::parse($usuario->fechaNacimiento)->format('Y-m-d')
                : null
        ),
        [
            'class' => 'form-control' . ($errors->has('fechaNacimiento') ? ' is-invalid' : ''),
            'max' => now()->format('Y-m-d'),
        ]
    ) !!}
    @error('fechaNacimiento')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Formato: AAAA-MM-DD</small>
</div>

<<<<<<< HEAD
<!-- Sexo Field -->
=======
{{-- Sexo --}}
>>>>>>> upstream/main
<div class="form-group col-sm-6">
    {!! Form::label('sexo', 'Sexo:') !!}
    {!! Form::select(
        'sexo',
        ['masculino' => 'Masculino', 'femenino' => 'Femenino', 'otro' => 'Otro'],
        old('sexo', $usuario->sexo ?? null),
        [
            'class' => 'form-select' . ($errors->has('sexo') ? ' is-invalid' : ''),
            'placeholder' => 'Seleccione…',
        ]
    ) !!}
    @error('sexo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<<<<<<< HEAD
<!-- Teléfono Field -->
<div class="form-group col-sm-6">
    {!! Form::label('telefono', 'Teléfono:') !!}
    {!! Form::text('telefono', null, ['class' => 'form-control']) !!}
</div>

<!-- Tipo de Usuario Field -->
=======
{{-- Teléfono --}}
<div class="form-group col-sm-6">
    {!! Form::label('telefono', 'Teléfono:') !!}
    {!! Form::text('telefono', old('telefono', $usuario->telefono ?? null), [
        'class' => 'form-control' . ($errors->has('telefono') ? ' is-invalid' : ''),
        'maxlength' => 10,
        'inputmode' => 'numeric', // guía para móviles
        'autocomplete' => 'tel',
    ]) !!}
    @error('telefono')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">10 dígitos sin espacios ni guiones.</small>
</div>

{{-- Tipo de usuario --}}
>>>>>>> upstream/main
<div class="form-group col-sm-6">
    {!! Form::label('tipoUsuario', 'Tipo de usuario:') !!}
    {!! Form::select(
        'tipoUsuario',
        ['administrador' => 'Administrador', 'paciente' => 'Paciente'],
        old('tipoUsuario', $usuario->tipoUsuario ?? null),
        [
            'class' => 'form-select' . ($errors->has('tipoUsuario') ? ' is-invalid' : ''),
            'placeholder' => 'Seleccione…',
        ]
    ) !!}
    @error('tipoUsuario')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<<<<<<< HEAD
<!-- Estado de Cuenta Field -->
=======


>>>>>>> upstream/main
<div class="form-group col-sm-6">
    {!! Form::label('estadoCuenta', 'Estado de Cuenta:') !!}
    {!! Form::select('estadoCuenta', [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo'
    ], null, ['class' => 'form-control', 'placeholder' => 'Seleccione estado']) !!}
</div>
