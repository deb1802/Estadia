@extends('layouts.app')

@push('styles')
<style>
  /* ==== Barra de búsqueda === */
  .card-search {
    background: #f8fbff;
    border: 1px solid #e6eefc;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 18px;
  }
  .search-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: center;
  }
  .search-input {
    flex: 1;
    min-width: 240px;
    border-radius: 10px;
    height: 42px;
  }
  .filter-select {
    width: 190px;
    border-radius: 10px;
    height: 42px;
  }
  .date-select {
    width: 160px;
    border-radius: 10px;
    height: 42px;
  }
</style>
@endpush

@section('content')
<section class="content-header text-center mb-3">
  <div class="container-fluid">
    <h1 class="fw-semibold text-primary">Mis Citas</h1>
  </div>
</section>

<div class="content px-3">

  {{-- ✅ Flash dinámico con SweetAlert --}}
  @if(session('flash_notification'))
    @foreach(session('flash_notification') as $msg)
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          Swal.fire({
            icon: "{{ $msg['level'] === 'danger' ? 'error' : ($msg['level'] === 'success' ? 'success' : 'info') }}",
            title: @json($msg['message']),
            showConfirmButton: false,
            timer: 2500,
            toast: true,
            position: 'top-end'
          });
        });
      </script>
    @endforeach
  @endif

  {{-- 🔍 Barra de búsqueda --}}
  <div class="card-search shadow-sm">
    <div class="search-bar">
      
      <input type="text" id="search-input" class="form-control search-input" placeholder="Buscar citas...">

      <select id="filter-type" class="form-select filter-select">
        <option value="all">Buscar en todo</option>
        <option value="medico">Por médico</option>
        <option value="motivo">Por motivo</option>
        <option value="estado">Por estado</option>
      </select>

      {{-- Filtros de fecha --}}
      <input type="date" id="filter-date" class="form-control date-select">
      <input type="time" id="filter-time" class="form-control date-select">

    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">

      <table class="table table-striped align-middle mb-0" id="tabla-citas">
        <thead class="table-light">
          <tr>
            <th>Médico</th>
            <th>Fecha</th>
            <th>Motivo</th>
            <th>Ubicación</th>
            <th>Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($citas as $cita)
          <tr>
            <td class="col-medico">{{ $cita->medico_nombre }} {{ $cita->medico_apellido }}</td>
            <td class="col-fecha">{{ \Carbon\Carbon::parse($cita->fechaHora)->format('Y-m-d H:i') }}</td>
            <td class="col-motivo">{{ $cita->motivo }}</td>
            <td>{{ $cita->ubicacion }}</td>

            <td class="col-estado">
              @if($cita->estado === 'cancelada')
                <span class="badge bg-danger">Cancelada</span>
              @elseif($cita->estado === 'programada')
                <span class="badge bg-info">Programada</span>
              @else
                <span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span>
              @endif
            </td>

            <td class="text-center">
              @if($cita->estado === 'programada')
              <a href="{{ route('paciente.citas.cancelar', $cita->idCita) }}" 
                 class="btn btn-outline-danger btn-sm"
                 onclick="confirmarCancelacion(event, this)">
                <i class="bi bi-x-circle"></i> Cancelar
              </a>
              @else
                —
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
</div>

@include('paciente.bottom-nabvar')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function confirmarCancelacion(event, link) {
    event.preventDefault();
    Swal.fire({
      title: '¿Cancelar cita?',
      text: 'Se notificará al médico.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, cancelar'
    }).then(result => {
      if (result.isConfirmed) window.location.href = link.href;
    });
  }

  // ✅ Búsqueda y filtros en vivo
  document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById('search-input');
    const type = document.getElementById('filter-type');
    const date = document.getElementById('filter-date');
    const time = document.getElementById('filter-time');
    const rows = document.querySelectorAll("#tabla-citas tbody tr");

    function filtrar() {
      const q = input.value.toLowerCase();
      const t = type.value;
      const d = date.value;
      const h = time.value;

      rows.forEach(row => {
        let show = true;

        const medico = row.querySelector('.col-medico').innerText.toLowerCase();
        const motivo = row.querySelector('.col-motivo').innerText.toLowerCase();
        const estado = row.querySelector('.col-estado').innerText.toLowerCase();
        const fecha = row.querySelector('.col-fecha').innerText;

        if (t === "medico" && !medico.includes(q)) show = false;
        else if (t === "motivo" && !motivo.includes(q)) show = false;
        else if (t === "estado" && !estado.includes(q)) show = false;
        else if (t === "all" && !row.innerText.toLowerCase().includes(q)) show = false;

        if (d && !fecha.includes(d)) show = false;
        if (h && !fecha.includes(h)) show = false;

        row.style.display = show ? "" : "none";
      });
    }

    input.addEventListener("input", filtrar);
    type.addEventListener("change", filtrar);
    date.addEventListener("change", filtrar);
    time.addEventListener("change", filtrar);
  });
</script>
@endpush
