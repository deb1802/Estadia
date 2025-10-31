@extends('layouts.app')

@section('title', 'Editor de preguntas y rangos')

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
  }
  body{ background:var(--bg); color:var(--ink); }

  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:.75rem;
  }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }
  .btn-soft{
    background: var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:12px;
    padding:.6rem .9rem; transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-soft:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }
  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:10px; padding:.45rem .65rem; font-weight:700;
    color:#1c3455;
  }
  .btn-ghost:hover{ background:#f7fbff; }

  .section{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 6px 20px rgba(10,30,60,.06); overflow:hidden; margin-bottom:14px;
  }
  .section-head{
    background: linear-gradient(90deg, var(--soft), var(--accent));
    padding:12px 14px; color:#0d223d; font-weight:800; display:flex; align-items:center; justify-content:space-between;
  }
  .section-body{ padding:14px; }

  .q-card{
    background:#fff; border:1px solid var(--stroke); border-radius:14px; padding:12px; margin-bottom:12px;
  }
  .q-grid{ display:grid; grid-template-columns: 1fr 180px 100px auto; gap:8px; }
  @media (max-width: 768px){
    .q-grid{ grid-template-columns: 1fr; }
  }
  .opts-table{ width:100%; border-collapse:separate; border-spacing:0 6px; }
  .opts-table th{ font-weight:700; color:#2b4466; font-size:.9rem; }
  .opts-table td{ background:#f7fbff; border:1px solid var(--stroke); padding:6px; border-radius:10px; }
  .chip-help{
    display:inline-flex; align-items:center; gap:6px; font-size:.8rem; color:#1f3b5a;
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.25rem .6rem;
  }
  .hint{ font-size:.85rem; color:var(--muted); }
  .danger{ color:#b91c1c; font-weight:700; }
  .ok{ color:#065f46; font-weight:700; }

  .form-control, .form-select{
    border-radius:12px; border:1px solid var(--stroke);
  }
</style>
@endpush

@section('content')
<section class="content-header">
  <div class="page-head">
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('medico.tests.edit', $test->idTest) }}" class="btn btn-ghost"><i class="bi bi-arrow-left"></i></a>
      <div>
        <h1 class="page-title h4">Editor del test</h1>
        <span class="chip-help">
          <i class="bi bi-clipboard-check me-1"></i>ID {{ $test->idTest }} · {{ $test->nombre }}
        </span>
      </div>
    </div>
    <a href="{{ route('medico.tests.index') }}" class="btn btn-ghost"><i class="bi bi-list-ul me-1"></i> Mis tests</a>
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

<form id="builderForm" method="POST" action="{{ route('medico.tests.builder.update', $test->idTest) }}">
  @csrf
  @method('PUT')

  {{-- ===================== PREGUNTAS ===================== --}}
  <div class="section" id="secPreguntas">
    <div class="section-head">
      <div><i class="bi bi-ui-checks-grid me-1"></i> Preguntas</div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-ghost" id="btnAddPregunta"><i class="bi bi-plus-lg me-1"></i> Agregar pregunta</button>
      </div>
    </div>

    <div class="section-body" id="preguntasList">
      {{-- Plantamos preguntas existentes --}}
      @foreach($test->preguntas as $pi => $p)
        <div class="q-card pregunta" data-index="{{ $pi }}">
          <div class="q-grid align-items-start">
            <div>
              <label class="form-label">Texto de la pregunta</label>
              <input type="text" class="form-control q-texto" value="{{ $p->texto }}" placeholder="Escribe el enunciado…">
            </div>
            <div>
              <label class="form-label">Tipo</label>
              <select class="form-select q-tipo">
                <option value="opcion_unica" {{ $p->tipo==='opcion_unica'?'selected':'' }}>Opción única</option>
                <option value="opcion_multiple" {{ $p->tipo==='opcion_multiple'?'selected':'' }}>Opción múltiple</option>
                <option value="abierta" {{ $p->tipo==='abierta'?'selected':'' }}>Abierta</option>
              </select>
            </div>
            <div>
              <label class="form-label">Orden</label>
              <input type="number" class="form-control q-orden" value="{{ $p->orden }}" min="1">
            </div>
            <div class="text-end">
              <label class="form-label d-block">&nbsp;</label>
              <button type="button" class="btn btn-ghost btnDelPregunta"><i class="bi bi-trash3"></i></button>
            </div>
          </div>

          {{-- Opciones --}}
          <div class="mt-2 opcionesWrap" {{ in_array($p->tipo, ['opcion_unica','opcion_multiple']) ? '' : 'style=display:none;' }}>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <div class="hint"><i class="bi bi-info-circle me-1"></i>Agrega al menos 2 opciones si no es abierta.</div>
              <button type="button" class="btn btn-ghost btnAddOpcion"><i class="bi bi-plus"></i> Opción</button>
            </div>
            <table class="opts-table">
              <thead>
                <tr>
                  <th style="width:55%;">Etiqueta</th>
                  <th style="width:15%;">Puntaje</th>
                  <th style="width:15%;">Orden</th>
                  <th style="width:15%;"></th>
                </tr>
              </thead>
              <tbody class="opcionesList">
                @foreach($p->opciones as $oi => $o)
                  <tr class="opcion" data-oindex="{{ $oi }}">
                    <td><input type="text" class="form-control o-etiqueta" value="{{ $o->etiqueta }}" placeholder="Nunca / Varios días / ..."></td>
                    <td><input type="number" class="form-control o-puntaje" value="{{ $o->puntaje }}" step="1"></td>
                    <td><input type="number" class="form-control o-orden" value="{{ $o->orden }}" min="1"></td>
                    <td class="text-end"><button type="button" class="btn btn-ghost btnDelOpcion"><i class="bi bi-x-lg"></i></button></td>
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
      <div><i class="bi bi-graph-up-arrow me-1"></i> Rangos de evaluación</div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-ghost" id="btnAddRango"><i class="bi bi-plus-lg me-1"></i> Agregar rango</button>
      </div>
    </div>

    <div class="section-body">
      <div class="hint mb-2">
        Define intervalos <strong>sin traslapes</strong>. Ej.: 0–3 (mínima), 4–6 (leve), 7–9 (moderada/severa).
      </div>
      <div id="rangosList">
        @foreach($test->rangos as $ri => $r)
          <div class="q-card rango" data-rindex="{{ $ri }}">
            <div class="row g-2">
              <div class="col-6 col-md-2">
                <label class="form-label">Mín</label>
                <input type="number" class="form-control r-min" value="{{ $r->minPuntaje }}">
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label">Máx</label>
                <input type="number" class="form-control r-max" value="{{ $r->maxPuntaje }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Diagnóstico</label>
                <input type="text" class="form-control r-dx" value="{{ $r->diagnostico }}" placeholder="Ansiedad leve / moderada…">
              </div>
              <div class="col-12 col-md-3">
                <label class="form-label">Descripción (opcional)</label>
                <input type="text" class="form-control r-desc" value="{{ $r->descripcion }}">
              </div>
              <div class="col-12 col-md-1 d-flex align-items-end justify-content-end">
                <button type="button" class="btn btn-ghost btnDelRango"><i class="bi bi-trash3"></i></button>
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
    <a href="{{ route('medico.tests.edit', $test->idTest) }}" class="btn btn-ghost"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
    <button type="submit" class="btn btn-soft"><i class="bi bi-save2 me-1"></i> Guardar contenido</button>
  </div>

  {{-- Aquí se inyectarán los inputs anidados antes de enviar --}}
  <div id="dynamicInputs" class="d-none"></div>
</form>

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

  // ==== Helpers ====
  function el(html){ const t = document.createElement('template'); t.innerHTML = html.trim(); return t.content.firstChild; }
  function escapeHTML(s){ return (s??'').toString().replace(/[&<>"']/g,m=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[m])); }

  // ==== Plantillas ====
  const tplPregunta = () => el(`
    <div class="q-card pregunta" data-index="0">
      <div class="q-grid align-items-start">
        <div>
          <label class="form-label">Texto de la pregunta</label>
          <input type="text" class="form-control q-texto" placeholder="Escribe el enunciado…">
        </div>
        <div>
          <label class="form-label">Tipo</label>
          <select class="form-select q-tipo">
            <option value="opcion_unica">Opción única</option>
            <option value="opcion_multiple">Opción múltiple</option>
            <option value="abierta">Abierta</option>
          </select>
        </div>
        <div>
          <label class="form-label">Orden</label>
          <input type="number" class="form-control q-orden" value="1" min="1">
        </div>
        <div class="text-end">
          <label class="form-label d-block">&nbsp;</label>
          <button type="button" class="btn btn-ghost btnDelPregunta"><i class="bi bi-trash3"></i></button>
        </div>
      </div>
      <div class="mt-2 opcionesWrap" style="">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <div class="hint"><i class="bi bi-info-circle me-1"></i>Agrega al menos 2 opciones si no es abierta.</div>
          <button type="button" class="btn btn-ghost btnAddOpcion"><i class="bi bi-plus"></i> Opción</button>
        </div>
        <table class="opts-table">
          <thead>
            <tr>
              <th style="width:55%;">Etiqueta</th>
              <th style="width:15%;">Puntaje</th>
              <th style="width:15%;">Orden</th>
              <th style="width:15%;"></th>
            </tr>
          </thead>
          <tbody class="opcionesList"></tbody>
        </table>
      </div>
    </div>
  `);

  const tplOpcion = () => el(`
    <tr class="opcion" data-oindex="0">
      <td><input type="text" class="form-control o-etiqueta" placeholder="Nunca / Varios días / ..."></td>
      <td><input type="number" class="form-control o-puntaje" value="0" step="1"></td>
      <td><input type="number" class="form-control o-orden" value="1" min="1"></td>
      <td class="text-end"><button type="button" class="btn btn-ghost btnDelOpcion"><i class="bi bi-x-lg"></i></button></td>
    </tr>
  `);

  const tplRango = () => el(`
    <div class="q-card rango" data-rindex="0">
      <div class="row g-2">
        <div class="col-6 col-md-2">
          <label class="form-label">Mín</label>
          <input type="number" class="form-control r-min" value="0">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label">Máx</label>
          <input type="number" class="form-control r-max" value="0">
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label">Diagnóstico</label>
          <input type="text" class="form-control r-dx" placeholder="Ansiedad leve / moderada…">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Descripción (opcional)</label>
          <input type="text" class="form-control r-desc">
        </div>
        <div class="col-12 col-md-1 d-flex align-items-end justify-content-end">
          <button type="button" class="btn btn-ghost btnDelRango"><i class="bi bi-trash3"></i></button>
        </div>
      </div>
    </div>
  `);

  // ==== Eventos dinámicos ====
  btnAddPregunta.addEventListener('click', () => {
    const node = tplPregunta();
    // agrega 4 opciones por defecto
    for(let i=0;i<4;i++) node.querySelector('.opcionesList').appendChild(tplOpcion());
    preguntasList.appendChild(node);
  });

  preguntasList.addEventListener('change', (e) => {
    const card = e.target.closest('.pregunta');
    if(!card) return;
    if(e.target.classList.contains('q-tipo')){
      const wrap = card.querySelector('.opcionesWrap');
      const isOpen = e.target.value === 'abierta' ? 'none' : '';
      wrap.style.display = isOpen;
    }
  });

  preguntasList.addEventListener('click', (e) => {
    if(e.target.closest('.btnDelPregunta')){
      e.target.closest('.pregunta').remove();
    }
    if(e.target.closest('.btnAddOpcion')){
      const card = e.target.closest('.pregunta');
      card.querySelector('.opcionesList').appendChild(tplOpcion());
    }
    if(e.target.closest('.btnDelOpcion')){
      e.target.closest('tr.opcion').remove();
    }
  });

  btnAddRango.addEventListener('click', () => {
    rangosList.appendChild(tplRango());
  });

  rangosList.addEventListener('click', (e) => {
    if(e.target.closest('.btnDelRango')){
      e.target.closest('.rango').remove();
    }
  });

  // ==== Validación de traslapes de rangos (cliente) & creación de inputs anidados ====
  builderForm.addEventListener('submit', (e) => {
    dynamicInputs.innerHTML = ''; // reset

    // 1) Recolectar preguntas
    const preguntas = [...document.querySelectorAll('.pregunta')].map((q,i) => {
      const tipo = q.querySelector('.q-tipo').value;
      const obj = {
        texto: q.querySelector('.q-texto').value.trim(),
        tipo: tipo,
        orden: parseInt(q.querySelector('.q-orden').value||'0',10) || (i+1),
        opciones: []
      };
      if(tipo !== 'abierta'){
        obj.opciones = [...q.querySelectorAll('.opcion')].map((tr,j) => ({
          etiqueta: tr.querySelector('.o-etiqueta').value.trim(),
          puntaje: parseInt(tr.querySelector('.o-puntaje').value||'0',10),
          orden: parseInt(tr.querySelector('.o-orden').value||'0',10) || (j+1)
        })).filter(o => o.etiqueta.length>0);
      }
      return obj;
    });

    // 2) Recolectar rangos
    const rangos = [...document.querySelectorAll('.rango')].map((r) => ({
      minPuntaje: parseInt(r.querySelector('.r-min').value||'0',10),
      maxPuntaje: parseInt(r.querySelector('.r-max').value||'0',10),
      diagnostico: r.querySelector('.r-dx').value.trim(),
      descripcion: r.querySelector('.r-desc').value.trim(),
    }));

    // 3) Validaciones rápidas
    // 3.1 preguntas
    if(preguntas.length === 0){
      alert('Debes agregar al menos una pregunta.');
      e.preventDefault(); return;
    }
    for(const p of preguntas){
      if(!p.texto){ alert('Hay una pregunta sin texto.'); e.preventDefault(); return; }
      if(p.tipo !== 'abierta' && (!p.opciones || p.opciones.length < 2)){
        alert('Cada pregunta de opción debe tener al menos 2 opciones.'); e.preventDefault(); return;
      }
    }
    // 3.2 rangos
    if(rangos.length === 0){
      alert('Debes agregar al menos un rango.'); e.preventDefault(); return;
    }
    rangos.sort((a,b)=>a.minPuntaje-b.minPuntaje);
    for(let i=1;i<rangos.length;i++){
      if(rangos[i].minPuntaje <= rangos[i-1].maxPuntaje){
        rangosAlert.innerHTML = '<div class="danger">Los rangos no deben traslaparse (min debe ser mayor que el máx anterior).</div>';
        e.preventDefault(); return;
      }
    }
    rangosAlert.innerHTML = '';

    // 4) Construir inputs anidados para Laravel
    // preguntas[i][texto], preguntas[i][tipo], preguntas[i][orden], preguntas[i][opciones][j][...]
    preguntas.forEach((p,i)=>{
      dynamicInputs.appendChild(el(`<input type="hidden" name="preguntas[${i}][texto]" value="${escapeHTML(p.texto)}">`));
      dynamicInputs.appendChild(el(`<input type="hidden" name="preguntas[${i}][tipo]" value="${escapeHTML(p.tipo)}">`));
      dynamicInputs.appendChild(el(`<input type="hidden" name="preguntas[${i}][orden]" value="${p.orden}">`));
      if(p.tipo !== 'abierta'){
        p.opciones.forEach((o,j)=>{
          dynamicInputs.appendChild(el(`<input type="hidden" name="preguntas[${i}][opciones][${j}][etiqueta]" value="${escapeHTML(o.etiqueta)}">`));
          dynamicInputs.appendChild(el(`<input type="hidden" name="preguntas[${i}][opciones][${j}][puntaje]" value="${o.puntaje}">`));
          dynamicInputs.appendChild(el(`<input type="hidden" name="preguntas[${i}][opciones][${j}][orden]" value="${o.orden}">`));
        });
      }
    });

    // rangos[k][minPuntaje], ...
    rangos.forEach((r,k)=>{
      dynamicInputs.appendChild(el(`<input type="hidden" name="rangos[${k}][minPuntaje]" value="${r.minPuntaje}">`));
      dynamicInputs.appendChild(el(`<input type="hidden" name="rangos[${k}][maxPuntaje]" value="${r.maxPuntaje}">`));
      dynamicInputs.appendChild(el(`<input type="hidden" name="rangos[${k}][diagnostico]" value="${escapeHTML(r.diagnostico)}">`));
      dynamicInputs.appendChild(el(`<input type="hidden" name="rangos[${k}][descripcion]" value="${escapeHTML(r.descripcion)}">`));
    });
  });
})();
</script>
@endpush
@endsection
