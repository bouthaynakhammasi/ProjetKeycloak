@extends('layouts.app')

@section('title', 'Gestion des Salaires')

@section('content')
<div class="p-6 animate-fadeIn">
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Gestion des Salaires</h1>
        <p class="mt-1 text-sm font-medium text-white/80">Gérez les salaires, primes et retenues des employés</p>
    </div>

    @if(session('user_role') === 'ROLE_ADMIN')
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('salaires.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Employé</label>
                <select name="employe_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tous les employés</option>
                    @foreach($employes as $employe)
                        <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>
                            {{ $employe->nom_complet }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Mois</label>
                <select name="mois" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tous les mois</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('mois') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Année</label>
                <select name="annee" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Toutes les années</option>
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="paye" {{ request('statut') === 'paye' ? 'selected' : '' }}>Payé</option>
                    <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('salaires.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>
    @endif

    <!-- Liste des salaires -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Liste des Salaires</h2>
            @if(session('user_role') === 'ROLE_ADMIN')
            <a href="{{ route('salaires.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouveau Salaire
            </a>
            @endif
        </div>

        @if($salaires->isEmpty())
            <div class="p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="file-text" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-500">Aucun salaire trouvé</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Période</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Salaire Base</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Prime</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Retenue</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Salaire Net</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($salaires as $salaire)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($salaire->employe->nom_complet) }}&background=10b981&color=fff&size=64"
                                         class="w-8 h-8 rounded-full object-cover" alt="{{ $salaire->employe->nom_complet }}">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $salaire->employe->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">{{ $salaire->employe->poste }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $salaire->nom_mois }} {{ $salaire->annee }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                                {{ number_format($salaire->salaire_base, 2) }} DT
                            </td>
                            <td class="px-4 py-3 text-sm text-green-600 text-right font-medium">
                                +{{ number_format($salaire->prime, 2) }} DT
                            </td>
                            <td class="px-4 py-3 text-sm text-red-600 text-right font-medium">
                                -{{ number_format($salaire->retenue, 2) }} DT
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-bold">
                                {{ number_format($salaire->salaire_net, 2) }} DT
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($salaire->statut_paiement === 'paye')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i> Payé
                                    </span>
                                @elseif($salaire->statut_paiement === 'en_attente')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-semibold">
                                        <i data-lucide="clock" class="w-3 h-3"></i> En attente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-full text-xs font-semibold">
                                        <i data-lucide="x-circle" class="w-3 h-3"></i> Annulé
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 justify-center">
                                    <a href="{{ route('salaires.show', $salaire) }}" class="text-gray-400 hover:text-primary transition-colors p-1" title="Voir détails">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if(session('user_role') === 'ROLE_ADMIN')
                                        @if($salaire->statut_paiement === 'en_attente')
                                        <form method="POST" action="{{ route('salaires.marquer-paye', $salaire) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-green-600 transition-colors p-1" title="Marquer comme payé">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <a href="{{ route('salaires.edit', $salaire) }}" class="text-gray-400 hover:text-primary transition-colors p-1" title="Modifier">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </a>
                                        <form method="POST" action="{{ route('salaires.destroy', $salaire) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="Supprimer"
                                                onclick="return confirm('Supprimer ce salaire ?')">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
