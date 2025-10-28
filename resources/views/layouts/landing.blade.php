{{-- resources/views/layouts/landing.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'MindWare')</title>

  {{-- ⚠️ Para esta landing NO cargamos Tailwind/Breeze (app.css) para evitar que “aplane” Bootstrap --}}
  {{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}

  {{-- Bootstrap + Icons (CDN) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- AOS (opcional) --}}
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  {{-- Aquí entran los @push('styles') de cada vista --}}
  @stack('styles')

  <style>
    :root{
      --ink:#123c7a; --soft:#f3f6fb; --stroke:#e6ecf5;
    }
    body{ background:#fff; }
    header.landing-topbar{
      background:#ffffffcc; backdrop-filter:blur(6px);
      border-bottom:1px solid var(--stroke);
    }
    .brand{ font-weight:800; color:var(--ink); text-decoration:none; }
    .footer-lite{ background:#fafcff; border-top:1px solid var(--stroke); color:#43536a; }
  </style>
</head>
<body class="antialiased">

  {{-- Topbar simple --}}
  <header class="landing-topbar py-2">
    <div class="container d-flex justify-content-between align-items-center">
      <a href="{{ url('/') }}" class="brand">MindWare</a>
      <nav class="d-flex align-items-center gap-3">
        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-door-open me-1"></i> Iniciar sesión
        </a>
      </nav>
    </div>
  </header>

  {{-- Contenido --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer legible (texto oscuro sobre fondo claro) --}}
  <footer class="footer-lite py-4 mt-5">
    <div class="container small d-flex flex-column flex-md-row justify-content-between gap-2">
      <span>© {{ date('Y') }} MindWare. Todos los derechos reservados.</span>
      <span>Contacto: mindwaremental@gmail.com</span>
    </div>
  </footer>

  {{-- Scripts Bootstrap + AOS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>if (window.AOS) AOS.init({ once:true, duration:700 });</script>

  {{-- Aquí entran los @push('scripts') de cada vista --}}
  @stack('scripts')
</body>
</html>
