@extends('layouts.app')

@section('title', 'Receta #'.$receta->idReceta.' | Admin')

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

    /* Paleta neutra (grises) para botones */
    --g-text:#374151;       /* gris oscuro */
    --g-text-strong:#111827;
    --g-borde:#d1d5db;      /* gris claro borde */
    --g-borde-2:#9ca3af;    /* gris medio borde hover */
    --g-bg:#ffffff;         /* fondo blanco */
    --g-bg-soft:#f9fafb;    /* gris muy claro */
    --g-bg-hover:#f3f4f6;   /* gris claro hover */
  }

  /* ===== Scope general ===== */
  .rx-scope, .rx-scope * { color: var(--ink); }
  .rx-scope .text-muted { color: var(--muted) !important; }

  /* ===== Centrado general ===== */
  .rx-main {
    max-width: 980px;
    margin: 0 auto;
    padding-bottom: 2rem;
  }

  /* ===== Encabezado (tu color) ===== */
  .rx-header {
    background: #b5c8e1;
    color: #fff;
    border-radius: 18px;
    padding: 1.5rem 1rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
    margin-bottom: 1.5rem;
  }
  .rx-header h1 { font-weight: 700; font-size: 1.6rem; margin-bottom: .5rem; }
  .rx-header .rx-meta { color: #eef2f7; font-size: .95rem; }

  /* ===== Botones superiores centrados ===== */
  .rx-actions-top {
    display: flex; flex-wrap: wrap; gap: .6rem; justify-content: center; margin-top: 1rem;
  }

  /* ===== Botón suave (Volver) – GRIS, SIN AZULES ===== */
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

  /* ===== Botón PDF (outline gris) ===== */
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

  /* ===== Botón Imprimir (suave, no fuerte) ===== */
  .btn-print{
    background: var(--g-bg-soft);
    border: 1px solid var(--g-borde);
    color: var(--g-text);
    border-radius: 50px;
    font-weight: 500;
    padding: .5rem 1.25rem;
    transition: all .2s ease;
  }
  .btn-print:hover{
    background: var(--g-bg-hover);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
  }

  /* ===== Tarjetas principales ===== */
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

  .rx-table thead th{ background:var(--table-head); font-weight:600; }
  .rx-table tbody tr:hover td{ background: #f9fbff; }

  /* Entrada con animación suave */
  @keyframes fadeInUp{ from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
  .fade-in-up{ animation: fadeInUp .4s ease forwards; }
</style>
@endpush

@section('content')
<main class="container py-3 py-lg-4 rx-scope fade-in-up">
  <div class="rx-main">

    {{-- Encabezado --}}
    <div class="rx-header">
      <h1>Receta médica</h1>
      <div class="rx-meta">
        Folio <strong>#{{ $receta->idReceta }}</strong> ·
        Fecha {{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}
      </div>

      {{-- Botones de acción (todos en grises) --}}
      <div class="rx-actions-top mt-3">
        <button type="button" class="btn btn-soft"
                onclick="window.history.length>1 ? history.back() : window.location='{{ route('admin.dashboard') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </button>

        <a href="{{ route('admin.recetas.pdf', $receta->idReceta) }}"
           class="btn btn-outline-soft" target="_blank" rel="noopener">
          <i class="bi bi-filetype-pdf me-1"></i> PDF
        </a>

        <button type="button" class="btn btn-print" onclick="window.print()">
          <i class="bi bi-printer me-1"></i> Imprimir
        </button>
      </div>
    </div>

    {{-- Contenido --}}
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

      <div class="col-12">
        <div class="box">
          <div class="fw-bold mb-3 text-center">Medicamentos prescritos</div>
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
                @forelse($detalles as $d)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $d->nombre }}</div>
                      @if(!empty($d->imagenMedicamento))
                        <small class="text-muted">ID: {{ $d->idMedicamento }}</small>
                      @endif
                    </td>
                    <td>{{ $d->presentacion }}</td>
                    <td>{{ $d->dosis }}</td>
                    <td>{{ $d->frecuencia }}</td>
                    <td>{{ $d->duracion }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-muted text-center">Sin medicamentos registrados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</main>
@endsection
