<!-- Nombre y Apellido -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $tutor->nombre }} {{ $tutor->apellido }}</p>
</div>

<!-- Parentesco -->
<div class="col-sm-12">
    {!! Form::label('parentesco', 'Parentesco:') !!}
    <p>{{ $tutor->parentesco }}</p>
</div>

<!-- Teléfono -->
<div class="col-sm-12">
    {!! Form::label('telefono', 'Teléfono:') !!}
    <p>{{ $tutor->telefono }}</p>
</div>

<!-- Correo -->
<div class="col-sm-12">
    {!! Form::label('correo', 'Correo electrónico:') !!}
    <p>{{ $tutor->correo }}</p>
</div>

<!-- Dirección -->
<div class="col-sm-12">
    {!! Form::label('direccion', 'Dirección:') !!}
    <p>{{ $tutor->direccion }}</p>
</div>

<!-- Observaciones -->
<div class="col-sm-12">
    {!! Form::label('observaciones', 'Observaciones:') !!}
    <p>{{ $tutor->observaciones }}</p>
</div>
