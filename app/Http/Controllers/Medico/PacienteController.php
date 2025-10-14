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
    public function index()
    {
        // Lista solo los pacientes del médico en sesión
        $medico = Medico::where('usuario_id', Auth::id())->firstOrFail();

        $pacientes = Paciente::with('usuario')
            ->where('medico_id', $medico->id)
            ->orderByDesc('id')
            ->paginate(20);

        return view('medico.pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('medico.pacientes.create');
    }

    public function store(Request $request)
    {
        // Valida datos: usar 'email' y verificar unicidad sobre la columna 'email' de la tabla Usuarios
        $validated = $request->validate([
            'nombre'          => ['required','string','max:50'],
            'apellido'        => ['nullable','string','max:50'],
            'email'           => ['required','email','max:100','unique:Usuarios,email'],
            'contrasena'      => ['required','string','min:6'],
            'fechaNacimiento' => ['nullable','date'],
            'sexo'            => ['nullable','in:masculino,femenino,otro'],
            'telefono'        => ['nullable','string','max:20'],
            'padecimientos'   => ['nullable','string'],
        ]);

        DB::beginTransaction();
        try {
            // 1) Crear Usuario como "paciente"
            $usuario = Usuario::create([
                'nombre'           => $validated['nombre'],
                'apellido'         => $validated['apellido'] ?? null,
                'email'            => $validated['email'],          // <- columna 'email'
                'contrasena'       => bcrypt($validated['contrasena']),
                'fechaNacimiento'  => $validated['fechaNacimiento'] ?? null,
                'sexo'             => $validated['sexo'] ?? null,
                'telefono'         => $validated['telefono'] ?? null,
                'tipoUsuario'      => 'paciente',
                'estadoCuenta'     => 'activo',
            ]);

            // 2) Médico en sesión (Medicos.usuario_id -> Usuarios.idUsuario actual)
            $medico = Medico::where('usuario_id', Auth::id())->firstOrFail();

            // 3) Crear registro en Pacientes
            Paciente::create([
                'usuario_id'    => $usuario->idUsuario,    // si tu PK es 'idUsuario'
                'medico_id'     => $medico->id,
                'padecimientos' => $validated['padecimientos'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('medico.pacientes.index')
                ->with('success', 'Paciente creado correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear paciente: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user(); // fila en Usuarios

        // Obtén el id del médico actual (intenta por relación y, si no existe, por usuario_id)
        $medicoIdActual = optional($user->medico)->id
            ?? Medico::where('usuario_id', $user->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');

        // Asegura que el paciente pertenece a este médico
        $paciente = Paciente::where('id', $id)
            ->where('medico_id', $medicoIdActual)
            ->firstOrFail();

        // Trae el usuario del paciente (Usuarios.idUsuario == Pacientes.usuario_id)
        $usuario = Usuario::where('idUsuario', $paciente->usuario_id)->firstOrFail();

        return view('medico.pacientes.show', compact('usuario', 'paciente'));
    }

    public function edit($id)
    {


        $user = Auth::user(); // Usuarios
        $medicoIdActual = optional($user->medico)->id
        ?? Medico::where('usuario_id', $user->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');

        // Solo puede editar pacientes asignados a él
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
            'nombre'           => ['required','string','max:50'],
            'apellido'         => ['required','string','max:50'],
            'email'            => [
                'required','email','max:100',
                Rule::unique('Usuarios', 'email')->ignore($usuario->idUsuario, 'idUsuario')
            ],
            'telefono'         => ['nullable','string','max:20'],
            'fechaNacimiento'  => ['nullable','date'],
            'sexo'             => ['nullable', Rule::in(['masculino','femenino','otro'])],
            'estadoCuenta'     => ['nullable', Rule::in(['activo','inactivo'])],
            'padecimientos'    => ['nullable','string'],
        ]);

        DB::transaction(function() use ($validated, $usuario, $paciente) {
            // Actualiza Usuarios
            $usuario->update([
                'nombre'          => $validated['nombre'],
                'apellido'        => $validated['apellido'],
                'email'           => $validated['email'],
                'telefono'        => $validated['telefono'] ?? null,
                'fechaNacimiento' => $validated['fechaNacimiento'] ?? null,
                'sexo'            => $validated['sexo'] ?? $usuario->sexo,
                'estadoCuenta'    => $validated['estadoCuenta'] ?? $usuario->estadoCuenta,
            ]);

            // Actualiza Pacientes
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
        // 1) localizar al paciente por su id (Pacientes.id)
        $paciente = \App\Models\Paciente::findOrFail($id);

        // 2) (Seguridad) comprobar que este paciente pertenece al médico logueado
        //    Evita que un médico elimine pacientes de otro médico.
        $medicoIdActual = optional(auth()->user()->medico)->id
            ?? \App\Models\Medico::where('usuario_id', auth()->user()->idUsuario)->value('id');

        abort_if(!$medicoIdActual, 403, 'Perfil de médico no encontrado.');
        abort_if((int)$paciente->medico_id !== (int)$medicoIdActual, 403, 'No puedes eliminar este paciente.');

        // 3) borrar SOLO el registro en Pacientes
        $paciente->delete();

        return redirect()
            ->route('medico.pacientes.index')
            ->with('success', 'Paciente eliminado de tu lista.');
    }


}
