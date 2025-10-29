@extends('layouts.app')

@section('title', 'Recetas médicas | Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --ink:#1b3b6f;
    --muted:#6b7280;
    --stroke:#e5e7eb;
    --chip:#eef2ff;
    --chip-b:#c7d2fe;
    --brand:#1d4ed8;
    --card:#ffffff;
    --soft:#f8fafc;

    /* grises neutros para botones suaves */
    --g-text:#374151;
    --g-text-strong:#111827;
    --g-borde:#d1d5db;
    --g-borde-2:#9ca3af;
    --g-bg:#ffffff;
    --g-bg-hover:#f3f4f6;
  }

  .content { color: var(--ink); }

  /* 🔍 Barra de búsqueda */
  .card-search {
    background: var(--soft);
    border: 1px solid var(--stroke);
    border-radius: 14px;
  }
  .search-bar {
    display:flex; align-items:center; gap:12px; flex-wrap: wrap;
  }
  .search-input-group { display:flex; gap:10px; align-items:center; flex:1; }
  .search-input-group input {
    flex:1; border-radius: 999px; padding:.75rem 1rem;
  }
  .btn-clear {
    border:1px solid var(--stroke);
  }

  /* 🔙 Botón volver */
  .btn-soft{
    background: var(--g-bg);
    border: 1px solid var(--g-borde);
    color: var(--g-text);
    border-radius: 50px;
    font-weight: 500;
    padding: .5rem 1.25rem;
    transition: all .25s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,.04);
  }
  .btn-soft:hover{
    background: var(--g-bg-hover);
    border-color: var(--g-borde-2);
    color: var(--g-text-strong);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.08);
  }

  /* 💊 Tarjetas */
  .rx-card {
    border: 1px solid var(--stroke);
    border-radius: 16px;
    background: var(--card);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .rx-card:hover { transform: translateY(-2px); box-shadow:0 8px 18px rgba(0,0,0,.06); }

  .rx-head { display:flex; justify-content:space-between; gap:10px; }
  .rx-patient { font-weight:800; font-size:1.05rem; color: var(--ink); }
  .rx-meta { color: var(--muted); font-size:.9rem; }

  .chip {
    display:inline-block; background:var(--chip); border:1px solid var(--chip-b);
    color:#3730a3; padding:2px 8px; border-radius:999px; font-size:.75rem; font-weight:600;
  }
  .rx-actions .btn { min-width: 110px; }
  .rx-observaciones { color: var(--muted); }
</style>
@endpush

@section('content')
<div class="content px-3">
    @include('flash::message')

    {{-- 🔍 Barra de búsqueda (solo por nombre del paciente) --}}
    <div class="card card-body shadow-sm mb-3 card-search">
        <form id="search-form" method="GET" action="{{ route('admin.recetas.index') }}" class="search-bar" role="search" aria-label="Buscar recetas por paciente">
            <div class="search-input-group">
                <span class="input-group-text rounded-pill">
                  <i class="bi bi-search"></i>
                </span>
                <input
                  type="text"
                  id="search-input"
                  name="q"
                  class="form-control"
                  value="{{ $q }}"
                  placeholder="Buscar por nombre del paciente..."
                  autocomplete="off"
                  aria-label="Nombre del paciente"
                >
            </div>

            @if($q !== '')
              <a class="btn btn-light btn-clear rounded-pill" href="{{ route('admin.recetas.index') }}">
                Limpiar
              </a>
            @endif

            <noscript>
              <button class="btn btn-primary rounded-pill" type="submit">Buscar</button>
            </noscript>
        </form>
    </div>

    {{--Botón Volver --}}
    <div class="mb-3">
        <button type="button" class="btn btn-soft"
                onclick="window.location='{{ route('admin.dashboard') }}'">
            <i class="bi bi-arrow-90deg-left me-1"></i> Volver al dashboard
        </button>
    </div>


    {{--Grid de tarjetas --}}
    @php use Illuminate\Support\Str; @endphp

    @if($recetas->count() === 0)
      <div class="alert alert-info border-0 shadow-sm">
        No se encontraron recetas @if($q) para “<strong>{{ $q }}</strong>” @endif.
      </div>
    @else
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
        @foreach ($recetas as $rx)
          <div class="col">
            <div class="rx-card h-100 p-3">
              <div class="rx-head mb-2">
                <div>
                  <div class="rx-patient">
                    {{ $rx->paciente_nombre }} {{ $rx->paciente_apellido }}
                  </div>
                  <div class="rx-meta">
                    <i class="bi bi-person-vcard me-1"></i>
                    Médico: {{ $rx->medico_nombre }} {{ $rx->medico_apellido }}
                    @if(!empty($rx->especialidad))
                      · {{ $rx->especialidad }}
                    @endif
                  </div>
                </div>
                <div class="text-end">
                  <div class="chip">Folio #{{ $rx->idReceta }}</div><br>
                  <small class="text-muted">
                    {{ \Carbon\Carbon::parse($rx->fecha)->format('d/m/Y') }}
                  </small>
                </div>
              </div>

              @if(!empty($rx->observaciones))
                <div class="rx-observaciones mb-3">
                  {{ Str::limit($rx->observaciones, 150) }}
                </div>
              @else
                <div class="rx-observaciones mb-3">
                  <em>Sin observaciones.</em>
                </div>
              @endif

              <div class="rx-actions d-flex gap-2">
                <a href="{{ route('admin.recetas.show', $rx->idReceta) }}" class="btn btn-primary">
                  <i class="bi bi-eye me-1"></i> Ver
                </a>
                <a href="{{ route('admin.recetas.pdf', $rx->idReceta) }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                  <i class="bi bi-filetype-pdf me-1"></i> PDF
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- 📄 Paginación --}}
      <div class="mt-3">
        {{ $recetas->links() }}
      </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function(){
  // 🔁 Auto-submit con debounce al tipear
  const $input = document.getElementById('search-input');
  const $form  = document.getElementById('search-form');
  if(!$input || !$form) return;

  let t = null;
  $input.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => {
      $form.requestSubmit();
    }, 350);
  });

  // Enter = submit inmediato
  $input.addEventListener('keydown', (e) => {
    if(e.key === 'Enter'){
      clearTimeout(t);
      $form.requestSubmit();
    }
  });
})();
</script>
@endpush
