@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
@endpush

@section('content')
<section class="content-header py-3" style="background: #e7f1ff;">
    <div class="container-fluid d-flex align-items-center gap-3">
        <h1 class="fw-bold text-primary mb-0 fs-3 d-flex align-items-center">
            <i class="fas fa-user-edit me-2"></i> Editar paciente
        </h1>
        <a href="{{ route('medico.pacientes.index', $paciente->id) }}" class="btn btn-secondary shadow-sm d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Regresar
        </a>
    </div>
</section>

<div class="content px-4 py-4" style="background: linear-gradient(180deg, #e7f1ff 0%, #f5f9ff 100%); min-height: 100vh;">
    @include('adminlte-templates::common.errors')

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 850px;">
        <div class="card-body p-4">
            {{-- Formulario para editar paciente --}}
            <form action="{{ route('medico.pacientes.update', $paciente->id) }}" method="POST" novalidate>
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    {{-- Nombre --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $usuario->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Apellido --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Apellido</label>
                        <input type="text" name="apellido"
                               class="form-control @error('apellido') is-invalid @enderror"
                               value="{{ old('apellido', $usuario->apellido) }}" required>
                        @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $usuario->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono"
                               class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', $usuario->telefono) }}">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Fecha de nacimiento --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de nacimiento</label>
                        <input type="date" name="fechaNacimiento"
                               class="form-control @error('fechaNacimiento') is-invalid @enderror"
                               value="{{ old('fechaNacimiento', $usuario->fechaNacimiento) }}">
                        @error('fechaNacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Sexo --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sexo</label>
                        <select name="sexo" class="form-select @error('sexo') is-invalid @enderror">
                            @php $sexoActual = old('sexo', $usuario->sexo); @endphp
                            <option value="">Seleccione…</option>
                            <option value="masculino" {{ $sexoActual === 'masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="femenino"  {{ $sexoActual === 'femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="otro"      {{ $sexoActual === 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('sexo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Estado de cuenta --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado de cuenta</label>
                        <select name="estadoCuenta" class="form-select @error('estadoCuenta') is-invalid @enderror">
                            @php $estado = old('estadoCuenta', $usuario->estadoCuenta); @endphp
                            <option value="">Sin cambios</option>
                            <option value="activo"   {{ $estado === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ $estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estadoCuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Padecimientos --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Padecimientos</label>
                        <textarea name="padecimientos" rows="5"
                                  class="form-control @error('padecimientos') is-invalid @enderror"
                                  placeholder="Describe los padecimientos del paciente…">{{ old('padecimientos', $paciente->padecimientos) }}</textarea>
                        @error('padecimientos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Guardar cambios
                    </button>
                    <a href="{{ route('medico.pacientes.show', $paciente->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
