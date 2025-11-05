@extends('layouts.app')

@section('content')
<div class="seguimiento-wrap">
  <div class="container-narrow">

    {{-- Topbar: título + volver (sin márgenes extra) --}}
    <div class="topbar">
      <h2 class="titulo">
        <i class="fas fa-user-md me-2"></i> Seguimiento de Pacientes
      </h2>
      <button type="button" class="btn-soft" onclick="window.location='{{ route('medico.dashboard') }}'">
        <i class="fas fa-arrow-left me-2"></i> Volver al dashboard
      </button>
    </div>

    {{-- 🔍 Barra de búsqueda (estilo card como la referencia) --}}
    <div class="card card-body shadow-sm mb-3 card-search">
      <form id="search-form" class="search-bar" onsubmit="return false;">
        <div class="search-input-group">
          <input
            type="text"
            id="search-input"
            name="q"
            class="form-control"
            placeholder="Buscar pacientes…"
            autocomplete="off"
            aria-label="Buscar pacientes"
          >

          <select id="search-type" name="type" class="form-select" aria-label="Tipo de búsqueda">
            <option value="all" selected>🔎 Buscar en todos</option>
            <option value="nombreCompleto">Por nombre completo</option>
            <option value="correo">Por correo</option>
            <option value="telefono">Por teléfono</option>
          </select>
        </div>

        <div class="search-actions">
          <button type="button" id="btnSearch" class="btn btn-primary">
            <i class="bi bi-search me-1"></i> Buscar
          </button>
          <button type="button" id="btnClear" class="btn btn-outline-secondary d-none">
            <i class="bi bi-x-circle me-1"></i> Limpiar
          </button>
        </div>
      </form>
    </div>

    {{-- Grid de tarjetas --}}
    <div id="gridPacientes" class="grid">
      @forelse($pacientes as $p)
        @php
          $full   = trim(($p->nombre ?? '').' '.($p->apellido ?? ''));
          $ini    = strtoupper(mb_substr($p->nombre ?? 'P',0,1).mb_substr($p->apellido ?? 'X',0,1));
          $correo = $p->correo ?? '';
          $tel    = $p->telefono ?? '—';
        @endphp

        <article
          class="card-paciente"
          data-nombre="{{ Str::lower($full) }}"
          data-correo="{{ Str::lower($correo) }}"
          data-telefono="{{ Str::lower($tel) }}"
        >
          <div class="card-head">
            <div class="avatar"><span>{{ $ini }}</span></div>
            <div class="id-box">
              <h5 class="nombre" title="{{ $full }}">{{ $full }}</h5>
              <div class="chips">
                <span class="chip mail" title="{{ $correo }}">
                  <i class="fas fa-envelope me-1"></i>{{ $correo }}
                </span>
                <span class="chip phone">
                  <i class="fas fa-phone-alt me-1"></i>{{ $tel }}
                </span>
              </div>
            </div>
          </div>

          <div class="card-foot">
            <a href="{{ route('medico.seguimiento.show', $p->id) }}" class="btn-follow">
              <i class="fas fa-chart-line me-2"></i> Ver seguimiento
            </a>
          </div>
        </article>
      @empty
        <div class="estado-vacio text-center">
          <p>No hay pacientes para mostrar.</p>
        </div>
      @endforelse
    </div>

    {{-- Vacío por búsqueda --}}
    <div id="emptySearch" class="estado-vacio text-center d-none">
      <p>No se encontraron coincidencias con tu búsqueda.</p>
    </div>
  </div>
</div>

{{-- Filtro en vivo (usa la barra nueva) --}}
<script>
(function(){
  const qInput   = document.getElementById('search-input');
  const typeSel  = document.getElementById('search-type');
  const btnSearch= document.getElementById('btnSearch');
  const btnClear = document.getElementById('btnClear');
  const grid     = document.getElementById('gridPacientes');
  const empty    = document.getElementById('emptySearch');

  function filtrar() {
    const q = (qInput.value || '').toLowerCase().trim();
    const type = (typeSel.value || 'all');
    const cards = grid.querySelectorAll('.card-paciente');
    let visibles = 0;

    cards.forEach(card => {
      const nombre   = card.dataset.nombre || '';
      const correo   = card.dataset.correo || '';
      const telefono = card.dataset.telefono || '';

      let hay = false;
      if (q.length === 0) {
        hay = true;
      } else if (type === 'all') {
        hay = nombre.includes(q) || correo.includes(q) || telefono.includes(q);
      } else if (type === 'nombreCompleto') {
        hay = nombre.includes(q);
      } else if (type === 'correo') {
        hay = correo.includes(q);
      } else if (type === 'telefono') {
        hay = telefono.includes(q);
      }

      card.style.display = hay ? '' : 'none';
      if (hay) visibles++;
    });

    empty.classList.toggle('d-none', visibles !== 0);
    btnClear.classList.toggle('d-none', q.length === 0 && (type === 'all'));
  }

  qInput.addEventListener('input', filtrar);
  typeSel.addEventListener('change', filtrar);
  btnSearch.addEventListener('click', filtrar);
  btnClear.addEventListener('click', () => {
    qInput.value = '';
    typeSel.value = 'all';
    filtrar();
    qInput.focus();
  });

  // Init
  filtrar();
})();
</script>

@include('medico.bottom-navbar')
@endsection

