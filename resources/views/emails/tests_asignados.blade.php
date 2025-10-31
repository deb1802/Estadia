@component('mail::message')
# ¡Tienes nuevos tests asignados!

Hola **{{ $pacienteNombre }}**,

@isset($medicoNombre)
El/la médico(a) **{{ $medicoNombre }}** te ha asignado los siguientes tests psicológicos:
@else
Se te han asignado los siguientes tests psicológicos:
@endisset

@component('mail::panel')
**Fecha de asignación:** {{ \Illuminate\Support\Carbon::parse($fechaAsignacion)->format('d/m/Y H:i') }}

**Tests:**
@foreach($testsAsignados as $t)
- **{{ $t['nombre'] }}** @if(!empty($t['tipo'])) ({{ $t['tipo'] }}) @endif
@endforeach
@endcomponent

Al responderlos, el sistema calculará automáticamente tu puntaje y mostrará un **diagnóstico sugerido** para revisión de tu médico.

@component('mail::button', ['url' => $urlAccion])
Responder tests
@endcomponent

Si el botón no funciona, copia y pega esta liga en tu navegador:  
{{ $urlAccion }}

Gracias,  
**Equipo Mindora**
@endcomponent
