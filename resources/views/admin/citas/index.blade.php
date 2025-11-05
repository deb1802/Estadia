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
    min-width: 260px;
    border-radius: 10px;
    height: 42px;
  }
  .filter-select {
    width: 220px;
    border-radius: 10px;
    height: 42px;
  }
  .date-select {
    width: 170px;
    border-radius: 10px;
    height: 42px;
  }
</style>
@endpush

@section('content')
<section class="content-header text-center mb-4">
  <div class="container-fluid">
    <h1 class="fw-semibold text-primary">
      <i class="fas fa-calendar-check me-2"></i> Gestión General de Citas
    </h1>
  </div>
</section>

<div class="content px-3">

  {{-- 🔹 Notificación dinámica --}}
  @if(session('flash_notification'))
    @php
      $alert = session('flash_notification')->first();
      $type  = str_contains(strtolower($alert->level ?? ''), 'success') ? 'success'
               : (str_contains(strtolower($alert->level ?? ''), 'error') ? 'error'
               : (str_contains(strtolower($alert->level ?? ''), 'warning') ? 'warning' : 'info'));
    @endphp

    <div id="alerta-dinamica"
         class="alert alert-{{ $type }} text-center fw-semibold px-4 py-3 shadow-sm"
         style="max-width:700px;margin:10px auto;border-radius:10px;
                background:
                  {{ $type == 'success' ? 'linear-gradient(90deg,#b5f5d1,#c3f7ea)' :
                     ($type == 'error' ? 'linear-gradient(90deg,#f8caca,#f1a1a1)' :
                     ($type == 'warning' ? 'linear-gradient(90deg,#fff3cd,#ffeeba)' :
                     'linear-gradient(90deg,#dbeafe,#bfdbfe)')) }};">
      {{ strip_tags($alert->message ?? '') }}
    </div>

    <script>
      setTimeout(() => {
        const alerta = document.getElementById('alerta-dinamica');
        if(alerta){
          alerta.style.transition = 'opacity .6s ease';
          alerta.style.opacity = '0';
          setTimeout(() => alerta.remove(), 600);
        }
      }, 4000);
    </script>
  @endif

  {{-- 🔍 Barra de búsqueda --}}
  <div class="card-search shadow-sm mb-3">
    <div class="search-bar">

      <input type="text" id="search-input" class="form-control search-input" placeholder="Buscar citas...">

      <select id="filter-type" class="form-select filter-select">
        <option value="all">Buscar en todo</option>
        <option value="paciente">Por paciente</option>
        <option value="medico">Por médico</option>
        <option value="motivo">Por motivo</option>
        <option value="estado">Por estado</option>
      </select>

      <input type="date" id="filter-date" class="form-control date-select">
      <input type="time" id="filter-time" class="form-control date-select">

    </div>
  </div>

  {{-- 📋 Tabla --}}
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <table class="table table-striped align-middle mb-0" id="tabla-citas">
        <thead class="table-light">
          <tr>
            <th class="col-paciente">Paciente</th>
            <th class="col-medico">Médico</th>
            <th class="col-fecha">Fecha y Hora</th>
            <th class="col-motivo">Motivo</th>
            <th class="col-estado">Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>

        <tbody>
          @forelse($citas as $cita)
          <tr>
            <td class="col-paciente">{{ $cita->paciente_nombre }} {{ $cita->paciente_apellido }}</td>
            <td class="col-medico">{{ $cita->medico_nombre }} {{ $cita->medico_apellido }}</td>
            <td class="col-fecha">{{ \Carbon\Carbon::parse($cita->fechaHora)->format('Y-m-d H:i') }}</td>
            <td class="col-motivo">{{ $cita->motivo }}</td>
            <td class="col-estado">
              <span class="badge
                @if($cita->estado === 'programada') bg-primary
                @elseif($cita->estado === 'cancelada') bg-danger
                @elseif($cita->estado === 'realizada') bg-success
                @else bg-secondary @endif">
                {{ ucfirst($cita->estado) }}
              </span>
            </td>
            <td class="text-center">
              <a href="{{ route('admin.citas.show', $cita->idCita) }}" class="btn btn-sm btn-outline-primary me-1">
                <i class="fas fa-eye"></i>
              </a>

              <form action="{{ route('admin.citas.destroy', $cita->idCita) }}" method="POST" class="d-inline" onsubmit="return confirmarEliminacion(event)">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>

            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-3">No hay citas registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(event){
  event.preventDefault();
  const form = event.target;
  Swal.fire({
    title:'¿Eliminar cita?',
    text:'Se notificará al paciente.',
    icon:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d33',
    cancelButtonColor:'#6c757d',
    confirmButtonText:'Sí, eliminar'
  }).then(r=>{ if(r.isConfirmed) form.submit(); });
}

document.addEventListener("DOMContentLoaded", () => {
  const rows = document.querySelectorAll("#tabla-citas tbody tr");
  const input = document.getElementById("search-input");
  const type = document.getElementById("filter-type");
  const date = document.getElementById("filter-date");
  const time = document.getElementById("filter-time");

  function filtrar(){
    const q = input.value.toLowerCase();
    const t = type.value;
    const d = date.value;
    const h = time.value;

    rows.forEach(row => {
      const paciente = row.querySelector('.col-paciente').innerText.toLowerCase();
      const medico = row.querySelector('.col-medico').innerText.toLowerCase();
      const motivo = row.querySelector('.col-motivo').innerText.toLowerCase();
      const estado = row.querySelector('.col-estado').innerText.toLowerCase();
      const fecha = row.querySelector('.col-fecha').innerText;

      let show = true;

      if (t === "paciente" && !paciente.includes(q)) show = false;
      if (t === "medico" && !medico.includes(q)) show = false;
      if (t === "motivo" && !motivo.includes(q)) show = false;
      if (t === "estado" && !estado.includes(q)) show = false;
      if (t === "all" && !row.innerText.toLowerCase().includes(q)) show = false;

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
@endsection
