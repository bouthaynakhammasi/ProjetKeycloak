@extends('layouts.app')

@section('title', 'Détails du compte - Admin')

@section('content')
<div class="p-6 animate-fadeIn">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=10b981&color=fff&size=128"
                     class="w-20 h-20 rounded-full object-cover" alt="{{ $user->name }}">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        @if($user->role === 'ROLE_ADMIN')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary/10 text-primary border border-primary/20 rounded-full text-xs font-semibold">
                                <i data-lucide="shield" class="w-3 h-3"></i> Administrateur
                            </span>
                        @elseif($user->role === 'ROLE_CLIENT')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-semibold">
                                <i data-lucide="briefcase" class="w-3 h-3"></i> Client
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                                <i data-lucide="user" class="w-3 h-3"></i> Employé
                            </span>
                        @endif
                        @if($user->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Actif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 border border-gray-200 rounded-full text-xs font-semibold">
                                <i data-lucide="x-circle" class="w-3 h-3"></i> Inactif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations du compte</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Keycloak ID</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $user->keycloak_id }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email</label>
                    <p class="text-sm text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nom complet</label>
                    <p class="text-sm text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Statut</label>
                    <p class="text-sm text-gray-900">{{ $user->status }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Rôle</label>
                    <p class="text-sm text-gray-900">{{ $user->role }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date de création</label>
                    <p class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($user->activated_at)
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date d'activation</label>
                    <p class="text-sm text-gray-900">{{ $user->activated_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            @if($keycloakDetails)
            <h2 class="text-lg font-semibold text-gray-900 mt-8 mb-4">Détails Keycloak</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Username</label>
                    <p class="text-sm text-gray-900">{{ $keycloakDetails['username'] ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email vérifié</label>
                    <p class="text-sm text-gray-900">{{ $keycloakDetails['emailVerified'] ? 'Oui' : 'Non' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Compte activé</label>
                    <p class="text-sm text-gray-900">{{ $keycloakDetails['enabled'] ? 'Oui' : 'Non' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Dernière connexion</label>
                    <p class="text-sm text-gray-900">{{ isset($keycloakDetails['attributes']) && isset($keycloakDetails['attributes']['lastLogin']) ? $keycloakDetails['attributes']['lastLogin'][0] : '-' }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
