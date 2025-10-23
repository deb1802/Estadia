<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Repositories\ActividadesTerapRepository;
use App\Models\ActividadesTerap;
use Illuminate\Http\Request;

class ActividadesTController extends Controller
{
    /** @var ActividadesTerapRepository */
    private $actividadesTerapRepository;

    public function __construct(ActividadesTerapRepository $actividadesTerapRepo)
    {
        $this->actividadesTerapRepository = $actividadesTerapRepo;

        // Vincula la policy automáticamente con los métodos del resource.
        // El nombre del parámetro ('actividad') DEBE coincidir con tus rutas ->parameters(['actividades_terap' => 'actividad'])
        $this->authorizeResource(ActividadesTerap::class, 'actividad');
    }

    /**
     * Listado
     */
    public function index(Request $request)
    {
        $actividadesTeraps = $this->actividadesTerapRepository->paginate(10);

        return view('medico.actividades_terap.index')
            ->with('actividadesTeraps', $actividadesTeraps);
    }

    /**
     * Form crear
     */
    public function create()
    {
        return view('medico.actividades_terap.create');
    }

    /**
     * Guardar
     */
    public function store(Request $request)
    {
        // Validación (ajusta a tus campos reales)
        $request->validate([
            'titulo'              => 'required|string|max:255',
            'tipoContenido'       => 'required|in:audio,video,lectura',
            'categoriaTerapeutica'=> 'required|string|max:255',
            'diagnosticoDirigido' => 'required|string|max:255',
            'nivelSeveridad'      => 'required|string|max:255',
            'link'                => 'nullable|url',
            'archivo'             => 'nullable|file|mimes:pdf,mp3,mp4,avi,mov,jpg,jpeg,png|max:102400',
        ]);

        $input = $request->all();

        // Buscar el médico asociado al usuario autenticado
        $medico = \App\Models\Medico::where('usuario_id', auth()->id())->first();
        if (!$medico) {
            \Flash::error('No se encontró el perfil del médico asociado al usuario actual.');
            return back();
        }
        $input['fkMedico'] = $medico->id;

        // Recurso (archivo o link)
        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('recursos', 'public');
            $input['recurso'] = $ruta;
        } elseif (!empty($request->link)) {
            $input['recurso'] = $request->link;
        } else {
            $input['recurso'] = null;
        }

        $this->actividadesTerapRepository->create($input);

        \Flash::success('Actividad terapéutica registrada correctamente.');

        $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
        return redirect()->route($routeArea . 'actividades_terap.index');
    }

    /**
     * Ver detalle
     *
     * Importante: Usamos Route Model Binding (ActividadesTerap $actividad)
     * para que authorizeResource + policy funcionen con el modelo real.
     */
    public function show(ActividadesTerap $actividad)
    {
        return view('medico.actividades_terap.show')
            ->with('actividadesTerap', $actividad);
    }

    /**
     * Form editar
     */
    public function edit(ActividadesTerap $actividad)
    {
        return view('medico.actividades_terap.edit')
            ->with('actividadesTerap', $actividad);
    }

    /**
     * Actualizar
     */
    public function update(Request $request, ActividadesTerap $actividad)
    {
        // Validación (ajusta a tus campos reales)
        $request->validate([
            'titulo'              => 'required|string|max:255',
            'tipoContenido'       => 'required|in:audio,video,lectura',
            'categoriaTerapeutica'=> 'required|string|max:255',
            'diagnosticoDirigido' => 'required|string|max:255',
            'nivelSeveridad'      => 'required|string|max:255',
            'link'                => 'nullable|url',
            'archivo'             => 'nullable|file|mimes:pdf,mp3,mp4,avi,mov,jpg,jpeg,png|max:102400',
        ]);

        $input = $request->all();

        // Mantén el mismo fkMedico salvo que el usuario actual sea médico y quieras reasignar
        $medico = \App\Models\Medico::where('usuario_id', auth()->id())->first();
        if ($medico) {
            $input['fkMedico'] = $medico->id;
        }

        // Recurso (si suben uno nuevo, reemplaza; si no, conserva el actual)
        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('recursos', 'public');
            $input['recurso'] = $ruta;
        } elseif (!empty($request->link)) {
            $input['recurso'] = $request->link;
        } else {
            // si no viene ni archivo ni link, no toques 'recurso'
            unset($input['recurso']);
        }

        // Usa el id real del modelo (respeta primaryKey idActividad)
        $this->actividadesTerapRepository->update($input, $actividad->getKey());

        \Flash::success('Actividad terapéutica actualizada correctamente.');

        $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
        return redirect()->route($routeArea . 'actividades_terap.show', $actividad);
    }

    /**
     * Eliminar
     */
    public function destroy(ActividadesTerap $actividad)
    {
        $this->actividadesTerapRepository->delete($actividad->getKey());

        \Flash::success('Actividad terapéutica eliminada correctamente.');

        $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
        return redirect()->route($routeArea . 'actividades_terap.index');
    }
}
