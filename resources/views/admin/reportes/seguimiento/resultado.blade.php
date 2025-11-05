@extends('layouts.app')

@section('content')

<style>
    .seg-wrapper { max-width: 1100px; margin: 0 auto; }
    .seg-card { max-width: 950px; margin-left: auto; margin-right: auto; border-radius: 12px; }
    .seg-header {
        background: linear-gradient(135deg, #bea4d2, #c8b1da);
        color: white;
        border-radius: 12px 12px 0 0;
    }
    .seg-timeline li {
        position: relative;
        padding-left: 20px;
        border-left: 3px solid #bea4d2;
    }
    .seg-timeline li::before {
        content: "";
        position: absolute;
        left: -6.5px;
        top: 6px;
        width: 12px;
        height: 12px;
        background: #bea4d2;
        border-radius: 50%;
    }
</style>

<div class="container py-4">
<div class="seg-wrapper">

    {{-- 🔹 Encabezado --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-user-md text-primary me-2"></i>
            Reporte de Seguimiento
        </h2>
        <p class="text-muted">
            Paciente: <strong class="text-primary">{{ $paciente->nombre }} {{ $paciente->apellido }}</strong>
        </p>
        <hr class="w-50 mx-auto opacity-50">
    </div>

    {{-- 📅 Citas --}}
    <div class="card shadow seg-card mb-4">
        <div class="card-header seg-header fw-semibold">
            <i class="fas fa-calendar-alt me-2"></i> Citas Realizadas
        </div>
        <div class="card-body text-center">
            @if($citas->isEmpty())
                <p class="text-muted m-0">No hay citas registradas.</p>
            @else
                <ul class="list-unstyled seg-timeline mx-auto" style="max-width: 600px;">
                    @foreach($citas as $cita)
                        <li class="mb-3">
                            <strong>{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</strong><br>
                            Motivo: {{ $cita->motivo }}<br>
                            Estado:
                            <span class="badge bg-{{ $cita->estado === 'realizada' ? 'success' : ($cita->estado === 'cancelada' ? 'danger' : 'warning') }}">
                                {{ ucfirst($cita->estado) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- 💬 Emociones --}}
    <div class="card shadow seg-card mb-4">
        <div class="card-header seg-header fw-semibold">
            <i class="fas fa-heart me-2"></i> Respuestas Emocionales Registradas
        </div>
        <div class="card-body text-center">
            @if($emociones->isEmpty())
                <p class="text-muted m-0">No se han registrado emociones.</p>
            @else
                <div style="max-width: 700px; margin: 0 auto;">
                    <canvas id="emocionesChart" height="140"></canvas>
                </div>
            @endif
        </div>
    </div>

    {{-- 🩺 Diagnósticos --}}
    <div class="card shadow seg-card mb-4">
        <div class="card-header seg-header fw-semibold">
            <i class="fas fa-stethoscope me-2"></i> Diagnósticos Clínicos
        </div>
        <div class="card-body text-center">
            @if($diagnosticos->isEmpty())
                <p class="text-muted m-0">No hay diagnósticos disponibles.</p>
            @else
                <ul class="list-group mx-auto" style="max-width: 700px;">
                    @foreach($diagnosticos as $diag)
                        <li class="list-group-item text-start">
                            <strong>{{ \Carbon\Carbon::parse($diag->fechaActualizacion)->format('d/m/Y') }}:</strong><br>
                            {!! nl2br(e($diag->diagnosticos)) !!}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- 📤 Botones --}}
    <div class="text-center my-4">
        <a href="{{ route('admin.reportes.seguimiento.excel', $paciente->idPaciente) }}"
           class="btn text-white px-4 shadow-sm"
           style="background: linear-gradient(135deg, #bea4d2, #c8b1da); border-radius: 8px;">
            <i class="fas fa-file-excel me-2"></i> Exportar a Excel
        </a>

        <a href="{{ route('admin.reportes.seguimiento') }}"
           class="btn btn-outline-secondary px-4 ms-2"
           style="border-radius: 8px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

</div>
</div>

{{-- 📊 Script Gráfico --}}
@if(!$emociones->isEmpty())
    @php
        $labels = $emociones->map(fn($e) => \Carbon\Carbon::parse($e->fechaHoraRegistro)->format('d/m/Y'))->toArray();
        $averages = $emociones->map(function($e){
            $int = json_decode($e->intensidades, true) ?? [];
            return count($int) ? array_sum($int) / count($int) : 0;
        })->toArray();
    @endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('emocionesChart'), {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Promedio de intensidad emocional',
            data: @json($averages),
            borderColor: '#9659b8',
            backgroundColor: 'rgba(150, 89, 184, 0.25)',
            tension: 0.35,
            fill: true,
            borderWidth: 2
        }]
    }
});
</script>
@endif

@endsection
