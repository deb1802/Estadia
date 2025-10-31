@push('modals')
  {{-- Modal DETALLE del test (vacío; lo llenamos por AJAX) --}}
  <div class="modal fade" id="dbnDetalleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title">Detalle del test</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0"><!-- aquí inyectamos el HTML via AJAX --></div>
      </div>
    </div>
  </div>
@endpush
