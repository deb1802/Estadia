@extends('layouts.app')

@section('content')
<style>
    .backup-container {
        max-width: 900px;
        margin: auto;
        padding-top: 40px;
        padding-bottom: 70px;
    }

    .glass-card {
        background: rgba(255,255,255,0.5);
        border-radius: 20px;
        padding: 45px 40px;
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        margin-bottom: 35px;
        transition: .3s;
    }

    .glass-card:hover {
        transform: translateY(-3px);
    }

    .section-title {
        font-size: 1.95rem;
        font-weight: 700;
    }

    .btn-mw-primary {
        background: #8cb8ff;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        padding: 10px 24px;
        transition: .2s;
    }

    .btn-mw-primary:hover {
        background: #76a9ff;
    }

    .btn-mw-danger {
        background: #ff8f8a;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        padding: 10px 24px;
        transition: .2s;
    }

    .btn-mw-danger:hover {
        background: #ff6b63;
    }

    /* 🟢 Alerta mejor visible */
    .custom-alert {
        padding: 14px 24px;
        border-radius: 12px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 25px;
        animation: fadeOut 6s forwards;
    }

    .alert-success.custom-alert {
        background: #d5f5e4;
        border-left: 6px solid #4caf50;
        color: #256c37;
    }

    .alert-danger.custom-alert {
        background: #ffd9d7;
        border-left: 6px solid #e53935;
        color: #8a1c1c;
    }

    @keyframes fadeOut {
        0%, 70% { opacity: 1; }
        100% { opacity: 0; display: none; }
    }

    .input-file {
        margin-top: 12px;
        margin-bottom: 22px;
    }
</style>

<div class="backup-container">

    <div class="text-center mb-4">
        <h2 class="section-title text-primary">
            <i class="fas fa-database me-2"></i> Gestión de Respaldo y Restauración
        </h2>
        <p class="text-muted">Administra copias de seguridad de la base de datos de MindWare.</p>
    </div>

    {{-- ✅ ALERTAS VISIBLES --}}
    @if(session('success'))
        <div class="alert-success custom-alert">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-danger custom-alert">{{ session('error') }}</div>
    @endif

    {{-- DESCARGAR --}}
    <div class="glass-card text-center">
        <h4 class="fw-semibold mb-3"><i class="fas fa-download me-2 text-primary"></i> Descargar Respaldo</h4>
        <p class="text-muted mb-4">Genera un archivo .sql con toda la información del sistema.</p>

        <a href="{{ route('admin.backup.download') }}" class="btn btn-mw-primary">
            <i class="fas fa-file-export me-2"></i> Descargar Respaldo .SQL
        </a>
    </div>

    {{-- RESTAURAR --}}
    <div class="glass-card text-center">
        <h4 class="fw-semibold mb-3"><i class="fas fa-upload me-2 text-danger"></i> Restaurar Base de Datos</h4>
        <p class="text-muted mb-4">Selecciona un archivo .sql previamente generado.</p>

        <form id="restoreForm" action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="backup_file" accept=".sql" class="form-control input-file" required>

            <button type="button" class="btn btn-mw-danger" id="restoreBtn">
                <i class="fas fa-exclamation-triangle me-2"></i> Restaurar Ahora
            </button>
        </form>
    </div>

</div>

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById("restoreBtn").addEventListener("click", function() {
    Swal.fire({
        title: '¿Restaurar Base de Datos?',
        html: "Esto <b>eliminará toda la información existente</b> y la reemplazará.<br>Acción irreversible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("restoreForm").submit();
        }
    });
});
</script>
@endsection
