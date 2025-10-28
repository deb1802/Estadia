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

    /** Prefijo de rutas según el área actual (admin. | medico.) */
    protected string $routeBase;

    public function __construct(ActividadesTerapRepository $actividadesTerapRepo, Request $request)
    {
        $this->actividadesTerapRepository = $actividadesTerapRepo;

        // Vincula la policy automáticamente con los métodos del resource.
        // Debe coincidir el nombre del parámetro de ruta: ->parameters(['actividades_terap' => 'actividad'])
        $this->authorizeResource(ActividadesTerap::class, 'actividad');

        // Detecta el área desde donde vienes y fija el prefijo de rutas
        $this->routeBase = $request->routeIs('admin.*') ? 'admin.' : 'medico.';
    }

    /**
     * Listado
     */
    public function index(Request $request)
    {
        $actividadesTeraps = $this->actividadesTerapRepository->paginate(10);

        // Reutilizamos las vistas bajo /medico/... como acordaste
        return view('medico.actividades_terap.index', [
            'actividadesTeraps' => $actividadesTeraps,
        ]);
    }

    /**
     * Form crear
     */
    public function create()
    {
        // Si entras como admin y tu Policy no permite create para admin,
        // authorizeResource ya te devolverá 403 (correcto).
        return view('medico.actividades_terap.create');
    }

    /**
     * Guardar
     */
    public function store(Request $request)
    {
        // Validación (ajusta a tus campos reales)
        $request->validate([
            'titulo'               => 'required|string|max:255',
            'tipoContenido'        => 'required|in:audio,video,lectura',
            'categoriaTerapeutica' => 'required|string|max:255',
            'diagnosticoDirigido'  => 'required|string|max:255',
            'nivelSeveridad'       => 'required|string|max:255',
            'link'                 => 'nullable|url',
            'archivo'              => 'nullable|file|mimes:pdf,mp3,mp4,avi,mov,jpg,jpeg,png|max:102400',
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

        return redirect()->route($this->routeBase . 'actividades_terap.index');
    }

    /**
     * Ver detalle
     *
     * Importante: Usamos Route Model Binding (ActividadesTerap $actividad)
     * para que authorizeResource + policy funcionen con el modelo real.
     */
    public function show(ActividadesTerap $actividad)
    {
        return view('medico.actividades_terap.show', [
            'actividadesTerap' => $actividad,
        ]);
    }

    /**
     * Form editar
     */
    public function edit(ActividadesTerap $actividad)
    {
        return view('medico.actividades_terap.edit', [
            'actividadesTerap' => $actividad,
        ]);
    }

    /**
     * Actualizar
     */
    public function update(Request $request, ActividadesTerap $actividad)
    {
        // Validación (ajusta a tus campos reales)
        $request->validate([
            'titulo'               => 'required|string|max:255',
            'tipoContenido'        => 'required|in:audio,video,lectura',
            'categoriaTerapeutica' => 'required|string|max:255',
            'diagnosticoDirigido'  => 'required|string|max:255',
            'nivelSeveridad'       => 'required|string|max:255',
            'link'                 => 'nullable|url',
            'archivo'              => 'nullable|file|mimes:pdf,mp3,mp4,avi,mov,jpg,jpeg,png|max:102400',
        ]);

        $input = $request->all();

        // Si el usuario actual es médico, conserva/actualiza su fkMedico
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
            // Ni archivo ni link => no tocar el campo
            unset($input['recurso']);
        }

        // Usa el id real del modelo (respeta primaryKey idActividad si aplica)
        $this->actividadesTerapRepository->update($input, $actividad->getKey());

        \Flash::success('Actividad terapéutica actualizada correctamente.');

        return redirect()->route($this->routeBase . 'actividades_terap.show', $actividad);
    }

    /**
     * Eliminar
     */
    public function destroy(ActividadesTerap $actividad)
    {
        $this->actividadesTerapRepository->delete($actividad->getKey());

        \Flash::success('Actividad terapéutica eliminada correctamente.');

        return redirect()->route($this->routeBase . 'actividades_terap.index');
    }
}
