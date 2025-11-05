@extends('layouts.app')

@section('title', 'Mis recetas')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    /* Paleta azul-morado que usas */
    --bg:#edf1f7;
    --ink:#1b2a4a;
    --muted:#5b6b84;
    --stroke:#dde6f3;

    --blue:#d7dfe9;   /* azul suave */
    --purple:#f0e2f8; /* morado suave */
    --accent:#90aacc; /* acento azulado */

    --btn-border:#cfd8ea;
    --btn-border-2:#b7c3dc;
  }

  .content-header{
    background: linear-gradient(180deg, #ffffff, var(--bg));
    border-bottom: 1px solid var(--stroke);
  }
  .content{ color:var(--ink); }

  /* Grid responsivo de tarjetas largas */
  .rx-grid{ --bs-gutter-x:1rem; --bs-gutter-y:1rem; }

  /* Tarjeta larga */
  .rx-card{
    position:relative;
    display:flex; gap:16px; align-items:center;
    background: linear-gradient(135deg, var(--blue), var(--purple));
    border: 1px solid var(--stroke);
    border-radius: 18px;
    padding: 14px 16px;
    box-shadow: 0 10px 24px rgba(34,55,90,.08);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    overflow: hidden;
    min-height: 96px;
  }
  .rx-card:hover{
    transform: translateY(-3px);
    box-shadow: 0 16px 32px rgba(34,55,90,.12);
    border-color:#cbd5e1;
  }

  /* Sutil brillo animado al fondo */
  .rx-card::after{
    content:"";
    position:absolute; inset:-40%;
    background: radial-gradient(60% 60% at 30% 30%, rgba(255,255,255,.22), transparent 60%),
                radial-gradient(60% 60% at 70% 70%, rgba(255,255,255,.16), transparent 60%);
    animation: floatGlow 6s ease-in-out infinite alternate;
    pointer-events:none;
  }
  @keyframes floatGlow{
    0%  { transform: translate3d(-10px,-6px,0) scale(1);    opacity:.85; }
    100%{ transform: translate3d(10px, 6px,0)  scale(1.02); opacity:1; }
  }

  /* Icono lateral */
  .rx-icon{
    flex:0 0 52px; height:52px; width:52px;
    display:grid; place-items:center;
    background:#fff;
    border:1px solid rgba(27,42,74,.08);
    border-radius:14px;
    box-shadow: 0 6px 16px rgba(27,42,74,.10);
    color:#22427a;
  }
  .rx-icon i{ font-size:1.35rem; }

  /* Contenido */
  .rx-body{ flex:1 1 auto; min-width: 0; }
  .rx-top{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    margin-bottom: .15rem;
  }
  .rx-title{
    margin:0; font-weight:800; letter-spacing:.2px;
  }
  .chip{
    display:inline-block;
    background: rgba(255,255,255,.6);
    border:1px solid rgba(27,42,74,.12);
    color:#12365e;
    border-radius:999px;
    padding:2px 10px;
    font-weight:700; font-size:.8rem;
  }
  .rx-meta{
    color: var(--muted);
    font-size:.92rem;
  }
  .rx-observ{
    color: var(--ink);
    opacity:.9;
    font-size:.95rem;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }

  /* Acciones */
  .rx-actions{
    display:flex; gap:.5rem; flex:0 0 auto; width: 260px;
  }
  .btn-soft{
    background:#ffffffd9;
    border:1px solid var(--btn-border);
    color:#1f2f52;
    border-radius: 12px;
    padding:.55rem .9rem;
    font-weight:600;
    transition: all .18s ease;
    box-shadow: 0 4px 12px rgba(27,42,74,.08);
  }
  .btn-soft:hover{
    background:#fff;
    border-color: var(--btn-border-2);
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(27,42,74,.12);
  }
  .btn-ghost{
    background: transparent;
    border:1px dashed var(--btn-border);
    color:#132f66;
  }
  .btn-ghost:hover{ border-style: solid; background:#ffffffbf; }

  /* Estado vacío */
  .state-empty{
    background: linear-gradient(180deg, #ffffff, var(--bg));
    border:1px dashed var(--stroke);
    border-radius:16px;
  }

  /* Responsive */
  @media (max-width: 992px){
    .rx-actions{ width: 220px; }
  }
  @media (max-width: 768px){
    .rx-card{ flex-direction: column; align-items: stretch; gap:10px; }
    .rx-actions{ width: 100%; }
  }
</style>
@endpush

@section('content')

<section class="content-header py-3">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h1 class="fw-bold text-primary mb-0">
      <i class="bi bi-file-medical me-2"></i> Mis recetas
    </h1>
    <a href="{{ route('paciente.dashboard') }}" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>
</section>

<div class="content px-4 py-3">

  @if($recetas->isEmpty())
    <div class="state-empty p-4 text-center">
      <div class="mb-2"><i class="bi bi-inbox fs-3 text-primary"></i></div>
      <h5 class="mb-1">Aún no tienes recetas registradas</h5>
      <p class="text-muted mb-0">Cuando tu médico emita una receta, aparecerá aquí para verla o descargarla en PDF.</p>
    </div>
  @else
    <div class="row rx-grid">
      @foreach($recetas as $r)
        @php
          $fechaFmt = \Carbon\Carbon::parse($r->fecha)->format('d/m/Y');
          $medico   = trim(($r->medico_nombre ?? '').' '.($r->medico_apellido ?? ''));
          $obs      = \Illuminate\Support\Str::limit($r->observaciones ?? 'Sin observaciones.', 160);
        @endphp

        <div class="col-12">
          <div class="rx-card">
            <div class="rx-icon">
              <i class="bi bi-filetype-pdf"></i>
            </div>

            <div class="rx-body">
              <div class="rx-top">
                <h5 class="rx-title">Receta médica</h5>
                <span class="chip">Folio #{{ $r->idReceta }}</span>
                <span class="chip"><i class="bi bi-calendar3 me-1"></i>{{ $fechaFmt }}</span>
              </div>
              <div class="rx-meta mb-1">
                <i class="bi bi-person-vcard me-1"></i>
                Médico: <strong>{{ $medico }}</strong>
              </div>
              <div class="rx-observ">
                {{ $obs }}
              </div>
            </div>

            <div class="rx-actions">
              <a href="{{ route('paciente.recetas.show', ['idReceta'=>$r->idReceta]) }}" class="btn btn-soft w-100">
                <i class="bi bi-eye me-1"></i> Ver
              </a>
              <a href="{{ route('paciente.recetas.pdf', ['idReceta'=>$r->idReceta]) }}" class="btn btn-soft btn-ghost w-100" target="_blank" rel="noopener">
                <i class="bi bi-download me-1"></i> PDF
              </a>
            </div>
          </div>
        </div>

      @endforeach
    </div>
  @endif
</div>
@include('paciente.bottom-nabvar')
@endsection