@push('styles')
<style>
  /* ===== Paleta MindWare */
  :root{
    --ink:#1b2a4a;
    --ink-2:#3a536d;
    --stroke:#e7eef7;
    --soft:#b5c8e1;
    --accent:#90aacc;
    --card:#ffffff;
  }

  /* ===== Lienzo plano (SIN degradado) */
  .seguimiento-wrap{
    background: #f7f9fc;  /* plano y limpio para no chocar con el navbar */
    min-height: calc(100vh - 0px);
    padding: 12px 12px 24px;
  }
  .container-narrow{ max-width: 980px; margin: 0 auto; }

  /* ===== Topbar compacto */
  .topbar{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin: 0 0 10px 0;
  }
  .titulo{
    margin:0; font-weight: 900; color: var(--ink); letter-spacing:.2px;
  }
  .btn-soft{
    background:#fff; border:1px solid var(--stroke); color: var(--ink);
    border-radius:999px; padding:.5rem 1.1rem; font-weight:700;
    transition:.2s ease; box-shadow:0 6px 16px rgba(17,35,61,.08);
  }
  .btn-soft:hover{ background:#f7fbff; color:#0f2442; }

  /* ===== Search card (como la referencia) ===== */
  .card-search{ border:1px solid var(--stroke); }
  .search-bar{ display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between; }
  .search-input-group{ display:flex; gap:12px; align-items:center; flex:1; min-width:260px; }
  .search-input-group .form-control{ flex:1; }
  .search-actions{ display:flex; gap:10px; }

  /* ===== Grid de tarjetas */
  .grid{
    display:grid; gap:14px;
    grid-template-columns: repeat( auto-fill, minmax(260px, 1fr) );
  }

  /* ===== Tarjeta de paciente */
  .card-paciente{
    background: var(--card);
    border:1px solid var(--stroke);
    border-radius:16px;
    padding: 14px 14px 12px;
    box-shadow:0 12px 24px rgba(20,40,70,.08);
    display:flex; flex-direction:column; justify-content:space-between; gap:10px;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }
  .card-paciente:hover{
    transform: translateY(-2px);
    box-shadow:0 16px 28px rgba(20,40,70,.10);
    border-color:#dbe6f5;
  }

  .card-head{ display:flex; gap:12px; align-items:center; }
  .avatar{
    width:54px; height:54px; border-radius:50%;
    display:grid; place-items:center; font-weight:900; color:#1f2d3d;
    background: radial-gradient(120% 120% at 10% 0%, #bea4d2 0%, rgba(190,164,210,.25) 35%, transparent 36%),
                radial-gradient(150% 150% at 90% 10%, #b5c8e1 0%, rgba(181,200,225,.18) 38%, transparent 40%),
                #ffffff;
    border:1px solid #eaeef7; box-shadow:0 6px 18px rgba(20,40,70,.06);
  }
  .id-box{ min-width:0; }
  .nombre{
    margin:0; font-weight:800; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }

  .chips{ display:flex; flex-wrap:wrap; gap:6px; margin-top:4px; }
  .chip{
    display:inline-flex; align-items:center; gap:6px;
    background:#f7fbff; border:1px solid var(--stroke); color:#26415f;
    border-radius:999px; padding:.15rem .6rem; font-size:.86rem; font-weight:600;
  }

  .card-foot{ display:flex; justify-content:flex-end; }
  .btn-follow{
    display:inline-flex; align-items:center; gap:.35rem;
    background: linear-gradient(90deg, var(--accent), var(--soft));
    color:#fff; border:none; border-radius:12px; padding:.5rem .9rem; font-weight:800;
    text-decoration:none; box-shadow:0 10px 18px rgba(37,99,235,.18);
    transition:.15s ease;
  }
  .btn-follow:hover{ filter:brightness(.96); transform: translateY(-1px); }

  .estado-vacio{
    background:#fff; border:1px solid var(--stroke); border-radius:14px; padding:16px;
    color:var(--ink-2); box-shadow:0 8px 18px rgba(25,55,100,.06); margin-top:12px;
  }

  .d-none{ display:none !important; }

  /* ===== Estilos de encabezado elegante centrado ===== */
.topbar {
  display: flex;
  flex-direction: column;          /* Centra todo en columna */
  align-items: center;             /* Alinea horizontalmente al centro */
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1.8rem;
  text-align: center;
}

.titulo {
  font-weight: 900;
  font-size: 2rem;
  color: #1b2a4a;                  /* Azul oscuro MindWare */
  letter-spacing: 0.3px;
  text-shadow: 0 2px 6px rgba(0,0,0,0.05); /* Suaviza el texto */
  display: flex;
  align-items: center;
  gap: 0.4rem;
  justify-content: center;         /* Centra el texto + ícono */
}

.titulo i {
  color: #90aacc;                  /* Azul suave MindWare */
  font-size: 1.8rem;
}

.btn-soft {
  background: #fff;
  border: 1px solid #e7eef7;
  color: #1b2a4a;
  border-radius: 999px;
  padding: .55rem 1.25rem;
  font-weight: 700;
  transition: .25s ease;
  box-shadow: 0 6px 14px rgba(17,35,61,.08);
}
.btn-soft:hover {
  background: #f7fbff;
  color: #0f2442;
}
</style>
@endpush
