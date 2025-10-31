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
        $validated = $request->validate([
            'nombre'          => ['required','string','max:50'],
            'apellido'        => ['nullable','string','max:50'],
            'email'           => ['required','email','max:100','unique:Usuarios,email'],
            'contrasena'      => ['required','string','min:6'],
            'fechaNacimiento' => ['nullable','date'],
            'telefono'        => ['nullable','string','max:20'],
            'padecimientos'   => ['nullable','string'],
        ]);

        DB::beginTransaction();
        try {
            $usuario = Usuario::create([
                'nombre'          => $validated['nombre'],
                'apellido'        => $validated['apellido'] ?? null,
                'email'           => $validated['email'],
                'contrasena'      => bcrypt($validated['contrasena']),
                'fechaNacimiento' => $validated['fechaNacimiento'] ?? null,
                'telefono'        => $validated['telefono'] ?? null,
                'tipoUsuario'     => 'paciente',
                'estadoCuenta'    => 'activo',
            ]);

            $medico = Medico::where('usuario_id', Auth::id())->firstOrFail();

            Paciente::create([
                'usuario_id'    => $usuario->idUsuario,
                'medico_id'     => $medico->id,
                'padecimientos' => $validated['padecimientos'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('medico.pacientes.index')
                ->with('success', 'Paciente creado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear paciente: '.$e->getMessage());
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
