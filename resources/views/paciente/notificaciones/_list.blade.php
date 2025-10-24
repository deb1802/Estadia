@php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $items */
@endphp

@if($items->isEmpty())
  <li class="list-group-item text-muted">Sin notificaciones.</li>
@else
  @foreach($items as $n)
    <li class="list-group-item {{ $n->leida ? '' : 'font-weight-bold' }}">
      <div class="small">{{ $n->titulo ?? 'Notificación' }}</div>
      <div class="text-muted small">{{ $n->mensaje ?? '' }}</div>
      <div class="d-flex justify-content-between align-items-center mt-1">
        <span class="text-muted small">{{ optional($n->fecha)->format('d/m/Y H:i') }}</span>
        @unless($n->leida)
          <button class="btn btn-sm btn-outline-primary"
                  data-id="{{ $n->idNotificacion }}"
                  onclick="pbnMarkOne(this)">Marcar leída</button>
        @endunless
      </div>
    </li>
  @endforeach
@endif
