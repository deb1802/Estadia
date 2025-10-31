@extends('layouts.app')

@section('title','Asignar tests psicológicos')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9;
    --ink:#1b2a4a;
    --muted:#6b7280;
    --soft:#b5c8e1;
    --accent:#90aacc;
    --card:#ffffff;
    --stroke:#e5edf6;
  }
  body{ background:linear-gradient(180deg, var(--bg) 0%, #eaf1f8 100%); color:var(--ink); }

  .panel{ background:#eef4fb; border:1px solid var(--stroke); border-radius:16px; padding:1rem; }
  .toolbar{
    display:flex; gap:12px; align-items:stretch; flex-wrap:wrap;
    background:#f5f9ff; border:1px solid var(--stroke); border-radius:16px; padding:.75rem;
  }
  .btn-assign{
    background:#3b82f6; color:#fff; border:none; border-radius:12px; padding:.6rem 1rem; font-weight:700;
  }
  .btn-assign:disabled{ opacity:.55; cursor:not-allowed; }
  .select-nice, .input-nice{
    background:#fff; border:1px solid var(--stroke); border-radius:12px; padding:.55rem .75rem; min-width:240px;
  }
  .search-group{ display:flex; gap:8px; align-items:center; }

  .grid{
    display:grid; gap:14px;
    grid-template-columns: repeat( auto-fill, minmax(260px,1fr) );
  }
  .card-test{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px;
    box-shadow:0 10px 24px rgba(25,55,100,.06); overflow:hidden; display:flex; flex-direction:column;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .card-test:hover{ transform: translateY(-1px); box-shadow:0 14px 28px rgba(25,55,100,.10); }
  .thumb{
    height:130px; background:linear-gradient(90deg, var(--soft), var(--accent));
    display:flex; align-items:center; justify-content:center; position:relative;
  }
  .thumb .photo-placeholder{
    width:90px; height:90px; border-radius:14px; background:#ffffffaa; border:2px dashed #cfe0f3;
    display:flex; align-items:center; justify-content:center; font-size:12px; color:#274; text-align:center; padding:6px;
  }
  .card-body{ padding:12px 12px 10px; }
  .title-row{ display:flex; justify-content:space-between; gap:8px; align-items:center; }
  .title{ font-weight:800; font-size:1.05rem; margin:0; }
  .pill{
    background:#eef6ff; border:1px solid var(--stroke); border-radius:999px; padding:.15rem .55rem; font-size:.78rem;
  }
  .desc{ color:var(--muted); font-size:.9rem; margin:.35rem 0 .6rem; min-height:42px; }
  .foot{
    display:flex; justify-content:space-between; align-items:center; gap:8px; padding:10px 12px;
    border-top:1px solid var(--stroke); background:#fafcff;
  }
  .btn-soft{
    background:var(--accent); color:#0e2a43; font-weight:700; border:none; border-radius:10px; padding:.45rem .7rem;
  }
  .check-wrap{ display:flex; align-items:center; gap:8px; }
  .counter{ font-size:.86rem; color:#334; }
</style>
@endpush

@section('content')
<section class="content-header mb-2">
  <h1 class="h3 fw-bold">Tests Psicológicos</h1>
</section>

<form id="form-asignar" method="POST" action="{{ route('medico.tests.asignar.store') }}">
  @csrf

  {{-- ======= Toolbar ======= --}}
  <div class="toolbar mb-3">
    <button type="submit" class="btn-assign" id="btn-assign" disabled>
      <i class="bi bi-plus-lg me-1"></i> Asignar test
    </button>

    {{-- Select paciente --}}
    <select name="paciente_id" class="select-nice" id="paciente_id" required>
      <option value="">Seleccionar paciente</option>
      @foreach(($pacientes ?? []) as $p)
        <option value="{{ $p->idPaciente ?? $p->id }}">{{ $p->nombre_completo ?? ($p->nombre.' '.$p->apellido) }}</option>
      @endforeach
    </select>

    {{-- Buscador / filtros simples --}}
    <div class="search-group">
      <input type="text" name="q" value="{{ request('q') }}" class="input-nice" placeholder="Buscar por nombre o trastorno">
      <button class="btn btn-light border" formaction="{{ route('medico.tests.asignar.index') }}">
        <i class="bi bi-funnel"></i>
      </button>
    </div>
  </div>

  {{-- ======= Listado de tests disponibles ======= --}}
  <div class="panel mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">Tests disponibles</div>
      <div class="counter"><span id="sel-count">0</span> seleccionados</div>
    </div>

    <div class="grid">
      @forelse(($tests ?? []) as $t)
        <div class="card-test">
          <div class="thumb">
            {{-- Espacio para foto/ilustración --}}
            <div class="photo-placeholder">
              Subir foto<br>(300×300)
            </div>
          </div>

          <div class="card-body">
            <div class="title-row">
              <h3 class="title">{{ $t->nombre }}</h3>
              <span class="pill">{{ $t->tipoTrastorno ?? 'General' }}</span>
            </div>
            <p class="desc">{{ \Illuminate\Support\Str::limit($t->descripcion ?? 'Sin descripción', 120) }}</p>
          </div>

          <div class="foot">
            <a href="{{ route('medico.tests.show', $t->idTest) }}" class="btn-soft">
              Ver detalle
            </a>
            <div class="check-wrap">
              <input class="form-check-input test-check" type="checkbox" name="tests[]" value="{{ $t->idTest }}" id="t{{ $t->idTest }}">
              <label class="form-check-label" for="t{{ $t->idTest }}">Seleccionar</label>
            </div>
          </div>
        </div>
      @empty
        <div class="text-muted">No hay tests disponibles. Crea uno primero.</div>
      @endforelse
    </div>
  </div>
</form>

{{-- Paginación opcional --}}
@if(isset($tests) && method_exists($tests,'links'))
  <div class="mt-2">
    {{ $tests->links() }}
  </div>
@endif
@endsection

@push('scripts')
<script>
  // Habilitar/deshabilitar botón Asignar en función de selección + paciente
  const form = document.getElementById('form-asignar');
  const checks = document.querySelectorAll('.test-check');
  const btn = document.getElementById('btn-assign');
  const selCount = document.getElementById('sel-count');
  const selPaciente = document.getElementById('paciente_id');

  function refreshState(){
    const selected = [...checks].filter(c => c.checked).length;
    selCount.textContent = selected;
    btn.disabled = !(selected > 0 && selPaciente.value);
  }
  checks.forEach(c => c.addEventListener('change', refreshState));
  selPaciente?.addEventListener('change', refreshState);
  refreshState();
</script>
@endpush
