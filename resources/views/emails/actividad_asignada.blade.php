@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Carbon;

  // Normaliza variables y formatos:
  $pacienteNombre       = Str::title($pacienteNombre      ?? 'Paciente');
  $medicoNombre         = $medicoNombre ? Str::title($medicoNombre) : null;
  $actividadNombre      = $actividadNombre     ?? 'Actividad terapéutica';
  $actividadDescripcion = $actividadDescripcion?? null;
  $fechaAsignacion      = Carbon::parse($fechaAsignacion ?? now())->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
  $fechaLimite          = isset($fechaLimite) && $fechaLimite ? Carbon::parse($fechaLimite)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') : null;
  $urlAccion            = $urlAccion ?? url('/paciente/actividades');
  $logoUrl              = rtrim(config('app.url'), '/') . '/img/logo.png';
@endphp

{{-- ======= ENCABEZADO CON LOGO ======= --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom: 25px;">
  <tr>
    <td align="center">
      <a href="{{ config('app.url') }}" target="_blank">
        <img src="{{ $logoUrl }}" alt="Mindware" style="height:70px; margin-top:10px; border-radius:10px;">
      </a>
    </td>
  </tr>
</table>

{{-- ======= ESTILOS PERSONALIZADOS ======= --}}
<style>
  /* Colores y tipografía personalizados */
  body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
  }

  h1, h2, h3 {
    color: #14532d !important; /* Verde Mindware */
  }

  .panel {
    background-color: #f0fdf4 !important;
    border-left: 4px solid #16a34a !important;
    color: #064e3b;
  }

  .button-success {
    background-color: #16a34a !important;
    border-color: #16a34a !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    padding: 10px 18px !important;
  }

  .subcopy p {
    color: #64748b !important;
  }
</style>

{{-- ======= CUERPO DEL MENSAJE ======= --}}
@component('mail::message')
# Nueva actividad asignada

Hola **{{ $pacienteNombre }}**,  
@if($medicoNombre)
tu médico **{{ $medicoNombre }}** te ha asignado una nueva actividad terapéutica.
@else
te han asignado una nueva actividad terapéutica.
@endif

@component('mail::panel')
**Actividad:** {{ $actividadNombre }}  
**Asignada el:** {{ $fechaAsignacion }}  
@if($fechaLimite)
**Fecha límite:** {{ $fechaLimite }}
@endif
@endcomponent

@if($actividadDescripcion)
**Descripción:**  
{{ $actividadDescripcion }}
@endif

@component('mail::button', ['url' => $urlAccion, 'color' => 'success'])
Ver mis actividades
@endcomponent

> 🌿 Sugerencia: completa la actividad dentro del periodo indicado para aprovechar al máximo tu proceso terapéutico.

Gracias,  
**Equipo Mindware**

@slot('subcopy')
Si el botón no funciona, copia y pega esta URL en tu navegador:  
{{ $urlAccion }}
@endslot
@endcomponent
