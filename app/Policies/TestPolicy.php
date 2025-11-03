<?php

namespace App\Policies;

use App\Models\Usuario; // tu modelo real
use App\Models\Test;

class TestPolicy
{
    public function viewAny(Usuario $user): bool
    {
        return in_array($user->tipoUsuario, ['medico','administrador','admin']);
    }

    public function view(Usuario $user, Test $test): bool
    {
        if (in_array($user->tipoUsuario, ['administrador','admin'])) return true;
        return $user->tipoUsuario === 'medico' && (int)$test->fkMedico === (int)($user->medico->id ?? 0);
    }

    public function create(Usuario $user): bool
    {
        return $user->tipoUsuario === 'medico';
    }

    public function update(Usuario $user, Test $test): bool
    {
        if (in_array($user->tipoUsuario, ['administrador','admin'])) return true;
        return $user->tipoUsuario === 'medico' && (int)$test->fkMedico === (int)($user->medico->id ?? 0);
    }

    public function delete(Usuario $user, Test $test): bool
    {
        if (in_array($user->tipoUsuario, ['administrador','admin'])) return true;
        return $user->tipoUsuario === 'medico' && (int)$test->fkMedico === (int)($user->medico->id ?? 0);
    }
}
