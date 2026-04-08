@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    /* Paleta admin bonita */
    --ink:#1b3b6f;
    --muted:#6b7280;
    --stroke:#e6eefc;
    --soft:#f8fafc;
    --chip:#eef2ff;
    --chip-b:#c7d2fe;

    /* Botones suaves (grises) */
    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-hover:#f3f4f6;

    /* Extras de tu vista previa */
    --ink-2:#2c4c86; --sky:#eaf3ff; --card:#fff; --ok:#198754;
  }

  /* Fondo suave tipo admin */
  .wrap{
    background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%);
  }
  .page{ max-width: 1100px; margin-inline:auto; padding: 20px 14px 40px; color:var(--ink); }

  /* ===== Banner / Header ===== */
  .rx-header{
    background:#b5c8e1;
    color:#fff;
    border-radius:18px;
    padding:1.1rem 1rem;
    box-shadow:0 4px 15px rgba(0,0,0,.1);
    margin-bottom:1rem;
  }
  .rx-title{ font-weight:800; margin:0; }
  .rx-meta{ color:#eef2f7; font-size:.95rem; }

  /* Chips */
  .chip{
    display:inline-block; background:var(--chip); border:1px solid var(--chip-b);
    color:#3730a3; padding:4px 12px; border-radius:999px; font-size:.8rem; font-weight:600;
  }

  /* Tarjetas */
  .card-soft{
    border:1px solid var(--stroke);
    background: var(--card);
    border-radius:16px;
    box-shadow:0 10px 30px rgba(27,59,111,.08);
  }

  /* Etiquetas, ayudas, req y separadores */
  .section-title{ font-weight:800; color:#fff; }
  .label{ font-size:.9rem; color:var(--muted); }
  .form-help{ font-size:.875rem; color:var(--muted); }
  .req::after{ content:"*"; color:#e55353; margin-left:4px; }
  .hr{ height:1px; background:var(--stroke); margin:.75rem 0 1rem; }

  /* Botón suave (volver / secundarios) */
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

  /* Inputs más marcados al foco */
  .form-control, .form-select, textarea{
    border-color: var(--stroke);
    border-radius: 12px;
  }
  .form-control:focus, .form-select:focus, textarea:focus{
    border-color:#9fb5dd;
    box-shadow: 0 0 0 .2rem rgba(181,200,225,.25);
  }

  /* Pastilla de medicamento (id) */
  .med-pill{
    display:inline-flex; gap:10px; align-items:center;
    background:#f3f8ff; border:1px solid #e6eefc;
    border-radius:999px; padding:6px 12px; font-weight:700; color:#20407a;
  }

  /* Caja de imagen */
  .thumb-wrap{
    width:120px; height:120px; background:#f8fafc;
    display:flex; align-items:center; justify-content:center;
    border-radius:12px; border:1px solid #e8eef9;
  }

  /* Animación suave */
  @keyframes fadeInUp{ from{opacity:0; transform:translateY(8px);} to{opacity:1; transform:translateY(0);} }
  .fade-in-up{ animation: fadeInUp .4s ease forwards; }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page fade-in-up">

    {{-- ===== Encabezado tipo admin ===== --}}
    <div class="rx-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h2 class="rx-title mb-1">Asignar medicamento a paciente</h2>
        <div class="rx-meta">
          <span class="chip me-1">
            <i class="bi bi-calendar3 me-1"></i> {{ $hoy ?? now()->toDateString() }}
          </span>
        </div>
      </div>
      <a href="{{ url()->previous() }}" class="btn btn-soft">
        <i class="bi bi-arrow-left me-1"></i> Volver
      </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))  <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))    <div class="alert alert-danger">{{ session('error') }}</div>   @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <strong>Revisa los campos:</strong>
        <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
      </div>
    @endif

    {{-- ===== Tarjeta del medicamento ===== --}}
    <div class="card card-soft mb-4">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row align-items-start gap-3">
          <div class="thumb-wrap">
            @php
              use Illuminate\Support\Facades\Storage;

              $img = $medicamento->imagenMedicamento ?? null;
              $url = null;
              if ($img) {
                  if (preg_match('/^https?:\/\//', $img)) {
                      $url = $img;
                  } else {
                      // Construir URL pública desde storage
                      $url = Storage::url('medicamentos/' . ltrim($img, '/'));
                  }
              }
            @endphp

            @if($url && Storage::disk('public')->exists('medicamentos/' . ltrim($img, '/')))
              <img src="{{ $url }}" alt="Imagen medicamento"
                  style="max-width:100%; max-height:100%; object-fit:contain;">
            @else
              <i class="bi bi-capsule" style="font-size:3rem; color:#94a3b8;"></i>
            @endif

          </div>

          <div class="flex-grow-1">
            <div class="med-pill mb-2">
              <i class="bi bi-prescription2"></i>
              <span>#{{ $medicamento->idMedicamento }}</span>
            </div>
            <h4 class="mb-1" style="font-weight:800; color:var(--ink);">{{ $medicamento->nombre }}</h4>
            <div class="text-muted mb-2">{{ $medicamento->presentacion }}</div>

            @if(!empty($medicamento->indicaciones))
              <div class="small"><strong>Indicaciones:</strong> {{ $medicamento->indicaciones }}</div>
            @endif
            @if(!empty($medicamento->efectosSecundarios))
              <div class="small text-muted mt-1">Efectos secundarios: {{ $medicamento->efectosSecundarios }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ===== Formulario de asignación (cabecera + detalle) ===== --}}
    <form method="POST" action="{{ route('medico.medicamentos.asignar.store') }}" class="card card-soft">
      @csrf
      <div class="card-body">
        <h5 class="mb-3" style="font-weight:700; color:var(--ink);">Datos de la receta</h5>

        {{-- Hidden: fkMedicamento y (opcional) fkMedico --}}
        <input type="hidden" name="fkMedicamento" value="{{ $medicamento->idMedicamento }}">
        <input type="hidden" name="fkMedico" value="{{ $medicoId }}">

        <div class="row g-3">
          {{-- Paciente --}}
          <div class="col-12 col-md-6">
            <label class="form-label req">Paciente</label>
            <select name="fkPaciente" class="form-select" required>
              <option value="" selected disabled>Selecciona un paciente…</option>
              @forelse($pacientes as $p)
                <option value="{{ $p->idPaciente }}" @selected(old('fkPaciente') == $p->idPaciente)>
                  {{ $p->display_name }}
                </option>
              @empty
                <option value="" disabled>No hay pacientes asignados a tu perfil.</option>
              @endforelse
            </select>
            <div class="form-help">Solo se listan pacientes que pertenecen al médico actual.</div>
          </div>

          {{-- Dosis --}}
          <div class="col-12 col-md-6">
            <label class="form-label req">Dosis</label>
            <input type="text" name="dosis" class="form-control" maxlength="100"
                   value="{{ old('dosis') }}" placeholder="Ej. 500 mg" required>
          </div>

          {{-- Frecuencia --}}
          <div class="col-12 col-md-6">
            <label class="form-label req">Frecuencia</label>
            <input type="text" name="frecuencia" class="form-control" maxlength="100"
                   value="{{ old('frecuencia') }}" placeholder="Ej. cada 8 horas" required>
          </div>

          {{-- Duración --}}
          <div class="col-12 col-md-6">
            <label class="form-label req">Duración</label>
            <input type="text" name="duracion" class="form-control" maxlength="100"
                   value="{{ old('duracion') }}" placeholder="Ej. 7 días" required>
          </div>

          {{-- Observaciones (cabecera receta) --}}
          <div class="col-12">
            <label class="form-label">Observaciones (opcional)</label>
            <textarea name="observaciones" class="form-control" rows="3"
                      placeholder="Indicaciones adicionales para el paciente…">{{ old('observaciones') }}</textarea>
            <div class="form-help">Se guardan en la cabecera de la receta (RecetasMedicas.observaciones).</div>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check2-circle"></i> Guardar receta y asignar
        </button>
      </div>
    </form>

    {{-- Hint flujo futuro --}}
    <div class="mt-4 text-muted small">
      <i class="bi bi-lightbulb"></i>
      Próximamente conectaremos el flujo <strong>“Generar receta médica”</strong> para crear la cabecera primero y luego agregar múltiples medicamentos.
    </div>
  </div>
</div>

@include('medico.bottom-navbar')
@endsection
