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

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Contraseña Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contrasena', 'Contraseña:') !!}
    {!! Form::password('contrasena', ['class' => 'form-control', 'required', 'minlength' => 6]) !!}
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

<!-- Sexo Field -->
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
<div class="form-group col-sm-6">
    {!! Form::label('tipoUsuario', 'Tipo de usuario:') !!}
    {!! Form::select(
        'tipoUsuario',
        ['administrador' => 'Administrador', 'medico' => 'Médico'],  {{-- ← solo estos --}}
        old('tipoUsuario', $usuario->tipoUsuario ?? null),
        [
            'class' => 'form-select',
            'id' => 'tipoUsuario',
            'placeholder' => 'Seleccione…',
        ]
    ) !!}
    @error('tipoUsuario')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


<!-- Campos exclusivos para médico -->
<div id="camposMedico" class="row" style="display:none;">
    <div class="form-group col-sm-6">
        {!! Form::label('cedulaProfesional', 'Cédula profesional:') !!}
        {!! Form::text('cedulaProfesional', old('cedulaProfesional', $usuario->medico->cedulaProfesional ?? null), [
            'class' => 'form-control' . ($errors->has('cedulaProfesional') ? ' is-invalid' : ''),
            'maxlength' => 20,
            'placeholder' => 'Ej. 1234567',
        ]) !!}
        @error('cedulaProfesional')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('especialidad', 'Especialidad:') !!}
        {!! Form::text('especialidad', old('especialidad', $usuario->medico->especialidad ?? null), [
            'class' => 'form-control' . ($errors->has('especialidad') ? ' is-invalid' : ''),
            'maxlength' => 100,
            'placeholder' => 'Ej. Psiquiatría',
        ]) !!}
        @error('especialidad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


<!-- Estado de Cuenta Field -->
<div class="form-group col-sm-6">
    {!! Form::label('estadoCuenta', 'Estado de Cuenta:') !!}
    {!! Form::select('estadoCuenta', [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo'
    ], null, ['class' => 'form-control', 'placeholder' => 'Seleccione estado']) !!}
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoUsuarioSelect = document.querySelector('#tipoUsuario');
    const camposMedico = document.querySelector('#camposMedico');

    if (!tipoUsuarioSelect || !camposMedico) return;

    const toggleCampos = () => {
        if (tipoUsuarioSelect.value === 'medico') {
            camposMedico.style.display = 'flex';
        } else {
            camposMedico.style.display = 'none';
        }
    };

    tipoUsuarioSelect.addEventListener('change', toggleCampos);
    toggleCampos(); // ejecutar al cargar (por si ya es médico)
});
</script>

