@extends('layouts.app')

@section('title', 'Editar test')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --card:#ffffff; --ink:#1b2a4a; --muted:#5b6b84;
    --soft:#b5c8e1; --accent:#90aacc; --stroke:#e7eef7; --ring:#7fa3c8;
  }
  body{ background:var(--bg); color:var(--ink); }

  /* ===== Layout centrado ===== */
  .page-wrap{ padding:18px 14px; }
  .container-narrow{ max-width: 980px; margin: 0 auto; }

  /* ===== Header ===== */
  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:.75rem;
  }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }

  /* ===== Botones ===== */
  .btn-soft{
    background: var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:14px;
    padding:.6rem 1rem; transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-soft:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:14px;
    padding:.55rem .9rem; font-weight:700; color:#1c3455;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  /* ===== Card ===== */
  .form-card{
    background:var(--card); border:1px solid var(--stroke); border-radius:20px;
    box-shadow:0 6px 20px rgba(10,30,60,.06); overflow:hidden;
  }
  .form-head{
    padding:14px 18px; background:#edf3fb; border-bottom:1px solid var(--stroke);
    display:flex; align-items:center; gap:8px; font-weight:800; color:#0d223d;
  }
  .form-body{ padding:20px; }

  /* ===== Inputs largos + redondeados (forzado) ===== */
  .form-label{ font-weight:700; }
  .form-control, .form-select{
    border-radius:20px !important;
    border:1px solid var(--stroke);
    padding:.65rem .9rem;
    font-size:1rem;
    color:var(--ink);
    box-shadow:0 1px 0 rgba(0,0,0,.02);
    transition: all .2s ease;
  }
  .form-control:focus, .form-select:focus, textarea.form-control:focus{
    border-color: var(--accent); outline: none; box-shadow: 0 0 0 4px rgba(144,170,204,.25);
  }
  textarea.form-control{ border-radius:22px !important; }

  /* Campos “largos” en desktop */
  .col-long{ flex:0 0 100%; max-width:100%; }
  @media (min-width: 992px){
    .col-long{ flex:0 0 75%; max-width:75%; }      /* ~3/4 del contenedor */
    .col-mid { flex:0 0 50%; max-width:50%; }      /* mitad */
    .col-smx { flex:0 0 33.333%; max-width:33.333%;}
  }

  .invalid-feedback{ display:block; }
  .hint{ font-size:.85rem; color:var(--muted); }
  .req{ color:#be123c; }

  .meta-row{ display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
  .chip{
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem;
    font-size:.83rem; color:#1f3b5a; font-weight:600;
  }
  .dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
  .on{ background:#22c55e; } .off{ background:#94a3b8; }

  /* ===== Botonera alineada ===== */
  .actions{
    display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
    margin-top: 12px;
  }
  @media (max-width: 576px){
    .actions{ flex-direction:column-reverse; align-items:stretch; }
    .actions a, .actions button{ width:100%; justify-content:center; }
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
            <h1 class="page-title h3">Editar test</h1>
            <div class="meta-row mt-1">
              @php $isOn = ($test->estado === 'activo'); @endphp
              <span class="chip"><span class="dot {{ $isOn ? 'on' : 'off' }}"></span>{{ ucfirst($test->estado) }}</span>
              <span class="chip"><i class="bi bi-hash me-1"></i>ID {{ $test->idTest }}</span>
              @if($test->fechaCreacion)
                <span class="chip"><i class="bi bi-calendar-event me-1"></i>{{ \Illuminate\Support\Carbon::parse($test->fechaCreacion)->format('d/m/Y') }}</span>
              @endif
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          @if(Route::has('medico.tests.builder.edit'))
            <a href="{{ route('medico.tests.builder.edit', $test->idTest) }}" class="btn btn-soft">
              <i class="bi bi-sliders me-1"></i> Editar preguntas y rangos
            </a>
          @endif
          <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost">
            <i class="bi bi-list-ul me-1"></i> Mis tests
          </a>
        </div>
      </div>
    </section>

    <!-- ===== Form ===== -->
    <section class="content-body">
      <form method="POST" action="{{ route('medico.tests.update', $test->idTest) }}" class="form-card">
        @csrf @method('PUT')

        <div class="form-head">
          <i class="bi bi-clipboard2-check me-1"></i> Información general
        </div>

        <div class="form-body">
          <div class="row g-3">
            {{-- Nombre (largo) --}}
            <div class="col-12 col-long">
              <label class="form-label">Nombre del test <span class="req">*</span></label>
              <input type="text" name="nombre" value="{{ old('nombre', $test->nombre) }}"
                     class="form-control @error('nombre') is-invalid @enderror" required
                     placeholder="Ej. Escala de Estrés Percibido (PSS-10)">
              @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Tipo de trastorno (mitad) --}}
            <div class="col-12 col-mid">
              <label class="form-label">Tipo de trastorno (opcional)</label>
              <input type="text" name="tipoTrastorno" value="{{ old('tipoTrastorno', $test->tipoTrastorno) }}"
                     class="form-control @error('tipoTrastorno') is-invalid @enderror"
                     placeholder="Estrés, Ansiedad, Depresión…">
              @error('tipoTrastorno') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Estado (tercio) --}}
            <div class="col-12 col-smx">
              <label class="form-label">Estado <span class="req">*</span></label>
              <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="inactivo" {{ old('estado', $test->estado)==='inactivo'?'selected':'' }}>Inactivo</option>
                <option value="activo"   {{ old('estado', $test->estado)==='activo'  ?'selected':'' }}>Activo</option>
              </select>
              @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="hint mt-1">Actívalo cuando ya tengas preguntas y rangos definidos.</div>
            </div>

            {{-- Descripción (largo) --}}
            <div class="col-12 col-long">
              <label class="form-label">Descripción (opcional)</label>
              <textarea name="descripcion" rows="4"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Describe objetivo, población, instrucciones…">{{ old('descripcion', $test->descripcion) }}</textarea>
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
                <i class="bi bi-save2 me-1"></i> Guardar cambios
              </button>
            </div>
          </div>
        </div>
      </form>

      <div class="mt-3 hint">
        <i class="bi bi-lightbulb me-1"></i>
        Desde <strong>“Editar preguntas y rangos”</strong> podrás gestionar reactivos, opciones con puntaje e interpretación por rangos.
      </div>
    </section>

  </div>
</div>
@endsection
