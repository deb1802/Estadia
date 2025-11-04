<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped" id="tutors-table">
            <thead>
                <tr>
                    <th>Nombre</th>
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
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('admin.tutores.show', $tutor->idTutor) }}" class="btn btn-default btn-xs" title="Ver tutor">
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.tutores.edit', $tutor->idTutor) }}" class="btn btn-default btn-xs" title="Editar tutor">
                                <i class="far fa-edit"></i>
                            </a>

                            {{-- 🔹 Formulario de eliminación con SweetAlert --}}
                            {!! Form::open(['route' => ['admin.tutores.destroy', $tutor->idTutor], 'method' => 'delete', 'class' => 'form-delete d-inline']) !!}
                                <button type="button" class="btn btn-danger btn-xs btn-delete" title="Eliminar tutor">
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

{{-- 🔸 Script de SweetAlert2 --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.form-delete');

    deleteForms.forEach(form => {
        form.querySelector('.btn-delete').addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Eliminar tutor?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
