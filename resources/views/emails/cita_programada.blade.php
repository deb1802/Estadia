@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Carbon;

  // Normaliza y formatea variables
  $pacienteNombre = Str::title($pacienteNombre ?? 'Paciente');
  $medicoNombre   = $medicoNombre ? Str::title($medicoNombre) : null;
  $fechaHora      = isset($fechaHora) ? Carbon::parse($fechaHora)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY [a las] HH:mm') : 'Próximamente';
  $motivo         = $motivo ?? null;
  $ubicacion      = $ubicacion ?? 'Sin ubicación especificada';
  $urlAccion      = $urlAccion ?? url('/paciente/citas');
  $logoUrl        = rtrim(config('app.url'), '/') . '/img/logo.png';
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
  body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
  }

  h1, h2, h3 {
    color: #1e3a8a !important; /* Azul Mindware */
  }

  .panel {
    background-color: #eff6ff !important;
    border-left: 4px solid #3b82f6 !important;
    color: #1e40af;
  }

  .button-primary {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
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
# Nueva cita programada

Hola **{{ $pacienteNombre }}**,  
@if($medicoNombre)
tu médico **{{ $medicoNombre }}** ha programado una nueva cita para ti.
@else
se ha programado una nueva cita para ti.
@endif

@component('mail::panel')
**Fecha y hora:** {{ $fechaHora }}  
**Ubicación:** {{ $ubicacion }}
@endcomponent

@if($motivo)
**Motivo de la cita:**  
{{ $motivo }}
@endif

@component('mail::button', ['url' => $urlAccion, 'color' => 'primary'])
Ver mis citas
@endcomponent

> 🧠 Consejo: llega unos minutos antes de tu cita para aprovechar mejor tu sesión.

Gracias,  
**Equipo Mindware**

@slot('subcopy')
Si el botón no funciona, copia y pega esta URL en tu navegador:  
{{ $urlAccion }}
@endslot
@endcomponent
