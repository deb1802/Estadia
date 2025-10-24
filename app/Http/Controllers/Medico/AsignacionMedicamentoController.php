<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class AsignacionMedicamentoController extends Controller
{
    /**
     * Muestra el formulario de asignación de un medicamento a un paciente.
     * URL esperada (cuando tengas rutas): GET /medico/medicamentos/asignar?medicamento={idMedicamento}
     * Vista: resources/views/medico/recetas/create-asignacion.blade.php
     */
    public function create(Request $request)
    {
        // 1) Verificar usuario autenticado y resolver su médico (Medicos.id por usuario_id)
        $usuarioId = Auth::id(); // Usuarios.idUsuario
        if (!$usuarioId) {
            abort(401, 'No autenticado.');
        }

        $medico = DB::table('Medicos')
            ->select('id', 'usuario_id')
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$medico) {
            abort(403, 'El usuario autenticado no está vinculado a un médico.');
        }

        // 2) Obtener id del medicamento desde querystring
        $idMedicamento = (int) $request->query('medicamento', 0);
        if ($idMedicamento <= 0) {
            abort(404, 'Falta el parámetro ?medicamento o es inválido.');
        }

        // 3) Cargar el medicamento
        $medicamento = DB::table('Medicamentos')
            ->where('idMedicamento', $idMedicamento)
            ->first();

        if (!$medicamento) {
            abort(404, 'El medicamento no existe.');
        }

        // 4) Listar pacientes del médico (JOIN con Usuarios para mostrar nombre)
        $pacientes = DB::table('Pacientes as p')
            ->join('Usuarios as u', 'u.idUsuario', '=', 'p.usuario_id')
            ->where('p.medico_id', $medico->id) // Pacientes.medico_id → Medicos.id
            ->orderBy('u.nombre')
            ->orderBy('u.apellido')
            ->get([
                'p.id as idPaciente',
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as display_name")
            ]);

        // 5) Renderizar la vista
        return view('medico.recetas.create-asignacion', [
            'medicoId'    => $medico->id,                     // hidden si lo quieres mostrar
            'medicamento' => $medicamento,                    // tarjeta informativa
            'pacientes'   => $pacientes,                      // opciones del <select>
            'hoy'         => Carbon::now()->toDateString(),   // fecha sugerida
        ]);
    }

    /**
     * Procesa la asignación: crea cabecera (RecetasMedicas) y detalle (Detalle_Medicamento).
     * URL esperada (cuando tengas rutas): POST /medico/medicamentos/asignar
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Autenticación y médico actual
        $usuarioId = Auth::id();
        if (!$usuarioId) {
            abort(401, 'No autenticado.');
        }

        $medico = DB::table('Medicos')
            ->select('id', 'usuario_id')
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$medico) {
            abort(403, 'El usuario autenticado no está vinculado a un médico.');
        }

        // 2) Validación de campos del formulario
        $data = $request->validate([
            'fkMedicamento' => ['required', 'integer', 'min:1'],
            'fkPaciente'    => ['required', 'integer', 'min:1'],
            'dosis'         => ['required', 'string', 'max:100'],
            'frecuencia'    => ['required', 'string', 'max:100'],
            'duracion'      => ['required', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $idMedicamento = (int) $data['fkMedicamento'];
        $idPaciente    = (int) $data['fkPaciente'];

        // 3) Verificaciones de existencia y pertenencia
        $existsMedicamento = DB::table('Medicamentos')
            ->where('idMedicamento', $idMedicamento)
            ->exists();

        if (!$existsMedicamento) {
            throw ValidationException::withMessages([
                'fkMedicamento' => 'El medicamento seleccionado no existe.',
            ]);
        }

        $paciente = DB::table('Pacientes')
            ->select('id', 'medico_id')
            ->where('id', $idPaciente)
            ->first();

        if (!$paciente) {
            throw ValidationException::withMessages([
                'fkPaciente' => 'El paciente seleccionado no existe.',
            ]);
        }

        if ((int) $paciente->medico_id !== (int) $medico->id) {
            throw ValidationException::withMessages([
                'fkPaciente' => 'El paciente no pertenece al médico actual.',
            ]);
        }

        // 4) Transacción: insertar cabecera y detalle
        DB::beginTransaction();
        try {
            // Cabecera: RecetasMedicas (fkMedico → Medicos.id, fkPaciente → Pacientes.id)
            $idReceta = DB::table('RecetasMedicas')->insertGetId([
                'fecha'         => Carbon::now()->toDateString(),
                'observaciones' => $data['observaciones'] ?? null,
                'fkMedico'      => $medico->id,
                'fkPaciente'    => $idPaciente,
            ]);

            // Detalle: Detalle_Medicamento (respeta UNIQUE fkReceta+fkMedicamento)
            DB::table('Detalle_Medicamento')->insert([
                'fkReceta'      => $idReceta,
                'fkMedicamento' => $idMedicamento,
                'dosis'         => $data['dosis'],
                'frecuencia'    => $data['frecuencia'],
                'duracion'      => $data['duracion'],
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->with('error', 'Ocurrió un error al guardar la receta: ' . $e->getMessage());
        }

        // 5) Redirección con éxito
        return redirect()
            ->back()
            ->with('success', 'Receta creada y medicamento asignado correctamente.');
    }
}
