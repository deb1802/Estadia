@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/crud-style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

<style>
  section.content-header { margin-top: -10px !important; padding-top: 5px !important; }

  /* ===== Barra de búsqueda ===== */
  .card-search{ background:#f8fbff; border:1px solid #e6eefc; border-radius:14px; }
  .search-bar{ display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .search-input-group{ display:flex; align-items:center; gap:10px; flex:1; min-width:260px; }
  .search-input-group input{ flex:1; border-radius:10px; height:44px; }
  .search-input-group select{ width:260px; border-radius:10px; height:44px; }

  /* ===== Botón agregar ===== */
  .btn-add {
    background-color: #6c63ff;
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 8px 14px;
    font-weight: 500;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn-add:hover { background-color: #5848e5; }

  /* ===== Mensajes Flash ===== */
  .alert {
    position: relative;
    margin: 1rem 0 1rem 1rem;
    max-width: 400px;
    border-radius: 6px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    font-weight: 500;
    font-size: 0.9rem;
    box-shadow: 0 2px 5px rgba(0,0,0,.05);
  }
  .alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
    border-left: 6px solid #198754;
  }
  .alert-info {
    background: #cff4fc;
    color: #055160;
    border: 1px solid #b6effb;
    border-left: 6px solid #0dcaf0;
  }
  .alert-danger, .alert-error {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
    border-left: 6px solid #dc3545;
  }
  .alert-warning {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
    border-left: 6px solid #ffc107;
  }
</style>
@endpush

@section('content')
<section class="content-header text-center mb-2">
  <div class="container-fluid">
    <h1 class="fw-semibold text-primary" data-aos="fade-down" style="font-size:2.2rem;">
      Gestión de Tutores
    </h1>
  </div>
</section>

<div class="content px-3">
  {{-- ✅ Mensajes Flash --}}
  @include('flash::message')

  {{-- 🔍 Barra de búsqueda dinámica --}}
  <div class="card card-body shadow-sm mb-3 card-search">
    <div class="search-bar">
      <div class="search-input-group">
        <input type="text" id="search-input" class="form-control"
               placeholder="Buscar tutores..." autocomplete="off">
        <select id="search-type" class="form-select">
          <option value="all">Buscar en todos</option>
          <option value="nombre">Por nombre del tutor</option>
          <option value="parentesco">Por parentesco</option>
          <option value="paciente">Por paciente</option>
        </select>
      </div>
    </div>
  </div>

  {{-- 📋 Tabla --}}
  <div class="card shadow-sm">
    <div id="table-container" class="card-body p-0">
      @include('admin.tutores.table')
    </div>
  </div>
</div>

{{-- ==== Scripts ==== --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script> AOS.init(); </script>

{{-- ✅ Desvanecer alertas --}}
<script>
(function() {
  const alerts = document.querySelectorAll('.alert');
  if (!alerts.length) return;
  setTimeout(() => {
    alerts.forEach(el => {
      el.style.transition = 'opacity .8s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 800);
    });
  }, 6000);
})();
</script>

{{-- ✅ Búsqueda dinámica en tiempo real --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById('search-input');
  const type = document.getElementById('search-type');
  const container = document.getElementById('table-container');
  const baseUrl = @json(route('admin.tutores.index'));
  let timeout = null;

  const performSearch = () => {
    const query = input.value.trim();
    const filter = type.value;
    const url = new URL(baseUrl);
    if (query !== '') url.searchParams.set('search', query);
    url.searchParams.set('type', filter);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.text())
      .then(html => container.innerHTML = html)
      .catch(err => console.error('Error al buscar tutores:', err));
  };

  input.addEventListener('input', () => {
    clearTimeout(timeout);
    timeout = setTimeout(performSearch, 300);
  });
  type.addEventListener('change', performSearch);
});
</script>

{{-- ✅ SweetAlert al eliminar --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('click', function (e) {
  const btn = e.target.closest('form.form-delete button[type="submit"]');
  if (!btn) return;

  e.preventDefault();
  const form = btn.closest('form.form-delete');
  if (!form) return;

  Swal.fire({
    title: '¿Eliminar tutor?',
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    reverseButtons: true,
    focusCancel: true
  }).then(result => {
    if (result.isConfirmed) {
      HTMLFormElement.prototype.submit.call(form);
    }
  });
}, true);
</script>
@endpush
@endsection
