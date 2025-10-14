<div class="card-body p-0">
  {{-- Envoltura responsive + scroll con min-width y (opcional) sticky header --}}
  <div class="table-responsive table-scroll">
    <table class="table table-crud align-middle mb-0" id="pacientes-table">
      <thead class="table-primary text-center">
        <tr>
          <th>Nombre</th>
          <th>Apellido</th>
          <th class="td-email">Email</th>
          <th class="td-date">Fecha de Nacimiento</th>
          <th>Sexo</th>
          <th class="td-phone">Teléfono</th>
          <th>Tipo de Usuario</th>
          <th>Estado de Cuenta</th>
          <th class="text-center col-actions">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @foreach($pacientes as $paciente)
          @php $u = $paciente->usuario; @endphp
          <tr>
            <td>{{ $u->nombre ?? '—' }}</td>
            <td>{{ $u->apellido ?? '—' }}</td>
            <td class="td-email">{{ $u->correo ?? ($u->email ?? '—') }}</td>
            <td class="td-date">{{ $u->fechaNacimiento ?? '—' }}</td>
            <td>{{ isset($u->sexo) ? ucfirst($u->sexo) : '—' }}</td>
            <td class="td-phone">{{ $u->telefono ?? '—' }}</td>
            <td>
              <span class="badge bg-info text-dark text-capitalize px-2 py-1">paciente</span>
            </td>
            <td>
              @if(($u->estadoCuenta ?? 'activo') === 'activo')
                <span class="badge bg-success px-2 py-1">Activo</span>
              @else
                <span class="badge bg-danger px-2 py-1">Inactivo</span>
              @endif
            </td>

            {{-- Acciones --}}
            <td class="text-center align-middle col-actions">
              {!! Form::open([
                  'route' => ['medico.pacientes.destroy', $paciente->id],
                  'method' => 'delete',
                  'class' => 'form-delete d-inline'
              ]) !!}

              <div class="btn-group" role="group" aria-label="Acciones">
                {{-- Ver --}}
                <a href="{{ route('medico.pacientes.show', $paciente->id) }}"
                   class="btn btn-outline-info btn-action"
                   title="Ver paciente">
                  <i class="fas fa-eye"></i>
                </a>

                {{-- Editar --}}
                <a href="{{ route('medico.pacientes.edit', $paciente->id) }}"
                   class="btn btn-outline-warning btn-action"
                   title="Editar paciente">
                  <i class="fas fa-edit"></i>
                </a>

                {{-- Eliminar --}}
                <button type="button"
                        class="btn btn-outline-danger btn-action btn-delete"
                        title="Eliminar paciente">
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

  {{-- Paginación (si se usa paginate) --}}
  @if(method_exists($pacientes, 'links'))
    <div class="card-footer clearfix">
      <div class="float-right">
        {{ $pacientes->links() }}
      </div>
    </div>
  @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      if (!form) return;
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '¿Eliminar paciente?',
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((r) => r.isConfirmed && form.submit());
      } else {
        if (confirm('¿Eliminar paciente? Esta acción no se puede deshacer.')) form.submit();
      }
    });
  });
});
</script>
@endpush
