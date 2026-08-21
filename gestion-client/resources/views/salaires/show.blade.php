@extends('layouts.app')

@section('title', 'Détails du Salaire')

@section('content')
<div class="p-6 animate-fadeIn">
    <div class="mb-6">
        <a href="{{ route('salaires.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Détails du Salaire</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $salaire->employe->nom_complet }} - {{ $salaire->nom_mois }} {{ $salaire->annee }}</p>
                </div>
                @if(session('user_role') === 'ROLE_ADMIN')
                <div class="flex gap-2">
                    <a href="{{ route('salaires.edit', $salaire) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                        Modifier
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations employé -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Informations Employé</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Nom complet</span>
                            <span class="text-sm font-medium text-gray-900">{{ $salaire->employe->nom_complet }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Poste</span>
                            <span class="text-sm font-medium text-gray-900">{{ $salaire->employe->poste }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Département</span>
                            <span class="text-sm font-medium text-gray-900">{{ $salaire->employe->departement }}</span>
                        </div>
                    </div>
                </div>

                <!-- Période -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Période</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Mois</span>
                            <span class="text-sm font-medium text-gray-900">{{ $salaire->nom_mois }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Année</span>
                            <span class="text-sm font-medium text-gray-900">{{ $salaire->annee }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Statut</span>
                            @if($salaire->statut_paiement === 'paye')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                                    Payé
                                </span>
                            @elseif($salaire->statut_paiement === 'en_attente')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-semibold">
                                    En attente
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-full text-xs font-semibold">
                                    Annulé
                                </span>
                            @endif
                        </div>
                        @if($salaire->date_paiement)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Date de paiement</span>
                            <span class="text-sm font-medium text-gray-900">{{ $salaire->date_paiement->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Détails salaire -->
                <div class="md:col-span-2 bg-gray-50 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Détails du Salaire</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Salaire Base</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($salaire->salaire_base, 2) }} DT</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Prime</p>
                            <p class="text-lg font-bold text-green-600">+{{ number_format($salaire->prime, 2) }} DT</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <p class="text-xs text-gray-500 mb-1">Retenue</p>
                            <p class="text-lg font-bold text-red-600">-{{ number_format($salaire->retenue, 2) }} DT</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border-2 border-primary">
                            <p class="text-xs text-gray-500 mb-1">Salaire Net</p>
                            <p class="text-lg font-bold text-primary">{{ number_format($salaire->salaire_net, 2) }} DT</p>
                        </div>
                    </div>
                </div>

                @if($salaire->notes)
                <div class="md:col-span-2 bg-gray-50 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Notes</h3>
                    <p class="text-sm text-gray-600">{{ $salaire->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
