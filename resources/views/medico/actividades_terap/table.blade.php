@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    // Detecta si la URL pertenece a médico o admin
    $routeArea = request()->is('medico/*') ? 'medico.' : 'admin.';
@endphp

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle table-crud" id="actividades-terap-table">
            <thead class="table-primary text-center">
                <tr>
                    <th>Título</th>
                    <th>Tipo de Contenido</th>
                    <th>Categoría Terapéutica</th>
                    <th>Diagnóstico Dirigido</th>
                    <th>Nivel de Severidad</th>
                    <th>Recurso</th>
                    <th style="width: 160px;" class="text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
            @forelse($actividadesTeraps as $actividad)
                @php
                    // Resolver ruta del recurso (URL externa o archivo local)
                    $raw = trim((string)($actividad->recurso ?? ''));
                    $src = '';
                    if ($raw !== '') {
                        if (Str::startsWith($raw, ['http://', 'https://', '//'])) {
                            $src = $raw;
                        } elseif (Storage::disk('public')->exists($raw)) {
                            $src = Storage::url($raw); // /storage/...
                        } else {
                            // fallback si ya está en public/
                            $src = asset(ltrim($raw,'/'));
                        }
                    }
                @endphp

                <tr>
                    <td>{{ $actividad->titulo }}</td>
                    <td>{{ ucfirst($actividad->tipoContenido) }}</td>
                    <td>{{ $actividad->categoriaTerapeutica }}</td>
                    <td>{{ $actividad->diagnosticoDirigido }}</td>
                    <td>{{ $actividad->nivelSeveridad }}</td>

                    {{-- Recurso --}}
                    <td>
                        @if($src)
                            @if(Str::startsWith($src, ['http://','https://']))
                                <a href="{{ $src }}" target="_blank" class="btn btn-sm btn-link text-info">
                                    <i class="fas fa-external-link-alt"></i> Ver enlace
                                </a>
                            @else
                                <a href="{{ $src }}" target="_blank" class="btn btn-sm btn-link text-success">
                                    <i class="fas fa-play-circle"></i> Ver recurso
                                </a>
                            @endif
                        @else
                            <span class="text-muted">Sin recurso</span>
                        @endif
                    </td>

                    {{-- Acciones con Policies --}}
                    <td class="text-center">
                        <div class="btn-group" style="gap:4px;">

                            {{-- Ver (permitido para todos los roles) --}}
                            <a href="{{ route($routeArea.'actividades_terap.show', $actividad) }}"
                               class="btn btn-sm btn-outline-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Editar (solo médico) --}}
                            @can('update', $actividad)
                                <a href="{{ route($routeArea.'actividades_terap.edit', $actividad) }}"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan

                            {{-- Eliminar (médico y admin) --}}
                            @can('delete', $actividad)
                                {!! Form::open([
                                    'route'  => [$routeArea.'actividades_terap.destroy', $actividad],
                                    'method' => 'delete',
                                    'style'  => 'display:inline'
                                ]) !!}
                                    {!! Form::button('<i class="fas fa-trash-alt"></i>', [
                                        'type'    => 'submit',
                                        'class'   => 'btn btn-sm btn-outline-danger',
                                        'title'   => 'Eliminar',
                                        'onclick' => "return confirm('¿Deseas eliminar esta actividad terapéutica?')"
                                    ]) !!}
                                {!! Form::close() !!}
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay actividades terapéuticas registradas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $actividadesTeraps])
        </div>
    </div>
</div>
