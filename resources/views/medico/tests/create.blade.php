@extends('layouts.app')

@section('title', 'Crear test')

@push('styles')
<style>
  :root{
    --bg:#eaf1f8;
    --card:#ffffff;
    --ink:#16314f;
    --muted:#5b6b84;
    --accent:#3b82f6;
    --accent-ink:#0b2550;
    --stroke:#e6edf6;
    --ring:#7fa3c8;
    --soft:#f6f9fe;
  }

  body{ background:var(--bg); color:var(--ink); }

  /* ===== Layout ===== */
  .page-wrap{ padding:22px 14px; }
  .container-narrow{ max-width: 980px; margin: 0 auto; }

  /* ===== Encabezado ===== */
  .page-head{ margin-bottom: .75rem; }
  .page-row-top{
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
  }
  .title-block{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .page-title{ font-weight:800; letter-spacing:.2px; margin:0; }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:999px;
    padding:.5rem .9rem; font-weight:700; color:#1c3455; line-height:1;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  .btn-soft{
    background: var(--accent); color:#fff; font-weight:700; border:none; border-radius:12px;
    padding:.62rem 1.05rem; transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-soft:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(2,6,23,.12); }

  /* “Volver” debajo del título */
  .page-row-bottom{ margin-top:.35rem; }
  .btn-back{
    background:#fff; border:1px solid var(--stroke); border-radius:12px;
    color:#0d223d; font-weight:700; padding:.55rem .9rem;
  }
  .btn-back:hover{ background:#f9fbff; }

  /* ===== Card formulario ===== */
  .form-card{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 6px 22px rgba(10,30,60,.06); overflow:hidden;
  }
  .form-head{
    padding:14px 18px; background: linear-gradient(180deg, #f3f7ff 0%, #eef4fb 100%);
    border-bottom:1px solid var(--stroke); display:flex; align-items:center; gap:8px;
    font-weight:800; color:#0d223d;
  }
  .form-body{ padding:18px; }

  /* Inputs agradables */
  .form-label{ font-weight:700; }
  .form-control, .form-select{
    border-radius:14px; border:1px solid var(--stroke);
    padding:.6rem .8rem; box-shadow: 0 1px 0 rgba(0,0,0,.02);
    background:#fff;
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--ring); outline:0; box-shadow: 0 0 0 4px rgba(127,163,200,.22);
  }
  textarea.form-control{ border-radius:16px; }

  .hint{ font-size:.86rem; color:var(--muted); }
  .req{ color:#be123c; }

  /* Botonera */
  .actions{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-top: 14px;
  }
  .actions .left, .actions .right{ display:flex; gap:8px; align-items:center; }
  .btn-cancel{
    background:#fff; border:1px solid var(--stroke); color:#0f2746; border-radius:12px;
    padding:.55rem .95rem; font-weight:700;
  }
  .btn-cancel:hover{ background:#f7fbff; }

  /* ===== Alertas compactas (estilo “anteriores”) ===== */
  .alert{
    margin-top:.5rem;
    width: fit-content;
    max-width: 640px;
    border-radius: 8px;
    padding: 10px 16px;
    font-weight: 500;
    font-size: 0.95rem;
  }
  .alert-danger{
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
    border-left: 5px solid #dc3545;
  }
  .alert-success{
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
    border-left: 5px solid #198754;
  }

  /* Forzar que los mensajes de error por campo se vean siempre debajo del input */
  .invalid-feedback{ display:block; }

  @media (max-width: 576px){
    .page-row-top{ flex-direction:column; align-items:flex-start; }
    .title-block{ width:100%; justify-content:space-between; }
    .page-row-bottom .btn-back{ width:100%; }
    .actions{ flex-direction:column-reverse; align-items:stretch; }
    .actions .left, .actions .right{ width:100%; }
    .actions .left a, .actions .right button{ width:100%; justify-content:center; }
  }
</style>
@endpush

@section('content')
<div class="page-wrap">
  <div class="container-narrow">

    <!-- ===== Header ===== -->
    <section class="content-header">
      <div class="page-head">
        <!-- Fila superior: título + Mis tests -->
        <div class="page-row-top">
          <div class="title-block">
            <h1 class="page-title h3 mb-0">Crear nuevo test</h1>
            <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost">
              <i class="bi bi-list-ul me-1"></i> Mis tests
            </a>
          </div>
        </div>

        <!-- Fila inferior: Volver (debajo) -->
        <div class="page-row-bottom">
          <button type="button" class="btn btn-back"
                  onclick="window.location='{{ route('medico.tests.index') }}'">
            <i class="bi bi-arrow-90deg-left me-1"></i> Volver
          </button>
        </div>

        {{-- 🔴 Resumen de errores (compacto, como las alertas anteriores) --}}
        @if ($errors->any())
          <div class="alert alert-danger mt-2">
            <i class="bi bi-exclamation-octagon me-2"></i>
            Corrige los siguientes campos:
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- 🟢 (Opcional) Mensaje verde si llegaras a usar esta vista tras éxito --}}
        @if (session('success'))
          <div id="alert-success" class="alert alert-success mt-2">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
          </div>
          <script>
            setTimeout(()=>{const el=document.getElementById('alert-success');
              if(el){ el.style.transition='opacity .8s ease'; el.style.opacity='0'; setTimeout(()=>el.remove(),800); }
            },6000);
          </script>
        @endif
      </div>
    </section>

    <!-- ===== Body ===== -->
    <section class="content-body">
      <form method="POST" action="{{ route('medico.tests.store') }}" class="form-card" novalidate>
        @csrf

        <div class="form-head">
          <i class="bi bi-clipboard2-plus"></i>
          <span>Información general</span>
        </div>

        <div class="form-body">
          <div class="row g-3">
            <!-- Nombre -->
            <div class="col-12 col-md-6">
              <label class="form-label">Nombre del test <span class="req">*</span></label>
              <input
                type="text" name="nombre" value="{{ old('nombre') }}"
                class="form-control @error('nombre') is-invalid @enderror"
                placeholder="Ej. GAD-7, PHQ-9, PSS-10"
                aria-describedby="helpNombre">
              @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div id="helpNombre" class="hint mt-1">Nombre visible para ti y tus pacientes.</div>
            </div>

            <!-- Tipo de trastorno -->
            <div class="col-12 col-md-6">
              <label class="form-label">Tipo de trastorno (opcional)</label>
              <input
                type="text" name="tipoTrastorno" value="{{ old('tipoTrastorno') }}"
                class="form-control @error('tipoTrastorno') is-invalid @enderror"
                placeholder="Ansiedad, Depresión, Estrés">
              @error('tipoTrastorno') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Estado -->
            <div class="col-12 col-md-4">
              <label class="form-label">Estado <span class="req">*</span></label>
              <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                <option value="inactivo" {{ old('estado','inactivo')==='inactivo'?'selected':'' }}>
                  Inactivo (recomendado mientras lo editas)
                </option>
                <option value="activo" {{ old('estado')==='activo'?'selected':'' }}>
                  Activo
                </option>
              </select>
              @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="hint mt-1">Podrás activarlo cuando termines de definir preguntas y rangos.</div>
            </div>

            <!-- Descripción -->
            <div class="col-12">
              <label class="form-label">Descripción <span class="req">*</span></label>
              <textarea
                name="descripcion" rows="4"
                class="form-control @error('descripcion') is-invalid @enderror"
                placeholder="Describe brevemente el objetivo del test, población, instrucciones, etc.">{{ old('descripcion') }}</textarea>
              @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="hint mt-1">Incluye al menos una breve descripción del propósito del test.</div>
            </div>

          </div>

          <!-- Botonera -->
          <div class="actions">
            <div class="left">
              <a href="{{ route('medico.tests.index') }}" class="btn btn-cancel">
                <i class="bi bi-x-lg me-1"></i> Cancelar
              </a>
            </div>
            <div class="right">
              <button type="submit" class="btn btn-soft">
                <i class="bi bi-save2 me-1"></i> Guardar y continuar
              </button>
            </div>
          </div>
        </div>
      </form>

      <div class="mt-3 hint">
        <i class="bi bi-lightbulb me-1"></i>
        Después de guardar podrás agregar <strong>preguntas</strong>, <strong>opciones con puntaje</strong> y
        <strong>rangos de evaluación</strong> desde el editor.
      </div>
    </section>

  </div>
</div>

@include('medico.bottom-navbar')
@endsection
