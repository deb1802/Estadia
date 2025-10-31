@extends('layouts.app')
@section('title','Respuestas recibidas')

@push('styles')
<style>
:root{ --bg:#d7dfe9; --soft:#b5c8e1; --accent:#90aacc; --ink:#1b2a4a; --stroke:#e7eef7; }
body{ background:linear-gradient(180deg,var(--bg),#eef3f9); color:var(--ink); }
.wrap{ max-width:780px; margin:0 auto; }
.card{
  background:#fff; border:1px solid var(--stroke); border-radius:18px;
  box-shadow:0 12px 28px rgba(25,55,100,.08); overflow:hidden;
}
.head{ padding:1rem 1.2rem; border-bottom:1px solid var(--stroke); background:#fbfdff; }
.body{ padding:1rem 1.2rem; }
.badge{
  display:inline-block; padding:.25rem .6rem; border-radius:999px; background:#eef6ff; border:1px solid var(--stroke);
}
.cta{ display:flex; gap:.65rem; flex-wrap:wrap; margin-top:1rem; }
.btn{ border-radius:10px; padding:.55rem .9rem; }
.btn-primary{ background:#2563eb; color:#fff; border:none; }
.btn-ghost{ background:#fff; border:1px solid var(--stroke); }
.note{
  background:#f5f9ff; border:1px dashed var(--soft); padding:.8rem; border-radius:12px; font-size:.95rem;
}
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="card">
    <div class="head d-flex justify-content-between align-items-center">
      <h1 class="h5 m-0">Respuestas recibidas</h1>
      <span class="badge">{{ $asignacion->tipoTrastorno ?? 'Evaluación' }}</span>
    </div>
    <div class="body">
      <p class="mb-2"><strong>{{ $asignacion->nombreTest }}</strong></p>
      <p class="text-muted">¡Gracias! Tus respuestas fueron enviadas correctamente.</p>

      <div class="note mb-3">
        Tu médico revisará tus resultados y te compartirá comentarios en tu próxima consulta.
        Si detectas cambios importantes en tu estado de ánimo, ansiedad o bienestar,
        comunícalo a tu médico o acude a atención inmediata si es necesario.
      </div>

      <div class="cta">
        <a class="btn btn-primary" href="{{ route('paciente.tests.index') }}">
          Ver mis tests
        </a>
        <a class="btn btn-ghost" href="{{ route('paciente.dashboard') }}">
          Ir al panel
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
