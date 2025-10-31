@extends('layouts.app')

@section('title','Asignar tests psicológicos')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --ink:#1b2a4a; --muted:#6b7280; --soft:#b5c8e1; --accent:#90aacc;
    --card:#ffffff; --stroke:#e5edf6;
  }
  body{ background:linear-gradient(180deg, var(--bg) 0%, #eaf1f8 100%); color:var(--ink); }

  .wrap{ max-width:1200px; margin:0 auto; padding:16px 14px; }

  /* ====== Search (full width) ====== */
  .card-search{ background:#f5f9ff; border:1px solid var(--stroke); border-radius:16px; }
  .search-bar{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
  }
  .search-input-group{
    display:flex; align-items:center; gap:8px; flex:1 1 520px;
  }
  .search-input-group .form-control{
    border-radius:999px; border:1px solid var(--stroke); padding:.6rem .9rem;
  }
  .search-input-group .form-select{
    min-width:190px; border-radius:12px; border:1px solid var(--stroke);
  }

  /* ====== Toolbar (solo acciones) ====== */
  .toolbar{
    display:flex; gap:12px; align-items:stretch; flex-wrap:wrap;
    background:#f5f9ff; border:1px solid var(--stroke); border-radius:16px; padding:.75rem;
    box-shadow:0 8px 20px rgba(20,40,70,.05);
  }
  .btn-soft{
    background:var(--accent); color:#0e2a43; font-weight:800; border:none; border-radius:12px; padding:.6rem 1rem;
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-soft:hover{ transform:translateY(-1px); box-shadow:0 10px 22px rgba(0,0,0,.08); }
  .btn-soft:disabled{ opacity:.55; cursor:not-allowed; }

  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:12px; padding:.5rem .9rem; font-weight:800; color:#1c3455;
  }
  .btn-ghost:hover{ background:#f4f8ff; }

  .select-nice{
    background:#fff; border:1px solid var(--stroke); border-radius:12px; padding:.55rem .75rem; min-width:260px;
  }

  /* Panel y contador */
  .panel{ background:#eef4fb; border:1px solid var(--stroke); border-radius:16px; padding:1rem; }
  .counter{ font-size:.88rem; color:#445; }

  /* Grid tarjetas */
  .grid{ display:grid; gap:16px; grid-template-columns: repeat( auto-fill, minmax(280px,1fr) ); }

  .card-test{
    background:var(--card); border:1px solid var(--stroke); border-radius:20px; overflow:hidden;
    box-shadow:0 12px 28px rgba(25,55,100,.08);
    display:flex; flex-direction:column; transition:transform .15s ease, box-shadow .15s ease;
  }
  .card-test:hover{ transform:translateY(-2px); box-shadow:0 16px 34px rgba(25,55,100,.12); }

  /* Header con imagen centrada en recuadro */
  .thumb{
    height:160px;
    background:linear-gradient(180deg, var(--soft), var(--accent));
    display:flex; align-items:center; justify-content:center;
  }
  .thumb .img-box{
    background:#fff;
    border-radius:16px;
    padding:10px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
    width:120px; height:120px;
    display:flex; align-items:center; justify-content:center;
  }
  .thumb .img-box img{
    max-width:90%;
    max-height:90%;
    object-fit:contain;
  }

  .card-body{ padding:14px 14px 10px; }
  .title-row{ display:flex; justify-content:space-between; gap:10px; align-items:center; }
  .title{ font-weight:900; font-size:1.06rem; margin:0; line-height:1.2; color:#0f2444; }
  .pill{
    background:#eef6ff; border:1px solid var(--stroke); border-radius:999px; padding:.2rem .55rem; font-size:.78rem; color:#123a6a; font-weight:700;
  }
  .desc{ color:var(--muted); font-size:.92rem; margin:.4rem 0 .6rem; min-height:44px; }

  .foot{
    display:flex; justify-content:space-between; align-items:center; gap:8px; padding:10px 14px;
    border-top:1px solid var(--stroke); background:#fafcff;
  }
  .check-wrap{ display:flex; align-items:center; gap:8px; }
</style>
@endpush

@section('content')
<div class="wrap">
  <section class="content-header mb-2">
    <h1 class="h3 fw-bold mb-2">Asignar tests</h1>
  </section>

  {{-- 🔍 Barra de búsqueda (full width + dinámica) --}}
  <div class="card card-body shadow-sm mb-3 card-search">
    <form id="search-form" method="GET" action="{{ route('medico.tests.asignar.index') }}" class="search-bar">
      <div class="search-input-group">
        <input
          type="text"
          id="search-input"
          name="q"
          class="form-control"
          value="{{ old('q', request('q')) }}"
          placeholder="Buscar en todos los campos…"
          autocomplete="off"
          aria-label="Buscar tests"
        >
        @php $type = request('type','all'); @endphp
        <select id="search-type" name="type" class="form-select">
          <option value="all"       {{ $type==='all' ? 'selected':'' }}>Todos</option>
          <option value="nombre"    {{ $type==='nombre' ? 'selected':'' }}>Por nombre</option>
          <option value="trastorno" {{ $type==='trastorno' ? 'selected':'' }}>Por trastorno</option>
          <option value="estado"    {{ $type==='estado' ? 'selected':'' }}>Por estado</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button class="btn-soft" type="submit" title="Filtrar">
          <i class="bi bi-funnel"></i>
        </button>
        <a href="{{ route('medico.tests.asignar.index') }}" class="btn-ghost" title="Limpiar">
          <i class="bi bi-x-circle"></i>
        </a>
      </div>
    </form>
  </div>

  <form id="form-asignar" method="POST" action="{{ route('medico.tests.asignar.store') }}">
    @csrf

    {{-- ======= Toolbar (acciones principales) ======= --}}
    <div class="toolbar mb-3">
      <button type="submit" class="btn-soft" id="btn-assign" disabled>
        <i class="bi bi-plus-lg me-1"></i> Asignar test
      </button>

      {{-- Select paciente --}}
      <select name="paciente_id" class="select-nice" id="paciente_id" required>
        <option value="">Seleccionar paciente</option>
        @foreach(($pacientes ?? []) as $p)
          <option value="{{ $p->idPaciente ?? $p->id }}">{{ $p->nombre_completo ?? ($p->nombre.' '.$p->apellido) }}</option>
        @endforeach
      </select>
    </div>

    {{-- ======= Listado de tests disponibles ======= --}}
    <div class="panel mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-bold">Tests disponibles</div>
        <div class="counter"><span id="sel-count">0</span> seleccionados</div>
      </div>

      <div class="grid">
        @forelse(($tests ?? []) as $t)
          @php
            $tipo = strtolower($t->tipoTrastorno ?? '');
            $key = 'general';
            if (str_contains($tipo, 'ansied'))      { $key = 'ansiedad'; }
            elseif (str_contains($tipo, 'depres'))  { $key = 'depresion'; }
            elseif (str_contains($tipo, 'autoest')) { $key = 'autoestima'; }
            $img = asset("img/$key.png");
            $fallback = asset('img/general.png');
          @endphp

          <div class="card-test">
            <div class="thumb">
              <div class="img-box">
                <img src="{{ $img }}" alt="Imagen {{ $key }}" onerror="this.onerror=null;this.src='{{ $fallback }}'">
              </div>
            </div>

            <div class="card-body">
              <div class="title-row">
                <h3 class="title">{{ $t->nombre }}</h3>
                <span class="pill">{{ $t->tipoTrastorno ?? 'General' }}</span>
              </div>
              <p class="desc">{{ \Illuminate\Support\Str::limit($t->descripcion ?? 'Sin descripción', 150) }}</p>
            </div>

            <div class="foot">
              <a href="{{ route('medico.tests.show', $t->idTest) }}" class="btn-ghost">
                <i class="bi bi-eye me-1"></i> Ver detalle
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
</div>
@endsection

@push('scripts')
<script>
  // ===== Búsqueda dinámica (debounce) =====
  const debounce = (fn, delay = 450) => {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
  };

  (function(){
    const form   = document.getElementById('search-form');
    const input  = document.getElementById('search-input');
    const select = document.getElementById('search-type');

    if(form && input && select){
      const autoSubmit = debounce(() => {
        if (input.value.trim() === '' && select.value === 'all') return;
        form.requestSubmit();
      }, 450);

      input.addEventListener('keyup', autoSubmit);
      select.addEventListener('change', () => form.requestSubmit());

      const placeholders = {
        all:        'Buscar en todos los campos…',
        nombre:     'Ej. GAD-7, PHQ-9, PSS-10…',
        trastorno:  'Ej. Ansiedad, Depresión, Estrés…',
        estado:     'Ej. activo | inactivo'
      };
      const setPh = () => { input.placeholder = placeholders[select.value] || placeholders.all; };
      setPh();
      select.addEventListener('change', setPh);
    }

    // ===== Habilitar botón Asignar según selección + paciente =====
    const checks = document.querySelectorAll('.test-check');
    const btn = document.getElementById('btn-assign');
    const selCount = document.getElementById('sel-count');
    const selPaciente = document.getElementById('paciente_id');

    function refreshState(){
      const selected = [...checks].filter(c => c.checked).length;
      if (selCount) selCount.textContent = selected;
      if (btn) btn.disabled = !(selected > 0 && selPaciente && selPaciente.value);
    }
    checks.forEach(c => c.addEventListener('change', refreshState));
    selPaciente?.addEventListener('change', refreshState);
    refreshState();
  })();
</script>
@endpush
