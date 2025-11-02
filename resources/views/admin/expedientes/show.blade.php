@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- 🔹 Encabezado del expediente --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body text-center text-white rounded-top" style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <h3 class="mb-1"><i class="fas fa-file-medical-alt"></i> Expediente Clínico</h3>
            <p class="mb-0">Última actualización:
                <strong>{{ \Carbon\Carbon::parse($expediente->fechaActualizacion)->format('d/m/Y') }}</strong>
            </p>
        </div>

        <div class="card-body text-center">
            <h4 class="text-dark mb-2">
                <i class="fas fa-user"></i> Paciente:
                <span class="text-primary">{{ $expediente->nombre_paciente }}</span>
            </h4>

            <hr>

            <div class="row text-start">
                <div class="col-md-6">
                    <h5><i class="fas fa-notes-medical text-success"></i> Antecedentes</h5>
                    <p>{{ $expediente->antecedentes ?? 'Sin registrar.' }}</p>
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-diagnoses text-danger"></i> Diagnóstico</h5>
                    <p>{{ $expediente->diagnosticos ?? 'Sin registrar.' }}</p>
                </div>
            </div>

            <div class="mt-3 text-start">
                <h5><i class="fas fa-stethoscope text-info"></i> Notas Clínicas</h5>
                <p>{{ $expediente->notasClinicas ?? 'Sin registrar.' }}</p>
            </div>

            <div class="mt-3 text-start">
                <h5><i class="fas fa-comment-medical text-secondary"></i> Observaciones</h5>
                <p>{{ $expediente->observaciones ?? 'Sin observaciones.' }}</p>
            </div>
        </div>
    </div>

    {{-- 🔹 Sección de información consolidada --}}
    <div class="row justify-content-center">
        {{-- 📅 Citas --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header text-white" style="background-color: #bea4d2;">
                    <i class="fas fa-calendar-check"></i> Historial de Citas
                </div>
                <div class="card-body">
                    @forelse($citas as $cita)
                        <div class="mb-3 border-bottom pb-2">
                            <strong>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</strong><br>
                            Motivo: {{ $cita->motivo }}<br>
                            Lugar: {{ $cita->ubicacion }}<br>
                            Estado:
                            <span class="badge bg-{{ $cita->estado === 'realizada' ? 'success' : ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                                {{ ucfirst($cita->estado) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted">No hay citas registradas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 🧠 Tests Psicológicos --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header text-white" style="background-color: #bea4d2;">
                    <i class="fas fa-brain"></i> Tests Psicológicos Aplicados
                </div>
                <div class="card-body">
                    @forelse($tests as $test)
                        <div class="mb-3 border-bottom pb-2">
                            <strong>{{ $test->nombre }}</strong><br>
                            Tipo: {{ $test->tipoTrastorno }}<br>
                            Puntaje total: {{ $test->puntajeTotal ?? 'Pendiente' }}<br>
                            Diagnóstico sugerido: {{ $test->diagnosticoSugerido ?? 'N/A' }}<br>
                            Diagnóstico confirmado: {{ $test->diagnosticoConfirmado ?? 'N/A' }}<br>
                            Fecha: {{ $test->fechaRespuesta ? \Carbon\Carbon::parse($test->fechaRespuesta)->format('d/m/Y') : 'N/A' }}
                        </div>
                    @empty
                        <p class="text-muted">No hay tests registrados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Segunda fila --}}
    <div class="row justify-content-center">
        {{-- 🧘 Actividades Terapéuticas --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header text-white" style="background-color: #bea4d2;">
                    <i class="fas fa-heartbeat"></i> Actividades Terapéuticas
                </div>
                <div class="card-body">
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
                        <p class="text-muted">No hay actividades asignadas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 💊 Medicamentos Prescritos --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header text-white" style="background-color: #bea4d2;">
                    <i class="fas fa-pills"></i> Medicamentos Prescritos
                </div>
                <div class="card-body">
                    @forelse($medicamentos as $m)
                        <div class="mb-3 border-bottom pb-2">
                            <strong>{{ $m->nombre }}</strong><br>
                            Dosis: {{ $m->dosis }}<br>
                            Fecha: {{ \Carbon\Carbon::parse($m->fechaReceta)->format('d/m/Y') }}
                        </div>
                    @empty
                        <p class="text-muted">No hay medicamentos prescritos.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Respuestas emocionales --}}
    <div class="card shadow border-0 mb-4">
        <div class="card-header text-white" style="background-color: #bea4d2;">
            <i class="fas fa-smile"></i> Respuestas Emocionales
        </div>
        <div class="card-body">
            @forelse($respuestas as $r)
                <div class="mb-3 border-bottom pb-2">
                    <strong>{{ \Carbon\Carbon::parse($r->fechaRegistro)->format('d/m/Y') }}</strong><br>
                    Emoción: {{ $r->emocion }}<br>
                    Intensidad: {{ $r->intensidad }}/10
                </div>
            @empty
                <p class="text-muted">No se han registrado respuestas emocionales.</p>
            @endforelse
        </div>
    </div>

    {{-- 🔹 Botón de regreso --}}
    <div class="text-center mt-4">
        <a href="{{ route('admin.expedientes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>
@endsection
