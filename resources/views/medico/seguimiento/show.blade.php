@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- 🔹 Encabezado principal --}}
    <h3 class="fw-bold mb-4 text-center text-dark">
        <i class="fas fa-user-circle text-primary me-2"></i> Seguimiento de {{ $paciente->nombre }} {{ $paciente->apellido }}
    </h3>

    {{-- 🔹 Timeline de citas --}}
    <div class="card shadow border-0 mx-auto mb-5" style="max-width: 900px;">
        <div class="card-header text-center fw-semibold text-white" style="background-color: #b5c8e1;">
            <i class="fas fa-calendar-check me-2"></i> Línea de tiempo de citas
        </div>
        <div class="card-body p-4">
            @if($citas->isEmpty())
                <p class="text-center text-muted mb-0">No hay citas registradas.</p>
            @else
                <div class="timeline">
                    @foreach($citas as $index => $cita)
                        <div class="timeline-item">
                            <div class="timeline-icon 
                                @if($cita->estado == 'realizada') bg-success 
                                @elseif($cita->estado == 'programada') bg-warning 
                                @elseif($cita->estado == 'cancelada') bg-danger 
                                @else bg-secondary @endif">
                                <i class="fas fa-stethoscope text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="fw-bold text-dark mb-1">
                                    {{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}
                                </h6>
                                <p class="mb-1">
                                    <strong>Estado:</strong> 
                                    <span class="text-capitalize">{{ $cita->estado }}</span>
                                </p>
                                <p class="mb-1"><strong>Motivo:</strong> {{ $cita->motivo }}</p>
                                <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1"></i> {{ $cita->ubicacion }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- 📊 Evolución emocional --}}
    <div class="card shadow border-0 mx-auto" style="max-width: 900px;">
        <div class="card-header text-center fw-semibold text-white" style="background-color: #b5c8e1;">
            <i class="fas fa-heartbeat me-2"></i> Evolución emocional del paciente
        </div>
        <div class="card-body">
            <canvas id="graficoEmociones" height="120"></canvas>
        </div>
    </div>
</div>

{{-- 📈 Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoEmociones');
const emociones = @json($emociones);

const labels = emociones.map(e => e.fechaHoraRegistro);
const intensidades = emociones.map(e => e.intensidad);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nivel de intensidad emocional',
            data: intensidades,
            borderColor: '#b5c8e1',
            backgroundColor: 'rgba(181, 200, 225, 0.3)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: '#b5c8e1'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 5,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

{{-- 🎨 Estilos del timeline --}}
<style>
.timeline {
    position: relative;
    margin: 0 auto;
    padding: 10px 0;
    max-width: 700px;
}

.timeline::before {
    content: "";
    position: absolute;
    left: 50%;
    top: 0;
    transform: translateX(-50%);
    width: 4px;
    height: 100%;
    background-color: #b5c8e1;
    border-radius: 2px;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 2rem;
    position: relative;
}

.timeline-item:nth-child(odd) .timeline-content {
    margin-left: calc(50% + 30px);
    text-align: left;
}

.timeline-item:nth-child(even) .timeline-content {
    margin-right: calc(50% + 30px);
    text-align: right;
}

.timeline-icon {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

.timeline-content {
    background: #f9fbff;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    width: 45%;
    transition: all 0.3s ease-in-out;
}

.timeline-content:hover {
    transform: scale(1.02);
    background-color: #eef3fb;
}

.bg-success { background-color: #a8d5a2 !important; }
.bg-warning { background-color: #ffe29a !important; color: #333; }
.bg-danger { background-color: #f5a3a3 !important; }
.bg-secondary { background-color: #b5c8e1 !important; }
</style>
@endsection
