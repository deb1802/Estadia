@php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $items */

  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Str;

  // URLs de destino con fallbacks seguros
  $testsUrl = Route::has('paciente.tests.index') ? route('paciente.tests.index') : url('/paciente/tests');
  $actsUrl  = Route::has('paciente.actividades_terap.index')
              ? route('paciente.actividades_terap.index')
              : (Route::has('paciente.actividades.index') ? route('paciente.actividades.index') : url('/paciente/actividades'));
@endphp

@if($items->isEmpty())
  <li class="list-group-item text-muted">Sin notificaciones.</li>
@else
  @foreach($items as $n)
    @php
      // Heurística simple por palabras clave/urls en título+mensaje
      $texto  = Str::lower(trim(($n->titulo ?? '').' '.($n->mensaje ?? '')));
      $isTest = Str::contains($texto, [' test ', ' tests ', '/paciente/tests', 'mis tests', 'ir al test']);
      $isActs = Str::contains($texto, ['actividad', 'actividades', '/paciente/actividades', 'terap']);
    @endphp

    <li class="list-group-item {{ $n->leida ? '' : 'font-weight-bold' }}">
      <div class="small">{{ $n->titulo ?? 'Notificación' }}</div>
      <div class="text-muted small">{{ $n->mensaje ?? '' }}</div>

      <div class="d-flex justify-content-between align-items-center mt-1">
        <span class="text-muted small">{{ optional($n->fecha)->format('d/m/Y H:i') }}</span>

        <div class="d-flex gap-2">
          {{-- Botón contextual según el tipo detectado --}}
          @if($isTest)
            <a href="{{ $testsUrl }}" class="btn btn-sm btn-outline-primary" title="Ir a mis tests">Ir al test</a>
          @elseif($isActs)
            <a href="{{ $actsUrl }}" class="btn btn-sm btn-outline-primary" title="Ir a mis actividades">Ir a la actividad</a>
          @endif

          @unless($n->leida)
            <button class="btn btn-sm btn-outline-primary"
                    data-id="{{ $n->idNotificacion }}"
                    onclick="pbnMarkOne(this)">
              Marcar leída
            </button>
          @endunless
        </div>
      </div>
    </li>
  @endforeach
@endif
