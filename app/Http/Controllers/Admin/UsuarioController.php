<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\CreateUsuariosRequest;
use App\Http\Requests\UpdateUsuariosRequest;

class UsuarioController extends Controller
{
    /**
     * 📋 Mostrar lista paginada de usuarios
     */
    public function index(Request $request)
    {
        $usuarios = Usuario::paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * 🆕 Mostrar formulario para crear un nuevo usuario
     */
    public function create()
    {
        return view('admin.usuarios.create');
    }

    /**
     * 💾 Guardar un nuevo usuario en la base de datos
     */
    public function store(CreateUsuariosRequest $request)
    {
        $data = $request->validated();

        // Estado por defecto si no se envía
        $data['estadoCuenta'] = $data['estadoCuenta'] ?? 'activo';

        // Hashear la contraseña si existe
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        }

        // Crear el usuario con los campos validados
        Usuario::create($data);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    /**
     * ✏️ Mostrar formulario de edición de usuario
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuarios.edit', compact('usuario'));
    }

    /**
     * 🔄 Actualizar usuario existente
     */
    public function update(UpdateUsuariosRequest $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $data = $request->validated();

        // Si el estado no debe modificarse desde el formulario:
        unset($data['estadoCuenta']);

        // Solo hashear la contraseña si se envía
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        } else {
            unset($data['contrasena']);
        }

        $usuario->update($data);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * 🔍 Mostrar detalles de un usuario
     */
    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * ❌ Eliminar un usuario
     */
    public function destroy($id)
    {
        Usuario::destroy($id);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
