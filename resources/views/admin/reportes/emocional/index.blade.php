@extends('layouts.app')

@section('content')
<div class="container py-5" id="reporte-completo">
    <h2 class="fw-bold text-center mb-4 text-dark">
        <i class="fas fa-brain text-primary me-2"></i>
        Reporte de Clasificación por Estado Emocional
    </h2>

    {{-- 🔹 Botones de acciones --}}
    <div class="text-center mb-4">
        <a href="{{ route('admin.reportes.emocional.export') }}"
           class="btn text-white px-4 py-2 me-2"
           style="background-color: #b5c8e1; border-radius: 10px;">
           <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel
        </a>

        <button id="btnDescargarImagen"
                class="btn text-white px-4 py-2"
                style="background-color: #bea4d2; border-radius: 10px;">
            <i class="bi bi-image"></i> Descargar reporte en PNG
        </button>
    </div>

    {{-- 🔹 Contenedor completo (gráficas + tabla) --}}
    <div class="contenido-reporte" style="max-width: 95%; margin: 0 auto;">

        {{-- 🔹 Gráfica de barras --}}
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white"
                style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
                <strong>Distribución de diagnósticos</strong>
            </div>
            <div class="card-body text-center">
                <canvas id="diagnosticosBarChart" style="height: 380px;"></canvas>
            </div>
        </div>

        {{-- 🔹 Gráfica circular --}}
        <div class="card shadow border-0 mb-4 text-center">
            <div class="card-header text-white"
                style="background: linear-gradient(135deg, #b5c8e1, #d3e2f3);">
                <strong>Porcentaje por diagnóstico</strong>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center flex-column">
                <canvas id="diagnosticosPieChart" style="max-width: 500px; height: 420px;"></canvas>
            </div>
        </div>

        {{-- 🔹 Tabla resumen --}}
        <div class="card shadow border-0 mt-5">
            <div class="card-header text-white"
                style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
                <strong>Resumen detallado</strong>
            </div>
            <div class="card-body">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Diagnóstico</th>
                            <th>Total</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($porcentajes as $diag)
                        <tr>
                            <td>{{ $diag->diagnosticos }}</td>
                            <td>{{ $diag->total }}</td>
                            <td>{{ $diag->porcentaje }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- html2canvas para capturar imagen --}}
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const labels = @json($diagnosticos->pluck('diagnosticos'));
    const data = @json($diagnosticos->pluck('total'));
    const porcentajes = @json($porcentajes->pluck('porcentaje'));

    // 🔹 Gráfica de barras
    new Chart(document.getElementById('diagnosticosBarChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Número de Pacientes',
                data,
                backgroundColor: '#b5c8e1',
                borderColor: '#8fa4c4',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: { legend: { display: false } }
        }
    });

    // 🔹 Gráfica circular
    new Chart(document.getElementById('diagnosticosPieChart'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: porcentajes,
                backgroundColor: [
                    '#b5c8e1', '#c8b1da', '#a3c4f3', '#b7e4c7',
                    '#fbc4ab', '#ffd6a5', '#cdb4db', '#ffc8dd',
                    '#bde0fe', '#a2d2ff', '#c0fdff', '#bee1e6'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: ${ctx.parsed.toFixed(2)}%`
                    }
                }
            }
        }
    });

    // 🔹 Captura completa (gráficas + tabla)
    document.getElementById('btnDescargarImagen').addEventListener('click', async () => {
        const seccion = document.getElementById('reporte-completo');
        if (!seccion) {
            alert('No se encontró el contenido del reporte.');
            return;
        }

        const canvas = await html2canvas(seccion, {
            backgroundColor: '#ffffff',
            scale: 2,
            useCORS: true
        });

        const fecha = new Date().toLocaleDateString('es-MX').replace(/\//g, '-');
        const enlace = document.createElement('a');
        enlace.href = canvas.toDataURL('image/png');
        enlace.download = `Reporte_Emocional_Mindware_${fecha}.png`;
        enlace.click();
    });
});
</script>
@endsection
