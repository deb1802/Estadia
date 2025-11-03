{{-- resources/views/layouts/landing.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'MindWare')</title>

  {{-- No cargamos Tailwind/Breeze aquí --}}
  {{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}

  {{-- Fuente igual que el navbar principal --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  {{-- Bootstrap + Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- AOS opcional --}}
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  @stack('styles')

  <style>
    :root{
      --gray-50:#f9fafb; --gray-100:#f3f4f6; --gray-200:#e5e7eb; --gray-300:#d1d5db;
      --gray-700:#374151; --gray-800:#1f2937;
      --shadow-sm:0 1px 2px rgba(0,0,0,.05);
    }

    html,body{
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      background:#fff;
    }

    /* ===== Navbar tipo Tailwind (bg-white, border-b, sombra ligera) ===== */
    header.landing-topbar{
      background:#fff;
      border-bottom:1px solid var(--gray-100);
      box-shadow:var(--shadow-sm);
    }

    .topbar-wrap{
      height:64px;                       /* h-16 */
      display:flex; align-items:center; justify-content:space-between;
      gap:1rem; flex-wrap:nowrap;
    }

    /* contenedor tipo max-w-7xl */
    .container-7xl{ max-width:1280px; margin-inline:auto; padding-inline:1rem; }
    @media (min-width:640px){ .container-7xl{ padding-inline:1.5rem; } }
    @media (min-width:1024px){ .container-7xl{ padding-inline:2rem; } }

    /* === Identidad === */
    .brand{
      display:flex; align-items:center; gap:.5rem;
      text-decoration:none; color:var(--gray-800);
      white-space:nowrap;
    }
    .brand:hover{ text-decoration:none; color:var(--gray-800); }

    .brand-logo{
      width:36px; height:36px;
      object-fit:contain;
      display:block;
    }

    .brand-text{
      font-weight:700;
      letter-spacing:.2px;
    }

    /* === Botón Iniciar sesión === */
    .btn-gray{
      background:var(--gray-200); color:var(--gray-700);
      border:1px solid var(--gray-200);
      font-weight:600; padding:.5rem 1rem; border-radius:.5rem;
      white-space:nowrap;
      transition: background-color .15s ease, box-shadow .15s ease;
    }
    .btn-gray:hover{ background:var(--gray-300); color:var(--gray-700); }
    .btn-gray:focus{ outline:0; box-shadow:0 0 0 .2rem rgba(31,41,55,.08); }

    /* Footer */
    .footer-lite{ background:#fafcff; border-top:1px solid var(--gray-200); color:#43536a; }
  </style>
</head>
<body class="antialiased">

  {{-- === Navbar limpio con logo.png === --}}
  <header class="landing-topbar">
    <div class="container-7xl">
      <div class="topbar-wrap">

        {{-- Izquierda: logo + marca --}}
        <a href="{{ url('/') }}" class="brand">
          <img src="{{ asset('img/logo.png') }}" alt="Logo MindWare" class="brand-logo">
          <span class="brand-text d-none d-sm-inline">MindWare</span>
        </a>

        {{-- Derecha: solo Iniciar sesión --}}
        <nav class="d-flex align-items-center">
          <a href="{{ route('login') }}" class="btn btn-gray">
            <i class="bi bi-door-open me-1"></i> Iniciar sesión
          </a>
        </nav>

      </div>
    </div>
  </header>

  {{-- Contenido dinámico --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
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

  @stack('scripts')
</body>
</html>
