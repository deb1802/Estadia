<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificación de restablecimiento de contraseña</title>
</head>
<body style="margin:0; padding:0; background:#f8fafd; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <!-- 🔹 Header personalizado -->
    <table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td style="background: linear-gradient(to bottom, #ffffff, #f0e2f8); padding: 35px 0; text-align: center;">
                <a href="{{ config('app.url') }}" style="display:inline-block;">
                    <img src="https://raw.githubusercontent.com/deb1802/Estadia/main/public/img/mindware-logo.png"
                         alt="Mindware"
                         width="150"
                         style="display:block; margin:0 auto;">
                </a>
            </td>
        </tr>
    </table>

    <!-- 🔹 Cuerpo del correo -->
    <table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="570" cellpadding="0" cellspacing="0" role="presentation"
                       style="background:#ffffff; border-radius:6px; margin:40px auto; padding:40px; box-shadow:0 2px 4px rgba(0,0,0,0.05); text-align:center;">
                    {{ $slot }}
                </table>
            </td>
        </tr>
    </table>

    <!-- 🔹 Footer -->
    <table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td style="padding:40px 20px; text-align:center; background:#f8fafd;">
                <p style="font-size:14px; color:#4a5568; font-style:italic; margin-bottom:8px;">
                    “La salud mental no es un destino, es un proceso constante. Así como en la tecnología, mejorar cada día es la clave.”
                </p>
                <p style="font-size:13px; color:#6b7280; margin-top:8px;">
                    — Equipo de Mindware 💙
                </p>
                <p style="font-size:12px; color:#a0aec0; margin-top:25px;">
                    © {{ date('Y') }} Mindware. Todos los derechos reservados.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
