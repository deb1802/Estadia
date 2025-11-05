@extends('layouts.app')

@section('title', 'Receta #'.$receta->idReceta.' | Paciente')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b2a4a;
    --muted:#5b6b84;
    --stroke:#e5e7eb;
    --soft:#f8fafc;
    --chip:#eef2ff;
    --chip-b:#c7d2fe;
    --table-head:#f1f5f9;

    --blue:#d7dfe9;
    --purple:#f0e2f8;
    --card-bg:linear-gradient(135deg,var(--blue),var(--purple));

    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-hover:#f3f4f6;
  }

  .rx-scope, .rx-scope * { color: var(--ink); }
  .rx-scope .text-muted { color: var(--muted)!important; }

  .rx-main{ max-width:980px; margin:0 auto; padding-bottom:2rem; }

  /* ===== Encabezado ===== */
  .rx-header{
    background: var(--card-bg);
    color:#1b2a4a;
    border-radius:18px;
    padding:1.5rem 1rem;
    text-align:center;
    box-shadow:0 4px 15px rgba(0,0,0,.1);
    margin-bottom:1.5rem;
  }
  .rx-header h1{font-weight:700;font-size:1.6rem;margin-bottom:.4rem;}
  .rx-header .rx-meta{color:#2e4066;font-size:.95rem;}

  /* ===== Botones ===== */
  .rx-actions-top{
    display:flex;flex-wrap:wrap;gap:.6rem;justify-content:center;margin-top:1rem;
  }
  .btn-soft{
    background:var(--g-bg);
    border:1px solid var(--g-borde);
    color:var(--g-text);
    border-radius:50px;
    font-weight:500;
    padding:.5rem 1.25rem;
    transition:all .25s ease;
    box-shadow:0 2px 5px rgba(0,0,0,.04);
  }
  .btn-soft:hover{
    background:var(--g-bg-hover);
    border-color:var(--g-borde-2);
    color:var(--g-text-strong);
    transform:translateY(-1px);
    box-shadow:0 4px 10px rgba(0,0,0,.08);
  }
  .btn-outline-soft{
    background:var(--g-bg);
    border:1px solid var(--g-borde);
    color:var(--g-text);
    border-radius:50px;
    font-weight:500;
    padding:.5rem 1.25rem;
    transition:all .2s ease;
  }
  .btn-outline-soft:hover{
    background:var(--g-bg-hover);
    border-color:var(--g-borde-2);
    color:var(--g-text-strong);
  }

  /* ===== Tarjetas ===== */
  .box{
    background:#fff;
    border:1px solid var(--stroke);
    border-radius:18px;
    padding:1.5rem;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
    transition:transform .2s ease, box-shadow .2s ease;
  }
  .box:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.08);}
  .kv strong{min-width:110px;display:inline-block;}
  .chip{
    display:inline-block;background:var(--chip);border:1px solid var(--chip-b);
    color:#3730a3;padding:4px 12px;border-radius:999px;font-size:.8rem;font-weight:600;
  }

  /* ===== Tabla ===== */
  .rx-table thead th{background:var(--table-head);font-weight:600;}
  .rx-table tbody tr:hover td{background:#f9fbff;}
  .rx-table td:first-child{width:150px;} /* más ancha para las imágenes */

  /* Animación suave */
  @keyframes fadeInUp{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
  .fade-in-up{animation:fadeInUp .5s ease forwards;}

  /* Miniaturas grandes */
  .thumb{
    width:120px;
    height:120px;
    object-fit:contain;
    border:2px solid #d0dae9;
    border-radius:12px;
    background:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,.07);
    transition:transform .2s ease, box-shadow .2s ease;
  }
  .thumb:hover{
    transform:scale(1.05);
    box-shadow:0 8px 20px rgba(0,0,0,.12);
  }
</style>
@endpush

@section('content')
<main class="container py-3 py-lg-4 rx-scope fade-in-up">
  <div class="rx-main">

    {{-- ===== Encabezado ===== --}}
    <div class="rx-header">
      <h1>Receta médica</h1>
      <div class="rx-meta">
        Folio <strong>#{{ $receta->idReceta }}</strong> ·
        Fecha {{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}
      </div>

      <div class="rx-actions-top mt-3">
        <a href="{{ route('paciente.recetas.index') }}" class="btn btn-soft">
          <i class="bi bi-arrow-left me-1"></i> Volver
        </a>

        <a href="{{ route('paciente.recetas.pdf', ['idReceta'=>$receta->idReceta]) }}"
           class="btn btn-outline-soft" target="_blank" rel="noopener">
          <i class="bi bi-filetype-pdf me-1"></i> PDF
        </a>
      </div>
    </div>

    {{-- ===== Contenido ===== --}}
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
                  <th class="text-center">Foto</th>
                  <th>Medicamento</th>
                  <th>Presentación</th>
                  <th>Dosis</th>
                  <th>Frecuencia</th>
                  <th>Duración</th>
                </tr>
              </thead>
              <tbody>
                @forelse($detalles as $d)
                  @php
                    $raw = trim((string)($d->imagenMedicamento ?? ''));
                    $url = null;

                    if ($raw !== '') {
                      if (\Illuminate\Support\Str::startsWith($raw, ['http://','https://','/'])) {
                        $url = $raw;
                      } else {
                        $clean = \Illuminate\Support\Str::of($raw)
                          ->replace(['storage/app/public/','app/public/','public/','resources/storage/'], 'storage/')
                          ->value();

                        if (!\Illuminate\Support\Str::contains($clean, '/')) {
                          $clean = 'storage/medicamentos/'.$clean;
                        }
                        if (\Illuminate\Support\Str::startsWith($clean, 'medicamentos/')) {
                          $clean = 'storage/'.$clean;
                        }

                        $url = asset($clean);
                      }
                    }
                  @endphp

                  <tr>
                    <td class="text-center">
                      @if($url)
                        <img src="{{ $url }}" alt="Imagen" class="thumb">
                      @else
                        <span class="text-muted">Sin imagen</span>
                      @endif
                    </td>
                    <td class="fw-semibold">{{ $d->nombre }}</td>
                    <td>{{ $d->presentacion }}</td>
                    <td>{{ $d->dosis }}</td>
                    <td>{{ $d->frecuencia }}</td>
                    <td>{{ $d->duracion }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-muted text-center">Sin medicamentos registrados.</td>
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
@include('paciente.bottom-nabvar')
@endsection
