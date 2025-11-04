<div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Nombre completo</th>
                <th>Parentesco</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Observaciones</th>
                <th class="text-center">Ver detalles</th>
            </tr>
        </thead>
        <tbody>
        @forelse($tutors as $tutor)
            <tr>
                {{-- 🔹 Muestra nombre + apellido correctamente --}}
                <td>{{ $tutor->nombre }} {{ $tutor->apellido }}</td>

                <td>{{ $tutor->parentesco }}</td>
                <td>{{ $tutor->telefono }}</td>
                <td>{{ $tutor->correo }}</td>

                {{-- Si no hay observaciones, muestra un guion --}}
                <td>{{ $tutor->observaciones ? $tutor->observaciones : '—' }}</td>

                <td class="text-center">
                    <a href="{{ route('paciente.tutores.show', $tutor->idTutor) }}" 
                       class="btn btn-outline-secondary btn-sm" 
                       title="Ver detalles">
                        <i class="far fa-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    No tienes tutores registrados actualmente.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- 🔹 Paginación --}}
    @if ($tutors->hasPages())
    <div class="card-footer clearfix">
        <div class="float-end">
            @include('adminlte-templates::common.paginate', ['records' => $tutors])
        </div>
    </div>
    @endif
</div>
