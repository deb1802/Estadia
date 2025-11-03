@component('mail::message')

{{-- Encabezado con logo Mindware --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom: 10px;">
<tr>
<td align="center" style="padding: 20px 0;">
    <img src="{{ url('img/mindware-logo.png') }}" alt="Mindware" width="90" style="margin-bottom: 10px;">
    <h2 style="margin: 0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; color: #3a4b61;">Mindware</h2>
</td>
</tr>
</table>

{{-- Contenedor con márgenes --}}
<div style="
    text-align: center;
    font-family: 'Segoe UI', Roboto, Arial, sans-serif;
    padding: 25px 40px;
    background-color: #ffffff;
    border-radius: 10px;
    margin: 0 auto;
    width: 90%;
    max-width: 520px;
">

<h1 style="color:#3a4b61;">¡Hola!</h1>

<p style="font-size:16px; line-height:1.7; color:#4a5568; margin-bottom:25px;">
Has recibido este mensaje porque se solicitó un <strong>restablecimiento de contraseña</strong> para tu cuenta en <strong>Mindware</strong>.
</p>

@component('mail::button', ['url' => $actionUrl, 'color' => 'blue'])
Restablecer contraseña
@endcomponent

<p style="font-size:15px; color:#4a5568; margin-top:25px; line-height:1.6;">
Este enlace expirará en <strong>60 minutos</strong>.<br><br>
Si no realizaste esta solicitud, puedes ignorar este mensaje con seguridad.
</p>

<p style="font-size:15px; color:#3a4b61; margin-top:30px;">
Gracias por utilizar <strong>Mindware</strong> 🧠<br>
Saludos<br>
</p>
</div>

{{-- Frase motivacional --}}
@slot('subcopy')
<div style="text-align: center; margin-top: 40px; font-size:14px; color:#718096; line-height:1.7;">
    “Ten el coraje de cuidar tu mente como cuidas tu código: cuando limpias tus pensamientos, mejoras tu sistema.”<br>
    — <em>Equipo de Mindware</em>
</div>
@endslot

@endcomponent
