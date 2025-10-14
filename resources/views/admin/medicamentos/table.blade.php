<div class="card-body p-0">
  {{-- Envoltura responsive con scroll horizontal --}}
  <div class="table-responsive table-scroll">
    <table class="table table-crud align-middle mb-0" id="medicamentos-table">
      <thead class="table-primary text-center">
        <tr>
          <th>Nombre</th>
          <th>Presentación</th>
          <th class="td-wrap">Indicaciones</th>
          <th class="td-wrap">Efectos secundarios</th>
          <th>Imagen</th>
          <th class="text-center col-actions" style="width:160px">Acciones</th>
        </tr>
      </thead>

      <tbody>
      @foreach($medicamentos as $medicamento)
        <tr>
          <td>{{ $medicamento->nombre }}</td>
          <td>{{ $medicamento->presentacion }}</td>
          <td class="td-wrap">{{ $medicamento->indicaciones }}</td>
          <td class="td-wrap">{{ $medicamento->efectosSecundarios }}</td>
          <td>
            @if($medicamento->imagenMedicamento)
              <img src="{{ asset('storage/'.$medicamento->imagenMedicamento) }}"
                   alt="img" class="img-thumbnail" style="max-width:80px;">
            @else
              <span class="text-muted">—</span>
            @endif
          </td>

          <td class="text-center align-middle col-actions">
            <div class="btn-group btn-group-sm" role="group">
              {{-- Ver --}}
              <a class="btn btn-outline-info"
                 href="{{ route('admin.medicamentos.show', $medicamento->idMedicamento) }}"
                 title="Ver"><i class="fas fa-eye"></i></a>

              {{-- Editar --}}
              <a class="btn btn-outline-warning"
                 href="{{ route('admin.medicamentos.edit', $medicamento->idMedicamento) }}"
                 title="Editar"><i class="fas fa-edit"></i></a>

              {{-- Eliminar con modal SweetAlert --}}
              {!! Form::open([
                    'route' => ['admin.medicamentos.destroy', $medicamento->idMedicamento],
                    'method' => 'delete',
                    'class' => 'd-inline form-delete'
                ]) !!}
                <button type="button"
                        class="btn btn-outline-danger btn-delete"
                        title="Eliminar medicamento">
                  <i class="fas fa-trash-alt"></i>
                </button>
              {!! Form::close() !!}
            </div>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    {{ $medicamentos->links() }}
  </div>
</div>

{{-- === Modal SweetAlert para eliminar === --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      if (!form) return;

      Swal.fire({
        title: '¿Eliminar medicamento?',
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
