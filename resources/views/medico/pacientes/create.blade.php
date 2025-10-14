@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
@endpush

@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif


@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@section('content')
<section class="content-header py-3" style="background: #e7f1ff;">
    <div class="container-fluid d-flex align-items-center gap-3">
        <h1 class="fw-bold text-primary mb-0 fs-3 d-flex align-items-center">
            <i class="fas fa-user-plus me-2"></i> Crear nuevo paciente
        </h1>
        <a href="{{ route('medico.pacientes.index') }}" class="btn btn-secondary shadow-sm d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</section>

<div class="content px-4 py-4"
     style="background: linear-gradient(180deg, #e7f1ff 0%, #f5f9ff 100%); min-height: 100vh;">

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            {{-- Importante: ruta del flujo del médico --}}
            {!! Form::open(['route' => 'medico.pacientes.store', 'method' => 'post', 'novalidate' => true]) !!}
                <div class="row g-3">
                    {{-- Reutiliza los campos de usuario --}}
                    @include('admin.usuarios.fields')

                    {{-- Fuerza el tipo de usuario a "paciente" --}}
                    {!! Form::hidden('tipoUsuario', 'paciente') !!}

                    {{-- Campo extra de Pacientes --}}
                    <div class="col-12">
                        {!! Form::label('padecimientos', 'Padecimientos:') !!}
                        {!! Form::textarea('padecimientos', old('padecimientos'), [
                            'class' => 'form-control',
                            'rows'  => 3,
                            'placeholder' => 'Describa padecimientos o antecedentes relevantes'
                        ]) !!}
                    </div>
                </div>

                <div class="text-end mt-4">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary me-2']) !!}
                    <a href="{{ route('medico.pacientes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Si el partial trae un <select name="tipoUsuario">, lo forzamos a 'paciente' y lo ocultamos.
  const tipoSelect = document.querySelector('select[name="tipoUsuario"]');
  if (tipoSelect) {
    const opt = Array.from(tipoSelect.options).find(o => (o.value || '').toLowerCase() === 'paciente');
    if (opt) {
      tipoSelect.value = opt.value;
    } else {
      const o = new Option('Paciente', 'paciente', true, true);
      tipoSelect.appendChild(o);
      tipoSelect.value = 'paciente';
    }
    tipoSelect.closest('.form-group')?.classList.add('d-none');
    tipoSelect.setAttribute('disabled', 'disabled');
  }
});
</script>
@endpush


