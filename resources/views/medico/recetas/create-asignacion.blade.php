
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b3b6f; --ink-2:#2c4c86; --sky:#eaf3ff; --card:#fff; --bd:#e6eefc; --ok:#198754;
  }
  .wrap{background: radial-gradient(1100px 700px at 10% -10%, #e9f4ff 0%, #f6fbff 55%, #ffffff 100%);}
  .page{max-width: 1100px; margin-inline:auto; padding: 20px 14px 40px;}
  .card-soft{border:1px solid var(--bd); box-shadow:0 10px 30px rgba(27,59,111,.08); border-radius:16px;}
  .med-pill{display:inline-flex; gap:10px; align-items:center; background:#f3f8ff; border:1px solid #e6eefc; border-radius:999px; padding:6px 12px; font-weight:600; color:#20407a;}
  .section-title{font-weight:800; color:var(--ink);}
  .req::after{content:"*"; color:#e55353; margin-left:4px;}
  .form-help{font-size:.875rem; color:#6b7280;}
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <h2 class="section-title mb-0">Asignar medicamento a paciente</h2>
        <div class="text-muted">Fecha: {{ $hoy ?? now()->toDateString() }}</div>
      </div>
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <strong>Revisa los campos:</strong>
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Tarjeta del medicamento --}}
    <div class="card card-soft mb-4">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row align-items-start gap-3">
          <div class="rounded overflow-hidden" style="width:120px; height:120px; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
            @php
              $img = $medicamento->imagenMedicamento ?? null;
            @endphp
            @if($img)
              <img src="{{ asset(ltrim($img,'/')) }}" alt="Imagen medicamento" style="max-width:100%; max-height:100%; object-fit:contain;">
            @else
              <i class="bi bi-capsule" style="font-size:3rem; color:#94a3b8;"></i>
            @endif
          </div>

          <div class="flex-grow-1">
            <div class="med-pill mb-2">
              <i class="bi bi-prescription2"></i>
              <span>#{{ $medicamento->idMedicamento }}</span>
            </div>
            <h4 class="mb-1">{{ $medicamento->nombre }}</h4>
            <div class="text-muted mb-2">{{ $medicamento->presentacion }}</div>
            @if(!empty($medicamento->indicaciones))
              <div class="small"><strong>Indicaciones:</strong> {{ Str::limit($medicamento->indicaciones, 160) }}</div>
            @endif
            @if(!empty($medicamento->efectosSecundarios))
              <div class="small text-muted mt-1">Efectos secundarios: {{ Str::limit($medicamento->efectosSecundarios, 160) }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Formulario de asignación (cabecera + detalle en un tiro) --}}
    <form method="POST" action="{{ route('medico.medicamentos.asignar.store') }}" class="card card-soft">
      @csrf
      <div class="card-body">
        <h5 class="mb-3">Datos de la receta</h5>

        {{-- Hidden: fkMedicamento y fkMedico (este último el backend no lo usa, pero lo dejamos si lo necesitas luego) --}}
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

          {{-- Observaciones (cabecera de receta) --}}
          <div class="col-12">
            <label class="form-label">Observaciones (opcional)</label>
            <textarea name="observaciones" class="form-control" rows="3"
                      placeholder="Indicaciones adicionales para el paciente…">{{ old('observaciones') }}</textarea>
            <div class="form-help">Se guardan en la cabecera de la receta (RecetasMedicas.observaciones).</div>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
          Cancelar
        </a>
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check2-circle"></i> Guardar receta y asignar
        </button>
      </div>
    </form>

    {{-- Hint para el otro flujo que haremos en el siguiente paso --}}
    <div class="mt-4 text-muted small">
      <i class="bi bi-lightbulb"></i>
      Próximamente aquí conectaremos el flujo “<strong>Generar receta médica</strong>” desde Pacientes
      para crear la cabecera primero y luego agregar múltiples medicamentos.
    </div>
  </div>
</div>
@endsection
