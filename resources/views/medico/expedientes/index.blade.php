@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1>Expedientes Clínicos</h1>
            <a class="btn btn-primary" href="{{ route('medico.expedientes.create') }}">
                <i class="fas fa-plus"></i> Nuevo Expediente
            </a>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm">
        {{-- 🔍 BARRA DE BÚSQUEDA DINÁMICA --}}
        <div class="card-header py-3 d-flex flex-wrap justify-content-center align-items-center gap-2 search-bar">
    <input type="text" id="searchInput" class="form-control w-50 shadow-sm border-0" placeholder="Escribe para buscar...">

    <select id="searchType" class="form-select w-auto border-0 shadow-sm">
        <option value="paciente">Buscar por paciente</option>
        <option value="diagnostico">Buscar por diagnóstico</option>
    </select>
</div>


        {{-- 📋 TABLA DE EXPEDIENTES --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle text-center" id="expedientes-table">
                <thead class="table-primary">
                    <tr>
                        <th>Paciente</th>
                        <th>Diagnóstico</th>
                        <th>Notas Clínicas</th>
                        <th>Última Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="expedientesBody">
                    @foreach($expedientes as $exp)
                        <tr>
                            <td>{{ $exp->nombre_paciente }}</td>
                            <td>{{ $exp->diagnosticos }}</td>
                            <td>{{ $exp->notasClinicas }}</td>
                            <td>{{ \Carbon\Carbon::parse($exp->fechaActualizacion)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('medico.expedientes.show', $exp->idExpediente) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('medico.expedientes.edit', $exp->idExpediente) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p id="noResults" class="text-center text-muted my-3" style="display: none;">No se encontraron resultados.</p>
        </div>
    </div>
</div>

{{-- 🧠 SCRIPT DE FILTRO EN TIEMPO REAL --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchType = document.getElementById('searchType');
    const tableRows = document.querySelectorAll('#expedientesBody tr');
    const noResults = document.getElementById('noResults');

    const filterRows = () => {
        const query = searchInput.value.toLowerCase().trim();
        const type = searchType.value;
        let visibleCount = 0;

        tableRows.forEach(row => {
            const paciente = row.cells[0]?.textContent.toLowerCase() || '';
            const diagnostico = row.cells[1]?.textContent.toLowerCase() || '';

            const matches =
                (type === 'paciente' && paciente.includes(query)) ||
                (type === 'diagnostico' && diagnostico.includes(query)) ||
                query === '';

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar o esconder mensaje si no hay resultados
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    };

    searchInput.addEventListener('input', filterRows);
    searchType.addEventListener('change', filterRows);
});
</script>
<style>
/* 🔹 Contenedor de la barra de búsqueda */
.search-bar {
    background: linear-gradient(135deg, #bea4d2, #cbb5de);
    border: none;
    border-radius: 10px;
}

/* 🔹 Input del buscador */
#searchInput {
    color: #000;
    background-color: #fff;
    border-radius: 8px;
    transition: all 0.3s ease;
}
#searchInput:focus {
    border-color: #bea4d2;
    box-shadow: 0 0 6px rgba(190, 164, 210, 0.8);
}

/* 🔹 Selector de búsqueda */
#searchType {
    color: #000 !important;
    background-color: #fff !important;
    border: 2px solid #bea4d2 !important;
    border-radius: 8px;
    padding: 6px 32px 6px 10px;
    font-weight: 500;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg width='10' height='6' viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='%23bea4d2' d='M5 6L0 0h10L5 6z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 12px;
}
#searchType option {
    color: #000 !important;
    background-color: #fff !important;
}
#searchType:hover, 
#searchType:focus {
    border-color: #a88fc0 !important;
    box-shadow: 0 0 8px rgba(190,164,210,0.7);
}

/* 🔹 Centrado y suavizado general */
.card-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}
</style>


@endsection
