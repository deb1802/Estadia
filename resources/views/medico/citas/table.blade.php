<div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th>Paciente</th>
        <th>Fecha y hora</th>
        <th>Motivo</th>
        <th>Ubicación</th>
        <th>Estado</th>
        <th class="text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($citas as $cita)
      <tr>
        <td>{{ $cita->paciente_nombre }} {{ $cita->paciente_apellido }}</td>
        <td>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</td>
        <td>{{ $cita->motivo }}</td>
        <td>{{ $cita->ubicacion }}</td>
        <td>
          <span class="badge bg-{{ $cita->estado == 'programada' ? 'info' : ($cita->estado == 'atendida' ? 'success' : 'secondary') }}">
            {{ ucfirst($cita->estado) }}
          </span>
        </td>
        <td class="text-center">
          <a href="{{ route('medico.citas.show', $cita->idCita) }}" class="btn btn-default btn-xs"><i class="far fa-eye"></i></a>
          <a href="{{ route('medico.citas.edit', $cita->idCita) }}" class="btn btn-default btn-xs"><i class="far fa-edit"></i></a>
          {!! Form::open(['route' => ['medico.citas.destroy', $cita->idCita], 'method' => 'delete', 'class' => 'form-delete d-inline']) !!}
          <button type="submit" class="btn btn-danger btn-xs"><i class="far fa-trash-alt"></i></button>
          {!! Form::close() !!}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="card-footer clearfix">
  <div class="float-end">
    @include('adminlte-templates::common.paginate', ['records' => $citas])
  </div>
</div>
