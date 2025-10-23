<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped" id="tutors-table">
    <thead>
        <tr>
            <th>Nombre completo</th>
            <th>Parentesco</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Dirección</th>
            <th>Observaciones</th>
            <th>Paciente</th>
            <th colspan="3">Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($tutors as $tutor)
        <tr>
            <td>{{ $tutor->nombreCompleto }}</td>
            <td>{{ $tutor->parentesco }}</td>
            <td>{{ $tutor->telefono }}</td>
            <td>{{ $tutor->correo }}</td>
            <td>{{ $tutor->direccion }}</td>
            <td>{{ $tutor->observaciones }}</td>

            {{-- Si ya definiste la relación en el modelo Tutor:
                 public function paciente() { return $this->belongsTo(Paciente::class, 'fkPaciente', 'id'); }
                 y en Paciente la relación usuario():
                 public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id', 'idUsuario'); }
                 Entonces puedes mostrar el nombre así: --}}
            <td>
    @if(!empty($tutor->paciente_nombre))
        {{ $tutor->paciente_nombre . ' ' . $tutor->paciente_apellido }}
    @else
        <em>Sin asignar</em>
    @endif
</td>



            <td style="width: 160px">
                <div class="btn-group" role="group" aria-label="Acciones">
                    <a href="{{ route('admin.tutores.show', $tutor->idTutor) }}" class="btn btn-default btn-xs">
                        <i class="far fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.tutores.edit', $tutor->idTutor) }}" class="btn btn-default btn-xs">
                        <i class="far fa-edit"></i>
                    </a>

                    {!! Form::open(['route' => ['admin.tutores.destroy', $tutor->idTutor], 'method' => 'delete', 'style' => 'display:inline']) !!}
                        {!! Form::button('<i class="far fa-trash-alt"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-danger btn-xs',
                            'onclick' => "return confirm('¿Seguro que deseas eliminar este tutor?')"
                        ]) !!}
                    {!! Form::close() !!}
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $tutors])
        </div>
    </div>
</div>
