@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crud-style.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<style>
  /* 🔹 Reducir espacio superior */
  section.content-header {
    margin-top: -10px !important;
    padding-top: 5px !important;
  }

  /* 🔹 Barra de búsqueda alineada y compacta */
  .search-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    width: 100%;
  }

  .search-input-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
  }

  .search-input-group input {
    flex: 1;
    max-width: 500px;
    border-radius: 10px;
    font-size: 1rem;
  }

  .search-input-group select {
    width: 200px;
    border-radius: 10px;
  }

  .btn-add {
    white-space: nowrap;
    background-color: #6c63ff;
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 8px 14px;
    font-weight: 500;
    transition: 0.3s;
  }

  .btn-add:hover {
    background-color: #5848e5;
  }

  .card-search {
    border-radius: 12px;
    padding: 15px 20px;
  }

  #no-results {
    padding: 25px;
    text-align: center;
    color: #6b7280;
    font-style: italic;
  }
</style>

<section class="content-header text-center mb-2">
  <div class="container-fluid">
      <h1 class="fw-semibold text-primary" data-aos="fade-down" style="font-size:2.2rem;">
          Gestión de Tutores
      </h1>
  </div>
</section>

<div class="content px-3">
    @include('flash::message')

    {{-- 🔍 Barra de búsqueda + botón de agregar --}}
    <div class="card card-body shadow-sm mb-3 card-search">
        <div class="search-bar">
            <div class="search-input-group">
                <input type="text" id="search-input" class="form-control"
                       placeholder="Buscar tutores..." autocomplete="off">
                <select id="search-type" class="form-select">
                    <option value="nombreCompleto">Por nombre del tutor</option>
                    <option value="paciente">Por nombre del paciente</option>
                    <option value="parentesco">Por parentesco</option>
                </select>
            </div>

            {{-- 🔹 Botón "Agregar nuevo tutor" solo visible para médicos --}}
            @if(Auth::user()->tipoUsuario === 'medico')
                <a href="{{ route('medico.tutores.create') }}" class="btn btn-add shadow-sm">
                    <i class="fas fa-user-plus me-1"></i> Agregar nuevo tutor
                </a>
            @endif
        </div>
    </div>

    {{-- 📋 Tabla de tutores --}}
    <div class="card shadow-sm">
        <div id="table-container" class="card-body p-0">
            @include('tutors.table')
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById('search-input');
    const type = document.getElementById('search-type');
    let timeout = null;

    input.addEventListener('input', () => debounceSearch());
    type.addEventListener('change', () => performSearch());

    function debounceSearch() {
        clearTimeout(timeout);
        timeout = setTimeout(performSearch, 350);
    }

    function performSearch() {
        const query = input.value.trim();
        const filterType = type.value;

        // 🔧 Detecta automáticamente si es médico o admin
        const baseUrl = @json(Auth::user()->tipoUsuario === 'medico' 
            ? route('medico.tutores.index') 
            : route('admin.tutores.index'));

        const url = new URL(baseUrl);
        if (query !== '') url.searchParams.set('search', query);
        url.searchParams.set('type', filterType);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                document.querySelector('#table-container').innerHTML = html;
            })
            .catch(err => console.error('Error en búsqueda dinámica:', err));
    }
});
</script>

@endsection
