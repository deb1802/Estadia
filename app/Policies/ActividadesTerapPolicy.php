<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\ActividadesTerap;

class ActividadesTerapPolicy
{
    /** Ver listado: Admin y Médico */
    public function viewAny(Usuario $usuario): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    /** Ver detalle: Admin y Médico */
    public function view(Usuario $usuario, ActividadesTerap $actividad): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    /** Crear: SOLO Médico */
    public function create(Usuario $usuario): bool
    {
        return strtolower($usuario->tipoUsuario) === 'medico';
    }

    /** Editar: SOLO Médico (admin no edita) */
    public function update(Usuario $usuario, ActividadesTerap $actividad): bool
    {
        return strtolower($usuario->tipoUsuario) === 'medico';
    }

    /** Eliminar: Admin y Médico */
    public function delete(Usuario $usuario, ActividadesTerap $actividad): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    public function restore(Usuario $usuario, ActividadesTerap $actividad): bool { return false; }
    public function forceDelete(Usuario $usuario, ActividadesTerap $actividad): bool { return false; }
}
