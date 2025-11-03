@props([
  'id' => 'mwMascot',
  'g1' => '#bea4d2',
  'g2' => '#b5c8e1',
  'face' => 'neutral', // happy | neutral | focus
])

<svg id="{{ $id }}" viewBox="0 0 120 120" width="120" height="120" aria-hidden="true" class="mw-mascot">
  <defs>
    <linearGradient id="{{ $id }}-grad" x1="0" y1="0" x2="1" y2="1">
      <stop class="mw-gstop-1" offset="0%" stop-color="{{ $g1 }}"/>
      <stop class="mw-gstop-2" offset="100%" stop-color="{{ $g2 }}"/>
    </linearGradient>
  </defs>

  <g class="mw-blob" style="transform-origin: 50% 50%; animation: mwBlobPulse 3.5s ease-in-out infinite;">
    <path d="M60 15c16 0 31 8 38 19 7 11 6 24 0 36-6 12-19 23-35 24-16 2-34-5-41-17-7-12-3-28 6-41 9-13 16-21 32-21z" fill="url(#{{ $id }}-grad)"></path>
  </g>

  <g class="mw-eyes" style="animation: mwBlink 4.2s infinite; transform-origin:center;">
    <circle class="mw-eye-left" cx="48" cy="55" r="5" fill="#1f2d3d"/>
    <circle class="mw-eye-right" cx="72" cy="55" r="5" fill="#1f2d3d"/>
  </g>

  @php
    $d = match($face){
      'happy'  => 'M46 70c6 10 22 10 28 0',
      'focus'  => 'M46 73c10 0 18 0 28 0',
      default  => 'M46 73c6 6 22 6 28 0'
    };
  @endphp
  <path class="mw-mouth" d="{{ $d }}" stroke="#1f2d3d" stroke-width="3" stroke-linecap="round" fill="none"/>
  <style>
    @keyframes mwBlobPulse{ 0%,100%{transform:scale(1)} 50%{transform:scale(1.06)} }
    @keyframes mwBlink{ 0%,92%,100%{transform:scaleY(1)} 96%{transform:scaleY(.2)} }
  </style>
</svg>
