@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-user-md text-primary me-2"></i>
            Reporte de Seguimiento: {{ $paciente->nombre }} {{ $paciente->apellido }}
        </h2>
        <p class="text-muted">Resumen cronológico de citas, emociones y diagnósticos registrados.</p>
    </div>

    {{-- 🔹 Línea de tiempo de citas --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <strong><i class="fas fa-calendar-alt me-2"></i> Citas realizadas</strong>
        </div>
        <div class="card-body text-center">
            @if($citas->isEmpty())
                <p class="text-muted mb-0">No hay citas registradas.</p>
            @else
                <ul class="timeline list-unstyled text-start d-inline-block">
                    @foreach($citas as $cita)
                        <li class="mb-3 position-relative ps-4">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-purple rounded-circle"></span>
                            <strong>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</strong> <br>
                            <small class="text-muted">Motivo:</small> {{ $cita->motivo }} <br>
                            <small class="text-muted">Estado:</small> {{ ucfirst($cita->estado) }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- 🔹 Emociones registradas --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <strong><i class="fas fa-heart me-2"></i> Emociones registradas</strong>
        </div>
        <div class="card-body">
            @if($emociones->isEmpty())
                <p class="text-center text-muted">No se han registrado emociones.</p>
            @else
                <canvas id="emocionesChart" height="120"></canvas>
            @endif
        </div>
    </div>

    {{-- 🔹 Diagnósticos --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <strong><i class="fas fa-stethoscope me-2"></i> Diagnósticos clínicos</strong>
        </div>
        <div class="card-body">
            @if($diagnosticos->isEmpty())
                <p class="text-center text-muted">No hay diagnósticos disponibles.</p>
            @else
                <ul class="list-group">
                    @foreach($diagnosticos as $diag)
                        <li class="list-group-item">
                            <strong>{{ \Carbon\Carbon::parse($diag->fechaActualizacion)->format('d/m/Y') }}:</strong><br>
                            {!! nl2br(e($diag->diagnosticos)) !!}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- 🔹 Botón exportar --}}
    <div class="text-center mt-4">
        <a href="{{ route('admin.reportes.seguimiento.excel', ['idPaciente' => request()->input('paciente')]) }}"
           class="btn text-white px-4 py-2"
           style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <i class="fas fa-file-excel me-2"></i> Exportar a Excel
        </a>
        <a href="{{ route('admin.reportes.seguimiento') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

{{-- 🔹 Script para gráfico dinámico --}}
@if(!$emociones->isEmpty())
    @php
        $labels = $emociones->map(fn($e) => \Carbon\Carbon::parse($e->fechaHoraRegistro)->format('d/m/Y'))->toArray();
        $data = $emociones->map(fn($e) => $e->intensidad ?? 0)->toArray();
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('emocionesChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Nivel de intensidad emocional',
                    data: @json($data),
                    borderColor: '#bea4d2',
                    backgroundColor: 'rgba(190, 164, 210, 0.3)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#b5c8e1',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: true, position: 'bottom' }
                }
            }
        });
    </script>
@endif
@endsection
