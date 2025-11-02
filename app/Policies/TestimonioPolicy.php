<?php

namespace App\Policies;

use App\Models\Usuario;     // 👈 usa tu modelo real
use App\Models\Testimonio;

class TestimonioPolicy
{
    public function delete(Usuario $user, Testimonio $testimonio): bool
    {
        return $user->tipoUsuario === 'administrador' || $user->tipoUsuario === 'admin';
    }
}
