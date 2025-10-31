@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
      /* ===== Estilos del botón Volver ===== */
      :root{
        --g-text:#374151;
        --g-text-strong:#111827;
        --g-borde:#d1d5db;
        --g-borde-2:#9ca3af;
        --g-bg:#ffffff;
        --g-bg-hover:#f3f4f6;
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
        margin-top: 10px; /* 🔹 Baja el botón para que no se encime */
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

      /* ===== Barra de búsqueda ===== */
      .card-search{
        background:#f8fbff;
        border:1px solid #e6eefc;
        border-radius:14px;
      }
      .search-bar{
        display:flex; align-items:center; gap:12px; flex-wrap:wrap;
      }
      .search-input-group{
        display:flex; align-items:center; gap:10px; flex:1; min-width:260px;
      }
      .search-input-group input{
        flex:1; border-radius:10px; height:44px;
      }
      .search-input-group select{
        width:260px; border-radius:10px; height:44px;
      }
      .search-actions{
        display:flex; gap:10px; align-items:center; flex-wrap:wrap;
      }
    </style>
@endpush

@section('content')
<section class="content-header py-3" style="background:#e7f1ff;">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <h1 class="fw-bold text-primary mb-0 fs-3 d-flex align-items-center">
                <i class="fas fa-users me-2"></i> Gestión de pacientes
            </h1>

            <a class="btn btn-primary shadow-sm btn-lg d-flex align-items-center"
               href="{{ route('medico.pacientes.create') }}">
                <i class="fas fa-user-plus me-2"></i> Crear nuevo paciente
            </a>
        </div>

        {{-- 🔙 Botón Volver (ajustado hacia abajo) --}}
        <button type="button" class="btn btn-soft"
                onclick="window.history.length>1 ? history.back() : window.location='{{ route('medico.dashboard') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </button>
    </div>
</section>

<div class="content px-4 py-3" style="background:linear-gradient(180deg,#e7f1ff 0%,#f5f9ff 100%);min-height:100vh;">
    @include('flash::message')

    {{-- 🔍 Barra de búsqueda --}}
    <div class="card card-body shadow-sm mb-3 card-search">
        <form id="search-form" method="GET" action="{{ route('medico.pacientes.index') }}" class="search-bar">
            <div class="search-input-group">
                <input type="text" id="search-input" name="q"
                       class="form-control"
                       value="{{ old('q', $q ?? request('q')) }}"
                       placeholder="Buscar pacientes..." autocomplete="off">

                <select id="search-type" name="type" class="form-select">
                    @php $typeVal = $type ?? request('type', 'all'); @endphp
                    <option value="all" {{ $typeVal==='all' ? 'selected' : '' }}>🔎 Buscar en todos</option>
                    <option value="nombreCompleto" {{ $typeVal==='nombreCompleto' ? 'selected' : '' }}>Por nombre completo</option>
                    <option value="nombre" {{ $typeVal==='nombre' ? 'selected' : '' }}>Por nombre</option>
                    <option value="apellido" {{ $typeVal==='apellido' ? 'selected' : '' }}>Por apellido</option>
                    <option value="email" {{ $typeVal==='email' ? 'selected' : '' }}>Por email</option>
                    <option value="telefono" {{ $typeVal==='telefono' ? 'selected' : '' }}>Por teléfono</option>
                    <option value="tipoUsuario" {{ $typeVal==='tipoUsuario' ? 'selected' : '' }}>Por tipo de usuario</option>
                    <option value="estadoCuenta" {{ $typeVal==='estadoCuenta' ? 'selected' : '' }}>Por estado de cuenta</option>
                    <option value="padecimientos" {{ $typeVal==='padecimientos' ? 'selected' : '' }}>Por padecimientos</option>
                </select>
            </div>

            <div class="search-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Buscar</button>
                @if(request()->filled('q') || request()->filled('type'))
                    <a href="{{ route('medico.pacientes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @include('medico.pacientes.table', ['pacientes' => $pacientes])
        </div>
    </div>
</div>

@push('scripts')
<script>
const debounce=(fn,delay=450)=>{let t;return(...a)=>{clearTimeout(t);t=setTimeout(()=>fn(...a),delay)}};
(function(){
  const f=document.getElementById('search-form'),
        i=document.getElementById('search-input'),
        s=document.getElementById('search-type');
  const auto=debounce(()=>{if(i.value.trim()===''&&s.value==='all')return;f.requestSubmit()},450);
  i.addEventListener('keyup',auto);
  s.addEventListener('change',()=>f.requestSubmit());
})();
</script>
@endpush
@endsection
