<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateActividadesTerapRequest;
use App\Http\Requests\UpdateActividadesTerapRequest;
use App\Repositories\ActividadesTerapRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flash;
use Response;

class ActividadesTController extends Controller
{
    /** @var ActividadesTerapRepository */
    private $actividadesTerapRepository;

    public function __construct(ActividadesTerapRepository $actividadesTerapRepo)
    {
        $this->actividadesTerapRepository = $actividadesTerapRepo;
        $this->authorizeResource(\App\Models\ActividadesTerap::class, 'actividad');
    }

    /**
     * Muestra un listado de actividades terapéuticas.
     */
    public function index(Request $request)
    {
        $actividadesTeraps = $this->actividadesTerapRepository->paginate(10);

        return view('medico.actividades_terap.index')
            ->with('actividadesTeraps', $actividadesTeraps);
    }

    /**
     * Muestra el formulario para crear una nueva actividad terapéutica.
     */
    public function create()
    {
        return view('medico.actividades_terap.create');
    }

    /**
     * Guarda una nueva actividad terapéutica en la base de datos.
     */
  public function store(Request $request)
{
    // 🔹 Validación básica
        $request->validate([
        'titulo' => 'required|string|max:255',
        'tipoContenido' => 'required|in:audio,video,lectura',
        'categoriaTerapeutica' => 'required|string|max:255',
        'diagnosticoDirigido' => 'required|string|max:255',
        'nivelSeveridad' => 'required|string|max:255',
        'link' => 'nullable|url',
        'archivo' => 'nullable|file|mimes:pdf,mp3,mp4,avi,mov,jpg,jpeg,png|max:102400', // 👈 PDF permitido
    ]);


    $input = $request->all();

    // 🔹 Buscar el médico correspondiente al usuario logueado
    $medico = \App\Models\Medico::where('usuario_id', auth()->id())->first();

    if (!$medico) {
        \Flash::error('No se encontró el perfil del médico asociado al usuario actual.');
        return redirect()->back();
    }

    $input['fkMedico'] = $medico->id;

    // 🔹 Determinar qué tipo de recurso guardar
    if ($request->hasFile('archivo')) {
        $ruta = $request->file('archivo')->store('recursos', 'public');
        $input['recurso'] = $ruta;
    } elseif (!empty($request->link)) {
        $input['recurso'] = $request->link;
    } else {
        $input['recurso'] = null;
    }

    // 🔹 Crear registro
    $this->actividadesTerapRepository->create($input);

    \Flash::success('Actividad terapéutica registrada correctamente.');

    return redirect()->route('medico.actividades_terap.index');
}


    /**
     * Muestra los detalles de una actividad terapéutica.
     */
    public function show($id)
{
    $actividad = $this->actividadesTerapRepository->find($id);

    if (!$actividad) {
        \Flash::error('Actividad no encontrada.');
        return redirect()->route('medico.actividades_terap.index');
    }

    return view('medico.actividades_terap.show')
        ->with('actividadesTerap', $actividad);
}

public function edit($id)
{
    $actividad = $this->actividadesTerapRepository->find($id);

    if (!$actividad) {
        \Flash::error('Actividad no encontrada.');
        return redirect()->route('medico.actividades_terap.index');
    }

    return view('medico.actividades_terap.edit')
        ->with('actividadesTerap', $actividad);
}


    /**
     * Actualiza una actividad terapéutica existente.
     */
    public function update($id, Request $request)
    {
        $actividad = $this->actividadesTerapRepository->find($id);

        if (empty($actividad)) {
            Flash::error('Actividad no encontrada.');
            return redirect()->route('medico.actividades_terap.index');
        }

        $input = $request->all();
        $input['fkMedico'] = Auth::user()->id;

        $this->actividadesTerapRepository->update($input, $id);

        Flash::success('Actividad terapéutica actualizada correctamente.');
        return redirect()->route('medico.actividades_terap.index');
    }

    /**
     * Elimina una actividad terapéutica.
     */
    public function destroy($id)
    {
        $actividad = $this->actividadesTerapRepository->find($id);

        if (empty($actividad)) {
            Flash::error('Actividad no encontrada.');
            return redirect()->route('medico.actividades_terap.index');
        }

        $this->actividadesTerapRepository->delete($id);

        Flash::success('Actividad terapéutica eliminada correctamente.');
        return redirect()->route('medico.actividades_terap.index');
    }
}
