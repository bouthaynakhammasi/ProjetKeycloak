<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', 'Gestion Client')</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
  @yield('styles')
</head>

<body class="starter-page-page">

  <header id="header" class="header d-flex align-items-center sticky-top shadow-sm">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="/" class="logo d-flex align-items-center">
        <h1 class="sitename">Gestion Client</h1><span>.</span>
      </a>

      @if(session()->has('user_id'))
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">Tableau de bord</a></li>
          <li><a href="#clients">Clients</a></li>
          <li><a href="#employees">Employés</a></li>
          <li><a href="#projects">Projets</a></li>
          <li class="dropdown"><a href="#"><span>{{ session('user_name') }}</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li>
                <div class="px-3 py-2 text-muted small">
                  <strong>ID:</strong> <code style="font-size: 11px;">{{ session('user_id') }}</code><br>
                  <strong>Email:</strong> {{ session('user_email') }}
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li><a href="/logout" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
            </ul>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      @else
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="/" class="{{ Request::is('/') ? 'active' : '' }}">Accueil</a></li>
          <li><a href="/login" class="btn btn-outline-success px-4 py-2 text-dark font-weight-bold" style="border-radius: 20px;">Se connecter</a></li>
        </ul>
      </nav>
      @endif

    </div>
  </header>

  <main class="main py-5 light-background">
    @yield('content')
  </main>

  <footer id="footer" class="footer light-background border-top">
    <div class="container copyright text-center py-3">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Gestion Client</strong> <span>All Rights Reserved</span></p>
      <div class="credits text-muted" style="font-size: 12px;">
        Intégration Keycloak SSO effectuée avec succès
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @yield('scripts')
</body>

</html>
