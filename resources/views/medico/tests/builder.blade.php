{{-- resources/views/medico/tests/builder.blade.php  (reusada por admin) --}}
@extends('layouts.app')

@section('title', 'Editor de preguntas y rangos')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --card:#ffffff; --ink:#1b2a4a; --muted:#5b6b84;
    --soft:#b5c8e1; --accent:#90aacc; --stroke:#e7eef7; --ring:#7fa3c8;
  }
  body{ background:var(--bg); color:var(--ink); }

  /* ===== Layout centrado ===== */
  .page-wrap{ padding:18px 14px; }
  .container-narrow{ max-width:980px; margin:0 auto; }

  /* ===== Header ===== */
  .page-head{ margin-bottom:.75rem; }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }
  .meta-row{ display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
  .chip{
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem;
    font-size:.83rem; color:#1f3b5a; font-weight:600;
  }

  /* ===== Botones ===== */
  .btn-soft {
    background:#fff; border:1px solid #ccc; color:#333;
    border-radius:50px; padding:.5rem 1.25rem; transition:.2s ease; white-space:nowrap;
  }
  .btn-soft:hover{ background:#f2f2f2; color:#000; }

  .btn-accent{
    background:var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:12px;
    padding:.6rem .9rem; transition: transform .15s ease, box-shadow .15s ease; white-space:nowrap;
  }
  .btn-accent:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:10px; padding:.5rem .8rem; font-weight:700; color:#1c3455;
    white-space:nowrap;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  .btn-danger-ghost{
    background:#fff; border:1px solid #fecaca; color:#b91c1c; border-radius:10px; padding:.45rem .65rem; font-weight:700;
  }
  .btn-danger-ghost:hover{ background:#fff1f2; }

  .btn-wrap{ display:flex; flex-wrap:wrap; gap:10px; }
  .btn-wrap-right{ justify-content:flex-end; }
  @media (max-width:576px){
    .btn-wrap,.btn-wrap-right{ display:grid; grid-template-columns:1fr; }
  }

  /* ===== Secciones ===== */
  .section{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 6px 20px rgba(10,30,60,.06); overflow:hidden; margin-bottom:16px;
  }
  .section-head{
    background:linear-gradient(90deg, var(--soft), var(--accent));
    padding:12px 14px; color:#0d223d; font-weight:800; display:flex; align-items:center; justify-content:space-between;
  }
  .section-body{ padding:14px; }

  /* ===== Tarjetitas ===== */
  .q-card{
    background:#fff; border:1px solid var(--stroke); border-radius:14px; padding:12px; margin-bottom:12px;
  }

  /* Grid pregunta */
  .q-grid{ display:grid; grid-template-columns: 1fr 200px 120px 140px; gap:10px; }
  @media (max-width: 992px){ .q-grid{ grid-template-columns: 1fr 1fr; } }
  @media (max-width: 576px){ .q-grid{ grid-template-columns: 1fr; } }

  /* ===== Tabla de opciones ===== */
  .opts-table{ width:100%; border-collapse:separate; border-spacing:0 6px; }
  .opts-table th{
    font-weight:800; color:#1b2a4a; font-size:.92rem; padding:4px 6px;
    border-bottom:2px solid var(--soft);
    text-decoration-thickness:2px;
  }
  .opts-table td{ background:#f7fbff; border:1px solid var(--stroke); padding:6px; border-radius:10px; vertical-align:middle; }
  .opts-table .col-etq{ width:55%; }
  .opts-table .col-pts{ width:15%; }
  .opts-table .col-ord{ width:15%; }
  .opts-table .col-act{ width:15%; }

  .underline-label{
    display:inline-flex; align-items:center; gap:6px;
    padding-bottom:2px; border-bottom:2px solid var(--soft); font-weight:800; color:#1b2a4a;
  }

  .hint{ font-size:.85rem; color:var(--muted); }
  .muted-strong{ color:#35507a; font-weight:700; }
  .danger{ color:#b91c1c; font-weight:700; }
  .ok{ color:#065f46; font-weight:700; }

  /* ===== Inputs ===== */
  .form-label{ font-weight:700; color:#162945; }
  .form-control, .form-select{
    border-radius:12px; border:1px solid var(--stroke); color:#0f1d36;
    padding:.55rem .65rem;
  }
  .form-control::placeholder{ color:#8aa0be; }

  /* Mostrar feedback siempre cuando exista */
  .invalid-feedback{ display:block; }
</style>
@endpush

@section('content')
@php
  $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
  $isAdmin   = !request()->is('medico/*');
@endphp

<div class="page-wrap">
  <div class="container-narrow">

    <!-- ===== Header centrado ===== -->
    <section class="page-head">
      <h1 class="page-title h3 mb-1">Editar preguntas y rangos</h1>

      <div class="meta-row mb-2">
        <span class="chip" title="Identificador"><i class="bi bi-hash me-1"></i>ID {{ $test->idTest }}</span>
        <span class="chip" title="Nombre del test"><i class="bi bi-clipboard-check me-1"></i>{{ $test->nombre }}</span>
      </div>

      <div class="btn-wrap mb-2">
        <button type="button" class="btn btn-soft"
                onclick="window.location='{{ route('medico.tests.index') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver 
        </button>
      </div>

      <div class="btn-wrap btn-wrap-right">
        <a href="{{ route($routeArea.'tests.index') }}" class="btn btn-ghost">
          <i class="bi bi-list-ul me-1"></i> Listado de tests
        </a>
      </div>
    </section>

    @if ($errors->any())
      <div class="alert alert-danger">
        <div class="fw-bold mb-1">Corrige los siguientes errores:</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="builderForm" method="POST" action="{{ route($routeArea.'tests.builder.update', $test->idTest) }}" novalidate>
      @csrf
      @method('PUT')

      {{-- ===================== PREGUNTAS ===================== --}}
      <div class="section" id="secPreguntas">
        <div class="section-head">
          <div class="underline-label"><i class="bi bi-ui-checks-grid"></i> Preguntas</div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-accent" id="btnAddPregunta" title="Agregar una nueva pregunta">
              <i class="bi bi-plus-lg me-1"></i> Agregar pregunta
            </button>
          </div>
        </div>

        <div class="section-body" id="preguntasList">
          {{-- Plantamos preguntas existentes --}}
          @foreach($test->preguntas as $pi => $p)
            <div class="q-card pregunta" data-index="{{ $pi }}">
              <div class="q-grid align-items-start">
                <div>
                  <label class="form-label"><i class="bi bi-chat-dots me-1"></i> Texto de la pregunta</label>
                  <input type="text" class="form-control q-texto" value="{{ $p->texto }}"
                         placeholder="Escribe el enunciado…" title="Enunciado visible para el paciente" required>
                </div>
                <div>
                  <label class="form-label"><i class="bi bi-diagram-3 me-1"></i> Tipo</label>
                  <select class="form-select q-tipo" title="Tipo de respuesta" required>
                    <option value="opcion_unica" {{ $p->tipo==='opcion_unica'?'selected':'' }}>Opción única</option>
                    <option value="opcion_multiple" {{ $p->tipo==='opcion_multiple'?'selected':'' }}>Opción múltiple</option>
                    <option value="abierta" {{ $p->tipo==='abierta'?'selected':'' }}>Abierta</option>
                  </select>
                </div>
                <div>
                  <label class="form-label"><i class="bi bi-sort-numeric-down me-1"></i> Orden</label>
                  <input type="number" class="form-control q-orden" value="{{ $p->orden }}" min="1" title="Posición de la pregunta" required>
                </div>
                <div class="text-end d-flex align-items-end">
                  <button type="button" class="btn btn-danger-ghost btnDelPregunta" title="Eliminar esta pregunta">
                    <i class="bi bi-trash3 me-1"></i> Eliminar
                  </button>
                </div>
              </div>

              {{-- Opciones --}}
              <div class="mt-2 opcionesWrap" {{ in_array($p->tipo, ['opcion_unica','opcion_multiple']) ? '' : 'style=display:none;' }}>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="hint"><i class="bi bi-info-circle me-1"></i>Agrega al menos 2 opciones si no es abierta.</div>
                  <button type="button" class="btn btn-ghost btnAddOpcion" title="Añadir opción">
                    <i class="bi bi-plus-lg me-1"></i> Opción
                  </button>
                </div>

                <table class="opts-table">
                  <thead>
                    <tr>
                      <th class="col-etq"><i class="bi bi-tags me-1"></i> Etiqueta</th>
                      <th class="col-pts"><i class="bi bi-bullseye me-1"></i> Puntaje</th>
                      <th class="col-ord"><i class="bi bi-sort-numeric-down me-1"></i> Orden</th>
                      <th class="col-act text-end"><i class="bi bi-gear me-1"></i> Acciones</th>
                    </tr>
                  </thead>
                  <tbody class="opcionesList">
                    @foreach($p->opciones as $oi => $o)
                      <tr class="opcion" data-oindex="{{ $oi }}">
                        <td>
                          <input type="text" class="form-control o-etiqueta" value="{{ $o->etiqueta }}"
                                 placeholder="Nunca / Varios días / ..." title="Texto visible de la opción" required>
                        </td>
                        <td>
                          <input type="number" class="form-control o-puntaje" value="{{ $o->puntaje }}" step="1" title="Puntaje de esta opción" required>
                        </td>
                        <td>
                          <input type="number" class="form-control o-orden" value="{{ $o->orden }}" min="1" title="Orden de la opción" required>
                        </td>
                        <td class="text-end">
                          <button type="button" class="btn btn-danger-ghost btnDelOpcion" title="Eliminar opción">
                            <i class="bi bi-x-lg me-1"></i> Eliminar
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- ===================== RANGOS ===================== --}}
      <div class="section" id="secRangos">
        <div class="section-head">
          <div class="underline-label"><i class="bi bi-graph-up-arrow"></i> Rangos de evaluación</div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-accent" id="btnAddRango" title="Agregar un rango">
              <i class="bi bi-plus-lg me-1"></i> Agregar rango
            </button>
          </div>
        </div>

        <div class="section-body">
          <div class="hint mb-2">
            Define intervalos <strong>sin traslapes</strong>. Ej.: <span class="muted-strong">0–3</span> (mínima), <span class="muted-strong">4–6</span> (leve), <span class="muted-strong">7–9</span> (moderada/severa).
          </div>

          <div id="rangosList">
            @foreach($test->rangos as $ri => $r)
              <div class="q-card rango" data-rindex="{{ $ri }}">
                <div class="row g-2 align-items-end">
                  <div class="col-6 col-md-2">
                    <label class="form-label"><i class="bi bi-chevron-double-down me-1"></i> Mín</label>
                    <input type="number" class="form-control r-min" value="{{ $r->minPuntaje }}" title="Puntaje mínimo" required>
                  </div>
                  <div class="col-6 col-md-2">
                    <label class="form-label"><i class="bi bi-chevron-double-up me-1"></i> Máx</label>
                    <input type="number" class="form-control r-max" value="{{ $r->maxPuntaje }}" title="Puntaje máximo" required>
                  </div>
                  <div class="col-12 col-md-4">
                    <label class="form-label"><i class="bi bi-activity me-1"></i> Diagnóstico</label>
                    <input type="text" class="form-control r-dx" value="{{ $r->diagnostico }}" placeholder="Ansiedad leve / moderada…" title="Etiqueta del resultado" required>
                  </div>
                  <div class="col-12 col-md-3">
                    <label class="form-label"><i class="bi bi-text-left me-1"></i> Descripción (opcional)</label>
                    <input type="text" class="form-control r-desc" value="{{ $r->descripcion }}" title="Descripción breve del rango">
                  </div>
                  <div class="col-12 col-md-1 d-flex justify-content-end">
                    <button type="button" class="btn btn-danger-ghost btnDelRango" title="Eliminar este rango">
                      <i class="bi bi-trash3 me-1"></i> Eliminar
                    </button>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div id="rangosAlert" class="mt-2"></div>
        </div>
      </div>

      {{-- ===================== SUBMIT ===================== --}}
      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route($routeArea.'tests.edit', $test->idTest) }}" class="btn btn-ghost">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-accent">
          <i class="bi bi-save2 me-1"></i> Guardar contenido
        </button>
      </div>

      <div id="dynamicInputs" class="d-none"></div>
    </form>

  </div>
</div>
 @include('medico.bottom-navbar')
@endsection

@push('scripts')
<script>
(function(){
  const preguntasList = document.getElementById('preguntasList');
  const btnAddPregunta = document.getElementById('btnAddPregunta');
  const rangosList = document.getElementById('rangosList');
  const btnAddRango = document.getElementById('btnAddRango');
  const builderForm = document.getElementById('builderForm');
  const dynamicInputs = document.getElementById('dynamicInputs');
  const rangosAlert = document.getElementById('rangosAlert');

  // ==== Helpers: feedback Bootstrap ====
  function showError(el, msg='Completa este campo'){
    el.classList.add('is-invalid');
    let fb = el.parentElement.querySelector('.invalid-feedback');
    if(!fb){
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      el.parentElement.appendChild(fb);
    }
    fb.textContent = msg;
  }
  function clearError(el){
    el.classList.remove('is-invalid');
    const fb = el.parentElement.querySelector('.invalid-feedback');
    if(fb) fb.remove();
  }
  function isEmpty(el){ return (el.value ?? '').toString().trim().length === 0; }
  function isPosInt(el){ const v=parseInt(el.value,10); return Number.isInteger(v) && v>=1; }
  function isNumber(el){ return el.value !== '' && !isNaN(Number(el.value)); }

  // Validación en tiempo real
  document.addEventListener('blur', (e)=>{
    const el = e.target;
    if(el.matches('.q-texto, .q-orden, .o-etiqueta, .o-puntaje, .o-orden, .r-min, .r-max, .r-dx')){
      // regla por tipo
      if(el.classList.contains('q-texto') || el.classList.contains('o-etiqueta') || el.classList.contains('r-dx')){
        isEmpty(el) ? showError(el) : clearError(el);
      }else if(el.classList.contains('q-orden') || el.classList.contains('o-orden')){
        isPosInt(el) ? clearError(el) : showError(el, 'Debe ser entero ≥ 1');
      }else if(el.classList.contains('o-puntaje') || el.classList.contains('r-min') || el.classList.contains('r-max')){
        isNumber(el) ? clearError(el) : showError(el, 'Ingresa un número');
      }
    }
  }, true);

  // ==== Plantillas ====
  function elFrom(html){ const t=document.createElement('template'); t.innerHTML=html.trim(); return t.content.firstChild; }

  const tplOpcion = () => elFrom(`
    <tr class="opcion" data-oindex="0">
      <td>
        <input type="text" class="form-control o-etiqueta" placeholder="Nunca / Varios días / ..." title="Texto visible de la opción" required>
      </td>
      <td>
        <input type="number" class="form-control o-puntaje" value="0" step="1" title="Puntaje de esta opción" required>
      </td>
      <td>
        <input type="number" class="form-control o-orden" value="1" min="1" title="Orden de la opción" required>
      </td>
      <td class="text-end">
        <button type="button" class="btn btn-danger-ghost btnDelOpcion" title="Eliminar opción">
          <i class="bi bi-x-lg me-1"></i> Eliminar
        </button>
      </td>
    </tr>
  `);

  const tplPregunta = () => elFrom(`
    <div class="q-card pregunta" data-index="0">
      <div class="q-grid align-items-start">
        <div>
          <label class="form-label"><i class="bi bi-chat-dots me-1"></i> Texto de la pregunta</label>
          <input type="text" class="form-control q-texto" placeholder="Escribe el enunciado…" title="Enunciado visible para el paciente" required>
        </div>
        <div>
          <label class="form-label"><i class="bi bi-diagram-3 me-1"></i> Tipo</label>
          <select class="form-select q-tipo" title="Tipo de respuesta" required>
            <option value="opcion_unica">Opción única</option>
            <option value="opcion_multiple">Opción múltiple</option>
            <option value="abierta">Abierta</option>
          </select>
        </div>
        <div>
          <label class="form-label"><i class="bi bi-sort-numeric-down me-1"></i> Orden</label>
          <input type="number" class="form-control q-orden" value="1" min="1" title="Posición de la pregunta" required>
        </div>
        <div class="text-end d-flex align-items-end">
          <button type="button" class="btn btn-danger-ghost btnDelPregunta" title="Eliminar esta pregunta">
            <i class="bi bi-trash3 me-1"></i> Eliminar
          </button>
        </div>
      </div>
      <div class="mt-2 opcionesWrap">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="hint"><i class="bi bi-info-circle me-1"></i>Agrega al menos 2 opciones si no es abierta.</div>
          <button type="button" class="btn btn-ghost btnAddOpcion" title="Añadir opción">
            <i class="bi bi-plus-lg me-1"></i> Opción
          </button>
        </div>
        <table class="opts-table">
          <thead>
            <tr>
              <th class="col-etq"><i class="bi bi-tags me-1"></i> Etiqueta</th>
              <th class="col-pts"><i class="bi bi-bullseye me-1"></i> Puntaje</th>
              <th class="col-ord"><i class="bi bi-sort-numeric-down me-1"></i> Orden</th>
              <th class="col-act text-end"><i class="bi bi-gear me-1"></i> Acciones</th>
            </tr>
          </thead>
          <tbody class="opcionesList"></tbody>
        </table>
      </div>
    </div>
  `);

  const tplRango = () => elFrom(`
    <div class="q-card rango" data-rindex="0">
      <div class="row g-2 align-items-end">
        <div class="col-6 col-md-2">
          <label class="form-label"><i class="bi bi-chevron-double-down me-1"></i> Mín</label>
          <input type="number" class="form-control r-min" value="0" title="Puntaje mínimo" required>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label"><i class="bi bi-chevron-double-up me-1"></i> Máx</label>
          <input type="number" class="form-control r-max" value="0" title="Puntaje máximo" required>
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label"><i class="bi bi-activity me-1"></i> Diagnóstico</label>
          <input type="text" class="form-control r-dx" placeholder="Ansiedad leve / moderada…" title="Etiqueta del resultado" required>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label"><i class="bi bi-text-left me-1"></i> Descripción (opcional)</label>
          <input type="text" class="form-control r-desc" title="Descripción breve del rango">
        </div>
        <div class="col-12 col-md-1 d-flex justify-content-end">
          <button type="button" class="btn btn-danger-ghost btnDelRango" title="Eliminar este rango">
            <i class="bi bi-trash3 me-1"></i> Eliminar
          </button>
        </div>
      </div>
    </div>
  `);

  // ==== Alta dinámica ====
  btnAddPregunta.addEventListener('click', () => {
    const node = tplPregunta();
    // Por defecto, 2 opciones
    for(let i=0;i<2;i++) node.querySelector('.opcionesList').appendChild(tplOpcion());
    preguntasList.appendChild(node);
  });

  preguntasList.addEventListener('change', (e) => {
    const card = e.target.closest('.pregunta');
    if(!card) return;
    if(e.target.classList.contains('q-tipo')){
      const wrap = card.querySelector('.opcionesWrap');
      wrap.style.display = (e.target.value === 'abierta') ? 'none' : '';
      clearError(e.target);
    }
  });

  preguntasList.addEventListener('click', (e) => {
    if(e.target.closest('.btnDelPregunta')){
      e.preventDefault();
      e.target.closest('.pregunta').remove();
      return;
    }
    if(e.target.closest('.btnAddOpcion')){
      e.preventDefault();
      const card = e.target.closest('.pregunta');
      card.querySelector('.opcionesList').appendChild(tplOpcion());
      return;
    }
    if(e.target.closest('.btnDelOpcion')){
      e.preventDefault();
      e.target.closest('tr.opcion').remove();
      return;
    }
  });

  btnAddRango.addEventListener('click', (e) => {
    e.preventDefault();
    rangosList.appendChild(tplRango());
  });

  rangosList.addEventListener('click', (e) => {
    if(e.target.closest('.btnDelRango')){
      e.preventDefault();
      e.target.closest('.rango').remove();
    }
  });

  // ==== Submit: validaciones + construcción de inputs ====
  builderForm.addEventListener('submit', (e) => {
    let hasError = false;
    const firstInvalid = [];

    // Limpia previos
    document.querySelectorAll('.is-invalid').forEach(el => clearError(el));
    rangosAlert.innerHTML = '';

    // 1) Validar preguntas
    const preguntas = [...document.querySelectorAll('.pregunta')].map((q,i) => {
      const texto = q.querySelector('.q-texto');
      const tipoSel = q.querySelector('.q-tipo');
      const orden = q.querySelector('.q-orden');

      if(isEmpty(texto)){ showError(texto); hasError = true; firstInvalid.push(texto); }
      if(isEmpty(tipoSel)){ showError(tipoSel); hasError = true; firstInvalid.push(tipoSel); }
      if(!isPosInt(orden)){ showError(orden, 'Debe ser entero ≥ 1'); hasError = true; firstInvalid.push(orden); }

      let opciones = [];
      if(tipoSel.value !== 'abierta'){
        opciones = [...q.querySelectorAll('.opcion')].map((tr) => {
          const etq = tr.querySelector('.o-etiqueta');
          const pts = tr.querySelector('.o-puntaje');
          const ord = tr.querySelector('.o-orden');
          if(isEmpty(etq)){ showError(etq); hasError = true; firstInvalid.push(etq); }
          if(!isNumber(pts)){ showError(pts, 'Ingresa un número'); hasError = true; firstInvalid.push(pts); }
          if(!isPosInt(ord)){ showError(ord, 'Debe ser entero ≥ 1'); hasError = true; firstInvalid.push(ord); }
          return {
            etiqueta: (etq.value||'').trim(),
            puntaje: parseInt(pts.value||'0',10),
            orden: parseInt(ord.value||'0',10)
          };
        }).filter(o => o.etiqueta.length>0);
        if(opciones.length < 2){
          // Marca mínimo los primeros inputs de opción si existen
          const firstRow = q.querySelector('.opcion');
          if(firstRow){
            const etq = firstRow.querySelector('.o-etiqueta');
            showError(etq, 'Agrega al menos 2 opciones');
            hasError = true; firstInvalid.push(etq);
          }
        }
      }

      return {
        texto: (texto.value||'').trim(),
        tipo: tipoSel.value,
        orden: parseInt(orden.value||'0',10) || (i+1),
        opciones
      };
    });

    // 2) Validar rangos
    const rangos = [...document.querySelectorAll('.rango')].map((r) => {
      const min = r.querySelector('.r-min');
      const max = r.querySelector('.r-max');
      const dx  = r.querySelector('.r-dx');
      const desc= r.querySelector('.r-desc');

      if(!isNumber(min)){ showError(min, 'Ingresa un número'); hasError = true; firstInvalid.push(min); }
      if(!isNumber(max)){ showError(max, 'Ingresa un número'); hasError = true; firstInvalid.push(max); }
      if(isEmpty(dx)){ showError(dx); hasError = true; firstInvalid.push(dx); }

      return {
        minPuntaje: parseInt(min.value||'0',10),
        maxPuntaje: parseInt(max.value||'0',10),
        diagnostico: (dx.value||'').trim(),
        descripcion: (desc.value||'').trim(),
        _refs: {min,max}
      };
    });

    // 2b) Reglas de traslape y min<=max
    rangos.forEach(r=>{
      if(r.minPuntaje > r.maxPuntaje){
        showError(r._refs.min, 'Mín ≤ Máx');
        showError(r._refs.max, 'Mín ≤ Máx');
        hasError = true;
        firstInvalid.push(r._refs.min);
      }
    });

    const sorted = [...rangos].sort((a,b)=>a.minPuntaje - b.minPuntaje);
    for(let i=1;i<sorted.length;i++){
      if(sorted[i].minPuntaje <= sorted[i-1].maxPuntaje){
        rangosAlert.innerHTML = '<div class="danger">Los rangos no deben traslaparse (el mínimo debe ser mayor que el máximo anterior).</div>';
        hasError = true;
        break;
      }
    }

    if(hasError){
      e.preventDefault();
      // scroll al primer error
      const first = firstInvalid.find(Boolean);
      if(first){
        first.scrollIntoView({behavior:'smooth', block:'center'});
        first.classList.add('shake-once');
        setTimeout(()=>first.classList.remove('shake-once'), 600);
      }
      return;
    }

    // ==== Construcción de inputs anidados =====
    // limpia
    dynamicInputs.innerHTML = '';

    // serializa preguntas
    preguntas.forEach((p,i)=>{
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="preguntas[${i}][texto]" value="${escapeHtml(p.texto)}">`));
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="preguntas[${i}][tipo]" value="${escapeHtml(p.tipo)}">`));
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="preguntas[${i}][orden]" value="${p.orden}">`));
      if(p.tipo !== 'abierta'){
        p.opciones.forEach((o,j)=>{
          dynamicInputs.appendChild(elFrom(`<input type="hidden" name="preguntas[${i}][opciones][${j}][etiqueta]" value="${escapeHtml(o.etiqueta)}">`));
          dynamicInputs.appendChild(elFrom(`<input type="hidden" name="preguntas[${i}][opciones][${j}][puntaje]" value="${o.puntaje}">`));
          dynamicInputs.appendChild(elFrom(`<input type="hidden" name="preguntas[${i}][opciones][${j}][orden]" value="${o.orden}">`));
        });
      }
    });

    // serializa rangos
    rangos.forEach((r,k)=>{
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="rangos[${k}][minPuntaje]" value="${r.minPuntaje}">`));
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="rangos[${k}][maxPuntaje]" value="${r.maxPuntaje}">`));
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="rangos[${k}][diagnostico]" value="${escapeHtml(r.diagnostico)}">`));
      dynamicInputs.appendChild(elFrom(`<input type="hidden" name="rangos[${k}][descripcion]" value="${escapeHtml(r.descripcion)}">`));
    });
  });

  // util: escape html
  function escapeHtml(s){ return (s??'').toString().replace(/[&<>"']/g,(m)=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[m])); }

})();
</script>
@endpush
