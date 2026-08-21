<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Gestion Client</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            color: #6b7280;
        }

        .btn-sso {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px 20px;
            background: #7C3AED;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-sso:hover {
            background: #6d28d9;
            color: white;
            text-decoration: none;
        }

        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .trust-info {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Connexion</h1>
            <p>Connectez-vous via Keycloak SSO</p>
        </div>

        @if(session('error'))
            <div class="alert-err">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('keycloak.redirect') }}" class="btn-sso" id="ssoBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
            </svg>
            Se connecter avec Keycloak
        </a>

        <div class="trust-info">
            Sécurisé par Keycloak · OAuth 2.0 · HTTPS
        </div>
    </div>

    <script>
        document.getElementById('ssoBtn').addEventListener('click', function() {
            this.style.opacity = '0.7';
            this.innerHTML = 'Redirection...';
        });
    </script>
</body>
</html>
