@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
@endpush



@section('content')
<section class="content-header py-3" style="background: #e7f1ff;">
    <div class="container-fluid d-flex align-items-center gap-3">
        <h1 class="fw-bold text-primary mb-0 fs-3 d-flex align-items-center">
            <i class="fas fa-user-plus me-2"></i> Crear nuevo usuario
        </h1>
        
 <div class="mb-3">
                <button type="button"
                        class="btn btn-soft"
                        onclick="window.location='{{ route('admin.usuarios.index') }}'">
                    <i class="bi bi-arrow-90deg-left me-1"></i> Volver
                </button>
            </div>
    </div>
</section>

<div class="content px-4 py-4"
     style="background: linear-gradient(180deg, #e7f1ff 0%, #f5f9ff 100%); min-height: 100vh;">

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            {!! Form::open(['route' => 'admin.usuarios.store', 'novalidate' => true]) !!}
                <div class="row g-3">
                    @include('admin.usuarios.fields')
                </div>

                <div class="text-end mt-4">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary me-2']) !!}
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

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

  .btn-soft:active{
    transform: scale(.98);
    box-shadow: 0 2px 6px rgba(0,0,0,.06);
  }

  .btn-soft i{
    font-size: 1rem;
    vertical-align: middle;
  }
</style>
@endpush
