@extends('layouts.app')

@section('content')

{{-- ✅ SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ✅ Mensaje de éxito visual con fade-out --}}
@if(session('success'))
<div id="alertMessage" 
     class="mx-auto text-center fw-semibold shadow-sm"
     style="
        max-width: 600px;
        background: #d4edda;
        color: #155724;
        border-left: 6px solid #28a745;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 18px;
     ">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>

<script>
    setTimeout(() => document.getElementById('alertMessage').style.opacity = '0', 2000);
    setTimeout(() => document.getElementById('alertMessage')?.remove(), 3000);
</script>
@endif

<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1>Expedientes Clínicos</h1>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm border-0">

        {{-- 🔹 Barra morada filtro --}}
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
                style="min-width: 260px; border-radius: 8px; padding: 10px 15px;">

            <select id="searchType" class="form-select w-auto"
                style="min-width: 220px; border-radius: 8px; padding: 10px 15px;">
                <option value="paciente">Buscar por paciente</option>
                <option value="medico">Buscar por médico</option>
                <option value="diagnostico">Buscar por diagnóstico</option>
            </select>
        </div>

        {{-- 🔹 Tabla --}}
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

                                {{-- ✅ Botón SweetAlert Delete --}}
                                <button class="btn btn-outline-danger btn-sm btn-delete"
                                        data-url="{{ route('admin.expedientes.destroy', $exp->idExpediente) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
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

{{-- ✅ SweetAlert lógica --}}
<script>
document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.addEventListener("click", function(e){
        e.preventDefault();
        let url = this.dataset.url;

        Swal.fire({
            title: '¿Eliminar expediente?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

{{-- 🔍 Filtro dinámico --}}
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
            const p = row.cells[0]?.textContent.toLowerCase();
            const m = row.cells[1]?.textContent.toLowerCase();
            const d = row.cells[2]?.textContent.toLowerCase();

            let match =
                (type === "paciente" && p.includes(term)) ||
                (type === "medico" && m.includes(term)) ||
                (type === "diagnostico" && d.includes(term));

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
