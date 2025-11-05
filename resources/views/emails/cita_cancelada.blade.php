@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Carbon;

  $pacienteNombre = Str::title($pacienteNombre ?? 'Paciente');
  $medicoNombre   = $medicoNombre ? Str::title($medicoNombre) : null;
  $fechaHora      = isset($fechaCita) ? Carbon::parse($fechaCita)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY [a las] HH:mm') : 'Fecha no disponible';
  $motivo         = $motivo ?? 'Sin motivo especificado';
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

{{-- ======= ESTILOS ======= --}}
<style>
  body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
  h1, h2, h3 { color: #b91c1c !important; }
  .panel { background-color: #fef2f2 !important; border-left: 4px solid #dc2626 !important; color: #7f1d1d; }
  .button-danger {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    padding: 10px 18px !important;
  }
  .subcopy p { color: #64748b !important; }
</style>

{{-- ======= CUERPO ======= --}}
@component('mail::message')
# Cita cancelada

@switch($canceladaPor ?? 'paciente')
  @case('paciente')
  Hola **{{ $medicoNombre }}**,  
  el paciente **{{ $pacienteNombre }}** ha cancelado una cita programada contigo.
  @break

  @case('medico')
  Hola **{{ $pacienteNombre }}**,  
  lamentamos informarte que tu médico **{{ $medicoNombre }}** ha cancelado tu cita programada.
  @break

  @case('admin')
  Hola **{{ $pacienteNombre }}**,  
  te informamos que tu cita ha sido **eliminada del sistema** por el administrador.
  @break

  @default
  Hola **{{ $pacienteNombre }}**,  
  una de tus citas ha sido cancelada.
@endswitch

@component('mail::panel')
**Fecha original:** {{ $fechaHora }}  
**Motivo:** {{ $motivo }}
@endcomponent

@component('mail::button', ['url' => $urlAccion, 'color' => 'error'])
Ver mis citas
@endcomponent

> ⚠️ Si necesitas reprogramarla, puedes hacerlo directamente desde el portal o comunicarte con tu médico.

Gracias por tu comprensión,  
**Equipo Mindware**

@slot('subcopy')
Si el botón no funciona, copia y pega esta URL en tu navegador:  
{{ $urlAccion }}
@endslot
@endcomponent
