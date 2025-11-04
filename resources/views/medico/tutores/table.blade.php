<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped" id="tutors-table">
            <thead>
                <tr>
                    <th>Nombre completo</th>
                    <th>Parentesco</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Paciente</th>
                    <th class="text-center" colspan="3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($tutors as $tutor)
                <tr>
                    <td>{{ $tutor->nombre }} {{ $tutor->apellido }}</td>
                    <td>{{ $tutor->parentesco }}</td>
                    <td>{{ $tutor->telefono }}</td>
                    <td>{{ $tutor->correo }}</td>
                    <td>{{ $tutor->paciente_nombre }} {{ $tutor->paciente_apellido }}</td>

                    <td class="text-center" style="width: 160px;">
                        <div class="btn-group">
                            {{-- Ver --}}
                            <a href="{{ route('medico.tutores.show', $tutor->idTutor) }}" class="btn btn-default btn-xs" title="Ver detalles">
                                <i class="far fa-eye"></i>
                            </a>

                            {{-- Editar --}}
                            <a href="{{ route('medico.tutores.edit', $tutor->idTutor) }}" class="btn btn-default btn-xs" title="Editar tutor">
                                <i class="far fa-edit"></i>
                            </a>

                            {{-- Eliminar con SweetAlert --}}
                            {!! Form::open([
                                'route'  => ['medico.tutores.destroy', $tutor->idTutor],
                                'method' => 'delete',
                                'class'  => 'form-delete d-inline'
                            ]) !!}
                                <button type="button"
                                        class="btn btn-danger btn-xs btn-delete"
                                        title="Eliminar tutor">
                                    <i class="far fa-trash-alt"></i>
                                </button>
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

{{-- 🔔 Script de confirmación SweetAlert2 --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');

            Swal.fire({
                title: '¿Eliminar tutor?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
