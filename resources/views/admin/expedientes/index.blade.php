@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1>Expedientes Clínicos</h1>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm border-0">
        {{-- 🔹 Barra morada flotante centrada --}}
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 p-3"
            style="
                background: linear-gradient(135deg, #bea4d2, #c8b1da);
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                margin: 15px auto;
                width: 95%;
                max-width: 1200px;
            ">

            <input type="text" id="searchInput" placeholder="Escribe para buscar..."
                class="form-control w-auto"
                style="
                    min-width: 260px;
                    border: none;
                    border-radius: 8px;
                    padding: 10px 15px;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                ">

            <select id="searchType" class="form-select w-auto"
                style="
                    min-width: 220px;
                    border: none;
                    border-radius: 8px;
                    padding: 10px 15px;
                    color: #333;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                ">
                <option value="paciente">Buscar por paciente</option>
                <option value="medico">Buscar por médico</option>
                <option value="diagnostico">Buscar por diagnóstico</option>
            </select>
        </div>


        {{-- 🔹 Tabla de resultados --}}
        <div class="table-responsive">
            <table class="table table-hover text-center align-middle mb-0">
                <thead style="background-color: #b7c7dd; color: white;">
                    <tr>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Diagnóstico</th>
                        <th>Notas Clínicas</th>
                        <th>Última Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="expedientesTable">
                    @forelse($expedientes as $exp)
                        <tr>
                            <td>{{ $exp->nombre_paciente }}</td>
                            <td>{{ $exp->nombre_medico }}</td>
                            <td>{{ $exp->diagnosticos ?? '—' }}</td>
                            <td>{{ $exp->notasClinicas ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($exp->fechaActualizacion)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.expedientes.show', $exp->idExpediente) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.expedientes.destroy', $exp->idExpediente) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('¿Seguro que deseas eliminar este expediente?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4">No se encontraron expedientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 🔹 Script de búsqueda dinámica --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const searchType = document.getElementById("searchType");
    const rows = document.querySelectorAll("#expedientesTable tr");

    searchInput.addEventListener("input", () => {
        const term = searchInput.value.toLowerCase();
        const type = searchType.value;

        let hasResults = false;

        rows.forEach(row => {
            const patient = row.cells[0]?.textContent.toLowerCase() || "";
            const doctor = row.cells[1]?.textContent.toLowerCase() || "";
            const diagnosis = row.cells[2]?.textContent.toLowerCase() || "";

            let match = false;
            if (type === "paciente" && patient.includes(term)) match = true;
            if (type === "medico" && doctor.includes(term)) match = true;
            if (type === "diagnostico" && diagnosis.includes(term)) match = true;

            row.style.display = match || term === "" ? "" : "none";
            if (match) hasResults = true;
        });

        const noResults = document.getElementById("noResults");
        if (!hasResults && term !== "") {
            if (!noResults) {
                const tr = document.createElement("tr");
                tr.id = "noResults";
                tr.innerHTML = `<td colspan="6" class="text-muted py-4">No se encontraron coincidencias.</td>`;
                document.getElementById("expedientesTable").appendChild(tr);
            }
        } else if (noResults) {
            noResults.remove();
        }
    });
});
</script>
@endsection
