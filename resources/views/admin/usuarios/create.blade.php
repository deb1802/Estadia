@extends('layouts.app')

@push('styles')
    @vite(['resources/css/crud-users.css'])
@endpush

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
            <i class="fas fa-user-plus me-2"></i> Crear nuevo usuario
        </h1>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary shadow-sm d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</section>

<div class="content px-4 py-4"
     style="background: linear-gradient(180deg, #e7f1ff 0%, #f5f9ff 100%); min-height: 100vh;">

    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            {!! Form::open(['route' => 'admin.usuarios.store', 'novalidate' => true]) !!}
                <div class="row g-3">
                    @include('admin.usuarios.fields')
                </div>

                <div class="text-end mt-4">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary me-2']) !!}
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  if (!form) return;

  // Desactiva validación nativa
  form.setAttribute('novalidate','novalidate');

  // Helpers para poner/quitar errores visuales
  const containerOf = (el) =>
    el.closest('.form-group, .mb-3, .col-sm-6, .col-12, .col-md-6, .col-lg-6') || el.parentNode;

  const clearError = (el) => {
    if (!el) return;
    el.classList.remove('is-invalid');
    const c = containerOf(el);
    const msg = c.querySelector('.field-error');
    if (msg) msg.remove();
  };

  const showError = (el, text) => {
    if (!el) return;
    clearError(el);
    el.classList.add('is-invalid');
    const c = containerOf(el);
    const div = document.createElement('div');
    div.className = 'field-error';
    div.textContent = text;
    c.appendChild(div);
  };

  // Reglas regex
  const rxLetters  = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
  const rxEmail    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const rxPhone10  = /^\d{10}$/;
  const rxPassword = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-={}\[\]:;"'<>.,.?/\\|]).{6,}$/;

  // Dada una cadena de nombre (lowercase), infiere la “clave” de regla
  const inferKey = (name) => {
    if (!name) return 'desconocido';
    if (name.includes('nombre')) return 'nombre';
    if (name.includes('apell')) return 'apellido';
    if (name.includes('mail') || name.includes('correo')) return 'email';
    if (name.includes('pass') || name.includes('contra')) return 'password';
    if (name.includes('tel')) return 'telefono';
    if (name.includes('fecha')) return 'fecha';
    if (name.includes('sexo') || name.includes('genero')) return 'sexo';
    if (name.includes('tipo') || name.includes('rol') || name === 'role') return 'tipo';
    if (name.includes('estado')) return 'estado';
    return 'desconocido';
  };

  // Valida un solo campo según su “clave” inferida
  const validateOne = (el) => {
    if (!el) return true;
    const name = (el.name || '').toLowerCase();
    const key  = inferKey(name);
    const val  = (el.value || '').trim();

    // Selects: trata valor vacío/0 como inválido
    const isSelect = el.tagName === 'SELECT';

    // Reglas por clave
    switch (key) {
      case 'nombre':
        if (val === '') return showError(el, 'El nombre es obligatorio.'), false;
        if (!rxLetters.test(val)) return showError(el, 'El nombre solo debe llevar letras.'), false;
        break;

      case 'apellido':
        if (val === '') return showError(el, 'El apellido es obligatorio.'), false;
        if (!rxLetters.test(val)) return showError(el, 'El apellido solo debe llevar letras.'), false;
        break;

      case 'email':
        if (val === '') return showError(el, 'El email es obligatorio.'), false;
        if (!rxEmail.test(val)) return showError(el, 'Correo no válido.'), false;
        break;

      case 'password':
        if (val === '') return showError(el, 'La contraseña es obligatoria.'), false;
        if (!rxPassword.test(val)) return showError(el, 'Mín. 6, una MAYÚSCULA y un símbolo.'), false;
        break;

      case 'fecha':
        if (val === '') return showError(el, 'Selecciona la fecha.'), false;
        break;

      case 'sexo':
      case 'tipo':
      case 'estado':
        if (val === '' || val === '0' || val === null) {
          return showError(el, 'Selecciona una opción.'), false;
        }
        break;

      case 'telefono':
        if (val === '') return showError(el, 'El teléfono es obligatorio.'), false;
        if (!rxPhone10.test(val)) return showError(el, 'Debe tener 10 dígitos numéricos.'), false;
        break;

      case 'desconocido':
        // Si no sabemos qué es, solo marcamos si está vacío (requerido básico),
        // excepto si es un select con opción por defecto vacía.
        if (isSelect) {
          if (val === '' || val === '0' || val === null) {
            return showError(el, 'Selecciona una opción.'), false;
          }
        } else {
          if (val === '') return showError(el, 'Este campo es obligatorio.'), false;
        }
        break;
    }

    clearError(el);
    return true;
  };

  // Enlaza a todos los inputs/selects del form
  const controls = form.querySelectorAll('input, select, textarea');
  controls.forEach(el => {
    ['blur','change','input'].forEach(evt => {
      el.addEventListener(evt, () => validateOne(el));
    });
  });

  // Validación al enviar
  form.addEventListener('submit', (e) => {
    let ok = true;
    controls.forEach(el => { ok = validateOne(el) && ok; });
    if (!ok) {
      e.preventDefault();
      if (window.Swal) {
        Swal.fire({ icon:'error', title:'Revisa los campos', text:'Corrige los marcados en rojo.' });
      }
    }
  });
});
</script>
@endpush


@endsection
