@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Carbon;

  // Normalización y formato
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
    color: #2563eb !important; /* Azul principal */
  }

  .panel {
    background-color: #dbeafe !important;
    border-left: 4px solid #1d4ed8 !important;
    color: #1e3a8a;
  }

  .button-primary {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
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
# Tu cita ha sido actualizada

Hola **{{ $pacienteNombre }}**,  
@if($medicoNombre)
tu médico **{{ $medicoNombre }}** ha modificado los detalles de tu cita.
@else
los detalles de tu cita han sido actualizados.
@endif

@component('mail::panel')
**Nueva fecha y hora:** {{ $fechaHora }}  
**Ubicación:** {{ $ubicacion }}
@endcomponent

@if($motivo)
**Motivo de la cita:**  
{{ $motivo }}
@endif

@component('mail::button', ['url' => $urlAccion, 'color' => 'primary'])
Ver mis citas actualizadas
@endcomponent

> 📅 Recuerda revisar tu agenda para confirmar tu asistencia con la nueva fecha y hora.

Gracias,  
**Equipo Mindware**

@slot('subcopy')
Si el botón no funciona, copia y pega esta URL en tu navegador:  
{{ $urlAccion }}
@endslot
@endcomponent
