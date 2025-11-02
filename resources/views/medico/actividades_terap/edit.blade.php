@extends('layouts.app')

@php
  // Detecta si estás en /medico/* o /admin/*
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Editar Actividad Terapéutica</h1>
                </div>
                
                <div class="mb-3">
                        <button type="button"
                            class="btn btn-soft"
                            onclick="window.location='{{ route($routeArea.'actividades_terap.index') }}'">
                            <i class="bi bi-arrow-90deg-left me-1"></i> Volver
                            </button>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('adminlte-templates::common.errors')

        <div class="card">
            {!! Form::model($actividadesTerap, [
                'route'  => [$routeArea . 'actividades_terap.update', $actividadesTerap],
                'method' => 'patch',
                'files'  => true
            ]) !!}

            <div class="card-body">
                <div class="row">
                    @include('medico.actividades_terap.fields')
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                {!! Form::submit('Guardar cambios', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route($routeArea . 'actividades_terap.index') }}" class="btn btn-default">
                    Cancelar
                </a>
            </div>

            {!! Form::close() !!}
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
