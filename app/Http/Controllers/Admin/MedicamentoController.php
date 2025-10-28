<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\MedicamentosRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Medicamento;
use Flash;

class MedicamentoController extends Controller
{
    /** @var MedicamentosRepository */
    private $medicamentosRepository;

    public function __construct(MedicamentosRepository $medicamentosRepo)
    {
        $this->medicamentosRepository = $medicamentosRepo;

        // (Opcional) Si usas policies:
        // $this->authorizeResource(Medicamento::class, 'medicamento');
    }

    /**
     * Devuelve 'admin.' o 'medico.' según el contexto actual.
     * Detecta por primer segmento de la URL y por prefijo del nombre de ruta.
     */
    private function base(): string
    {
        $seg1 = request()->segment(1);
        if ($seg1 === 'admin')  return 'admin.';
        if ($seg1 === 'medico') return 'medico.';

        $name = request()->route()?->getName() ?? '';
        if (is_string($name)) {
            if (str_starts_with($name, 'admin.'))  return 'admin.';
            if (str_starts_with($name, 'medico.')) return 'medico.';
        }

        // Fallback sensato (ajústalo si lo prefieres):
        return 'medico.';
    }

    /**
     * Listado de medicamentos.
     */
    public function index(Request $request)
    {
        $medicamentos = $this->medicamentosRepository->paginate(10);

        return view('admin.medicamentos.index', compact('medicamentos'));
    }

    /**
     * Form de creación.
     */
    public function create()
    {
        return view('admin.medicamentos.create');
    }

    /**
     * Guardar nuevo medicamento (con imagen).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'             => 'required|string|max:100',
            'presentacion'       => 'nullable|string|max:50',
            'indicaciones'       => 'nullable|string',
            'efectosSecundarios' => 'nullable|string',
            'imagenMedicamento'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagenMedicamento')) {
            $path = $request->file('imagenMedicamento')->store('medicamentos', 'public');
            $data['imagenMedicamento'] = $path;
        }

        $this->medicamentosRepository->create($data);

        Flash::success('Medicamento creado correctamente.');
        return redirect()->route($this->base().'medicamentos.index');
    }

    /**
     * Detalle.
     */
    public function show($id)
    {
        $medicamento = $this->medicamentosRepository->find($id);

        if (empty($medicamento)) {
            Flash::error('Medicamento no encontrado');
            return redirect()->route($this->base().'medicamentos.index');
        }

        return view('admin.medicamentos.show', compact('medicamento'));
    }

    /**
     * Form de edición.
     */
    public function edit($id)
    {
        $medicamento = $this->medicamentosRepository->find($id);

        if (empty($medicamento)) {
            Flash::error('Medicamento no encontrado');
            return redirect()->route($this->base().'medicamentos.index');
        }
        return view('admin.medicamentos.edit', compact('medicamento'));
    }

    /**
     * Actualizar (con posible reemplazo de imagen).
     */
    public function update($id, Request $request)
    {
        $medicamento = $this->medicamentosRepository->find($id);

        if (empty($medicamento)) {
            Flash::error('Medicamento no encontrado');
            return redirect()->route($this->base().'medicamentos.index');
        }

        $data = $request->validate([
            'nombre'             => 'required|string|max:100',
            'presentacion'       => 'nullable|string|max:50',
            'indicaciones'       => 'nullable|string',
            'efectosSecundarios' => 'nullable|string',
            'imagenMedicamento'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagenMedicamento')) {
            // borra la anterior si existe
            if ($medicamento->imagenMedicamento && Storage::disk('public')->exists($medicamento->imagenMedicamento)) {
                Storage::disk('public')->delete($medicamento->imagenMedicamento);
            }
            $path = $request->file('imagenMedicamento')->store('medicamentos', 'public');
            $data['imagenMedicamento'] = $path;
        }

        $this->medicamentosRepository->update($data, $id);

        Flash::success('Medicamento actualizado correctamente.');
        return redirect()->route($this->base().'medicamentos.show', $medicamento);
    }

    /**
     * Eliminar (borra imagen si existe).
     */
    public function destroy($id)
    {
        $medicamento = $this->medicamentosRepository->find($id);

        if (empty($medicamento)) {
            Flash::error('Medicamento no encontrado');
            return redirect()->route($this->base().'medicamentos.index');
        }

        if ($medicamento->imagenMedicamento && Storage::disk('public')->exists($medicamento->imagenMedicamento)) {
            Storage::disk('public')->delete($medicamento->imagenMedicamento);
        }

        $this->medicamentosRepository->delete($id);

        Flash::success('Medicamento eliminado correctamente.');
        return redirect()->route($this->base().'medicamentos.index');
    }
}
