@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Carbon;

  $pacienteNombre       = Str::title($pacienteNombre      ?? 'Paciente');
  $medicoNombre         = $medicoNombre ? Str::title($medicoNombre) : null;
  $actividadNombre      = $actividadNombre     ?? 'Actividad terapéutica';
  $actividadDescripcion = $actividadDescripcion?? null;
  $fechaAsignacion      = Carbon::parse($fechaAsignacion ?? now())->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
  $fechaLimite          = isset($fechaLimite) && $fechaLimite ? Carbon::parse($fechaLimite)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') : null;
@endphp

@component('mail::message')
<style>
  :root{
    --ink:#1b2a4a;
    --muted:#5b6b84;
    --stroke:#e7eef7;
    --lav:#bea4d2;     /* morado */
    --soft:#b5c8e1;    /* azul suave */
    --accent:#90aacc;  /* azul verdoso */
    --bg:#eef3f9;      /* fondo suave */
  }
  /* Reset básico */
  *{ box-sizing:border-box; }
  h1,h2,h3,p,ul{ margin:0 0 10px 0; }
  /* Contenedor visual del cuerpo */
  .mw-wrap{
    background: linear-gradient(180deg, #ffffff, var(--bg));
    border:1px solid var(--stroke);
    border-radius:14px;
    padding:18px 18px 6px;
  }
  .mw-head{
    background: linear-gradient(90deg, var(--lav), var(--soft));
    border-radius:10px;
    padding:14px 16px;
    color:#0f2240;
    font-weight:800;
    font-size:20px;
    letter-spacing:.2px;
    margin-bottom:12px;
  }
  .mw-sub{
    color:var(--muted);
    margin:-6px 0 12px 2px;
    font-size:14px;
  }
  .panel{
    background: #fff;
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
  <div class="mw-head">Nueva actividad asignada</div>
  <p class="ink">Hola <strong>{{ $pacienteNombre }}</strong>,</p>

  @if($medicoNombre)
    <p class="ink">Tu médico <strong>{{ $medicoNombre }}</strong> te ha asignado una nueva actividad terapéutica.</p>
  @else
    <p class="ink">Se te ha asignado una nueva actividad terapéutica.</p>
  @endif

  <div class="panel">
    <p><strong>Actividad:</strong> {{ $actividadNombre }}<br>
    <strong>Asignada el:</strong> {{ $fechaAsignacion }}<br>
    @if($fechaLimite)
      <strong>Fecha límite:</strong> {{ $fechaLimite }}
    @endif
    </p>
  </div>

  @if($actividadDescripcion)
    <p class="ink"><strong>Descripción:</strong><br>{{ $actividadDescripcion }}</p>
  @endif

  <div class="hint">
    <strong>¿Dónde consultarla?</strong> Ingresa a tu cuenta en 
    <a href="{{ config('app.url') }}" class="link" target="_blank" rel="noopener">MindWare</a>
    y abre la sección <em>“Mis actividades”</em> para revisarla y completarla.
  </div>

  <hr class="divider">
  <p class="muted">Gracias,<br><strong>Equipo MindWare</strong></p>
</div>
@endcomponent
