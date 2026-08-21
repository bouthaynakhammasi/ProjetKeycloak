<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mailbox') — Messagerie SSO</title>
    <meta name="description" content="Interface de messagerie sécurisée avec authentification SSO Keycloak.">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                },
            },
        }
    </script>

    {{-- Google Fonts : Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Scrollbar fine et discrète */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Transition universelle douce */
        *, *::before, *::after { box-sizing: border-box; }

        .sidebar-link { transition: background-color 0.15s ease, color 0.15s ease; }
        .message-row  { transition: background-color 0.12s ease; }
        .star-btn      { transition: color 0.15s ease, transform 0.15s ease; }
        .star-btn:hover { transform: scale(1.2); }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeSlideIn 0.22s ease both; }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.96) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal-in { animation: modalIn 0.2s ease both; }
    </style>

    @stack('head')
</head>
<body class="h-full bg-gray-50 font-sans text-gray-800 antialiased">

{{-- ═══════════════════════════ LAYOUT PRINCIPAL ═══════════════════════════ --}}
<div class="flex h-screen overflow-hidden">

    {{-- ──────────── SIDEBAR ──────────── --}}
    <aside class="flex flex-col w-44 shrink-0 bg-white border-r border-gray-100 px-3 py-5 gap-y-4">

        {{-- Logo / Titre --}}
        <div class="px-2 mb-1">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-accent-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-800 tracking-tight">Mailbox</span>
            </div>
        </div>

        {{-- Bouton Nouveau message --}}
        <button
            onclick="openComposeModal()"
            class="flex items-center justify-center gap-2 w-full py-2 px-3 rounded-full bg-gray-900 text-white text-sm font-medium hover:bg-gray-700 active:scale-95 transition-all duration-150 shadow-sm"
            id="btn-new-message"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Nouveau</span>
        </button>

        {{-- Navigation --}}
        <nav class="flex flex-col gap-y-0.5">
            @php
                $navItems = [
                    ['folder' => 'inbox',  'label' => 'Boîte de réception', 'icon' => 'inbox'],
                    ['folder' => 'sent',   'label' => 'Envoyés',            'icon' => 'sent'],
                    ['folder' => 'drafts', 'label' => 'Brouillons',         'icon' => 'draft'],
                    ['folder' => 'trash',  'label' => 'Corbeille',          'icon' => 'trash'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php
                    $isActive = ($activeFolder ?? 'inbox') === $item['folder'];
                @endphp
                <a
                    href="{{ route('mailbox.index', ['folder' => $item['folder']]) }}"
                    class="sidebar-link flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium
                        {{ $isActive
                            ? 'bg-accent-50 text-accent-700'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    {{-- Icônes --}}
                    @if($item['icon'] === 'inbox')
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    @elseif($item['icon'] === 'sent')
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    @elseif($item['icon'] === 'draft')
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    @endif

                    <span class="flex-1 leading-tight">{{ $item['label'] }}</span>

                    {{-- Badge non-lus (uniquement inbox) --}}
                    @if($item['folder'] === 'inbox' && ($unreadCount ?? 0) > 0)
                        <span class="shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-accent-600 text-white text-[10px] font-semibold">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Déconnexion --}}
        <form method="POST" action="{{ route('keycloak.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link flex items-center gap-2.5 w-full px-2.5 py-2 rounded-lg text-sm text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Déconnexion</span>
            </button>
        </form>
    </aside>

    {{-- ──────────── COLONNE DROITE (topbar + contenu) ──────────── --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="flex items-center gap-3 px-5 py-3 bg-white border-b border-gray-100 shrink-0">

            {{-- Barre de recherche --}}
            <form method="GET" action="{{ route('mailbox.index') }}" class="flex-1 max-w-xl" role="search">
                <input type="hidden" name="folder" value="{{ $activeFolder ?? 'inbox' }}">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Rechercher dans les messages"
                        class="w-full pl-9 pr-4 py-2 bg-gray-100 border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-accent-300 focus:ring-2 focus:ring-accent-100 transition-all duration-150"
                        id="search-input"
                    >
                </div>
            </form>

            <div class="flex items-center gap-3 ml-auto shrink-0">

                {{-- Avatar utilisateur --}}
                @php
                    $kUser = session('keycloak_user', []);
                    $fullName = $kUser['name'] ?? ($kUser['preferred_username'] ?? 'Utilisateur');
                    $words = array_filter(explode(' ', trim($fullName)));
                    $initials = count($words) >= 2
                        ? strtoupper(mb_substr($words[array_key_first($words)], 0, 1) . mb_substr(end($words), 0, 1))
                        : strtoupper(mb_substr($fullName, 0, 2));
                @endphp
                <div
                    class="w-8 h-8 rounded-full bg-accent-600 flex items-center justify-center text-white text-xs font-semibold cursor-default select-none"
                    title="{{ $fullName }}"
                    id="user-avatar"
                >
                    {{ $initials }}
                </div>
            </div>
        </header>

        {{-- Notifications flash --}}
        @if(session('success'))
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade-in flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-5 mt-3 px-4 py-2.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm animate-fade-in flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Contenu principal --}}
        <main class="flex-1 overflow-y-auto">
            {{-- Messages flash (succès/erreurs) --}}
            @if (session('success'))
                <div class="mx-5 mt-5 bg-green-50 border border-green-200 text-green-700 rounded-lg p-4">
                    <div class="text-sm">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-5 mt-5 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
                    <div class="text-sm">{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- ═══════════════ MODALE DE COMPOSITION ═══════════════ --}}
<div
    id="compose-modal"
    class="fixed inset-0 z-50 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="compose-modal-title"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeComposeModal()"></div>

    {{-- Panneau --}}
    <div class="absolute bottom-4 right-6 w-full max-w-lg animate-modal-in">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">

            {{-- En-tête modale --}}
            <div class="flex items-center justify-between px-5 py-3.5 bg-gray-900 text-white">
                <h2 id="compose-modal-title" class="text-sm font-semibold">Nouveau message</h2>
                <div class="flex items-center gap-2">
                    <button onclick="closeComposeModal()" class="p-1 rounded hover:bg-white/10 transition-colors" title="Fermer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Messages d'erreur --}}
            @if ($errors->any())
                <div class="mx-5 mt-3 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
                    <div class="font-medium text-sm mb-2">Erreurs de validation :</div>
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Message de succès --}}
            @if (session('success'))
                <div class="mx-5 mt-3 bg-green-50 border border-green-200 text-green-700 rounded-lg p-4">
                    <div class="text-sm">{{ session('success') }}</div>
                </div>
            @endif

            {{-- Formulaire --}}
            <form
                method="POST"
                action="{{ route('mailbox.store') }}"
                id="compose-form"
                class="flex flex-col"
            >
                @csrf

                {{-- Champs cachés pré-remplis avec l'utilisateur connecté (expéditeur) --}}
                @php $kUser = session('keycloak_user', []); @endphp
                <input type="hidden" name="sender_name"  value="{{ $kUser['name'] ?? ($kUser['preferred_username'] ?? 'Utilisateur') }}">
                <input type="hidden" name="sender_email" value="{{ $kUser['email'] ?? 'noreply@mailbox.local' }}">

                <div class="divide-y divide-gray-100">
                    {{-- De --}}
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <span class="text-xs text-gray-400 w-6 shrink-0">De</span>
                        <span class="text-sm text-gray-700">{{ $kUser['name'] ?? ($kUser['preferred_username'] ?? 'Utilisateur') }}</span>
                    </div>

                    {{-- À (destinataire) --}}
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <label for="compose-recipient" class="text-xs text-gray-400 w-6 shrink-0">À</label>
                        <input
                            type="email"
                            name="recipient_email"
                            id="compose-recipient"
                            required
                            placeholder="destinataire@exemple.com"
                            class="flex-1 text-sm text-gray-800 bg-transparent border-none outline-none placeholder-gray-300"
                        >
                    </div>

                    {{-- Objet --}}
                    <div class="px-5 py-2.5 flex items-center gap-3">
                        <label for="compose-subject" class="text-xs text-gray-400 w-6 shrink-0">Objet</label>
                        <input
                            type="text"
                            name="subject"
                            id="compose-subject"
                            required
                            placeholder="Objet du message"
                            class="flex-1 text-sm text-gray-800 bg-transparent border-none outline-none placeholder-gray-300"
                        >
                    </div>
                </div>

                {{-- Corps du message --}}
                <textarea
                    name="body"
                    id="compose-body"
                    required
                    rows="10"
                    placeholder="Rédigez votre message ici…"
                    class="w-full px-5 py-4 text-sm text-gray-700 bg-transparent border-none resize-none outline-none placeholder-gray-300"
                ></textarea>

                {{-- Actions --}}
                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            name="save_draft"
                            value="0"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-accent-600 text-white text-sm font-medium hover:bg-accent-700 active:scale-95 transition-all"
                            id="btn-send"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Envoyer
                        </button>
                        <button
                            type="submit"
                            name="save_draft"
                            value="1"
                            class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-200 active:scale-95 transition-all"
                            id="btn-save-draft"
                        >
                            Brouillon
                        </button>
                    </div>
                    <button
                        type="button"
                        onclick="closeComposeModal()"
                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        title="Supprimer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openComposeModal(recipientEmail = null) {
        const modal = document.getElementById('compose-modal');
        modal.classList.remove('hidden');

        // Pré-remplir l'email du destinataire si fourni (mode réponse)
        if (recipientEmail) {
            const input = document.getElementById('compose-recipient');
            input.value = recipientEmail;
            input.readOnly = true; // Utiliser readonly au lieu de disabled
            input.classList.add('bg-gray-50', 'cursor-not-allowed');
        } else {
            const input = document.getElementById('compose-recipient');
            input.readOnly = false;
            input.classList.remove('bg-gray-50', 'cursor-not-allowed');
        }

        setTimeout(() => document.getElementById('compose-subject')?.focus(), 80);
    }

    function closeComposeModal() {
        document.getElementById('compose-modal').classList.add('hidden');
        document.getElementById('compose-form').reset();
        // Réactiver l'input destinataire
        const input = document.getElementById('compose-recipient');
        if (input) {
            input.readOnly = false;
            input.classList.remove('bg-gray-50', 'cursor-not-allowed');
        }
    }

    // Fermer avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeComposeModal();
    });

    // Ouvrir automatiquement si on revient d'une page /mailbox/compose ou /mailbox/{id}/reply
    @if(Request::routeIs('mailbox.compose') || Request::routeIs('mailbox.reply'))
        document.addEventListener('DOMContentLoaded', () => openComposeModal());
    @endif
</script>

@stack('scripts')
</body>
</html>
