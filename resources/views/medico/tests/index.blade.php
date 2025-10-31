@extends('layouts.app')

@section('title', 'Mis Tests')

@push('styles')
<style>
  :root{
    --bg:#d7dfe9;      /* fondo principal */
    --card:#ffffff;    /* fondo tarjeta */
    --ink:#1b2a4a;     /* texto primario */
    --muted:#5b6b84;   /* texto secundario */
    --soft:#b5c8e1;    /* acentos suaves */
    --accent:#90aacc;  /* acento principal */
    --stroke:#e7eef7;  /* borde sutil */
  }

  body{ background: var(--bg); color: var(--ink); }

  /* Header */
  .page-head{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin-bottom: 1rem;
  }
  .page-title{
    font-weight:800; letter-spacing:.3px; margin:0;
  }
  .badge-soft{
    background: linear-gradient(180deg, var(--soft), #cbd9ea);
    color:#1f3b5a; border:1px solid var(--stroke); padding:.35rem .6rem; border-radius:999px;
    font-size:.85rem; font-weight:600;
  }

  /* Toolbar búsqueda + botón */
  .toolbar{
    background: #f5f8fd; border:1px solid var(--stroke); border-radius:16px; padding:.75rem;
    display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;
  }
  .search-group{
    display:flex; align-items:center; gap:8px; flex:1; min-width:260px;
    background:#fff; border:1px solid var(--stroke); border-radius:999px; padding:.375rem .6rem;
    box-shadow: 0 3px 10px rgba(20,45,80,.05);
  }
  .search-group input{
    border:none; outline:none; width:100%; font-size:.95rem; color:var(--ink);
  }
  .btn-primary-soft{
    background: var(--accent);
    color:#0d223d; font-weight:700; border:none; border-radius:12px; padding:.6rem .9rem;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-primary-soft:hover{ transform: translateY(-1px); box-shadow:0 10px 20px rgba(0,0,0,.08); }

  /* Grid tarjetas */
  .cards-grid{
    display:grid; gap:16px;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    margin-top:1rem;
  }
  .test-card{
    background: var(--card); border:1px solid var(--stroke); border-radius:18px; overflow:hidden;
    box-shadow: 0 6px 20px rgba(10,30,60,.06); position:relative;
    display:flex; flex-direction:column;
  }
  .test-card .bar{
    height:6px; background: linear-gradient(90deg, var(--soft), var(--accent));
  }
  .test-card .body{
    padding:14px 14px 8px 14px; display:flex; flex-direction:column; gap:10px;
  }
  .test-title{
    font-size:1.05rem; font-weight:800; line-height:1.2; margin:0;
  }
  .test-meta{
    display:flex; flex-wrap:wrap; gap:8px;
  }
  .chip{
    background:#f2f6fb; border:1px solid var(--stroke); border-radius:999px; padding:.2rem .55rem;
    font-size:.78rem; color:#2b4466; font-weight:600;
  }
  .desc{
    color:var(--muted); font-size:.9rem; line-height:1.4; max-height:3.6em; overflow:hidden;
  }
  .footer-actions{
    display:flex; align-items:center; justify-content:space-between; gap:8px; padding:12px 14px 14px;
  }
  .btn-ghost{
    background:#fff; border:1px solid var(--stroke); border-radius:10px; padding:.45rem .65rem; font-weight:700;
    color:#1c3455;
  }
  .btn-ghost:hover{ background:#f7fbff; }
  .status-dot{
    width:8px; height:8px; border-radius:50%;
    display:inline-block; margin-right:6px; vertical-align:middle;
  }
  .on{ background:#22c55e; }
  .off{ background:#94a3b8; }

  /* Empty state */
  .empty{
    text-align:center; background:#fff; border:1px dashed var(--soft); border-radius:16px; padding:28px;
    color:var(--muted);
  }

  /* Paginación */
  .pagination{ gap:6px; }
  .pagination .page-link{
    border-radius:10px; border:1px solid var(--stroke); color:#1c3455;
  }
</style>
@endpush

@section('content')
<section class="content-header">
  <div class="page-head">
    <div>
      <h1 class="page-title h3">Mis Tests</h1>
      <span class="badge-soft"><i class="bi bi-clipboard-check me-1"></i>Gestor de tests psicológicos</span>
    </div>
    <a href="{{ route('medico.tests.create') }}" class="btn btn-primary-soft">
      <i class="bi bi-plus-lg me-1"></i> Nuevo test
    </a>
  </div>

  <form method="GET" action="{{ route('medico.tests.index') }}" class="toolbar">
    <div class="search-group">
      <i class="bi bi-search ms-1"></i>
      <input
        type="text"
        name="q"
        value="{{ $q ?? '' }}"
        placeholder="Buscar por nombre o trastorno…"
        aria-label="Buscar test"
      >
    </div>
    <button class="btn btn-primary-soft" type="submit">
      <i class="bi bi-funnel me-1"></i> Filtrar
    </button>
  </form>
</section>

<section class="content-body mt-3">
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
              <span class="chip"><i class="bi bi-calendar-event me-1"></i>{{ \Illuminate\Support\Carbon::parse($t->fechaCreacion)->format('d/m/Y') }}</span>
            </div>

            @if($t->descripcion)
              <p class="desc">{{ $t->descripcion }}</p>
            @endif
          </div>

          <div class="footer-actions">
            <div class="d-flex gap-2">
              <a href="{{ route('medico.tests.show', $t->idTest) }}" class="btn btn-ghost" title="Ver">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('medico.tests.edit', $t->idTest) }}" class="btn btn-ghost" title="Editar datos">
                <i class="bi bi-pencil-square"></i>
              </a>
              @if(Route::has('medico.tests.builder.edit'))
              <a href="{{ route('medico.tests.builder.edit', $t->idTest) }}" class="btn btn-ghost" title="Editar preguntas y rangos">
                <i class="bi bi-sliders"></i>
              </a>
              @endif
            </div>
            <form action="{{ route('medico.tests.destroy', $t->idTest) }}" method="POST" onsubmit="return confirm('¿Eliminar este test? Esta acción no se puede deshacer.');">
              @csrf
              @method('DELETE')
              <button class="btn btn-ghost" type="submit" title="Eliminar">
                <i class="bi bi-trash3"></i>
              </button>
            </form>
          </div>
        </article>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $tests->links() }}
    </div>
  @endif
</section>
@endsection
