@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mx-auto" style="max-width:1200px;">

        {{-- ✅ Mensaje dinámico --}}
        @if(session('success'))
            <div class="alert ok text-center fw-semibold" id="alertMsg">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(()=> document.getElementById('alertMsg').style.opacity='0', 500);
                setTimeout(()=> document.getElementById('alertMsg').style.display='none', 1500);
            </script>
        @endif

        <h3 class="text-center mb-4 fw-bold text-dark">
            <i class="fas fa-heartbeat text-danger"></i> Registro Emocional de Pacientes
        </h3>

        {{-- 🔍 Buscador Dinámico --}}
        <div class="search-wrapper mb 4">
            <select id="filtroTipo" class="search-select">
                <option value="paciente" {{ $filtroPaciente ? 'selected' : '' }}>Buscar por paciente</option>
                <option value="actividad" {{ $filtroActividad ? 'selected' : '' }}>Buscar por actividad</option>
            </select>

            <input type="text" id="searchInput" class="search-input" placeholder="Escribe para buscar...">
        </div>

        @if($emociones->isEmpty())
            <p class="text-center text-muted">No se encontraron emociones registradas.</p>
        @else
        <div class="table-responsive">
            <table class="table align-middle text-center table-hover" id="tablaEmociones">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Actividad</th>
                        <th>Emociones & Intensidad</th>
                        <th>Comentario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($emociones as $emo)
                    @php
                        $emocionesList = json_decode($emo->emocionesExperimentadas, true) ?? [];
                        $intensidades = json_decode($emo->intensidades, true) ?? [];
                    @endphp

                    <tr>
                        <td>{{ \Carbon\Carbon::parse($emo->fechaHoraRegistro)->format('d/m/Y H:i') }}</td>
                        <td class="col-paciente">{{ $emo->paciente }}</td>
                        <td class="col-actividad">{{ $emo->actividad }}</td>

                        <td class="text-start" style="width:260px;">
                            @foreach($emocionesList as $e)
                                <div class="mb-2">
                                    <span class="badge bg-info text-dark">{{ $e }}</span>
                                    <div class="progress mt-1" style="height: 9px;">
                                        <div class="progress-bar" style="width: {{ ($intensidades[$e] ?? 1) * 20 }}%; background:#6b7fb3;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </td>

                        <td>{{ $emo->comentario ?? '—' }}</td>

                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminacion({{ $emo->idEmocion }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>

                            <form id="formDelete{{ $emo->idEmocion }}"
                                  action="{{ route('medico.emociones.destroy', $emo->idEmocion) }}"
                                  method="POST" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion(id) {
    Swal.fire({
        title: "¿Eliminar registro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('formDelete' + id).submit();
    });
}
</script>

{{-- 🔎 Filtro dinámico --}}
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let tipo = document.getElementById('filtroTipo').value;
    let filas = document.querySelectorAll("#tablaEmociones tbody tr");

    filas.forEach(fila => {
        let texto = fila.querySelector('.col-' + tipo).textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });
});
</script>

<style>
.alert.ok{
    background:#d1fae5;
    color:#065f46;
    border:1px solid #34d399;
    border-radius:12px;
    padding:10px;
    margin-bottom:15px;
    transition: .6s;
}

.search-wrapper{
    display:flex;
    background:#f1f6ff;
    border:1px solid #d5e3ff;
    padding:10px;
    border-radius:14px;
    gap:10px;
}
.search-select, .search-input{
    border:none;
    background:white;
    border-radius:10px;
    padding:8px 12px;
    flex:1;
}
.search-select{
    max-width:190px;
    cursor:pointer;
}
.progress{ background:#dbe3ff; }
.progress-bar{ height:100%; border-radius:6px; }
</style>

@endsection
