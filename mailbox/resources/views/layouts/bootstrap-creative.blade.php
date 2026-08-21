<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Mailbox - Messagerie sécurisée pour votre équipe">
    <meta name="author" content="">
    <title>@yield('title', 'Mailbox')</title>
    
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="{{ asset('bootstrap-creative/favicon.ico') }}">
    
    <!-- Bootstrap Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet">
    
    <!-- Bootstrap Creative CSS -->
    <link href="{{ asset('bootstrap-creative/css/styles.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="{{ route('mailbox.index') }}">Mailbox</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto my-2 my-lg-0">
                    @if(session()->has('keycloak_user'))
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->get('folder') ?? 'inbox') === 'inbox' ? 'active' : '' }}" href="{{ route('mailbox.index', ['folder' => 'inbox']) }}">
                                <i class="bi bi-inbox"></i> Boîte de réception
                                @if(($unreadCount ?? 0) > 0)
                                    <span class="badge bg-primary">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->get('folder') ?? 'inbox') === 'sent' ? 'active' : '' }}" href="{{ route('mailbox.index', ['folder' => 'sent']) }}">
                                <i class="bi bi-send"></i> Envoyés
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->get('folder') ?? 'inbox') === 'drafts' ? 'active' : '' }}" href="{{ route('mailbox.index', ['folder' => 'drafts']) }}">
                                <i class="bi bi-file-earmark"></i> Brouillons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->get('folder') ?? 'inbox') === 'trash' ? 'active' : '' }}" href="{{ route('mailbox.index', ['folder' => 'trash']) }}">
                                <i class="bi bi-trash"></i> Corbeille
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('keycloak.logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">Déconnexion</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('keycloak.redirect') }}">Connexion</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Contenu principal -->
    @yield('content')

    <!-- Bootstrap Creative JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('bootstrap-creative/js/scripts.js') }}"></script>
    
    @stack('scripts')
</body>
</html>