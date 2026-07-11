<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Gestion Client</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Roboto', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #ffffff;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80') center/cover;
            position: relative;
        }

        .login-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        /* Section gauche - Image et texte */
        .login-left {
            display: none;
        }

        /* Section droite - Formulaire */
        .login-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: transparent;
            min-height: 100vh;
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .login-card-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-card-subtitle {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-group input:focus {
            outline: none;
            background: white;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-check {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .form-check input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
            accent-color: #10b981;
        }

        .form-check label {
            font-size: 14px;
            color: #666;
            margin: 0;
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer-text {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .login-footer-text strong {
            color: #10b981;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
            }

            .login-left {
                min-height: 40vh;
                padding: 30px 20px;
            }

            .login-left h1 {
                font-size: 28px;
            }

            .login-right {
                min-height: 60vh;
                padding: 30px 20px;
                justify-content: flex-start;
                padding-top: 40px;
            }

            .login-card {
                max-width: 100%;
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .login-left {
                min-height: 35vh;
                padding: 20px;
            }

            .login-left h1 {
                font-size: 24px;
                margin-bottom: 15px;
            }

            .login-left p {
                font-size: 14px;
            }

            .login-logo {
                width: 70px;
                height: 70px;
            }

            .login-logo svg {
                width: 40px;
                height: 40px;
            }

            .login-right {
                min-height: 65vh;
                padding: 20px;
                padding-top: 30px;
            }

            .login-card {
                padding: 30px 20px;
            }

            .login-card-title {
                font-size: 24px;
            }

            .form-group input {
                padding: 10px 12px;
                font-size: 16px;
            }

            .btn-login {
                padding: 11px;
                font-size: 15px;
            }
        }

        /* Toggle password visibility */
        .toggle-password {
            position: relative;
        }

        .toggle-password input {
            padding-right: 40px;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password-btn:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Partie gauche -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="login-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h1>Bienvenue dans Gestion Client</h1>
                <p>Gérez vos clients et employés avec une authentification sécurisée et simplifiée.</p>
            </div>
        </div>

        <!-- Partie droite -->
        <div class="login-right">
            <div class="login-card">
                <h2 class="login-card-title">Connexion</h2>
                <p class="login-card-subtitle">Accédez à votre compte</p>

                @if (session('error'))
                    <div class="alert alert-danger text-center mb-3" style="font-size: 14px; border-radius: 8px;">
                        {{ session('error') }}
                    </div>
                @endif

                <form id="loginForm">
                    <!-- Champ Email -->
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email" placeholder="vous@example.com" readonly>
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="form-group toggle-password">
                        <label for="password">Mot de passe</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" placeholder="••••••••" readonly>
                            <button type="button" class="toggle-password-btn" id="togglePassword" onclick="togglePasswordVisibility()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox Afficher mot de passe -->
                    <div class="form-check">
                        <input type="checkbox" id="showPassword" class="form-check-input" onchange="togglePasswordVisibility()">
                        <label class="form-check-label" for="showPassword">Afficher le mot de passe</label>
                    </div>

                    <!-- Bouton Se connecter → redirige vers Keycloak SSO -->
                    <a href="{{ route('keycloak.redirect') }}" class="btn-login">Se connecter avec Keycloak</a>

                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const showPasswordCheckbox = document.getElementById('showPassword');
            const toggleBtn = document.getElementById('togglePassword');

            if (showPasswordCheckbox.checked) {
                passwordInput.type = 'text';
                toggleBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                passwordInput.type = 'password';
                toggleBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        }

        // Empêcher la soumission du formulaire
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
        });
    </script>
</body>
</html>
