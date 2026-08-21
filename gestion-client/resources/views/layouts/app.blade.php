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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

  <!-- Tailwind CSS & Lucide Icons via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            primary: '#7C3AED', // Purple
            background: '#F5F5F7',
            surface: '#FFFFFF',
            text: '#111827',
            textMuted: '#6B7280',
            border: '#E5E7EB',
            brand: {
                50: '#fff7ed',
                100: '#ffedd5',
                200: '#fed7aa',
                300: '#fdba74',
                400: '#fb923c',
                500: '#f97316',
                600: '#ea580c',
                700: '#c2410c',
            }
          }
        }
      }
    }
  </script>

  <style>
    /* Global Background with Image */
    body {
        background-image: url('{{ asset('assets/img/hero-carousel/hero-carousel-1.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    /* Dark overlay for better readability */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(2px);
        z-index: 0;
        pointer-events: none;
    }

    /* Hide scrollbar for sidebar */
    .sidebar-scroll::-webkit-scrollbar {
      display: none;
    }
    .sidebar-scroll {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    /* Decorative Blobs */
    .blob-1 {
        position: fixed;
        top: -120px;
        right: -80px;
        width: 500px;
        height: 500px;
        border-radius: 9999px;
        background: radial-gradient(circle, rgba(124,58,237,0.15) 0%, rgba(167,139,250,0.08) 60%, transparent 100%);
        filter: blur(48px);
        pointer-events: none;
        z-index: 0;
    }
    .blob-2 {
        position: fixed;
        bottom: -100px;
        left: -60px;
        width: 420px;
        height: 420px;
        border-radius: 9999px;
        background: radial-gradient(circle, rgba(124,58,237,0.10) 0%, rgba(167,139,250,0.06) 60%, transparent 100%);
        filter: blur(56px);
        pointer-events: none;
        z-index: 0;
    }
    .blob-3 {
        position: fixed;
        top: 40%;
        left: 30%;
        width: 320px;
        height: 320px;
        border-radius: 9999px;
        background: radial-gradient(circle, rgba(148,163,184,0.08) 0%, transparent 80%);
        filter: blur(40px);
        pointer-events: none;
        z-index: 0;
    }
    .page-content {
        position: relative;
        z-index: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        body {
            background-attachment: scroll;
        }
        body::before {
            background: rgba(0, 0, 0, 0.5);
        }
    }
  </style>

  @yield('styles')
</head>

<body class="@if(session()->has('user_id')) flex h-screen overflow-hidden font-sans @else starter-page-page @endif">

  @if(session()->has('user_id'))
    <!-- Top partial gradient bar -->
    <div style="height: 4px; background: linear-gradient(90deg, #ec4899 0%, #ef4444 100%); width: 15%; position: absolute; top: 0; left: 0; z-index: 50;"></div>

    <!-- Sidebar -->
    <aside class="w-[220px] bg-white border-r border-gray-200 flex flex-col h-full z-10 shrink-0">
        <!-- Logo -->
        <div class="h-16 flex items-center px-5 border-b border-gray-100">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <span class="text-lg font-bold text-gray-900 tracking-tight">Gestion Client</span>
            </a>
        </div>

        <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-4 flex flex-col gap-6">
            <!-- Main Navigation -->
            <nav class="px-3 flex flex-col gap-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    Tableau de bord
                </a>
                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('employees*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    Demo User
                </a>
            </nav>

            <!-- Manage Section -->
            <div class="px-3">
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gérer</h3>
                <nav class="flex flex-col gap-0.5">
                    @if(session('user_role') === 'ROLE_ADMIN')
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 text-sm no-underline">
                            <i data-lucide="building-2" class="w-4 h-4 text-gray-400"></i> Entreprise
                        </a>
                        <a href="{{ route('agenda.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('agenda*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i> Agenda
                        </a>
                        <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('employees*') || Request::is('employes*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                            <i data-lucide="users" class="w-4 h-4 text-gray-400"></i> Employés
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg {{ Request::is('admin/users*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                            <span class="flex items-center gap-3">
                                <i data-lucide="shield-check" class="w-4 h-4 text-gray-400"></i> Gestion des comptes
                            </span>
                            @php $pendingCount = \App\Models\KeycloakUser::pending()->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('presences.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('presences*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                            <i data-lucide="timer" class="w-4 h-4 text-gray-400"></i> Présence
                        </a>
                        <a href="{{ route('absences.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('absences*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                            <i data-lucide="palmtree" class="w-4 h-4 text-gray-400"></i> Absences
                        </a>
                    @endif
                    
                    <!-- Gestion des Salaires -->
                    @if(session('user_role') === 'ROLE_ADMIN')
                    <div class="px-3 mt-2">
                        <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Paie</h3>
                        <nav class="flex flex-col gap-0.5">
                            <a href="{{ route('salaires.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('salaires/dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 text-gray-400"></i> Dashboard Paie
                            </a>
                            <a href="{{ route('salaires.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('salaires*') && !Request::is('salaires/dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                <i data-lucide="coins" class="w-4 h-4 text-gray-400"></i> Salaires
                            </a>
                            <a href="{{ route('primes.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('primes*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                <i data-lucide="gift" class="w-4 h-4 text-gray-400"></i> Primes
                            </a>
                            <a href="{{ route('retenues.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('retenues*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                <i data-lucide="minus-circle" class="w-4 h-4 text-gray-400"></i> Retenues
                            </a>
                        </nav>
                    </div>
                    @endif
                    @if(session('user_role') === 'ROLE_EMPLOYEE')
                        <div class="px-3 mt-2">
                            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Mon Espace</h3>
                            <nav class="flex flex-col gap-0.5">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('dashboard/profile*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i> Mon Profil
                                </a>
                                <a href="{{ route('agenda.employee') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('agenda/employee*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i> Mon Agenda
                                </a>
                                <a href="{{ route('presences.employee') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('presences/employee') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                    <i data-lucide="timer" class="w-4 h-4 text-gray-400"></i> Mes Présences
                                </a>
                                <a href="{{ route('absences.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('absences*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                    <i data-lucide="palmtree" class="w-4 h-4 text-gray-400"></i> Mes Absences
                                </a>
                                <a href="{{ route('salaires.employee') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('salaires/employee*') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50' }} text-sm no-underline">
                                    <i data-lucide="coins" class="w-4 h-4 text-gray-400"></i> Mes Salaires
                                </a>
                            </nav>
                        </div>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Collapse/Logout Button -->
        <div class="p-4 border-t border-gray-100 flex flex-col gap-2">
            <a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 text-sm font-medium no-underline">
                <i data-lucide="log-out" class="w-4 h-4"></i> Déconnexion
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col h-full bg-transparent overflow-hidden relative">
        <!-- Decorative background blobs -->
        <div class="blob-1"></div>
        <div class="blob-2"></div>
        <div class="blob-3"></div>
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0 z-10">
            <!-- Search -->
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="global-search" name="global_search" placeholder="Rechercher ou aller à ..." class="w-full pl-9 pr-12 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                    <kbd class="hidden sm:inline-flex items-center gap-1 bg-white border border-gray-200 rounded px-1.5 text-[10px] font-medium text-gray-500">⌘K</kbd>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-4">
                <button class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary/90 transition-colors shadow-sm">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                </button>
                <div class="w-px h-6 bg-gray-200 mx-1"></div>
                <button class="text-gray-500 hover:text-gray-700"><i data-lucide="help-circle" class="w-5 h-5"></i></button>
                <button class="text-gray-500 hover:text-gray-700"><i data-lucide="settings" class="w-5 h-5"></i></button>
                
                <button class="text-gray-500 hover:text-gray-700 relative">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">1</span>
                </button>
                
                @if(session('user_role') === 'ROLE_ADMIN')
                @php $headerPendingCount = \App\Models\KeycloakUser::pending()->count(); @endphp
                <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 relative no-underline">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @if($headerPendingCount > 0)
                        <span id="notification-badge" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">{{ $headerPendingCount }}</span>
                    @endif
                </a>
                @else
                <button class="text-gray-500 hover:text-gray-700 relative">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                @endif

                <div class="ml-2 relative" id="user-menu-container">
                    <button id="user-menu-button" class="flex items-center focus:outline-none" aria-haspopup="true" aria-expanded="false">
                        @if(session('user_photo'))
                            <img src="{{ asset('storage/' . session('user_photo')) }}" alt="User Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200 hover:border-primary transition-colors cursor-pointer" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'Utilisateur') }}&background=random'">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'Utilisateur') }}&background=6366f1&color=ffffff&size=128" alt="User Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200 hover:border-primary transition-colors cursor-pointer">
                        @endif
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="user-dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-150 rounded-xl shadow-lg py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-50 text-left">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Mon Compte</p>
                            <p class="text-sm font-semibold text-gray-800 truncate mt-1">{{ session('user_name') ?? 'Utilisateur' }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ session('user_email') }}</p>
                            <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100 uppercase">
                                {{ session('user_role') ?? 'sans rôle' }}
                            </span>
                        </div>
                        @php
                            $selfProfileId = optional(\App\Models\Employe::where('keycloak_id', session('user_id'))->first())->id
                                ?? optional(\App\Models\Employe::where('email', session('user_email'))->first())->id;
                        @endphp
                        @if($selfProfileId)
                            <a href="{{ route('employees.profile.show', ['userId' => $selfProfileId]) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline font-medium transition-colors">
                                <i data-lucide="user" class="w-4 h-4"></i> Mon profil
                            </a>
                        @endif
                        <a href="{{ route('logout') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 no-underline font-medium transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-grow overflow-y-auto">
            @yield('content')
        </main>
    </div>
  @else
    <!-- Guest Layout -->
    <header id="header" class="header d-flex align-items-center sticky-top shadow-sm">
      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="/" class="logo d-flex align-items-center">
          <h1 class="sitename">Gestion Client</h1><span>.</span>
        </a>
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="/" class="{{ Request::is('/') ? 'active' : '' }}">Accueil</a></li>
            <li><a href="/login" class="btn btn-outline-success px-4 py-2 text-dark font-weight-bold" style="border-radius: 20px;">Se connecter</a></li>
          </ul>
        </nav>
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
  @endif

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

  <!-- Lucide script loader -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }

      // User Dropdown toggle
      const userMenuButton = document.getElementById('user-menu-button');
      const userDropdownMenu = document.getElementById('user-dropdown-menu');

      if (userMenuButton && userDropdownMenu) {
        userMenuButton.addEventListener('click', function(event) {
          event.stopPropagation();
          userDropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
          if (!userMenuButton.contains(event.target) && !userDropdownMenu.contains(event.target)) {
            userDropdownMenu.classList.add('hidden');
          }
        });
      }
    });
  </script>

  @yield('scripts')
</body>

</html>

