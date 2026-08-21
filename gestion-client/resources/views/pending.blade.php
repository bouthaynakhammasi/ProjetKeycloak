<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compte en attente - Gestion Client</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#7C3AED',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans flex items-center justify-center min-h-screen relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-purple-200/50 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-indigo-200/50 blur-3xl"></div>

    <div class="relative z-10 w-full max-w-md p-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl p-8 flex flex-col items-center text-center">
            
            <!-- Icon -->
            <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <i data-lucide="clock" class="w-8 h-8"></i>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-slate-900 mb-3">Compte en attente d'activation</h1>
            
            <!-- Description -->
            <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                Bonjour <span class="font-semibold text-slate-800" data-testid="user-name">{{ session('user_name') }}</span>. Votre inscription a bien été prise en compte. Un administrateur doit valider et configurer votre rôle avant que vous ne puissiez accéder à l'application.
            </p>

            <div class="w-full bg-slate-50 border border-slate-100 rounded-xl p-4 mb-6 flex flex-col items-start gap-2 text-left">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i> Détails du compte
                </div>
                <div class="text-sm">
                    <span class="text-slate-500">Email :</span>
                    <span class="font-medium text-slate-800" data-testid="user-email">{{ session('user_email') }}</span>
                </div>
                <div class="text-sm">
                    <span class="text-slate-500">Statut :</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                        En attente
                    </span>
                </div>
            </div>

            <!-- Call to action -->
            <div class="flex flex-col gap-3 w-full">
                <a href="{{ route('login') }}" class="w-full bg-primary hover:bg-primary/90 text-white font-medium py-2.5 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i> Actualiser ma session
                </a>
                
                <a href="{{ route('logout') }}" class="w-full bg-white hover:bg-slate-50 text-red-600 border border-slate-200 font-medium py-2.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Se déconnecter
                </a>
            </div>
        </div>
    </div>

    <script>
        // Initialize lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
