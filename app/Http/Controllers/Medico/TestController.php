<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreTestRequest;


class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // el rol lo aplica tu Route Group
    }

    /** ===== Helpers de rol/alcance ===== */

    /** ¿El usuario logueado es admin? */
    private function isAdmin(): bool
    {
        $u = Auth::user();
        return $u && in_array($u->tipoUsuario, ['admin', 'administrador']);
    }

    /**
     * Resolver id del médico a partir del usuario autenticado (solo si no es admin).
     * Lanza 401/403 si no corresponde.
     */
    private function medicoIdOrFail(): int
    {
        $user = Auth::user();
        if (!$user) abort(401);

        // Usuarios.idUsuario -> Medicos.usuario_id -> Medicos.id
        $medicoId = Medico::where('usuario_id', $user->idUsuario)->value('id');
        if (!$medicoId) {
            abort(403, 'Tu cuenta no está vinculada a un perfil de médico.');
        }
        return (int) $medicoId;
    }

    /**
     * Para admin devuelve null (sin filtro por fkMedico).
     * Para médico devuelve su id (para filtrar por fkMedico).
     */
    private function medicoIdOrNullIfAdmin(): ?int
    {
        return $this->isAdmin() ? null : $this->medicoIdOrFail();
    }

    /**
     * Aplica alcance por rol:
     * - Médico: filtra por fkMedico
     * - Admin : no filtra
     */
    private function scopeByRole(Builder $q): Builder
    {
        $medicoId = $this->medicoIdOrNullIfAdmin();
        return $medicoId ? $q->where('fkMedico', $medicoId) : $q;
    }

    /** ===== Acciones CRUD ===== */

    /** 🔹 Lista de tests (Admin puede ver todos; Médico solo los suyos) */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Test::class);

        $q = trim((string) $request->get('q', ''));

        $tests = $this->scopeByRole(Test::query())
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

    /** 🔹 Form crear (solo médico) */
    public function create()
    {
        $this->authorize('create', Test::class); // bloquea admin
        return view('medico.tests.create');
    }

    /** 🔹 Guardar test (solo médico) */
    public function store(StoreTestRequest $request)
    {
        // Datos ya validados y normalizados por el Form Request
        $data = $request->validated();

        // FK del médico autenticado
        $medicoId = $this->medicoIdOrFail();

        // Crear test
        $test = \App\Models\Test::create([
            'nombre'        => $data['nombre'],
            'tipoTrastorno' => $data['tipoTrastorno'] ?? null,
            'descripcion'   => $data['descripcion']   ?? null,
            'estado'        => $data['estado'],
            'fkMedico'      => $medicoId,
        ]);

        return redirect()
            ->route('medico.tests.edit', $test->idTest)
            ->with('success', '✅ Test creado correctamente. Ahora puedes agregar preguntas, opciones y rangos.');
    }
    /** 🔹 Ver detalle (admin o médico dueño) */
    public function show($idTest)
    {
        $test = $this->scopeByRole(Test::query())->findOrFail($idTest);
        $this->authorize('view', $test);

        return view('medico.tests.show', compact('test'));
    }

    /** 🔹 Form editar (admin o médico dueño) */
    public function edit($idTest)
    {
        $test = $this->scopeByRole(Test::query())->findOrFail($idTest);
        $this->authorize('update', $test);

        return view('medico.tests.edit', compact('test'));
    }

    /** 🔹 Actualizar (admin o médico dueño) */
    public function update(Request $request, $idTest)
    {
        $test = $this->scopeByRole(Test::query())->findOrFail($idTest);
        $this->authorize('update', $test);

        $request->validate([
            'nombre'        => 'required|string|max:150',
            'tipoTrastorno' => 'nullable|string|max:120',
            'descripcion'   => 'nullable|string',
            'estado'        => 'required|in:activo,inactivo',
        ]);

        $test->update($request->only('nombre', 'tipoTrastorno', 'descripcion', 'estado'));

        return redirect()
            ->route($this->isAdmin() ? 'admin.tests.index' : 'medico.tests.index')
            ->with('success', '✅ Test actualizado correctamente.');
    }

    /** 🔹 Eliminar (admin o médico dueño) */
    public function destroy($idTest)
    {
        $test = $this->scopeByRole(Test::query())->findOrFail($idTest);
        $this->authorize('delete', $test);

        $test->delete();

        return redirect()
            ->route($this->isAdmin() ? 'admin.tests.index' : 'medico.tests.index')
            ->with('success', '🗑️ Test eliminado correctamente.');
    }
}
