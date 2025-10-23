<?php

namespace App\Policies;

use App\Models\Usuario as User;        
use App\Models\ActividadesTerap;

class ActividadesTerapPolicy
{
    protected function isMedico(User $user): bool
    {
        return strcasecmp($user->tipoUsuario ?? '', 'medico') === 0;
    }

    protected function isAdmin(User $user): bool
    {
        return strcasecmp($user->tipoUsuario ?? '', 'administrador') === 0;
    }

    /** Listado / catálogo */
    public function viewAny(User $user): bool
    {
        return $this->isMedico($user) || $this->isAdmin($user);
    }

    /** Ver un registro */
    public function view(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user) || $this->isAdmin($user);
    }

    /** Crear (solo médico) */
    public function create(User $user): bool
    {
        return $this->isMedico($user);
    }

    /** Editar (solo médico) */
    public function update(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user);
    }

    /** Eliminar (médico y admin) */
    public function delete(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user) || $this->isAdmin($user);
    }

    public function restore(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user);
    }

    public function forceDelete(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user);
    }
}
