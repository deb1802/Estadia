@extends('layouts.app')

@php
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')

                    {{-- ✅ Alerta verde de éxito --}}
                    @if(session('success'))
                        <div id="alert-success" class="alert alert-success mt-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                        </div>
                        <script>
                            setTimeout(() => {
                                const alertBox = document.getElementById('alert-success');
                                if (alertBox) {
                                    alertBox.style.transition = 'opacity .8s ease';
                                    alertBox.style.opacity = '0';
                                    setTimeout(() => alertBox.remove(), 800);
                                }
                            }, 6000);
                        </script>
                    @endif
  <section class="content-header">
   {{-- Botón Volver --}}
                    <button type="button"
                            class="btn btn-soft"
                            onclick="window.location='{{ route($routeArea . 'actividades_terap.index') }}'">
                        <i class="bi bi-arrow-90deg-left me-1"></i> Volver
                    </button>
  </section>

  <div class="content px-3">
    <div class="card">
      <div class="card-body">
        @include('medico.actividades_terap.show_fields')
      </div>
    </div>
  </div>

  <div class="card-footer bg-white border-top py-3 mt-auto">
    <div class="d-flex justify-content-center">
      <a href="{{ route($routeArea.'actividades_terap.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-list me-1"></i> Volver al listado
      </a>
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

  /* ===== Botón suave reutilizable (Volver) ===== */
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

  /* ===== Alerta verde (pequeña, lateral izquierda) ===== */
  #alert-success,
  .alert-success{
    background: #d1e7dd !important;
    color: #0f5132 !important;
    border: 1px solid #badbcc !important;
    border-left: 5px solid #198754 !important;
    font-weight: 500;
    font-size: 0.95rem;
    border-radius: 8px;
    padding: 10px 16px;
    margin-top: .5rem;
    width: fit-content;
    max-width: 600px;
  }
</style>
@endpush

