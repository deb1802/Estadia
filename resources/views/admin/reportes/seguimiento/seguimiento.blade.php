@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-file-medical-alt text-primary me-2"></i>
            Reporte de Seguimiento de Pacientes
        </h2>
        <p class="text-muted">Genera un resumen del progreso emocional y clínico de tus pacientes.</p>
    </div>

    <div class="d-flex justify-content-center">
        <div class="card shadow-lg border-0" style="max-width: 600px; width: 100%;">
            <div class="card-header text-white text-center" 
                 style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
                <h5 class="mb-0">Seleccionar Paciente</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.reportes.seguimiento.generar') }}">
                    @csrf
                    <div class="mb-4 text-center">
                        <label for="paciente" class="form-label fw-semibold">Paciente</label>
                        <select name="paciente" id="paciente" class="form-select shadow-sm" required>
                            <option value="">Selecciona un paciente...</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nombre }} {{ $p->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn text-white px-4 py-2"
                                style="background: linear-gradient(135deg, #bea4d2, #c8b1da);">
                            <i class="fas fa-chart-line me-1"></i> Generar reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
