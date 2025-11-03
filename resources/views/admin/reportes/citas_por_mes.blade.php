@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-4 text-dark">
        <i class="fas fa-calendar-alt text-primary me-2"></i>
        Reporte de Citas por Mes
    </h2>

    {{-- 🔹 Selector de año centrado absolutamente --}}
    <div class="selector-wrapper">
        <form method="GET" action="{{ route('admin.reportes.citas.mes') }}" class="selector-box">
            <label for="year" class="fw-bold text-dark mb-0 me-2">Seleccionar año:</label>
            <select name="year" id="year" class="form-select year-dropdown" onchange="this.form.submit()">
                @for ($y = 2020; $y <= 2030; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    {{-- 🔹 Gráfica de barras --}}
    <div class="card shadow border-0 mb-5 chart-card">
        <div class="card-header text-white"
             style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
            <strong>Distribución mensual de citas ({{ $year }})</strong>
        </div>
        <div class="card-body text-center">
            <canvas id="citasBarChart"></canvas>
        </div>
    </div>

    {{-- 🔹 Gráfica circular --}}
    <div class="card shadow border-0 mb-5 chart-card text-center">
        <div class="card-header text-white"
             style="background: linear-gradient(135deg, #b5c8e1, #d3e2f3);">
            <strong>Porcentaje de citas por mes ({{ $year }})</strong>
        </div>
        <div class="card-body text-center">
            <canvas id="citasPieChart"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const datos = @json($meses->pluck('total'));
    const porcentajes = @json($porcentajes);

    // 🔹 Gráfica de barras
    new Chart(document.getElementById('citasBarChart'), {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: 'Citas registradas',
                data: datos,
                backgroundColor: '#b5c8e1',
                borderColor: '#8fa4c4',
                borderWidth: 1,
                hoverBackgroundColor: '#a2b9d9'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 🔹 Gráfica circular (pie)
    new Chart(document.getElementById('citasPieChart'), {
        type: 'pie',
        data: {
            labels: meses,
            datasets: [{
                data: porcentajes,
                backgroundColor: [
                    '#b5c8e1', '#c8b1da', '#a3c4f3', '#b7e4c7',
                    '#fbc4ab', '#ffd6a5', '#cdb4db', '#ffc8dd',
                    '#bde0fe', '#a2d2ff', '#c0fdff', '#bee1e6'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: ${ctx.parsed.toFixed(2)}%`
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: { color: '#333', font: { size: 13 } }
                }
            }
        }
    });
});
</script>

<style>
/* 🌐 Cuerpo general */
.container {
    max-width: 1200px;
}

/* 🎯 Centrado absoluto del selector */
.selector-wrapper {
    width: 100vw; /* Ocupar toda la ventana */
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 40px;
}

/* 🧭 Caja flotante */
.selector-box {
    background: #ffffff;
    border: 1.5px solid #b5c8e1;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    padding: 12px 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* 🎚️ Estilo del selector */
.year-dropdown {
    width: 130px;
    text-align: center;
    border-radius: 8px;
    border: 1px solid #b5c8e1;
    background-color: #f8faff;
    font-weight: 500;
    transition: all 0.3s ease;
}

.year-dropdown:hover {
    background-color: #e6efff;
    transform: scale(1.05);
}

/* 📊 Tarjetas de gráficas */
.chart-card {
    max-width: 1000px;
    margin: 0 auto;
}

/* 📏 Tamaños de las gráficas */
#citasBarChart {
    height: 420px !important;
    width: 100% !important;
}

#citasPieChart {
    height: 420px !important;
    max-width: 500px !important;
    margin: 0 auto;
}

/* 📱 Responsivo */
@media (max-width: 768px) {
    .selector-box {
        flex-direction: column;
        padding: 15px 20px;
        width: 90%;
    }

    #citasBarChart {
        height: 320px !important;
    }

    #citasPieChart {
        height: 320px !important;
        max-width: 360px !important;
    }
}
</style>
@endsection
