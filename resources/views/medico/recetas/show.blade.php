@extends('layouts.app')

@section('title', 'Receta #'.$receta->idReceta.' | Médico')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b3b6f;
    --muted:#6b7280;
    --stroke:#e5e7eb;
    --soft:#f8fafc;
    --chip:#eef2ff;
    --chip-b:#c7d2fe;
    --table-head:#f1f5f9;

    /* Paleta neutra (grises) para botones suaves */
    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-soft:#f9fafb;
    --g-bg-hover:#f3f4f6;
  }

  /* ===== Scope general ===== */
  .rx-scope, .rx-scope * { color: var(--ink); }
  .rx-scope .text-muted { color: var(--muted) !important; }

  /* ===== Contenedor centrado ===== */
  .rx-main {
    max-width: 980px;
    margin: 0 auto;
    padding-bottom: 2rem;
  }

  /* ===== Encabezado tipo admin ===== */
  .rx-header {
    background: #b5c8e1; /* mismo color del admin */
    color: #fff;
    border-radius: 18px;
    padding: 1.5rem 1rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
    margin-bottom: 1.5rem;
  }
  .rx-header h1 { font-weight: 700; font-size: 1.6rem; margin-bottom: .5rem; }
  .rx-header .rx-meta { color: #eef2f7; font-size: .95rem; }

  /* ===== Botones superiores centrados (grises) ===== */
  .rx-actions-top {
    display: flex; flex-wrap: wrap; gap: .6rem; justify-content: center; margin-top: 1rem;
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
  }
  .btn-soft:hover{
    background: var(--g-bg-hover);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
  }
  .btn-outline-soft{
    background: var(--g-bg);
    border: 1px solid var(--g-borde);
    color: var(--g-text);
    border-radius: 50px;
    font-weight: 500;
    padding: .5rem 1.25rem;
    transition: all .2s ease;
  }
  .btn-outline-soft:hover{
    background: var(--g-bg-soft);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
  }

  /* ===== Tarjetas ===== */
  .box{
    background: #fff;
    border: 1px solid var(--stroke);
    border-radius: 18px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,.05);
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .box:hover{ transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }

  .kv strong { min-width:110px; display:inline-block; }

  .chip{
    display:inline-block; background:var(--chip); border:1px solid var(--chip-b);
    color:#3730a3; padding:4px 12px; border-radius:999px; font-size:.8rem; font-weight:600;
  }

  /* ===== Tabla ===== */
  .rx-table thead th{ background:var(--table-head); font-weight:600; }
  .rx-table tbody tr:hover td{ background: #f9fbff; }

  /* Animación de entrada */
  @keyframes fadeInUp{ from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
  .fade-in-up{ animation: fadeInUp .4s ease forwards; }
</style>
@endpush

@section('content')
<main class="container py-3 py-lg-4 rx-scope fade-in-up">
  <div class="rx-main">

    {{-- Encabezado tipo admin --}}
    <div class="rx-header">
      <h1>Receta médica</h1>
      <div class="rx-meta">
        Folio <strong>#{{ $receta->idReceta }}</strong> ·
        Fecha {{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}
      </div>

      <div class="rx-actions-top mt-3">
        <a href="{{ route('medico.recetas.pdf', ['idReceta' => $receta->idReceta]) }}"
           class="btn btn-outline-soft" target="_blank" rel="noopener">
          <i class="bi bi-filetype-pdf me-1"></i> PDF
        </a>
        <button type="button" class="btn btn-soft" onclick="window.history.back()">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </button>
      </div>
    </div>

    {{-- Datos generales --}}
    <div class="row g-4 justify-content-center">
      <div class="col-12 col-lg-6">
        <div class="box text-center">
          <div class="fw-bold mb-2">Paciente</div>
          <div class="kv">
            <div><strong>Nombre:</strong> {{ $receta->paciente_nombre }} {{ $receta->paciente_apellido }}</div>
          </div>
          <div class="mt-3"><span class="chip">Receta vigente</span></div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="box text-center">
          <div class="fw-bold mb-2">Médico</div>
          <div class="kv">
            <div><strong>Nombre:</strong> {{ $receta->medico_nombre }} {{ $receta->medico_apellido }}</div>
            @if(!empty($receta->especialidad))
              <div><strong>Especialidad:</strong> {{ $receta->especialidad }}</div>
            @endif
            @if(!empty($receta->cedulaProfesional))
              <div><strong>Cédula:</strong> {{ $receta->cedulaProfesional }}</div>
            @endif
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="box">
          <div class="fw-bold mb-2 text-center">Observaciones</div>
          <div class="text-muted text-center" style="white-space:pre-wrap;">
            {{ $receta->observaciones ?: 'Sin observaciones' }}
          </div>
        </div>
      </div>

      {{-- Medicamentos (sin imagen) --}}
      <div class="col-12">
        <div class="box">
          <div class="fw-bold mb-3 text-center">Medicamentos prescritos</div>
          @if($detalles->isEmpty())
            <div class="text-muted text-center">No hay medicamentos en esta receta.</div>
          @else
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0 rx-table">
                <thead>
                  <tr>
                    <th>Medicamento</th>
                    <th>Presentación</th>
                    <th>Dosis</th>
                    <th>Frecuencia</th>
                    <th>Duración</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($detalles as $d)
                    <tr>
                      <td class="fw-semibold">{{ $d->nombre }}</td>
                      <td>{{ $d->presentacion }}</td>
                      <td>{{ $d->dosis }}</td>
                      <td>{{ $d->frecuencia }}</td>
                      <td>{{ $d->duracion }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</main>
@include('medico.bottom-navbar')
@endsection
