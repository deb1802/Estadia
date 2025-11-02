<?php

namespace App\Policies;

use App\Models\Usuario;     // 👈 usa tu modelo real
use App\Models\RespuestaTestimonio;

class RespuestaTestimonioPolicy
{
    public function delete(Usuario $user, RespuestaTestimonio $respuesta): bool
    {
        return $user->tipoUsuario === 'administrador' || $user->tipoUsuario === 'admin';
    }
}
