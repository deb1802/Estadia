<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Medicamento;

class MedicamentoPolicy
{
    /**
     * Ver listado o catálogo: Admin y Médico.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    /**
     * Ver un medicamento específico: Admin y Médico.
     */
    public function view(Usuario $usuario, Medicamento $medicamento): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    /**
     * Crear: SOLO Médico puede registrar medicamentos nuevos.
     */
    public function create(Usuario $usuario): bool
    {
        return strtolower($usuario->tipoUsuario) === 'medico';
    }

    /**
     * Editar: Admin o Médico pueden editar.
     */
    public function update(Usuario $usuario, Medicamento $medicamento): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    /**
     * Eliminar: Admin o Médico pueden eliminar.
     */
    public function delete(Usuario $usuario, Medicamento $medicamento): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    // (Opcional si usas SoftDeletes)
    public function restore(Usuario $usuario, Medicamento $medicamento): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }

    public function forceDelete(Usuario $usuario, Medicamento $medicamento): bool
    {
        return in_array(strtolower($usuario->tipoUsuario), ['administrador', 'medico']);
    }
}
