@php
  // Detecta si la URL pertenece a médico o admin
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
  $dashboardRoute = $routeArea.'dashboard';
@endphp
@php use Illuminate\Support\Str; @endphp


@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    /* ===== Botón suave reutilizable (Volver) ===== */
    :root{
      --g-text:#374151; --g-text-strong:#111827;
      --g-borde:#d1d5db; --g-borde-2:#9ca3af;
      --g-bg:#ffffff; --g-bg-hover:#f3f4f6;
    }
    .btn-soft{
      background: var(--g-bg);
      border: 1px solid var(--g-borde);
      color: var(--g-text);
      border-radius: 50px;
      font-weight: 500;
      padding: .5rem 1.25rem;
      transition: all .25s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,.04);
      margin-top: 6px;
    }
    .btn-soft:hover{
      background: var(--g-bg-hover);
      border-color: var(--g-borde-2);
      color: var(--g-text-strong);
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0,0,0,.08);
    }
    .btn-soft:active{ transform: scale(.98); box-shadow: 0 2px 6px rgba(0,0,0,.06); }
    .btn-soft i{ font-size:1rem; vertical-align:middle; }

    /* ===== Barra de búsqueda ===== */
    .card-search{ background:#f8fbff; border:1px solid #e6eefc; border-radius:14px; }
    .search-bar{ display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .search-input-group{ display:flex; align-items:center; gap:10px; flex:1; min-width:260px; }
    .search-input-group input{ flex:1; border-radius:10px; height:44px; }
    .search-input-group select{ width:260px; border-radius:10px; height:44px; }
    .search-actions{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  </style>
@endpush

@section('content')
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2 align-items-start">
      <div class="col-sm-6">
        <h1>Medicamentos</h1>
      </div>
      <div class="col-sm-6 d-flex justify-content-end gap-2 flex-wrap">
        {{-- 🔙 Volver directo al dashboard según área (sin history.back) --}}
        <a href="{{ route($dashboardRoute) }}" class="btn btn-soft">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </a>

        {{-- Botón de crear (según policy) --}}
        @can('create', App\Models\Medicamento::class)
          <a class="btn btn-primary" href="{{ route($routeArea.'medicamentos.create') }}">
            <i class="fas fa-plus"></i> Crear Nuevo Medicamento
          </a>
        @endcan
      </div>
    </div>
  </div>
</section>

<div class="content px-3">
  @include('flash::message')
  {{-- Estilos y auto-cierre para alerts (Laracasts o session) --}}
<style>
  /* ==== Estilo compacto alineado a la izquierda ==== */
  .alert {
    position: relative;
    margin: 1rem 0 1rem 1rem;   /* se separa del borde izquierdo */
    max-width: 400px;           /* tamaño reducido */
    border-radius: 6px;
    padding: 8px 12px;          /* más compacto */
    display: flex;
    align-items: center;
    justify-content: flex-start; /* alinea contenido a la izquierda */
    font-weight: 500;
    font-size: 0.9rem;
    box-shadow: 0 2px 5px rgba(0,0,0,.05);
  }

  /* Éxito */
  .alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
    border-left: 6px solid #198754;
  }

  /* Info */
  .alert-info {
    background: #cff4fc;
    color: #055160;
    border: 1px solid #b6effb;
    border-left: 6px solid #0dcaf0;
  }

  /* Error / Danger */
  .alert-danger, .alert-error {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
    border-left: 6px solid #dc3545;
  }

  /* Warning */
  .alert-warning {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
    border-left: 6px solid #ffc107;
  }

  /* Ícono dentro del alert */
  .alert i {
    font-size: 1rem;
    margin-right: 8px;
  }
</style>


<script>
  // Desvanece y remueve cualquier .alert (success/info/warn/error) a los 6s
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


  {{-- 🔍 Barra de búsqueda (siempre visible para admin y médico) --}}
  <div class="card card-body shadow-sm mb-3 card-search">
    <form id="search-form" method="GET" action="{{ route($routeArea.'medicamentos.index') }}" class="search-bar">
      <div class="search-input-group">
        <input type="text" id="search-input" name="q"
               class="form-control"
               value="{{ old('q', $q ?? request('q')) }}"
               placeholder="Buscar medicamentos..."
               autocomplete="off" aria-label="Buscar medicamentos">

        <select id="search-type" name="type" class="form-select" aria-label="Tipo de búsqueda">
          @php $typeVal = $type ?? request('type', 'all'); @endphp
          <option value="all" {{ $typeVal==='all' ? 'selected' : '' }}>🔎 Buscar en todos</option>
          <option value="nombre" {{ $typeVal==='nombre' ? 'selected' : '' }}>Por nombre</option>
          <option value="presentacion" {{ $typeVal==='presentacion' ? 'selected' : '' }}>Por presentación</option>
          <option value="indicaciones" {{ $typeVal==='indicaciones' ? 'selected' : '' }}>Por indicaciones</option>
          <option value="efectosSecundarios" {{ $typeVal==='efectosSecundarios' ? 'selected' : '' }}>Por efectos secundarios</option>
        </select>
      </div>

      <div class="search-actions">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-search me-1"></i> Buscar
        </button>
        @if(request()->filled('q') || request()->filled('type'))
          <a href="{{ route($routeArea.'medicamentos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i> Limpiar
          </a>
        @endif
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped" id="medicamentos-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Presentación</th>
              <th>Indicaciones</th>
              <th>Efectos Secundarios</th>
              <th>Imagen</th>
              <th style="width: 180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($medicamentos as $medicamento)
              <tr>
                <td>{{ $medicamento->nombre }}</td>
                <td>{{ $medicamento->presentacion }}</td>
                <td>{{ Str::limit($medicamento->indicaciones, 60) }}</td>
                <td>{{ Str::limit($medicamento->efectosSecundarios, 60) }}</td>
                <td>
                  @if($medicamento->imagenMedicamento)
                    <img src="{{ asset('storage/' . $medicamento->imagenMedicamento) }}"
                        alt="Imagen" width="60" height="60" class="rounded">
                  @else
                    <span class="text-muted">Sin imagen</span>
                  @endif
                </td>

                <td class="text-center align-middle">
                  <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                    {{-- Ver --}}
                    @can('view', $medicamento)
                      <a href="{{ route($routeArea.'medicamentos.show', $medicamento->idMedicamento) }}"
                        class="btn btn-outline-info"
                        title="Ver medicamento">
                        <i class="fas fa-eye"></i>
                      </a>
                    @endcan

                    {{-- Editar --}}
                    @can('update', $medicamento)
                      <a href="{{ route($routeArea.'medicamentos.edit', $medicamento->idMedicamento) }}"
                        class="btn btn-outline-warning"
                        title="Editar medicamento">
                        <i class="fas fa-edit"></i>
                      </a>
                    @endcan

                    {{-- Eliminar --}}
                    @can('delete', $medicamento)
                      {!! Form::open([
                        'route'  => [$routeArea.'medicamentos.destroy', $medicamento->idMedicamento],
                        'method' => 'delete',
                        'class'  => 'form-delete d-inline'
                      ]) !!}
                        <button type="submit"
                                class="btn btn-outline-danger btn-delete"
                                title="Eliminar medicamento">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      {!! Form::close() !!}
                    @endcan
                  </div>
                </td>
              </tr>

            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@if (request()->is('medico/*'))
  @include('medico.bottom-navbar')
@endif

@endsection

@push('scripts')
<script>
  // Debounce simple
  const debounce = (fn, delay = 450) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); }; };

  (function(){
    const form   = document.getElementById('search-form');
    if(!form) return;
    const input  = document.getElementById('search-input');
    const select = document.getElementById('search-type');

    const autoSubmit = debounce(() => {
      if (input.value.trim() === '' && select.value === 'all') return;
      form.requestSubmit();
    }, 450);

    input.addEventListener('keyup', autoSubmit);
    select.addEventListener('change', () => form.requestSubmit());

    const placeholders = {
      all: 'Buscar por nombre, presentación, indicaciones o efectos…',
      nombre: 'Ej. Paracetamol',
      presentacion: 'Ej. Tabletas 500mg',
      indicaciones: 'Ej. dolor, fiebre...',
      efectosSecundarios: 'Ej. náusea, somnolencia...'
    };
    const setPh = () => { input.placeholder = placeholders[select.value] || placeholders.all; };
    setPh(); select.addEventListener('change', setPh);
  })();
</script>
@endpush


@push('scripts')
  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  // Delegación: muestra el modal ANTES de enviar el form de borrar
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('form.form-delete button[type="submit"]');
    if (!btn) return;

    e.preventDefault(); // evita que se envíe de inmediato
    const form = btn.closest('form.form-delete');
    if (!form) return;

    Swal.fire({
      title: '¿Eliminar medicamento?',
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
        // Envío nativo (evita otros handlers que podrían reinterceptar)
        HTMLFormElement.prototype.submit.call(form);
      }
    });
  }, true); // capture=true por si hay otros listeners
  </script>
@endpush

<style>
  .btn-group-sm .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .35rem .55rem;
  }
  .btn-group > form.form-delete {
    display: inline-flex;
    margin: 0;
  }
</style>
