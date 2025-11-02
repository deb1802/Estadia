<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\Medico;
use Illuminate\Validation\Rule;
use App\Http\Requests\StorePacienteUsuarioRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;


class PacienteController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Obtener médico logueado
        $medico = Medico::where('usuario_id', Auth::id())->firstOrFail();

        // 🔹 Parámetros de búsqueda
        $q    = trim((string) $request->get('q', ''));
        $type = (string) $request->get('type', 'all');

        // 🔹 Campos válidos de la tabla Usuarios y Pacientes
        $userCols = ['nombre', 'apellido', 'email', 'telefono', 'tipoUsuario', 'estadoCuenta'];
        $pacCols  = ['padecimientos'];

        $query = Paciente::with('usuario')
            ->where('medico_id', $medico->id);

        if ($q !== '') {
            $query->where(function ($qq) use ($q, $type, $userCols, $pacCols) {

                // Buscar en todos los campos
                if ($type === 'all') {
                    $qq->whereHas('usuario', function ($u) use ($q, $userCols) {
                        $u->where(function ($uu) use ($q, $userCols) {
                            // nombre completo virtual
                            $uu->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$q}%"]);
                            foreach ($userCols as $col) {
                                $uu->orWhere($col, 'LIKE', "%{$q}%");
                            }
                        });
                    })->orWhere('padecimientos', 'LIKE', "%{$q}%");

                } else {
                    // Buscar por campo específico
                    switch ($type) {
                        case 'nombreCompleto':
                            $qq->whereHas('usuario', function ($u) use ($q) {
                                $u->whereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$q}%"]);
                            });
                            break;

                        case 'nombre':
                        case 'apellido':
                        case 'email':
                        case 'telefono':
                        case 'tipoUsuario':
                        case 'estadoCuenta':
                            $qq->whereHas('usuario', function ($u) use ($type, $q) {
                                $u->where($type, 'LIKE', "%{$q}%");
                            });
                            break;

                        case 'padecimientos':
                            $qq->where('padecimientos', 'LIKE', "%{$q}%");
                            break;

                        default:
                            $qq->whereHas('usuario', function ($u) use ($q, $userCols) {
                                $u->where(function ($uu) use ($q, $userCols) {
                                    $uu->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$q}%"]);
                                    foreach ($userCols as $col) {
                                        $uu->orWhere($col, 'LIKE', "%{$q}%");
                                    }
                                });
                            })->orWhere('padecimientos', 'LIKE', "%{$q}%");
                            break;
                    }
                }
            });
        }

        $pacientes = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('medico.pacientes.index', compact('pacientes', 'q', 'type'));
    }

    public function create()
    {
        return view('medico.pacientes.create');
    }

    public function store(Request $request)
{
    // 1) Validación mínima, inline
    $validated = $request->validate([
        'nombre'          => ['required','string','max:50'],
        'apellido'        => ['required','string','max:50'],
        'email'           => ['required','email','max:150',
            Rule::unique(Usuario::class, 'email')
        ],
        'contrasena'      => ['required','string','min:6'],
        'fechaNacimiento' => ['required','date','before_or_equal:today'],
        'sexo'            => ['required','in:masculino,femenino,otro'],
        'telefono'        => ['required','digits:10'],
        'estadoCuenta'    => ['nullable','in:activo,inactivo'],
        'padecimientos'   => ['required','string','max:1000'],
    ], [
        'apellido.required'        => 'El apellido es obligatorio.',
        'estadoCuenta.in'          => 'Selecciona un estado válido.',
        'padecimientos.required'   => 'Describe los padecimientos o antecedentes.',
    ]);

    // 2) Normalizaciones
    $email = strtolower(trim($validated['email']));

    // 3) Datos para la tabla Usuarios
    $usuarioData = [
        'nombre'          => $validated['nombre'],
        'apellido'        => $validated['apellido'],
        'email'           => $email,
        'contrasena'      => Hash::make($validated['contrasena']),
        'fechaNacimiento' => $validated['fechaNacimiento'],
        'sexo'            => $validated['sexo'],
        'telefono'        => $validated['telefono'],
        'tipoUsuario'     => 'paciente',
        'estadoCuenta'    => $validated['estadoCuenta'] ?? 'activo',
    ];

    // 4) Inserción atómica
    DB::beginTransaction();
    try {
        // Usuario
        $usuario = Usuario::create($usuarioData);

        // Médico dueño (por el usuario autenticado)
        $medico = Medico::where('usuario_id', Auth::id())->first();
        if (!$medico) {
            throw new \RuntimeException('Médico no encontrado para el usuario actual.');
        }

        // Paciente
        Paciente::create([
            'usuario_id'    => $usuario->getKey(),  // funciona con id o idUsuario
            'medico_id'     => $medico->getKey(),
            'padecimientos' => $validated['padecimientos'],
        ]);

        DB::commit();

        return redirect()
            ->route('medico.pacientes.index')
            ->with('success', 'Paciente creado correctamente.');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()
            ->withInput()
            ->withErrors(['general' => 'Error al crear paciente: '.$e->getMessage()]);
    }
}


    public function show($id)
    {
        $user = Auth::user();
        $medicoIdActual = optional($user->medico)->id
            ?? Medico::where('usuario_id', $user->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');

        $paciente = Paciente::where('id', $id)
            ->where('medico_id', $medicoIdActual)
            ->firstOrFail();

        $usuario = Usuario::where('idUsuario', $paciente->usuario_id)->firstOrFail();

        $recetas = DB::table('RecetasMedicas')
            ->where('fkPaciente', $paciente->id)
            ->orderByDesc('fecha')
            ->get(['idReceta', 'fecha', 'observaciones']);

        $ultimaRecetaId = optional($recetas->first())->idReceta;

        return view('medico.pacientes.show', compact('usuario', 'paciente', 'recetas', 'ultimaRecetaId'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $medicoIdActual = optional($user->medico)->id
            ?? Medico::where('usuario_id', $user->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');

        $paciente = Paciente::where('id', $id)
            ->where('medico_id', $medicoIdActual)
            ->firstOrFail();

        $usuario = Usuario::where('idUsuario', $paciente->usuario_id)->firstOrFail();

        return view('medico.pacientes.edit', compact('usuario', 'paciente'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $medicoIdActual = optional($user->medico)->id
            ?? Medico::where('usuario_id', $user->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');

        $paciente = Paciente::where('id', $id)
            ->where('medico_id', $medicoIdActual)
            ->firstOrFail();

        $usuario = Usuario::where('idUsuario', $paciente->usuario_id)->firstOrFail();

        $validated = $request->validate([
            'nombre'          => ['required','string','max:50'],
            'apellido'        => ['nullable','string','max:50'],
            'email'           => ['required','email','max:100', Rule::unique('Usuarios','email')->ignore($usuario->idUsuario, 'idUsuario')],
            'telefono'        => ['nullable','string','max:20'],
            'fechaNacimiento' => ['nullable','date'],
            'estadoCuenta'    => ['nullable', Rule::in(['activo','inactivo'])],
            'padecimientos'   => ['nullable','string'],
        ]);

        DB::transaction(function () use ($validated, $usuario, $paciente) {
            $usuario->update([
                'nombre'          => $validated['nombre'],
                'apellido'        => $validated['apellido'] ?? null,
                'email'           => $validated['email'],
                'telefono'        => $validated['telefono'] ?? null,
                'fechaNacimiento' => $validated['fechaNacimiento'] ?? null,
                'estadoCuenta'    => $validated['estadoCuenta'] ?? $usuario->estadoCuenta,
            ]);

            $paciente->update([
                'padecimientos' => $validated['padecimientos'] ?? null,
            ]);
        });

        return redirect()
            ->route('medico.pacientes.show', $paciente->id)
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy($id)
    {
        $paciente = Paciente::findOrFail($id);

        $medicoIdActual = optional(auth()->user()->medico)->id
            ?? Medico::where('usuario_id', auth()->user()->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');
        abort_if((int)$paciente->medico_id !== (int)$medicoIdActual, 403, 'No puedes eliminar este paciente.');

        $paciente->delete();

        return redirect()
            ->route('medico.pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }
}
