@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-4 text-dark">
        <i class="fas fa-user-md text-primary me-2"></i> Seguimiento de Pacientes
    </h2>

    {{-- 🔍 Barra de búsqueda --}}
    <div class="d-flex justify-content-center mb-4">
        <input type="text" id="buscadorPacientes" class="form-control shadow-sm"
               placeholder="Buscar paciente por nombre, apellido o correo..."
               style="max-width: 400px;">
    </div>

    {{-- 📋 Tabla de pacientes --}}
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle" id="tablaPacientes">
                <thead class="table-light">
                    <tr>
                        <th>Nombre completo</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pacientes as $p)
                        <tr>
                            <td>{{ $p->nombre }} {{ $p->apellido }}</td>
                            <td>{{ $p->correo }}</td>
                            <td>{{ $p->telefono ?? '-' }}</td>
                            <td>
                                <a href="{{ route('medico.seguimiento.show', $p->id) }}" 
                                   class="btn btn-sm btn-primary rounded-pill px-3">
                                   <i class="fas fa-chart-line me-1"></i> Ver seguimiento
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 🧭 Script para búsqueda dinámica --}}
<script>
document.getElementById('buscadorPacientes').addEventListener('keyup', function() {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('#tablaPacientes tbody tr').forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
    });
});
</script>
@endsection
