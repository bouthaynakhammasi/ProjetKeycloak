@extends('layouts.app')

@section('title', 'Notifications - Admin')

@section('content')
<div class="p-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-0.5">Nouvelles demandes d'accès et comptes en attente</p>
        </div>
    </div>

    {{-- Comptes Keycloak en attente --}}
    <section class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                <i data-lucide="user-clock" class="w-4 h-4 text-amber-600"></i>
            </div>
            <h2 class="text-base font-semibold text-gray-900">Comptes Keycloak en attente</h2>
            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold bg-amber-500 text-white rounded-full">{{ $pendingUsers->count() }}</span>
        </div>

        @if($pendingUsers->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-8 text-center">
                <p class="text-sm text-gray-400">Aucune demande d'accès en attente.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @foreach($pendingUsers as $user)
                        <div class="flex items-center gap-4 p-4 hover:bg-gray-50/50 transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=f59e0b&color=fff&size=64"
                                 class="w-10 h-10 rounded-full object-cover shrink-0" alt="{{ $user->name }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Demande reçue le {{ $user->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary border border-primary/20 rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors no-underline">
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i> Traiter
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- Comptes détectés sans rôle (comptes_en_attente) --}}
    <section>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-4 h-4 text-blue-600"></i>
            </div>
            <h2 class="text-base font-semibold text-gray-900">Connexions sans rôle détectées</h2>
            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold bg-blue-500 text-white rounded-full">{{ $comptesEnAttente->count() }}</span>
        </div>

        @if($comptesEnAttente->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-8 text-center">
                <p class="text-sm text-gray-400">Aucune connexion sans rôle.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @foreach($comptesEnAttente as $compte)
                        <div class="flex items-center gap-4 p-4 hover:bg-gray-50/50 transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(trim($compte->prenom . ' ' . $compte->nom)) }}&background=3b82f6&color=fff&size=64"
                                 class="w-10 h-10 rounded-full object-cover shrink-0" alt="{{ $compte->nom }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ trim($compte->prenom . ' ' . $compte->nom) }}</p>
                                <p class="text-xs text-gray-500">{{ $compte->email }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Détecté le {{ $compte->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-medium">
                                Sans rôle assigné
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

</div>
@endsection
