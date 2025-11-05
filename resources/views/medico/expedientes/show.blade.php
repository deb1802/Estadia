@extends('layouts.app')

@section('content')
<style>
  .exp-wrapper{ max-width: 1100px; margin: 0 auto; }
  .exp-card{ max-width: 1000px; margin-left: auto; margin-right: auto; }
  .exp-half{ max-width: 520px; margin-left: auto; margin-right: auto; }
  @media (min-width: 992px){
    .exp-two-col{ display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .exp-half{ max-width: none; }
  }
</style>

<div class="container py-4">
  <div class="exp-wrapper">

    {{-- 🔹 Encabezado --}}
    <div class="card shadow border-0 mb-4 text-center exp-card">
      <div class="card-body text-white rounded-top"
           style="background: linear-gradient(135deg, #007bff, #4c8dff);">
        <h3 class="fw-bold mb-2">🩺 Expediente Clínico Digital</h3>
        <p class="mb-0">
          Última actualización:
          <strong>{{ \Carbon\Carbon::parse($expediente->fechaActualizacion)->format('d/m/Y') }}</strong>
        </p>
      </div>
    </div>

    {{-- 🔹 Datos del paciente --}}
    <div class="card shadow border-0 mb-4 text-center exp-card">
      <div class="card-body">
        <h4 class="text-dark mb-3">
          <i class="fas fa-user-md text-primary"></i>
          Paciente:
          <span class="fw-bold text-primary">{{ $expediente->nombre }} {{ $expediente->apellido }}</span>
        </h4>

        <div class="exp-two-col">
          <div class="exp-half mb-3">
            <div class="p-3 rounded shadow-sm bg-light h-100">
              <h6><i class="fas fa-notes-medical text-success"></i> Antecedentes</h6>
              <p class="mb-0">{{ $expediente->antecedentes ?? 'Sin registrar.' }}</p>
            </div>
          </div>

          <div class="exp-half mb-3">
            <div class="p-3 rounded shadow-sm bg-light h-100">
              <h6><i class="fas fa-diagnoses text-danger"></i> Diagnóstico</h6>
              <p class="mb-0">{{ $expediente->diagnosticos ?? 'Sin registrar.' }}</p>
            </div>
          </div>

          <div class="exp-half mb-3">
            <div class="p-3 rounded shadow-sm bg-light h-100">
              <h6><i class="fas fa-stethoscope text-info"></i> Notas Clínicas</h6>
              <p class="mb-0">{{ $expediente->notasClinicas ?? 'Sin registrar.' }}</p>
            </div>
          </div>

          <div class="exp-half mb-1">
            <div class="p-3 rounded shadow-sm bg-light h-100">
              <h6><i class="fas fa-comment-medical text-secondary"></i> Observaciones</h6>
              <p class="mb-0">{{ $expediente->observaciones ?? 'Sin observaciones.' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 🔹 Bloques --}}
    <div class="exp-two-col mb-4">

      {{-- 📅 Citas --}}
      <div class="card shadow border-0 h-100 exp-half">
        <div class="card-header bg-info text-white fw-bold text-center">
          <i class="fas fa-calendar-check"></i> Historial de Citas
        </div>
        <div class="card-body text-center">
          @forelse($citas as $cita)
            <div class="mb-3 border-bottom pb-2">
              <strong>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</strong><br>
              Motivo: {{ $cita->motivo }}<br>
              Estado:
              <span class="badge bg-{{ $cita->estado === 'realizada' ? 'success' : ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                {{ ucfirst($cita->estado) }}
              </span>
            </div>
          @empty
            <p class="text-muted m-0">No hay citas registradas.</p>
          @endforelse
        </div>
      </div>

      {{-- 🧠 Tests --}}
      <div class="card shadow border-0 h-100 exp-half">
        <div class="card-header bg-warning text-dark fw-bold text-center">
          <i class="fas fa-brain"></i> Tests Psicológicos
        </div>
        <div class="card-body text-center">
          @forelse($tests as $test)
            <div class="mb-3 border-bottom pb-2">
              <strong>{{ $test->nombreTest }}</strong><br>
              Tipo: {{ $test->tipoTrastorno }}<br>
              Diagnóstico: {{ $test->diagnosticoConfirmado ?? 'Pendiente' }}<br>
              Fecha: {{ $test->fechaRespuesta ?? 'N/A' }}
            </div>
          @empty
            <p class="text-muted m-0">No hay tests aplicados.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- 🔹 Segunda fila --}}
    <div class="exp-two-col mb-4">

      {{-- 🧘 Actividades --}}
      <div class="card shadow border-0 h-100 exp-half">
        <div class="card-header bg-success text-white fw-bold text-center">
          <i class="fas fa-heartbeat"></i> Actividades Terapéuticas
        </div>
        <div class="card-body text-center">
          @forelse($actividades as $act)
            <div class="mb-3 border-bottom pb-2">
              <strong>{{ $act->nombreActividad }}</strong><br>
              Estado:
              <span class="badge bg-{{ $act->estado === 'completada' ? 'success' : 'secondary' }}">
                {{ ucfirst($act->estado) }}
              </span><br>
              Asignada el: {{ \Carbon\Carbon::parse($act->fechaAsignacion)->format('d/m/Y') }}
            </div>
          @empty
            <p class="text-muted m-0">No hay actividades asignadas.</p>
          @endforelse
        </div>
      </div>

      {{-- 💊 Medicamentos --}}
      <div class="card shadow border-0 h-100 exp-half">
        <div class="card-header bg-danger text-white fw-bold text-center">
          <i class="fas fa-pills"></i> Medicamentos Prescritos
        </div>
        <div class="card-body text-center">
          @forelse($medicamentos as $m)
            <div class="mb-3 border-bottom pb-2">
              <strong>{{ $m->nombre }}</strong><br>
              Dosis: {{ $m->dosis }}<br>
              Fecha: {{ \Carbon\Carbon::parse($m->fechaReceta)->format('d/m/Y') }}
            </div>
          @empty
            <p class="text-muted m-0">No hay medicamentos registrados.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- 💬 Emociones JSON --}}
    <div class="card shadow border-0 mb-4 exp-card">
      <div class="card-header bg-secondary text-white fw-bold text-center">
        <i class="fas fa-smile"></i> Respuestas Emocionales
      </div>
      <div class="card-body text-center">
        @forelse($respuestas as $r)
          <div class="mb-4 border-bottom pb-3">
            <strong>{{ \Carbon\Carbon::parse($r['fechaRegistro'])->format('d/m/Y H:i') }}</strong><br>
            
            @forelse($r['detalles'] as $d)
              <div class="mt-2">
                <span class="fw-bold">{{ $d['emocion'] }}</span>
                — Intensidad: <span class="text-primary">{{ $d['intensidad'] }}/10</span>
              </div>
            @empty
              <p class="text-muted">Sin detalles registrados.</p>
            @endforelse

            @if($r['comentario'])
              <p class="text-muted mt-2">📝 {{ $r['comentario'] }}</p>
            @endif
          </div>
        @empty
          <p class="text-muted m-0">No se han registrado respuestas emocionales.</p>
        @endforelse
      </div>
    </div>

    {{-- 🔙 Volver --}}
    <div class="text-center mb-5">
      <a href="{{ route('medico.expedientes.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left"></i> Volver al listado
      </a>
    </div>

  </div>
</div>
@endsection
