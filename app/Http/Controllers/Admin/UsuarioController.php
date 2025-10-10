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
     * Mostrar lista paginada de usuarios
     */
    public function index(Request $request)
    {
<<<<<<< HEAD
        $usuarios = Usuario::paginate(10); // Puedes ajustar la cantidad
        return view('admin.usuarios.index')->with('usuarios', $usuarios);
    }

    /**
     * Mostrar formulario para crear usuario
     */
=======
        $usuarios = Usuario::paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

>>>>>>> upstream/main
    public function create()
    {
        return view('admin.usuarios.create');
    }

    /**
<<<<<<< HEAD
     * Guardar un nuevo usuario en la base de datos
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:50',
            'email'        => 'required|email|unique:Usuarios,email',
            'contrasena'   => 'required|string|min:6',
            'tipoUsuario'  => 'required|in:administrador,medico,paciente',
            'estadoCuenta' => 'required|in:activo,inactivo'
        ]);

        $usuario = new Usuario();
        $usuario->nombre          = $request->nombre;
        $usuario->apellido        = $request->apellido;
        $usuario->email           = $request->email; // ✅ cambiado de correo → email
        $usuario->contrasena      = bcrypt($request->contrasena);
        $usuario->fechaNacimiento = $request->fechaNacimiento;
        $usuario->sexo            = $request->sexo;
        $usuario->telefono        = $request->telefono;
        $usuario->tipoUsuario     = $request->tipoUsuario;
        $usuario->estadoCuenta    = $request->estadoCuenta;
        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
=======
     * Guarda un usuario nuevo.
     * - Valida con CreateUsuariosRequest
     * - Fuerza estadoCuenta = activo si no viene
     * - Hashea la contraseña
     */
    public function store(CreateUsuariosRequest $request)
    {
        $data = $request->validated();

        // Asegurar estado por defecto
        $data['estadoCuenta'] = $data['estadoCuenta'] ?? 'activo';

        // Hashear contraseña si viene (en create es required por las rules)
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        }

        // Crea usando fillable del modelo
        Usuario::create($data);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
>>>>>>> upstream/main
    }

    /**
     * Mostrar formulario de edición de usuario
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuarios.edit', compact('usuario'));
    }

    /**
<<<<<<< HEAD
     * Actualizar usuario existente
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre'       => 'required|string|max:50',
            'email'        => 'required|email|unique:Usuarios,email,' . $usuario->idUsuario . ',idUsuario', // ✅ actualizado
            'tipoUsuario'  => 'required|in:administrador,medico,paciente',
            'estadoCuenta' => 'required|in:activo,inactivo'
        ]);

        $usuario->update([
            'nombre'          => $request->nombre,
            'apellido'        => $request->apellido,
            'email'           => $request->email, // ✅ actualizado
            'fechaNacimiento' => $request->fechaNacimiento,
            'sexo'            => $request->sexo,
            'telefono'        => $request->telefono,
            'tipoUsuario'     => $request->tipoUsuario,
            'estadoCuenta'    => $request->estadoCuenta,
        ]);

        // Si el usuario envía una nueva contraseña
        if ($request->filled('contrasena')) {
            $usuario->contrasena = bcrypt($request->contrasena);
            $usuario->save();
        }

=======
     * Actualiza un usuario existente.
     * - Valida con UpdateUsuariosRequest (email único ignorando el propio)
     * - La contraseña es opcional; si viene, se hashea
     * - Si no quieres que se edite estadoCuenta desde el form, lo ignoramos
     */
    public function update(UpdateUsuariosRequest $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $data = $request->validated();

        // Si no quieres permitir edición del estado desde este form:
        unset($data['estadoCuenta']);

        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        } else {
            unset($data['contrasena']);
        }

        $usuario->update($data);

>>>>>>> upstream/main
        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * Eliminar un usuario
     */
    public function destroy($id)
    {
        Usuario::destroy($id);
        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
