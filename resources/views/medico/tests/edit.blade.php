{{-- resources/views/medico/tests/edit.blade.php  (reusada por admin) --}}
@extends('layouts.app')
@php
  // Detecta área por URL
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
  $isAdmin   = !request()->is('medico/*');
  $isOn = ($test->estado === 'activo');
@endphp

@section('title', 'Editar test')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --card:#ffffff; --ink:#1b2a4a; --muted:#5b6b84;
    --soft:#b5c8e1; --accent:#90aacc; --stroke:#e7eef7; --ring:#7fa3c8;
  }
  body{ background:var(--bg); color:var(--ink); }

  /* ===== Layout centrado ===== */
  .page-wrap{ padding:18px 14px; }
  .container-narrow{ max-width: 980px; margin: 0 auto; }

  /* ===== Chips/Meta ===== */
  .meta-row{ display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
  .chip{
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem;
    font-size:.83rem; color:#1f3b5a; font-weight:600;
  }
  .dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
  .on{ background:#22c55e; } .off{ background:#94a3b8; }

  /* ===== Botones ===== */
  /* Botón suave — “Volver al dashboard” */
  .btn-soft {
    background: #fff;
    border: 1px solid #ccc;
    color: #333;
    border-radius: 50px;
    padding: .5rem 1.25rem;
    transition: .2s ease;
    white-space: nowrap;
  }
  .btn-soft:hover {
    background: #f2f2f2;
    color: #000;
  }

  /* Botón principal */
  .btn-accent{
    background: var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:14px;
    padding:.6rem 1rem; transition: transform .15s ease, box-shadow .15s ease;
    white-space: nowrap;
  }
  .btn-accent:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:14px;
    padding:.55rem .9rem; font-weight:700; color:#1c3455; white-space: nowrap;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  /* Contenedores de acciones */
  .btn-wrap{ display:flex; flex-wrap:wrap; gap:10px; }
  .btn-wrap-right{ justify-content:flex-end; }
  @media (max-width: 576px){
    .btn-wrap, .btn-wrap-right{ display:grid; grid-template-columns: 1fr; }
  }

  /* ===== Card / Form ===== */
  .form-card{
    background:var(--card); border:1px solid var(--stroke); border-radius:20px;
    box-shadow:0 6px 20px rgba(10,30,60,.06); overflow:hidden;
  }
  .form-head{
    padding:14px 18px; background:#edf3fb; border-bottom:1px solid var(--stroke);
    display:flex; align-items:center; gap:8px; font-weight:800; color:#0d223d;
  }
  .form-body{ padding:20px; }

  /* Inputs redondeados */
  .form-label{ font-weight:700; }
  .form-control, .form-select{
    border-radius:20px !important;
    border:1px solid var(--stroke);
    padding:.65rem .9rem;
    font-size:1rem;
    color:var(--ink);
    box-shadow:0 1px 0 rgba(0,0,0,.02);
    transition: all .2s ease;
  }
  .form-control:focus, .form-select:focus, textarea.form-control:focus{
    border-color: var(--accent); outline: none; box-shadow: 0 0 0 4px rgba(144,170,204,.25);
  }
  textarea.form-control{ border-radius:22px !important; }
  .invalid-feedback{ display:block; }
  .hint{ font-size:.85rem; color:var(--muted); }
  .req{ color:#be123c; }

  /* Campos “largos” en desktop */
  .col-long{ flex:0 0 100%; max-width:100%; }
  @media (min-width: 992px){
    .col-long{ flex:0 0 75%; max-width:75%; }
    .col-mid { flex:0 0 50%; max-width:50%; }
    .col-smx { flex:0 0 33.333%; max-width:33.333%;}
  }

  /* ===== Botonera del form ===== */
  .actions{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-top: 12px;
  }
  @media (max-width: 576px){
    .actions{ flex-direction:column-reverse; align-items:stretch; }
    .actions a, .actions button{ width:100%; justify-content:center; }
  }

  /* Alert éxito */
  .alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
    font-weight: 500;
    border-left: 5px solid #198754;
  }
</style>
@endpush

@section('content')
<div class="page-wrap">
  <div class="container-narrow">

    <!-- ===== Header: título arriba, botón volver debajo ===== -->
    <section class="content-header mb-2">
      <!-- Título -->
      <h1 class="h3 mb-1">Editar test psicológico</h1>
      <!-- Botón volver (debajo del título) -->
      <div class="btn-wrap mb-2">
        <button type="button" class="btn btn-soft"
                onclick="window.location='{{ route($routeArea.'tests.index') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver 
        </button>
      </div>

      <!-- Chips -->
      <div class="meta-row mb-2">
        <span class="chip"><span class="dot {{ $isOn ? 'on' : 'off' }}"></span>{{ ucfirst($test->estado) }}</span>
        <span class="chip"><i class="bi bi-hash me-1"></i>ID {{ $test->idTest }}</span>
        @if($test->fechaCreacion)
          <span class="chip"><i class="bi bi-calendar-event me-1"></i>{{ \Illuminate\Support\Carbon::parse($test->fechaCreacion)->format('d/m/Y') }}</span>
        @endif
      </div>



      <!-- Acciones a la derecha -->
      <div class="btn-wrap btn-wrap-right">
        @if(Route::has($routeArea.'tests.builder.edit'))
          <a href="{{ route($routeArea.'tests.builder.edit', $test->idTest) }}" class="btn btn-accent">
            <i class="bi bi-sliders me-1"></i> Editar y agregar preguntas y rangos
          </a>
        @endif
        <a href="{{ route($routeArea.'tests.index') }}" class="btn btn-ghost">
          <i class="bi bi-list-ul me-1"></i> Listado de tests
        </a>
      </div>

      {{-- Alert success (auto-hide) --}}
      @if (session('success'))
        <div id="alert-success" class="alert alert-success shadow-sm mt-3 mb-0" style="border-radius:8px;">
          <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
        <script>
          setTimeout(()=>{
            const e=document.getElementById('alert-success');
            if(e){ e.style.transition='opacity .8s'; e.style.opacity='0'; setTimeout(()=>e.remove(),800); }
          },6000);
        </script>
      @endif
    </section>

    <!-- ===== Form ===== -->
    <section class="content-body">
      <form method="POST" action="{{ route($routeArea.'tests.update', $test->idTest) }}" class="form-card">
        @csrf @method('PUT')

        <div class="form-head">
          <i class="bi bi-clipboard2-check me-1"></i> Información general
        </div>

        <div class="form-body">
          <div class="row g-3">
            {{-- Nombre (largo) --}}
            <div class="col-12 col-long">
              <label class="form-label">Nombre del test <span class="req">*</span></label>
              <input type="text" name="nombre" value="{{ old('nombre', $test->nombre) }}"
                     class="form-control @error('nombre') is-invalid @enderror" required
                     placeholder="Ej. Escala de Estrés Percibido (PSS-10)">
              @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Tipo de trastorno (mitad) --}}
            <div class="col-12 col-mid">
              <label class="form-label">Tipo de trastorno (opcional)</label>
              <input type="text" name="tipoTrastorno" value="{{ old('tipoTrastorno', $test->tipoTrastorno) }}"
                     class="form-control @error('tipoTrastorno') is-invalid @enderror"
                     placeholder="Estrés, Ansiedad, Depresión…">
              @error('tipoTrastorno') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Estado (tercio) --}}
            <div class="col-12 col-smx">
              <label class="form-label">Estado <span class="req">*</span></label>
              <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="inactivo" {{ old('estado', $test->estado)==='inactivo'?'selected':'' }}>Inactivo</option>
                <option value="activo"   {{ old('estado', $test->estado)==='activo'  ?'selected':'' }}>Activo</option>
              </select>
              @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="hint mt-1">Actívalo cuando ya tengas preguntas y rangos definidos.</div>
            </div>

            {{-- Descripción (largo) --}}
            <div class="col-12 col-long">
              <label class="form-label">Descripción (opcional)</label>
              <textarea name="descripcion" rows="4"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Describe objetivo, población, instrucciones…">{{ old('descripcion', $test->descripcion) }}</textarea>
              @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- Botonera del form -->
          <div class="actions">
            <div class="left">
              <a href="{{ route($routeArea.'tests.index') }}" class="btn btn-ghost">
                <i class="bi bi-x-lg me-1"></i> Cancelar
              </a>
            </div>
            <div class="right">
              <button type="submit" class="btn btn-accent">
                <i class="bi bi-save2 me-1"></i> Guardar cambios
              </button>
            </div>
          </div>
        </div>
      </form>

      <div class="mt-3 hint">
        <i class="bi bi-lightbulb me-1"></i>
        Desde <strong>“Editar preguntas y rangos”</strong> podrás gestionar reactivos, opciones con puntaje e interpretación por rangos.
      </div>
    </section>

  </div>
</div>

{{-- Navbar inferior solo para MÉDICO --}}
@if(!$isAdmin)
  @include('medico.bottom-navbar')
@endif
@endsection
