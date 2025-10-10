@extends('layouts.app')

@section('content')
<section class="content-header py-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="fw-bold text-primary mb-0">
            <i class="fas fa-user-circle me-2"></i> Detalle del usuario
        </h1>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
</section>

<div class="content px-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white text-center py-4">
            <h4 class="mb-1 fw-semibold">
                {{ $usuario->nombre }} {{ $usuario->apellido }}
            </h4>
            <p class="mb-2 small text-light">
                {{ ucfirst($usuario->tipoUsuario) }}
            </p>
            @if($usuario->estadoCuenta === 'activo')
                <span class="badge bg-success px-3 py-2 fs-6">Activo</span>
            @else
                <span class="badge bg-danger px-3 py-2 fs-6">Inactivo</span>
            @endif
        </div>

        <div class="card-body bg-light py-4 px-5">
            {{-- 📨 Email --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-envelope me-1 text-primary"></i>Correo electrónico
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->email }}</p> {{-- cambiado correo → email --}}
            </div>

            {{-- 📞 Teléfono --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-phone me-1 text-primary"></i>Teléfono
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->telefono }}</p>
            </div>

            {{-- 🎂 Fecha de nacimiento --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-birthday-cake me-1 text-primary"></i>Fecha de nacimiento
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->fechaNacimiento }}</p>
            </div>

            {{-- ⚧ Sexo --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-venus-mars me-1 text-primary"></i>Sexo
                </h6>
                <p class="fw-semibold text-dark">{{ ucfirst($usuario->sexo) }}</p>
            </div>

            {{-- 🆔 Tipo de usuario --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-id-badge me-1 text-primary"></i>Tipo de usuario
                </h6>
                <p class="fw-semibold text-dark text-capitalize">{{ $usuario->tipoUsuario }}</p>
            </div>

            {{-- 🔘 Estado de cuenta --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-toggle-on me-1 text-primary"></i>Estado de cuenta
                </h6>
                @if($usuario->estadoCuenta === 'activo')
                    <p class="fw-semibold text-success">Activo</p>
                @else
                    <p class="fw-semibold text-danger">Inactivo</p>
                @endif
            </div>
        </div>

        <div class="card-footer text-center bg-white border-top py-3">
            <a href="{{ route('admin.usuarios.edit', $usuario->idUsuario) }}" class="btn btn-primary px-4 me-2">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <form action="{{ route('admin.usuarios.destroy', $usuario->idUsuario) }}" 
                  method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger btn-delete px-4">
                    <i class="fas fa-trash-alt me-1"></i> Eliminar
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
});
</script>
@endpush
@endsection
