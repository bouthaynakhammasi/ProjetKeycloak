<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Mailbox - Messagerie sécurisée pour votre équipe">
    <meta name="author" content="">
    <title>Mailbox - Messagerie Sécurisée</title>
    
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="{{ asset('bootstrap-creative/favicon.ico') }}">
    
    <!-- Bootstrap Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet">
    
    <!-- Bootstrap Creative CSS -->
    <link href="{{ asset('bootstrap-creative/css/styles.css') }}" rel="stylesheet">
    
    <style>
        /* Custom styles for Mailbox */
        .feature-icon {
            font-size: 3rem;
            color: #4f46e5;
        }
        .btn-primary-custom {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .btn-primary-custom:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        .masthead {
            background: linear-gradient(to bottom, rgba(79, 70, 229, 0.8) 0%, rgba(67, 56, 202, 0.8) 100%), url('{{ asset('bootstrap-creative/img/bg-masthead.jpg') }}');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="/">Mailbox</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fonctionnalités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    @if(session()->has('keycloak_user'))
                        <li class="nav-item">
                            <form method="POST" action="{{ route('keycloak.logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-danger text-white px-4 ms-2">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary-custom text-white px-4 ms-2" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Se connecter
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Masthead-->
    <header class="masthead">
        <div class="container px-4 px-lg-5 h-100">
            <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-8 align-self-end">
                    <h1 class="text-white font-weight-bold">Gérez vos ressources humaines simplement</h1>
                    <hr class="divider" />
                </div>
                <div class="col-lg-8 align-self-baseline">
                    <p class="text-white-75 mb-5">Une messagerie complète et sécurisée pour votre équipe : échangez, organisez et communiquez simplement.</p>
                    <a class="btn btn-primary btn-xl" href="{{ route('login') }}">
                        <i class="bi bi-person-circle"></i> Commencer
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section-->
    <section class="page-section" id="features">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5">
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Boîte de réception</h3>
                        <p class="text-muted mb-0">Recevez et organisez tous vos messages dans une interface intuitive.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-send feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Envoi rapide</h3>
                        <p class="text-muted mb-0">Envoyez des messages instantanés à vos collègues.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-reply feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Réponses</h3>
                        <p class="text-muted mb-0">Répondez facilement aux messages avec citation automatique.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-star feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Favoris</h3>
                        <p class="text-muted mb-0">Marquez vos messages importants pour les retrouver facilement.</p>
                    </div>
                </div>
            </div>
            
            <!-- Additional Features Row -->
            <div class="row gx-4 gx-lg-5 mt-5">
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-file-earmark feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Brouillons</h3>
                        <p class="text-muted mb-0">Sauvegardez vos messages en cours et reprenez-les plus tard.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-shield-check feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Sécurité</h3>
                        <p class="text-muted mb-0">Authentification SSO Keycloak pour une sécurité renforcée.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="mt-5">
                        <div class="mb-3">
                            <i class="bi bi-phone feature-icon"></i>
                        </div>
                        <h3 class="h4 mb-2">Responsive</h3>
                        <p class="text-muted mb-0">Interface accessible depuis n'importe quel appareil.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section-->
    <section class="page-section bg-dark text-white">
        <div class="container px-4 px-lg-5 text-center">
            <h2 class="page-section-heading text-center text-uppercase text-white mb-4">Prêt à simplifier votre gestion RH ?</h2>
            <a class="btn btn-light btn-xl" href="{{ route('login') }}">
                <i class="bi bi-person-circle"></i> Se connecter maintenant
            </a>
        </div>
    </section>

    <!-- Contact Section-->
    <section class="page-section" id="contact">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-lg-8 col-xl-6 text-center">
                    <h2 class="mt-0">Besoin d'aide ?</h2>
                    <hr class="divider" />
                    <p class="text-muted mb-5">Notre équipe est à votre disposition pour répondre à toutes vos questions.</p>
                </div>
            </div>
            <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-envelope fs-1 mb-3 text-muted"></i>
                        <div>support@gestionclient.com</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-telephone fs-1 mb-3 text-muted"></i>
                        <div>+33 1 23 45 67 89</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer-->
    <footer class="bg-light py-5">
        <div class="container px-4 px-lg-5">
            <div class="small text-center text-muted">
                Copyright &copy; Mailbox 2026. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Bootstrap Creative JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('bootstrap-creative/js/scripts.js') }}"></script>
</body>
</html>