@extends('layouts.app')

@section('content')
<section class="content-header text-center mb-3">
    <div class="container-fluid">
        <h1 class="fw-semibold text-primary">
            <i class="fas fa-calendar-alt me-2"></i> Detalles de la Cita
        </h1>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            {{-- Paciente --}}
            <div class="mb-3">
                <h5 class="text-secondary mb-1"><i class="fas fa-user me-2 text-primary"></i>Paciente</h5>
                <p class="fs-5 fw-semibold text-dark">
                    {{ $cita->paciente_nombre ?? 'No especificado' }} {{ $cita->paciente_apellido ?? '' }}
                </p>
            </div>

            {{-- Fecha y hora --}}
            <div class="mb-3">
                <h5 class="text-secondary mb-1"><i class="fas fa-clock me-2 text-primary"></i>Fecha y Hora</h5>
                <p class="fs-5">{{ \Carbon\Carbon::parse($cita->fechaHora)->format('d/m/Y H:i') }}</p>
            </div>

            {{-- Motivo --}}
            <div class="mb-3">
                <h5 class="text-secondary mb-1"><i class="fas fa-info-circle me-2 text-primary"></i>Motivo</h5>
                <p class="fs-5">{{ $cita->motivo ?? 'Sin descripción' }}</p>
            </div>

            {{-- Ubicación --}}
            <div class="mb-3">
                <h5 class="text-secondary mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Ubicación</h5>
                <p class="fs-5">{{ $cita->ubicacion ?? 'Sin ubicación' }}</p>
            </div>

            {{-- Estado --}}
            <div class="mb-4">
                <h5 class="text-secondary mb-1"><i class="fas fa-tag me-2 text-primary"></i>Estado</h5>
                <span class="badge fs-6
                    @if($cita->estado == 'programada') bg-info
                    @elseif($cita->estado == 'atendida') bg-success
                    @elseif($cita->estado == 'cancelada') bg-danger
                    @else bg-secondary
                    @endif
                ">
                    {{ ucfirst($cita->estado) }}
                </span>
            </div>

            {{-- Botones de acción --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('medico.citas.index') }}" class="btn btn-secondary px-4">
                    <i class="fas fa-arrow-left me-1"></i> Regresar
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('medico.citas.edit', $cita->idCita) }}" class="btn btn-primary px-4">
                        <i class="fas fa-edit me-1"></i> Editar
                    </a>
                    {!! Form::open(['route' => ['medico.citas.destroy', $cita->idCita], 'method' => 'delete', 'class' => 'form-delete']) !!}
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                    </button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Bottom navbar para notificaciones --}}
@include('medico.bottom-navbar')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('click', function(e) {
    const btn = e.target.closest('form.form-delete button[type="submit"]');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('form.form-delete');
    Swal.fire({
        title: '¿Eliminar esta cita?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}, true);
</script>
@endpush
