<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // el rol lo aplica tu Route Group
    }

    /** Resolver id del médico a partir del usuario autenticado */
    private function medicoIdOrFail(): int
    {
        $user = Auth::user();
        if (!$user) abort(401);

        // Usuarios.idUsuario  -> Medicos.usuario_id  -> Medicos.id
        $medicoId = Medico::where('usuario_id', $user->idUsuario)->value('id');
        if (!$medicoId) {
            abort(403, 'Tu cuenta no está vinculada a un perfil de médico.');
        }
        return (int)$medicoId;
    }

    /** 🔹 Lista de tests del médico */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $medicoId = $this->medicoIdOrFail();

        $tests = Test::query()
            ->where('fkMedico', $medicoId)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('tipoTrastorno', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('idTest')
            ->paginate(10)
            ->withQueryString();

        return view('medico.tests.index', compact('tests', 'q'));
    }

    /** 🔹 Form crear */
    public function create()
    {
        return view('medico.tests.create');
    }

    /** 🔹 Guardar test */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:150',
            'tipoTrastorno' => 'nullable|string|max:120',
            'descripcion'   => 'nullable|string',
            'estado'        => 'required|in:activo,inactivo',
        ]);

        $medicoId = $this->medicoIdOrFail();

        $test = Test::create([
            'nombre'        => $request->nombre,
            'tipoTrastorno' => $request->tipoTrastorno,
            'descripcion'   => $request->descripcion,
            'estado'        => $request->estado,
            'fkMedico'      => $medicoId, // ✅ FK correcta
        ]);

        return redirect()
            ->route('medico.tests.edit', $test->idTest)
            ->with('success', '✅ Test creado correctamente. Ahora puedes agregar preguntas, opciones y rangos.');
    }

    /** 🔹 Ver detalle */
    public function show($idTest)
    {
        $medicoId = $this->medicoIdOrFail();
        $test = Test::where('fkMedico', $medicoId)->findOrFail($idTest);
        return view('medico.tests.show', compact('test'));
    }

    /** 🔹 Form editar (datos generales) */
    public function edit($idTest)
    {
        $medicoId = $this->medicoIdOrFail();
        $test = Test::where('fkMedico', $medicoId)->findOrFail($idTest);
        return view('medico.tests.edit', compact('test'));
    }

    /** 🔹 Actualizar (datos generales) */
    public function update(Request $request, $idTest)
    {
        $request->validate([
            'nombre'        => 'required|string|max:150',
            'tipoTrastorno' => 'nullable|string|max:120',
            'descripcion'   => 'nullable|string',
            'estado'        => 'required|in:activo,inactivo',
        ]);

        $medicoId = $this->medicoIdOrFail();
        $test = Test::where('fkMedico', $medicoId)->findOrFail($idTest);
        $test->update($request->only('nombre', 'tipoTrastorno', 'descripcion', 'estado'));

        return redirect()
            ->route('medico.tests.index')
            ->with('success', '✅ Test actualizado correctamente.');
    }

    /** 🔹 Eliminar */
    public function destroy($idTest)
    {
        $medicoId = $this->medicoIdOrFail();
        $test = Test::where('fkMedico', $medicoId)->findOrFail($idTest);
        $test->delete();

        return redirect()
            ->route('medico.tests.index')
            ->with('success', '🗑️ Test eliminado correctamente.');
    }
}
