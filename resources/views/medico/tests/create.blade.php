@extends('layouts.app')

@section('title', 'Crear test')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9;
    --card:#ffffff;
    --ink:#1b2a4a;
    --muted:#5b6b84;
    --soft:#b5c8e1;
    --accent:#90aacc;
    --stroke:#e7eef7;
    --ring:#7fa3c8;
  }
  body{ background:var(--bg); color:var(--ink); }

  /* ===== Layout centrado ===== */
  .page-wrap{ padding:18px 14px; }
  .container-narrow{
    max-width: 980px;   /* controla que no se pegue a los bordes */
    margin: 0 auto;
  }

  /* ===== Encabezado ===== */
  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:.75rem;
  }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:12px; padding:.55rem .8rem; font-weight:700;
    color:#1c3455;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  .btn-soft{
    background: var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:12px;
    padding:.6rem 1rem; transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-soft:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }

  /* ===== Card del formulario ===== */
  .form-card{
    background:var(--card);
    border:1px solid var(--stroke);
    border-radius:18px;
    box-shadow:0 6px 20px rgba(10,30,60,.06);
    overflow:hidden;
  }
  /* Encabezado suave tipo “pill” */
  .form-head{
    padding:14px 18px;
    background: #edf3fb;
    border-bottom:1px solid var(--stroke);
    display:flex; align-items:center; gap:8px; font-weight:800; color:#0d223d;
  }
  .form-body{ padding:18px; }

  /* ===== Inputs redondeados + foco agradable ===== */
  .form-label{ font-weight:700; }
  .form-control, .form-select{
    border-radius:14px;
    border:1px solid var(--stroke);
    padding:.6rem .75rem;
    box-shadow: 0 1px 0 rgba(0,0,0,.02);
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--ring);
    outline: 0;
    box-shadow: 0 0 0 4px rgba(127,163,200,.22);
  }
  textarea.form-control{
    border-radius:16px;
  }

  .invalid-feedback{ display:block; }
  .hint{ font-size:.85rem; color:var(--muted); }
  .req{ color:#be123c; }

  .chip-help{
    display:inline-flex; align-items:center; gap:6px; font-size:.82rem; color:#1f3b5a;
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem;
  }

  /* ===== Botonera bien alineada ===== */
  .actions{
    display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
    margin-top: 12px;
  }
  @media (max-width: 576px){
    .actions{ flex-direction:column-reverse; align-items:stretch; }
    .actions .left, .actions .right{ width:100%; display:flex; }
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
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost" title="Volver a mis tests">
            <i class="bi bi-arrow-left"></i>
          </a>
          <div>
            <h1 class="page-title h3 mb-1">Nuevo test</h1>
            <span class="chip-help"><i class="bi bi-info-circle"></i>El id del médico se asigna automáticamente desde tu sesión</span>
          </div>
        </div>

        <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost">
          <i class="bi bi-list-ul me-1"></i> Mis tests
        </a>
      </div>
    </section>

    <!-- ===== Body ===== -->
    <section class="content-body">
      <form method="POST" action="{{ route('medico.tests.store') }}" class="form-card">
        @csrf

        <div class="form-head">
          <i class="bi bi-clipboard2-plus"></i>
          <span>Información general</span>
        </div>

        <div class="form-body">
          <div class="row g-3">
            {{-- Nombre --}}
            <div class="col-12 col-md-6">
              <label class="form-label">Nombre del test <span class="req">*</span></label>
              <input type="text" name="nombre" value="{{ old('nombre') }}"
                     class="form-control @error('nombre') is-invalid @enderror"
                     placeholder="Ej. GAD-7, PHQ-9, PSS-10" required>
              @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="hint mt-1">Nombre visible para ti y tus pacientes.</div>
            </div>

            {{-- Tipo de trastorno --}}
            <div class="col-12 col-md-6">
              <label class="form-label">Tipo de trastorno (opcional)</label>
              <input type="text" name="tipoTrastorno" value="{{ old('tipoTrastorno') }}"
                     class="form-control @error('tipoTrastorno') is-invalid @enderror"
                     placeholder="Ansiedad, Depresión, Estrés…">
              @error('tipoTrastorno') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Estado --}}
            <div class="col-12 col-md-4">
              <label class="form-label">Estado <span class="req">*</span></label>
              <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
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

            {{-- Descripción --}}
            <div class="col-12">
              <label class="form-label">Descripción (opcional)</label>
              <textarea name="descripcion" rows="4"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Describe brevemente el objetivo del test, población, instrucciones, etc.">{{ old('descripcion') }}</textarea>
              @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <!-- Botonera -->
          <div class="actions">
            <div class="left">
              <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost">
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
