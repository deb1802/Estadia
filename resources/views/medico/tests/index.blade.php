@extends('layouts.app')

@section('title', 'Mis Tests')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9; --card:#ffffff; --ink:#1b2a4a; --muted:#5b6b84;
    --soft:#b5c8e1; --accent:#90aacc; --stroke:#e7eef7;
    --ink-strong:#0e2442;
  }
  body{ background: var(--bg); color: var(--ink); }

  .btn-soft{
        background: var(--g-bg);
        border: 1px solid var(--g-borde);
        color: var(--g-text);
        border-radius: 50px;
        font-weight: 500;
        padding: .5rem 1.25rem;
        transition: all .25s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,.04);
        margin-top: 8px; /* 🔹 Lo baja un poco para que no se encime */
      }

      .btn-soft:hover{
        background: var(--g-bg-hover);
        border-color: var(--g-borde-2);
        color: var(--g-text-strong);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,.08);
      }

      .btn-soft:active{
        transform: scale(.98);
        box-shadow: 0 2px 6px rgba(0,0,0,.06);
      }

      .btn-soft i{
        font-size: 1rem;
        vertical-align: middle;
      }

  .page-wrap{ max-width:1200px; margin:0 auto; padding:18px 14px; }

  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom:.5rem;
  }
  .page-title{ font-weight:800; letter-spacing:.3px; margin:0; }
  .badge-soft{
    background:linear-gradient(180deg, var(--soft), #cbd9ea);
    color:#1f3b5a; border:1px solid var(--stroke); padding:.35rem .6rem; border-radius:999px;
    font-size:.85rem; font-weight:600;
  }
  .btn-primary-soft{
    background:var(--accent); color:#0d223d; font-weight:700; border:none; border-radius:12px; padding:.6rem .9rem;
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-primary-soft:hover{ transform:translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }

  /* ===== Barra de búsqueda FULL WIDTH ===== */
  .search-block{ margin:.75rem 0 1rem; }
  .card-search{ background:#fff; border:1px solid var(--stroke); border-radius:16px; }
  .search-bar{ display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
  .search-input-group{ display:flex; align-items:center; gap:10px; flex:1 1 auto; min-width:280px; }
  .search-text{ flex:1 1 700px; min-width:360px; border-radius:12px; }
  .search-type{ flex:0 0 220px; max-width:260px; border-radius:12px; }
  .search-actions .btn{ border-radius:12px; }

  /* ===== Tarjetas ===== */
  .cards-grid{
    display:grid; gap:18px;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    margin-top:1rem;
  }
  .test-card{
    background:var(--card); border:1px solid var(--stroke); border-radius:18px; overflow:hidden;
    box-shadow:0 6px 20px rgba(10,30,60,.06); position:relative;
    display:flex; flex-direction:column; min-height:230px;
  }
  .test-card .bar{ height:6px; background:linear-gradient(90deg, var(--soft), var(--accent)); }
  .test-card .body{
    padding:14px 14px 8px; display:flex; flex-direction:column; gap:10px;
    max-height:220px; overflow:auto;
  }
  .test-card .body::-webkit-scrollbar{ width:8px; }
  .test-card .body::-webkit-scrollbar-thumb{ background:#e6edf6; border-radius:10px; }

  .test-title{ font-size:1.08rem; font-weight:800; line-height:1.2; margin:0; color:var(--ink-strong); }
  .test-meta{ display:flex; flex-wrap:wrap; gap:8px; }
  .chip{
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.2rem .55rem;
    font-size:.78rem; color:#2b4466; font-weight:600;
  }
  .desc{ color:var(--muted); font-size:.92rem; line-height:1.45; }

  .footer-actions{
    display:flex; align-items:center; justify-content:space-between; gap:8px; padding:12px 14px 14px;
  }

  /* ===== Botones de acción (ícono visible con buen contraste) ===== */
  .btn-icon{
    background:#f7f9fd;
    border:1px solid #cfd9ea;
    border-radius:12px;
    padding:.5rem .6rem;
    color:#183354;
    line-height:1;
    display:inline-flex; align-items:center; justify-content:center;
    box-shadow: 0 1px 0 rgba(0,0,0,.02);
  }
  .btn-icon i{ font-size:1.1rem; color:#183354; } /* fuerza color del ícono */
  .btn-icon:hover{
    background:#eef4ff;
    border-color:#b9c9e4;
  }
  .btn-icon-danger{
    background:#fff3f3;
    border:1px solid #f1c3c3;
    color:#7a1c1c;
  }
  .btn-icon-danger i{ color:#7a1c1c; }
  .btn-icon-danger:hover{
    background:#ffe9e9;
    border-color:#e7a9a9;
  }

  .status-dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; vertical-align:middle; }
  .on{ background:#22c55e; } .off{ background:#94a3b8; }

  .empty{
    text-align:center; background:#fff; border:1px dashed var(--soft); border-radius:16px; padding:28px; color:#5b6b84;
  }

  .pagination{ gap:6px; justify-content:center; }
  .pagination .page-link{ border-radius:10px; border:1px solid var(--stroke); color:#1c3455; }
</style>
@endpush



@section('content')
<div class="page-wrap">
  <!-- ===== Header ===== -->
  <section class="content-header">
    <div class="page-head flex-column align-items-start">
      <div class="w-100 d-flex justify-content-between flex-wrap gap-2">
        <div>
          <h1 class="page-title h3 mb-1">Tests Psicológicos</h1>
        </div>
        <a href="{{ route('medico.tests.create') }}" class="btn btn-primary-soft">
          <i class="bi bi-plus-lg me-1"></i> Crear nuevo test
        </a>
      </div>

      {{-- 🔹 Botón Volver (debajo, centrado) --}}
      <div class="w-100 text-center mt-3">
        <button type="button" class="btn btn-soft px-4 py-2"
                onclick="window.location='{{ route('medico.dashboard') }}'">
          <i class="bi bi-arrow-90deg-left me-1"></i> Volver
        </button>
      </div>

      {{-- 🔹 Botón Asignar Test a Pacientes (más abajo) --}}
      <div class="w-100 text-center mt-3">
        <a href="{{ route('medico.tests.asignar.index') }}" class="btn btn-accent px-4 py-2">
          <i class="bi bi-people-fill me-1"></i> Asignar test a pacientes
        </a>
      </div>
    </div>
  </section>

  <!-- ===== Barra de búsqueda FULL WIDTH ===== -->
  <div class="search-block">
    <form id="search-form" method="GET" action="{{ route('medico.tests.index') }}" class="card card-body shadow-sm card-search">
      <div class="search-bar">
        <div class="search-input-group">
          <input
            type="text"
            id="search-input"
            name="q"
            class="form-control search-text"
            value="{{ $q ?? '' }}"
            placeholder="Buscar en todos los campos…"
            autocomplete="off"
            aria-label="Buscar tests"
          >
          <select id="search-type" name="type" class="form-select search-type">
            <option value="all" {{ ($type ?? 'all')==='all' ? 'selected' : '' }}>Todos</option>
            <option value="nombre" {{ ($type ?? '')==='nombre' ? 'selected' : '' }}>Por nombre</option>
            <option value="trastorno" {{ ($type ?? '')==='trastorno' ? 'selected' : '' }}>Por trastorno</option>
            <option value="estado" {{ ($type ?? '')==='estado' ? 'selected' : '' }}>Por estado</option>
          </select>
        </div>

        <div class="d-flex gap-2 search-actions">
          <button class="btn btn-primary-soft" type="submit" title="Aplicar filtros" data-bs-toggle="tooltip" data-bs-title="Filtrar">
            <i class="bi bi-funnel"></i>
          </button>
          <button class="btn btn-outline-secondary" type="button" id="btn-clear" title="Limpiar filtros" data-bs-toggle="tooltip" data-bs-title="Limpiar">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- ===== Listado ===== -->
  <section class="content-body mt-2">
    @if($tests->count() === 0)
      <div class="empty">
        <div class="mb-2" style="font-weight:800;">No hay tests</div>
        <p class="mb-3">Crea tu primer test para comenzar a agregar preguntas, opciones y rangos.</p>
        <a href="{{ route('medico.tests.create') }}" class="btn btn-primary-soft">
          <i class="bi bi-plus-circle me-1"></i> Crear test
        </a>
      </div>
    @else
      <div class="cards-grid">
        @foreach($tests as $t)
          <article class="test-card">
            <div class="bar"></div>
            <div class="body">
              <h3 class="test-title">{{ $t->nombre }}</h3>
              <div class="test-meta">
                <span class="chip"><i class="bi bi-journal-text me-1"></i>ID: {{ $t->idTest }}</span>
                @if($t->tipoTrastorno)
                  <span class="chip"><i class="bi bi-activity me-1"></i>{{ $t->tipoTrastorno }}</span>
                @endif
                <span class="chip">
                  <span class="status-dot {{ $t->estado === 'activo' ? 'on':'off' }}"></span>
                  {{ ucfirst($t->estado) }}
                </span>
                <span class="chip">
                  <i class="bi bi-calendar-event me-1"></i>{{ \Illuminate\Support\Carbon::parse($t->fechaCreacion)->format('d/m/Y') }}
                </span>
              </div>
              @if($t->descripcion)
                <p class="desc mb-0">{{ $t->descripcion }}</p>
              @endif
            </div>

            <div class="footer-actions">
              <div class="d-flex gap-2 flex-wrap">
                <!-- Íconos con tooltips -->
                <a href="{{ route('medico.tests.show', $t->idTest) }}" class="btn-icon" data-bs-toggle="tooltip" data-bs-title="Ver">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('medico.tests.edit', $t->idTest) }}" class="btn-icon" data-bs-toggle="tooltip" data-bs-title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                @if(Route::has('medico.tests.builder.edit'))
                  <a href="{{ route('medico.tests.builder.edit', $t->idTest) }}" class="btn-icon" data-bs-toggle="tooltip" data-bs-title="Preguntas y rangos">
                    <i class="bi bi-sliders"></i>
                  </a>
                @endif
              </div>

              <form action="{{ route('medico.tests.destroy', $t->idTest) }}" method="POST" class="form-delete">
                @csrf
                @method('DELETE')
                <button type="button" class="btn-icon btn-icon-danger btn-delete" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                  <i class="bi bi-trash3"></i>
                </button>
              </form>
            </div>
          </article>
        @endforeach
      </div>

      <div class="mt-3 d-flex justify-content-center">
        {{ $tests->links() }}
      </div>
    @endif
  </section>
</div>
@endsection

@push('scripts')
<script>
  // --- Cargar Bootstrap Bundle si no está presente (para tooltips) ---
  (function ensureBootstrap(cb){
    if (window.bootstrap && window.bootstrap.Tooltip) { cb(); return; }
    const s = document.createElement('script');
    s.src = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js";
    s.onload = cb;
    document.head.appendChild(s);
  })(function initUI(){

    // ====== Tooltips Bootstrap (activa todo lo que tenga data-bs-toggle="tooltip") ======
    const initTooltips = () => {
      const els = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      els.forEach(el => new window.bootstrap.Tooltip(el));
    };
    initTooltips();

    // ====== Búsqueda dinámica + limpiar ======
    const debounce = (fn, delay = 450) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); }; };

    const form   = document.getElementById('search-form');
    const input  = document.getElementById('search-input');
    const select = document.getElementById('search-type');
    const btnClr = document.getElementById('btn-clear');

    const placeholders = {
      all:        'Buscar en todos los campos…',
      nombre:     'Ej. Inventario de ansiedad de Beck',
      trastorno:  'Ej. Ansiedad, Depresión…',
      estado:     'Ej. activo | inactivo'
    };
    const setPh = () => input.placeholder = placeholders[select.value] || 'Buscar tests…';

    const autoSubmit = debounce(() => {
      if ((input.value.trim() === '') && (select.value === 'all')) return;
      form.requestSubmit();
    }, 450);

    input.addEventListener('keyup', autoSubmit);
    select.addEventListener('change', () => { setPh(); form.requestSubmit(); });
    setPh();

    btnClr?.addEventListener('click', () => {
      input.value = '';
      select.value = 'all';
      setPh();
      form.requestSubmit();
    });

    // ====== Confirmación de eliminar (SweetAlert) ======
    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function () {
        const form = this.closest('form.form-delete');
        if (!form) return;
        if (!window.Swal) {
          console.warn('SweetAlert2 no está cargado.');
          return form.submit();
        }
        Swal.fire({
          title: '¿Eliminar test?',
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) form.submit();
        });
      });
    });
  });
</script>
@include('medico.bottom-navbar')
@endpush
