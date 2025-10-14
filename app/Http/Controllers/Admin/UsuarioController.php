<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateUsuariosRequest;
use App\Http\Requests\UpdateUsuariosRequest;



class UsuarioController extends Controller
{

    public function index(Request $request)
    {
        $usuarios = Usuario::paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(\App\Http\Requests\CreateUsuariosRequest $request)
    {
        $data = $request->all();

        // Defaults y hash
        $data['estadoCuenta'] = $data['estadoCuenta'] ?? 'activo';
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        }

        DB::transaction(function () use ($data, $request) {
            // 1) Crear usuario
            $usuario = \App\Models\Usuario::create($data);

            // 2) Si es médico, crear perfil en Medicos con FK usuario_id
            if (($data['tipoUsuario'] ?? null) === 'medico') {
                Medico::create([
                    'usuario_id'        => $usuario->idUsuario,
                    'cedulaProfesional' => $request->cedulaProfesional,
                    'especialidad'      => $request->especialidad,
                ]);
            }
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }




    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(\App\Http\Requests\UpdateUsuariosRequest $request, $id)
    {
        $usuario = \App\Models\Usuario::findOrFail($id);
        $data = $request->all();

        // Si no debes modificar estado desde el form:
        unset($data['estadoCuenta']);

        // Hash solo si viene
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        } else {
            unset($data['contrasena']);
        }

        DB::transaction(function () use ($usuario, $data, $request) {
            // 1) Actualizar usuario
            $usuario->update($data);

            // 2) Sincronizar perfil médico según tipoUsuario
            if (($data['tipoUsuario'] ?? null) === 'medico') {
                Medico::updateOrCreate(
                    ['usuario_id' => $usuario->idUsuario],
                    [
                        'cedulaProfesional' => $request->cedulaProfesional,
                        'especialidad'      => $request->especialidad,
                    ]
                );
            } else {
                // Si dejó de ser médico, elimina su perfil
                Medico::where('usuario_id', $usuario->idUsuario)->delete();
            }
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }


    public function show($id)
    {
        $usuario = \App\Models\Usuario::with('medico')->findOrFail($id);
        return view('admin.usuarios.show', compact('usuario'));
    }


    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            Medico::where('usuario_id', $id)->delete(); // borrar perfil médico si existe
            \App\Models\Usuario::destroy($id);
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

}
