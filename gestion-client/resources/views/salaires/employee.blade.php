@extends('layouts.app')

@section('title', 'Mes Salaires')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/90">Mon Espace</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Mes Salaires</h1>
                <p class="mt-1 text-sm font-medium text-white/80">Historique de mes fiches de paie</p>
            </div>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            {{-- Total --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total fiches</p>
                <p class="text-3xl font-bold text-gray-900">{{ $salaires->count() }}</p>
            </div>

            {{-- Total gagné --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <p class="text-xs font-medium text-green-600 uppercase tracking-wider mb-1">Total gagné</p>
                <p class="text-3xl font-bold text-green-700">{{ number_format($salaires->sum('salaire_net'), 2, ',', ' ') }} €</p>
            </div>

            {{-- Moyenne --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                <p class="text-xs font-medium text-blue-600 uppercase tracking-wider mb-1">Moyenne mensuelle</p>
                <p class="text-3xl font-bold text-blue-700">{{ $salaires->count() > 0 ? number_format($salaires->sum('salaire_net') / $salaires->count(), 2, ',', ' ') : '0,00' }} €</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Salaire brut</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Déductions</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Salaire net</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($salaires as $salaire)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::create()->month($salaire->mois)->locale('fr')->monthName }} {{ $salaire->annee }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ number_format($salaire->salaire_brut, 2, ',', ' ') }} €</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ number_format($salaire->deductions, 2, ',', ' ') }} €</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($salaire->salaire_net, 2, ',', ' ') }} €</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border
                                {{ $salaire->statut_paiement === 'paye' ? 'bg-green-50 text-green-700 border-green-200' : 
                                   ($salaire->statut_paiement === 'en_attente' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-700 border-red-200') }}">
                                {{ $salaire->statut_paiement === 'paye' ? 'Payé' : ($salaire->statut_paiement === 'en_attente' ? 'En attente' : 'Annulé') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('salaires.show', $salaire) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20 transition-colors">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i data-lucide="coins" class="w-12 h-12 stroke-1 mb-2"></i>
                                <p class="text-sm font-medium">Aucune fiche de paie disponible</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection