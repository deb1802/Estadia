<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped" id="expedientes-table">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Diagnóstico</th>
                    <th>Notas Clínicas</th>
                    <th>Última Actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expedientes as $exp)
                <tr>
                    <td>{{ $exp->nombre_paciente }}</td>
                    <td>{{ Str::limit($exp->diagnosticos, 50, '...') }}</td>
                    <td>{{ Str::limit($exp->notasClinicas, 50, '...') }}</td>
                    <td>{{ \Carbon\Carbon::parse($exp->fechaActualizacion)->format('d/m/Y') }}</td>

                    <td style="width: 140px">
                        <div class="btn-group" role="group" aria-label="Acciones">
                            <a href="{{ route('medico.expedientes.show', $exp->idExpediente) }}" class="btn btn-default btn-xs" title="Ver">
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('medico.expedientes.edit', $exp->idExpediente) }}" class="btn btn-default btn-xs" title="Editar">
                                <i class="far fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $expedientes])
        </div>
    </div>
</div>
