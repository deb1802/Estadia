@extends('layouts.app')

@php
    // Detecta si estás en /medico/* o /admin/*
    $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

@section('content')
    {{-- Errores de validación --}}
    @include('adminlte-templates::common.errors')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Actividades Terapéuticas</h1>

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
                </div>

                <div class="col-sm-6 d-flex justify-content-end align-items-center gap-2 flex-wrap">
                    {{-- Solo los médicos pueden crear nuevas actividades --}}
                    @can('create', App\Models\ActividadesTerap::class)
                        <a class="btn btn-primary"
                           href="{{ route($routeArea . 'actividades_terap.create') }}">
                            <i class="fas fa-plus"></i> Agregar nueva actividad
                        </a>
                    @endcan

                    {{-- Botón Volver --}}
                    <button type="button"
                            class="btn btn-soft"
                            onclick="window.location='{{ route($routeArea . 'dashboard') }}'">
                        <i class="bi bi-arrow-90deg-left me-1"></i> Volver
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔍 Barra de búsqueda (ancho completo y un poco más abajo del título) --}}
    <div class="container-fluid mt-3">
        <div class="card card-body shadow-sm mb-3 card-search">
            <form id="search-form" method="GET" action="{{ route($routeArea . 'actividades_terap.index') }}" class="search-bar">
                <div class="search-input-group">
                    <input type="text" id="search-input" name="q"
                           class="form-control"
                           value="{{ old('q', $q ?? request('q')) }}"
                           placeholder="Buscar actividades..."
                           autocomplete="off" aria-label="Buscar actividades">

                    <select id="search-type" name="type" class="form-select" aria-label="Tipo de búsqueda">
                        @php $typeVal = $type ?? request('type', 'all'); @endphp
                        <option value="all" {{ $typeVal==='all' ? 'selected' : '' }}>🔎 Buscar en todos</option>
                        <option value="titulo" {{ $typeVal==='titulo' ? 'selected' : '' }}>Por título</option>
                        <option value="tipoContenido" {{ $typeVal==='tipoContenido' ? 'selected' : '' }}>Por tipo de contenido</option>
                        <option value="categoriaTerapeutica" {{ $typeVal==='categoriaTerapeutica' ? 'selected' : '' }}>Por categoría terapéutica</option>
                        <option value="diagnosticoDirigido" {{ $typeVal==='diagnosticoDirigido' ? 'selected' : '' }}>Por diagnóstico dirigido</option>
                        <option value="nivelSeveridad" {{ $typeVal==='nivelSeveridad' ? 'selected' : '' }}>Por nivel de severidad</option>
                    </select>
                </div>

                <div class="search-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                    @if(request()->filled('q') || request()->filled('type'))
                        <a href="{{ route($routeArea . 'actividades_terap.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="content px-3">
        <div class="card">
            {{-- Pasa $routeArea para que los enlaces apunten a /admin o /medico según corresponda --}}
            @include('medico.actividades_terap.table', ['routeArea' => $routeArea])
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
    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-hover:#f3f4f6;
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

  /* ===== Alerta verde (pequeña, lateral izquierda) ===== */
  #alert-success {
      background: #d1e7dd;
      color: #0f5132;
      border: 1px solid #badbcc;
      border-left: 5px solid #198754;
      font-weight: 500;
      font-size: 0.95rem;
      border-radius: 8px;
      padding: 10px 16px;
      margin-top: .5rem;
      width: fit-content;
      max-width: 600px;
  }

  /* ===== Estilos de búsqueda ===== */
  .card-search{ background:#f8fbff; border:1px solid #e6eefc; border-radius:14px; }
  .search-bar{ display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .search-input-group{ display:flex; align-items:center; gap:10px; flex:1; min-width:260px; }
  .search-input-group input{ flex:1; border-radius:10px; height:44px; }
  .search-input-group select{ width:260px; border-radius:10px; height:44px; }
  .search-actions{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }

  /* Separación extra bajo el header para que no se vea encimado */
  .content-header + .container-fluid .card-search,
  .content-header + .container-fluid.mt-3 .card-search{
    margin-top: .25rem;
  }
</style>
@endpush

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
      // Evita submit si está vacío y el tipo es "all"
      if (input.value.trim() === '' && (select.value || 'all') === 'all') return;
      form.requestSubmit();
    }, 450);

    input.addEventListener('keyup', autoSubmit);
    select.addEventListener('change', () => form.requestSubmit());

    const placeholders = {
      all: 'Buscar por título, tipo, categoría, diagnóstico, severidad…',
      titulo: 'Ej. Respiración 4-7-8',
      tipoContenido: 'Ej. audio / video / lectura',
      categoriaTerapeutica: 'Ej. Relajación',
      diagnosticoDirigido: 'Ej. Ansiedad',
      nivelSeveridad: 'Ej. Leve'
    };
    const setPh = () => { input.placeholder = placeholders[select.value] || placeholders.all; };
    setPh(); select.addEventListener('change', setPh);
  })();
</script>
@endpush
