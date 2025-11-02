<div class="card-body p-3">
    {{-- 🩺 Médico responsable --}}
    <h5 class="text-primary mb-3">
        <strong>Médico responsable:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
    </h5>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="citas-table">
            <thead class="table-light">
                <tr>
                    <th>Paciente</th>
                    <th>Fecha y hora</th>
                    <th>Motivo</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>

            <tbody>
            @foreach($citas as $cita)
                <tr>
                    {{-- 🔹 Nombre completo del paciente --}}
                    <td>
                        @if(!empty($cita->paciente_nombre))
                            {{ $cita->paciente_nombre . ' ' . $cita->paciente_apellido }}
                        @else
                            <em>Sin asignar</em>
                        @endif
                    </td>

                    {{-- 🔹 Fecha y hora formateadas --}}
                    <td>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</td>

                    <td>{{ $cita->motivo }}</td>
                    <td>{{ $cita->ubicacion }}</td>

                    {{-- 🔹 Estado visual --}}
                    <td>
                        @switch($cita->estado)
                            @case('programada')
                                <span class="badge bg-info text-dark">Programada</span>
                                @break
                            @case('realizada')
                                <span class="badge bg-success">Realizada</span>
                                @break
                            @case('cancelada')
                                <span class="badge bg-danger">Cancelada</span>
                                @break
                            @default
                                <span class="badge bg-secondary">Desconocido</span>
                        @endswitch
                    </td>

                    {{-- 🔹 Acciones --}}
                    <td>
                        <div class='btn-group' role='group'>
                            <a href="{{ route('medico.citas.show', $cita->idCita) }}"
                               class='btn btn-default btn-xs' title="Ver">
                                <i class="far fa-eye"></i>
                            </a>

                            <a href="{{ route('medico.citas.edit', $cita->idCita) }}"
                               class='btn btn-default btn-xs' title="Editar">
                                <i class="far fa-edit"></i>
                            </a>

                            {!! Form::open(['route' => ['medico.citas.destroy', $cita->idCita], 'method' => 'delete', 'style' => 'display:inline']) !!}
                                {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'onclick' => "return confirm('¿Seguro que deseas eliminar esta cita?')"
                                ]) !!}
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- 🔹 Paginación --}}
    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $citas])
        </div>
    </div>
</div>
