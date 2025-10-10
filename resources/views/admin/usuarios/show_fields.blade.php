<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $usuario->nombre }}</p>
</div>

<!-- Apellido Field -->
<div class="col-sm-12">
    {!! Form::label('apellido', 'Apellido:') !!}
    <p>{{ $usuario->apellido }}</p>
</div>

<!-- Email Field (antes era Correo) -->
<div class="col-sm-12">
    {!! Form::label('email', 'Email:') !!}
    <p>{{ $usuario->email }}</p>
</div>

<!-- Contraseña Field -->
<div class="col-sm-12">
    {!! Form::label('contrasena', 'Contraseña:') !!}
    <p>{{ $usuario->contrasena }}</p>
</div>

<!-- Fecha de Nacimiento Field -->
<div class="col-sm-12">
    {!! Form::label('fechaNacimiento', 'Fecha de Nacimiento:') !!}
    <p>{{ $usuario->fechaNacimiento }}</p>
</div>

<!-- Sexo Field -->
<div class="col-sm-12">
    {!! Form::label('sexo', 'Sexo:') !!}
    <p>{{ ucfirst($usuario->sexo) }}</p>
</div>

<!-- Teléfono Field -->
<div class="col-sm-12">
    {!! Form::label('telefono', 'Teléfono:') !!}
    <p>{{ $usuario->telefono }}</p>
</div>

<!-- Tipo de Usuario Field -->
<div class="col-sm-12">
    {!! Form::label('tipoUsuario', 'Tipo de Usuario:') !!}
    <p>{{ ucfirst($usuario->tipoUsuario) }}</p>
</div>

<!-- Estado de Cuenta Field -->
<div class="col-sm-12">
    {!! Form::label('estadoCuenta', 'Estado de Cuenta:') !!}
    <p>
        <span class="badge {{ $usuario->estadoCuenta === 'activo' ? 'bg-success' : 'bg-danger' }}">
            {{ ucfirst($usuario->estadoCuenta) }}
        </span>
    </p>
</div>
