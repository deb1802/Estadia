@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b3b6f; --muted:#64748b; --stroke:#e6eefc; --paper:#ffffff; --paper-2:#f9fbff;
    --ribbon:#b5c8e1; --thead:#d7e3f9; --thead-b:#c5d5ee;
    --spiral-dark:#4b5b7a; --spiral-light:#c7d6f0;
    --g-text:#374151; --g-text-strong:#111827; --g-borde:#d1d5db; --g-borde-2:#9ca3af;
    --g-bg:#ffffff; --g-bg-hover:#f3f4f6;
  }

  .wrap{
    background: radial-gradient(1000px 600px at 0% 0%, #eaf3ff 0%, #f6fbff 50%, #ffffff 100%);
    padding-bottom: 24px;
  }
  .page{ max-width:1100px; margin:20px auto 40px; padding:0 14px; color:var(--ink); }

  /* ===== Cuaderno con espirales a la izquierda ===== */
  .notebook{
    position: relative; display:flex; background:var(--paper); border:1px solid var(--stroke);
    border-radius:18px; box-shadow:0 18px 40px rgba(27,59,111,.12); overflow:hidden;
  }
  .spirals{
    width:60px; background:linear-gradient(180deg,#22335c 0%,#314b84 100%);
    position:relative; box-shadow:inset -2px 0 5px rgba(0,0,0,.15);
  }
  .spirals::before{
    content:""; position:absolute; top:0; bottom:0; left:22px; width:14px;
    background: radial-gradient(circle 7px at center, #3e4e72 38%, #c7d6f0 39% 60%, transparent 61%)
               top / 50px 36px repeat-y;
  }

  /* ===== Hoja (lado derecho) ===== */
  .sheet{
    flex:1; background: repeating-linear-gradient(180deg, transparent 0 32px, #edf3ff 32px 33px), var(--paper);
    border-left:2px solid var(--stroke); border-radius:0 18px 18px 0; position:relative; z-index:1;
  }

  /* Cintilla superior */
  .rx-ribbon{
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;
    background:var(--ribbon); border-bottom:1px solid var(--stroke); padding:14px 20px 14px 22px;
    border-top-right-radius:18px;
  }
  .rx-title{ font-weight:800; margin:0; letter-spacing:.3px; color:#0f264a; }
  .rx-meta{ display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
  .rx-chip{
    display:inline-flex; align-items:center; gap:8px; background:#eef4ff; border:1px solid #cddbf7;
    color:#253a73; font-weight:700; border-radius:999px; padding:4px 12px; font-size:.85rem;
  }

  /* Logo circular esquina */
  .rx-logo-wrap{
    width:70px; height:70px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center;
    box-shadow:0 0 0 4px rgba(255,255,255,.5), 0 4px 12px rgba(0,0,0,.08);
  }
  .rx-logo-wrap img{ width:46px; height:auto; }

  /* Secciones */
  .rx-section{ padding:20px; }
  .rx-section + .rx-section{ border-top:1px dashed var(--stroke); }
  .rx-head{ display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:.6rem; }
  .rx-head h5{ margin:0; font-weight:800; color:#14305f; }
  .rx-note{ color:var(--muted); font-size:.9rem; }
  .rx-label{ font-size:.9rem; color:var(--muted); }
  .rx-value{ font-weight:700; color:#14305f; }
  .rx-hr{ height:1px; background:var(--stroke); margin:.8rem 0 1rem; border-radius:2px; }

  /* ===== Tabla medicamentos con buen contraste ===== */
  .rx-table{ border-collapse:separate; border-spacing:0; width:100%; }
  .rx-table thead th{
    background:var(--thead); color:#1b3b6f; font-weight:700; border-bottom:2px solid var(--thead-b);
    text-transform:uppercase; font-size:.87rem; letter-spacing:.3px;
  }
  .rx-table td, .rx-table th{ vertical-align:middle; padding:10px 14px; }
  .rx-table tbody tr{ background:#fff; border-bottom:1px solid #e8effa; }
  .rx-table tbody tr:hover td{ background:#f2f7ff; }
  .rx-table td{ color:#1b2a4a; font-size:.95rem; }
  .rx-table .btn-outline-danger{ border-color:#e6a7a7; color:#c0392b; font-weight:600; }
  .rx-table .btn-outline-danger:hover{ background:#ffecec; border-color:#c0392b; color:#a92a1f; }

  /* Formulario */
  .req::after{ content:"*"; color:#dc3545; margin-left:4px; }
  .form-control, .form-select, textarea{ border-color:var(--stroke); border-radius:12px; }
  .form-control:focus, .form-select:focus, textarea:focus{
    border-color:#9fb5dd; box-shadow:0 0 0 .2rem rgba(181,200,225,.25);
  }

  /* Botones suaves */
  .btn-soft{
    background:var(--g-bg); border:1px solid var(--g-borde); color:var(--g-text);
    border-radius:50px; font-weight:600; padding:.5rem 1.25rem; transition:all .25s ease; box-shadow:0 2px 5px rgba(0,0,0,.04);
  }
  .btn-soft:hover{ background:var(--g-bg-hover); border-color:var(--g-borde-2); color:var(--g-text-strong); transform:translateY(-1px); box-shadow:0 4px 10px rgba(0,0,0,.08); }

  /* Footer acciones */
  .sheet-actions{ display:flex; justify-content:flex-end; gap:.5rem; padding:16px 20px; border-top:1px solid var(--stroke); }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="page">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <button type="button" class="btn btn-soft" onclick="window.history.back()">
        <i class="bi bi-arrow-90deg-left me-1"></i> Volver
      </button>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-soft" onclick="window.print()">
          <i class="bi bi-printer me-1"></i> Imprimir
        </button>
      </div>
    </div>

    {{-- ===== Cuaderno ===== --}}
    <div class="notebook">
      <div class="spirals"></div>

      <div class="sheet">

        {{-- Encabezado --}}
        <div class="rx-ribbon">
          <div class="d-flex align-items-center gap-3">
            <h3 class="rx-title mb-0">Receta médica</h3>
            <div class="rx-meta">
              <span class="rx-chip"><i class="bi bi-hash"></i> {{ $receta->idReceta }}</span>
              <span class="rx-chip"><i class="bi bi-calendar3"></i> {{ $receta->fecha }}</span>
            </div>
          </div>
          <div class="rx-logo-wrap">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
          </div>
        </div>

        {{-- Mensajes --}}
        <div class="rx-section pt-3 pb-0">
          @if(session('success')) <div class="alert alert-success mb-2">{{ session('success') }}</div> @endif
          @if(session('error'))   <div class="alert alert-danger  mb-2">{{ session('error') }}</div> @endif
          @if($errors->any())
            <div class="alert alert-danger">
              <strong>Revisa los campos:</strong>
              <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          @endif
        </div>

        {{-- Datos del paciente --}}
        <section class="rx-section">
          <div class="rx-head">
            <h5>Datos del paciente</h5>
            <span class="rx-note">Resumen general</span>
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-5">
              <div class="rx-label">Paciente</div>
              <div class="rx-value">{{ $receta->nombre }} {{ $receta->apellido }}</div>
            </div>
            <div class="col-6 col-md-3">
              <div class="rx-label">Fecha</div>
              <div class="rx-value">{{ $receta->fecha }}</div>
            </div>
            <div class="col-12">
              <div class="rx-hr"></div>
              <div class="rx-label mb-1">Observaciones</div>
              <div style="white-space:pre-wrap;">{{ $receta->observaciones ?: 'Sin observaciones' }}</div>
            </div>
          </div>
        </section>

        {{-- Formulario para agregar medicamentos --}}
        <section class="rx-section">
          <div class="rx-head">
            <h5>Agregar medicamento</h5>
            <span class="rx-note">Indica dosis, frecuencia y duración</span>
          </div>

          <form method="POST" action="{{ route('medico.recetas.detalle.agregar', ['idReceta' => $receta->idReceta]) }}">
            @csrf
            <div class="row g-3">
              <div class="col-12 col-lg-4">
                <label class="form-label req">Medicamento</label>
                <select name="fkMedicamento" class="form-select" required>
                  <option value="" selected disabled>Selecciona…</option>
                  @foreach($medicamentos as $m)
                    <option value="{{ $m->idMedicamento }}" @selected(old('fkMedicamento')==$m->idMedicamento)>
                      {{ $m->nombre }} @if($m->presentacion) - {{ $m->presentacion }} @endif
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-lg-2">
                <label class="form-label req">Dosis</label>
                <input type="text" name="dosis" class="form-control" maxlength="100"
                       value="{{ old('dosis') }}" placeholder="Ej. 1 tableta" required>
              </div>
              <div class="col-12 col-lg-3">
                <label class="form-label req">Frecuencia</label>
                <input type="text" name="frecuencia" class="form-control" maxlength="100"
                       value="{{ old('frecuencia') }}" placeholder="Ej. cada 8 h" required>
              </div>
              <div class="col-12 col-lg-3">
                <label class="form-label req">Duración</label>
                <input type="text" name="duracion" class="form-control" maxlength="100"
                       value="{{ old('duracion') }}" placeholder="Ej. 7 días" required>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> Agregar
              </button>
            </div>
          </form>
        </section>

        {{-- Tabla de medicamentos --}}
        <section class="rx-section">
          <div class="rx-head">
            <h5>Medicamentos indicados</h5>
            <span class="rx-note">Renglones agregados a esta receta</span>
          </div>

          @if($detalles->isEmpty())
            <div class="text-muted">Aún no hay medicamentos agregados.</div>
          @else
            <div class="table-responsive">
              <table class="table table-sm align-middle rx-table mb-0">
                <thead>
                  <tr>
                    <th>Medicamento</th>
                    <th>Presentación</th>
                    <th>Dosis</th>
                    <th>Frecuencia</th>
                    <th>Duración</th>
                    <th class="text-end">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($detalles as $d)
                    <tr>
                      <td class="fw-semibold">{{ $d->medicamento }}</td>
                      <td>{{ $d->presentacion }}</td>
                      <td>{{ $d->dosis }}</td>
                      <td>{{ $d->frecuencia }}</td>
                      <td>{{ $d->duracion }}</td>
                      <td class="text-end">
                        <form method="POST"
                              action="{{ route('medico.recetas.detalle.borrar', ['idReceta'=>$receta->idReceta, 'idDetalle'=>$d->idDetalleMedicamento]) }}"
                              onsubmit="return confirm('¿Eliminar este medicamento de la receta?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i> Eliminar
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif

          {{-- Botón Finalizar --}}
          <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ url('medico/pacientes/'.$receta->idPaciente) }}" class="btn btn-outline-secondary">
              Finalizar
            </a>
          </div>
        </section>

      </div> {{-- /sheet --}}
    </div> {{-- /notebook --}}
  </div>
</div>

@include('medico.bottom-navbar')
@endsection
