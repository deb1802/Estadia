@php
  // Detecta si la URL pertenece a médico o admin
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6 d-flex align-items-center gap-2">
        <h1 class="mb-0">
          <i class="fa-solid fa-capsules text-info me-2"></i>
          Detalles del medicamento
        </h1>
      </div>

       <div class="mb-3">
                <button type="button"
                        class="btn btn-soft"
                        onclick="window.location='{{ route($routeArea . 'medicamentos.index') }}'">
                    <i class="bi bi-arrow-90deg-left me-1"></i> Volver
                </button>
        </div>
    </div>

    {{-- === Alertas de éxito (session y Laracasts) === --}}
    @if (session('success'))
      <div id="alert-success" class="alert alert-success shadow-sm my-3">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
      </div>
    @endif

    {{-- Si usas Laracasts Flash::success(), también lo mostramos --}}
    @include('flash::message')
  </div>
</section>

<div class="content px-3">
  <div class="card">
    <div class="card-body">
      <div class="row">
        @include('admin.medicamentos.show_fields')
      </div>
    </div>

    <div class="card-footer bg-white border-top py-3">
      <div class="d-flex justify-content-center">
        <a href="{{ route($routeArea . 'medicamentos.index') }}" class="btn btn-outline-primary">
          <i class="fas fa-list me-1"></i> Volver al listado de medicamentos
        </a>
      </div>
    </div>
  </div>
</div>
@if (request()->is('medico/*'))
  @include('medico.bottom-navbar')
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --g-text:#374151;       /* gris oscuro */
    --g-text-strong:#111827;
    --g-borde:#d1d5db;      /* gris claro borde */
    --g-borde-2:#9ca3af;    /* gris medio hover */
    --g-bg:#ffffff;         /* fondo blanco */
    --g-bg-hover:#f3f4f6;   /* gris claro hover */
  }

  /* Botón suave reutilizable (Volver) */
  .btn-soft{
    background: var(--g-bg);
    border: 1px solid var(--g-borde);
    color: var(--g-text);
    border-radius: 50px;
    font-weight: 500;
    padding: .5rem 1.25rem;
    transition: all .25s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,.04);
  }
  .btn-soft:hover{
    background: var(--g-bg-hover);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
  }
  .btn-soft:active{ transform: scale(.98); box-shadow: 0 2px 6px rgba(0,0,0,.06); }
  .btn-soft i{ font-size: 1rem; vertical-align: middle; }

  /* Estilo del alerta verde (si no tienes Bootstrap completo) */
  .alert {
    border-radius: 10px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    font-weight: 500;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
  }
  .alert-success{
    background-color: #d1e7dd;   /* verde suave */
    color: #0f5132;              /* texto verde oscuro */
    border: 1px solid #badbcc;   /* borde claro */
    border-left: 6px solid #198754; /* línea lateral más fuerte */
  }
</style>
@endpush

@push('scripts')
<script>
  // Auto fade-out a los 6s para cualquier alerta de éxito (session o laracasts)
  document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('#alert-success, .alert-success');
    if (!alerts.length) return;

    setTimeout(() => {
      alerts.forEach(el => {
        el.style.transition = 'opacity .8s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 800);
      });
    }, 6000);
  });
</script>
@endpush
