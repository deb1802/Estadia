<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-crud align-middle" id="usuarios-table">
            <thead class="table-primary text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th> {{-- antes: Correo --}}
                    {{-- Por seguridad no mostramos la contraseña --}}
                    <th>Fecha de Nacimiento</th>
                    <th>Sexo</th>
                    <th>Teléfono</th>
                    <th>Tipo de Usuario</th>
                    <th>Estado de Cuenta</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->apellido }}</td>
                        <td>{{ $usuario->email }}</td> {{-- cambiado correo → email --}}
                        <td>{{ $usuario->fechaNacimiento }}</td>
                        <td>{{ ucfirst($usuario->sexo) }}</td>
                        <td>{{ $usuario->telefono }}</td>
                        <td>
                            <span class="badge bg-info text-dark text-capitalize px-2 py-1">
                                {{ $usuario->tipoUsuario }}
                            </span>
                        </td>
                        <td>
                            @if($usuario->estadoCuenta === 'activo')
                                <span class="badge bg-success px-2 py-1">Activo</span>
                            @else
                                <span class="badge bg-danger px-2 py-1">Inactivo</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="text-center align-middle">
                            {!! Form::open([
                                'route' => ['admin.usuarios.destroy', $usuario->idUsuario],
                                'method' => 'delete',
                                'class' => 'form-delete d-inline'
                            ]) !!}
                            <div class="btn-group" role="group" aria-label="Acciones">
                                {{-- Ver --}}
                                <a href="{{ route('admin.usuarios.show', $usuario->idUsuario) }}" 
                                   class="btn btn-outline-info btn-action" 
                                   title="Ver usuario">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Editar --}}
                                <a href="{{ route('admin.usuarios.edit', $usuario->idUsuario) }}" 
                                   class="btn btn-outline-warning btn-action" 
                                   title="Editar usuario">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Eliminar --}}
                                <button type="button" 
                                        class="btn btn-outline-danger btn-action btn-delete" 
                                        title="Eliminar usuario">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            {{-- Paginación opcional --}}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
});
</script>
@endpush
