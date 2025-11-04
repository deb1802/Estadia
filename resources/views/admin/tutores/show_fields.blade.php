<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $tutor->nombre }} {{ $tutor->apellido }}</p>
</div>

<div class="col-sm-12">
    {!! Form::label('parentesco', 'Parentesco:') !!}
    <p>{{ $tutor->parentesco }}</p>
</div>

<div class="col-sm-12">
    {!! Form::label('telefono', 'Teléfono:') !!}
    <p>{{ $tutor->telefono }}</p>
</div>

<div class="col-sm-12">
    {!! Form::label('correo', 'Correo electrónico:') !!}
    <p>{{ $tutor->correo }}</p>
</div>

<div class="col-sm-12">
    {!! Form::label('direccion', 'Dirección:') !!}
    <p>{{ $tutor->direccion }}</p>
</div>

<div class="col-sm-12">
    {!! Form::label('observaciones', 'Observaciones:') !!}
    <p>{{ $tutor->observaciones }}</p>
</div>
