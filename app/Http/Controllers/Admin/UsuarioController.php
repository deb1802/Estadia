<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // Texto y tipo de búsqueda
        $q    = trim((string) $request->get('q', ''));
        $type = (string) $request->get('type', 'all');

        /**
         * Mapa de "alias de la vista" -> "columna real"
         * (Incluye alias tolerantes como 'correo' y 'nombreCompleto')
         */
        $fieldMap = [
            'nombre'         => 'nombre',
            'apellido'       => 'apellido',
            'email'          => 'email',
            'correo'         => 'email',     // alias tolerante
            'telefono'       => 'telefono',
            'tipoUsuario'    => 'tipoUsuario',
            'estadoCuenta'   => 'estadoCuenta',
            'sexo'           => 'sexo',
            // 'usuario' NO existe en tu esquema; si llegara desde la vista, lo mapeamos a email
            'usuario'        => 'email',     // alias tolerante para no romper
            // nombreCompleto es virtual (CONCAT)
            'nombreCompleto' => null,
        ];

        $query = Usuario::query();

        if ($q !== '') {
            // Buscar en todos los campos relevantes
            if ($type === 'all') {
                $query->where(function ($qq) use ($q, $fieldMap) {
                    foreach ($fieldMap as $alias => $col) {
                        if ($alias === 'nombreCompleto') {
                            $qq->orWhereRaw("CONCAT(nombre,' ',apellido) LIKE ?", ["%{$q}%"]);
                        } elseif ($col) {
                            $qq->orWhere($col, 'LIKE', "%{$q}%");
                        }
                    }
                });
            } else {
                // Buscar por un campo específico (si viene un alias “viejo”, cae en el mapa)
                if (array_key_exists($type, $fieldMap)) {
                    if ($type === 'nombreCompleto') {
                        $query->whereRaw("CONCAT(nombre,' ',apellido) LIKE ?", ["%{$q}%"]);
                    } else {
                        $col = $fieldMap[$type];
                        $query->where($col, 'LIKE', "%{$q}%");
                    }
                } else {
                    // Fallback seguro: busca en todos
                    $query->where(function ($qq) use ($q, $fieldMap) {
                        foreach ($fieldMap as $alias => $col) {
                            if ($alias === 'nombreCompleto') {
                                $qq->orWhereRaw("CONCAT(nombre,' ',apellido) LIKE ?", ["%{$q}%"]);
                            } elseif ($col) {
                                $qq->orWhere($col, 'LIKE', "%{$q}%");
                            }
                        }
                    });
                }
            }
        }

        $usuarios = $query
            ->orderBy('idUsuario', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'q', 'type'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(\App\Http\Requests\CreateUsuariosRequest $request)
    {
        $data = $request->all();

        $data['estadoCuenta'] = $data['estadoCuenta'] ?? 'activo';
        if (!empty($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        }

        DB::transaction(function () use ($data, $request) {
            $usuario = \App\Models\Usuario::create($data);

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

    public function update(Request $request, $id)
{
    $usuario = \App\Models\Usuario::findOrFail($id);

    // ── Candado: si el usuario actual es PACIENTE, no permitimos cambiarlo de tipo
    $puedeCambiarTipo = ($usuario->tipoUsuario !== 'paciente');

    // Normaliza email para evitar falsos positivos
    $emailNuevo  = strtolower(trim((string) $request->input('email')));
    $emailActual = strtolower((string) $usuario->email);

    $emailRule = ['required','email','max:100'];
    if ($emailNuevo !== $emailActual) {
        $emailRule[] = \Illuminate\Validation\Rule::unique(\App\Models\Usuario::class, 'email')
            ->ignore($usuario->getKey(), $usuario->getKeyName()); // idUsuario
    }

    $validated = $request->validate([
        'nombre'          => ['required','string','max:50'],
        'apellido'        => ['nullable','string','max:50'],
        'email'           => $emailRule,
        'telefono'        => ['nullable','string','max:20'],
        'fechaNacimiento' => ['nullable','date'],
        // Si es paciente, NO validamos cambio de tipo (lo ignoramos); si no, sí permitimos
        'tipoUsuario'     => $puedeCambiarTipo ? ['nullable', \Illuminate\Validation\Rule::in(['paciente','medico','admin'])] : ['nullable'],
        'estadoCuenta'    => ['nullable', \Illuminate\Validation\Rule::in(['activo','inactivo'])],
        'contrasena'      => ['nullable','string','min:6'],
    ], [
        'email.unique' => 'Este correo ya está registrado por otro usuario.',
    ]);

    $validated['email'] = $emailNuevo;

    \DB::transaction(function () use ($validated, $usuario, $request, $puedeCambiarTipo) {
        // Si NO puede cambiar tipo (porque es paciente), forzamos el valor actual
        if (!$puedeCambiarTipo) {
            $validated['tipoUsuario'] = $usuario->tipoUsuario;
        }

        $datos = [
            'nombre'          => $validated['nombre'],
            'apellido'        => $validated['apellido']        ?? $usuario->apellido,
            'email'           => $validated['email'],
            'telefono'        => $validated['telefono']        ?? $usuario->telefono,
            'fechaNacimiento' => $validated['fechaNacimiento'] ?? $usuario->fechaNacimiento,
            'estadoCuenta'    => $validated['estadoCuenta']    ?? $usuario->estadoCuenta,
            'tipoUsuario'     => $validated['tipoUsuario']     ?? $usuario->tipoUsuario,
        ];

        if (!empty($validated['contrasena'])) {
            $datos['contrasena'] = \Illuminate\Support\Facades\Hash::make($validated['contrasena']);
        }

        $usuario->update($datos);

        // (Opcional) Solo crear perfil de médico si se cambió a 'medico' y antes no lo era
        if ($puedeCambiarTipo && ($datos['tipoUsuario'] ?? null) === 'medico') {
            \App\Models\Medico::firstOrCreate(
                ['usuario_id' => $usuario->idUsuario],
                [
                    'cedulaProfesional' => $request->cedulaProfesional,
                    'especialidad'      => $request->especialidad,
                ]
            );
        }
    });

    return redirect()
        ->route('admin.usuarios.show', $usuario->idUsuario)
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
            Medico::where('usuario_id', $id)->delete();
            \App\Models\Usuario::destroy($id);
        });

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
