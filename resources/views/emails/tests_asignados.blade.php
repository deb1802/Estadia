@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Carbon;

  $pacienteNombre  = Str::title($pacienteNombre ?? 'Paciente');
  $medicoNombre    = $medicoNombre ? Str::title($medicoNombre) : null;
  $fechaAsignacion = Carbon::parse($fechaAsignacion ?? now())->locale('es')->isoFormat('DD [de] MMMM [de] YYYY, HH:mm');
@endphp

@component('mail::message')
<style>
  :root{
    --ink:#1b2a4a;
    --muted:#5b6b84;
    --stroke:#e7eef7;
    --lav:#bea4d2;
    --soft:#b5c8e1;
    --accent:#90aacc;
    --bg:#eef3f9;
  }
  *{ box-sizing:border-box; }
  h1,h2,h3,p,ul{ margin:0 0 10px 0; }
  .mw-wrap{
    background: linear-gradient(180deg, #ffffff, var(--bg));
    border:1px solid var(--stroke);
    border-radius:14px;
    padding:18px 18px 6px;
  }
  .mw-head{
    background: linear-gradient(90deg, var(--soft), var(--lav));
    border-radius:10px;
    padding:14px 16px;
    color:#0f2240;
    font-weight:800;
    font-size:20px;
    letter-spacing:.2px;
    margin-bottom:12px;
  }
  .panel{
    background:#fff;
    border:1px solid var(--stroke);
    border-left:4px solid var(--accent);
    border-radius:10px;
    padding:12px 14px;
    color:var(--ink);
    margin:12px 0;
  }
  .hint{
    background: linear-gradient(180deg, #fff, #f7faff);
    border:1px solid var(--stroke);
    border-left:4px solid var(--lav);
    border-radius:10px;
    padding:12px 14px;
    color:var(--muted);
    margin-top:12px;
  }
  .divider{
    height:1px; background:linear-gradient(90deg, transparent, var(--stroke), transparent);
    border:0; margin:18px 0 10px;
  }
  .ink{ color:var(--ink); }
  .muted{ color:var(--muted); }
  .link{ color:var(--accent); text-decoration:underline; }
</style>

<div class="mw-wrap">
  <div class="mw-head">Tienes nuevos tests asignados</div>

  <p class="ink">Hola <strong>{{ $pacienteNombre }}</strong>,</p>

  @if($medicoNombre)
    <p class="ink">El médico <strong>{{ $medicoNombre }}</strong> te ha asignado los siguientes tests psicológicos:</p>
  @else
    <p class="ink">Se te han asignado los siguientes tests psicológicos:</p>
  @endif

  <div class="panel">
    <p><strong>Fecha de asignación:</strong> {{ $fechaAsignacion }}</p>
    <p><strong>Tests:</strong></p>
    <ul style="margin:0; padding-left:18px;">
      @foreach($testsAsignados as $t)
        <li>{{ $t['nombre'] }}@if(!empty($t['tipo'])) <em> ({{ $t['tipo'] }})</em>@endif</li>
      @endforeach
    </ul>
  </div>

  <p class="ink">Al responderlos, el sistema calculará automáticamente tu puntaje y mostrará un diagnóstico sugerido para revisión del médico.</p>

  <div class="hint">
    <strong>¿Cómo responderlos?</strong> Ingresa a tu cuenta en 
    <a href="{{ config('app.url') }}" class="link" target="_blank" rel="noopener">MindWare</a> y entra a la sección 
    <em>“Mis tests asignados”</em>.
  </div>

  <hr class="divider">
  <p class="muted">Gracias,<br><strong>Equipo MindWare</strong></p>
</div>
@endcomponent
 