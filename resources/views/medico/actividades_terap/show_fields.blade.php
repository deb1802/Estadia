@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $a = $actividadesTerap ?? null;

    // Valor crudo tal como viene de BD (puede ser http(s) o algo como 'recursos/archivo.mp4')
    $raw = trim($a->recurso ?? '');
    $src = '';

    if ($raw !== '') {
        if (Str::startsWith($raw, ['http://','https://','//'])) {
            // URL absoluta (YouTube u otro host)
            $src = $raw;
        } else {
            // 1) ¿Existe en storage/app/public ?
            if (Storage::disk('public')->exists($raw)) {
                $src = Storage::url($raw);         // => /storage/recursos/archivo.mp4
            }
            // 2) ¿Existe en storage/app ? (algunos guardan así por error)
            elseif (Storage::disk('local')->exists($raw)) {
                $src = asset('storage/'.ltrim($raw,'/')); // intenta servirlo desde /storage
            }
            // 3) Último intento: está ya bajo /public (ej. public/recursos/…)
            else {
                $src = asset(ltrim($raw,'/'));
            }
        }
    }

    // Detecciones por patrón/extensión
    $isYoutube = $src && preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)/i', $src);
    $path      = $src ? (parse_url($src, PHP_URL_PATH) ?? '') : '';
    $ext       = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $isImage   = in_array($ext, ['jpg','jpeg','png','gif','webp','bmp','svg']);
    $isVideo   = in_array($ext, ['mp4','webm','ogg']);
    $isAudio   = in_array($ext, ['mp3','wav','ogg','m4a']);

    // ID de YouTube si aplica
    $ytId = null;
    if ($isYoutube) {
        if (preg_match('/youtu\.be\/([^?&]+)/i', $src, $m))      $ytId = $m[1];
        elseif (preg_match('/[?&]v=([^?&]+)/i', $src, $m))       $ytId = $m[1];
    }
@endphp


{{-- ===== Datos en “tarjetitas” usando tu .info-box ===== --}}
<div class="col-12 col-md-6 mb-3">
  <div class="info-box h-100">
    <h6>Título</h6>
    <p>{{ $a->titulo }}</p>
  </div>
</div>

<div class="col-12 col-md-6 mb-3">
  <div class="info-box h-100">
    <h6>Tipo de contenido</h6>
    <p class="mb-0 text-capitalize">{{ $a->tipoContenido }}</p>
  </div>
</div>

<div class="col-12 col-md-6 mb-3">
  <div class="info-box h-100">
    <h6>Categoría terapéutica</h6>
    <p class="mb-0">{{ $a->categoriaTerapeutica }}</p>
  </div>
</div>

<div class="col-12 col-md-6 mb-3">
  <div class="info-box h-100">
    <h6>Diagnóstico dirigido</h6>
    <p class="mb-0">{{ $a->diagnosticoDirigido }}</p>
  </div>
</div>

<div class="col-12 col-md-6 mb-3">
  <div class="info-box h-100">
    <h6>Nivel de severidad</h6>
    <p class="mb-0">{{ $a->nivelSeveridad }}</p>
  </div>
</div>

{{-- ===== Recurso embebido bonito ===== --}}
<div class="col-12">
  <div class="info-box">
    <h6>Recurso</h6>

    @if(!$src)
      <p class="mb-0 text-muted">Sin recurso adjunto.</p>

    @elseif($isYoutube && $ytId)
      <div style="position:relative;width:100%;padding-top:56.25%;border-radius:12px;overflow:hidden;background:#000;">
        <iframe
          src="https://www.youtube.com/embed/{{ $ytId }}"
          title="YouTube video player"
          style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen
          loading="lazy">
        </iframe>
      </div>
      <div class="mt-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ $src }}" target="_blank" rel="noopener">Abrir en YouTube</a>
      </div>

    @elseif($isImage)
      <img src="{{ $src }}" alt="Recurso de la actividad" class="img-fluid rounded" style="max-height:520px;object-fit:contain;">

    @elseif($isVideo)
      <video src="{{ $src }}" controls class="w-100 rounded" style="max-height:520px;"></video>

    @elseif($isAudio)
      <audio src="{{ $src }}" controls class="w-100"></audio>

    @else
      <div class="d-flex align-items-center gap-2">
        <span class="text-muted me-2">Recurso disponible:</span>
        <a class="btn btn-primary btn-sm" href="{{ $src }}" target="_blank" rel="noopener">Abrir recurso</a>
      </div>
      <small class="text-muted d-block mt-1">{{ $src }}</small>
    @endif
  </div>
</div>
