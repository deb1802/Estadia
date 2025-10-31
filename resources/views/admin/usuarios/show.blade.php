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
        {{-- HEADER --}}
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

        {{-- BODY --}}
        <div class="card-body bg-light py-4 px-5">
            {{-- 📨 Email --}}
            <div class="info-box mb-3">
                <h6 class="text-muted mb-1">
                    <i class="fas fa-envelope me-1 text-primary"></i>Correo electrónico
                </h6>
                <p class="fw-semibold text-dark">{{ $usuario->email }}</p>
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

            {{-- ===== Detalles médicos (solo si aplica) ===== --}}
            @if($usuario->medico)
            <hr class="my-4">

            <div id="detallesMedicos" class="mt-3 d-none">
                <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold bg-primary text-white">
                    Perfil médico
                </div>
                <div class="card-body bg-light">
                    <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">
                        <i class="fas fa-id-card me-1"></i> Cédula profesional
                        </div>
                        <div class="fw-medium">{{ $usuario->medico->cedulaProfesional ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">
                        <i class="fas fa-graduation-cap me-1"></i> Especialidad
                        </div>
                        <div class="fw-medium">{{ $usuario->medico->especialidad ?? '—' }}</div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>



{{-- Script directo para la confirmación (no dependemos de @push) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form.form-delete');
      if (!form) return;
      if (typeof Swal !== 'undefined') {
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
      } else {
        if (confirm('¿Eliminar usuario? Esta acción no se puede deshacer.')) form.submit();
      }
    });
  });
});
</script>

{{-- FOOTER opcional: acciones solo de lectura para médico --}}
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i> Volver al listado de usuarios
                </a>
            </div>
        </div>


@endsection
