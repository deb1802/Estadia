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

    // ✅ Admin y médico pueden ver el listado
    public function viewAny(User $user): bool
    {
        return $this->isMedico($user) || $this->isAdmin($user);
    }

    // ✅ Admin y médico pueden ver detalle
    public function view(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user) || $this->isAdmin($user);
    }

    // ✅ Solo médico puede crear
    public function create(User $user): bool
    {
        return $this->isMedico($user);
    }

    // ✅ Solo médico puede editar
    public function update(User $user, ActividadesTerap $actividad): bool
    {
        return $this->isMedico($user);
    }

    // ✅ Admin y médico pueden eliminar
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
