@extends('layouts.app')

@section('title', 'Gestion des Primes')

@section('content')
<div class="p-6 animate-fadeIn">
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Gestion des Primes</h1>
        <p class="mt-1 text-sm font-medium text-white/80">Gérez les primes et bonus des employés</p>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('primes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <label class="block text-xs font-medium text-gray-500 mb-1">Type de prime</label>
                <input type="text" name="type" value="{{ request('type') }}" placeholder="Ex: Prime rendement" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('primes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des primes -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Liste des Primes</h2>
            <a href="{{ route('primes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouvelle Prime
            </a>
        </div>

        @if($primes->isEmpty())
            <div class="p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="gift" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-500">Aucune prime trouvée</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employé</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type de Prime</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($primes as $prime)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($prime->employe->nom_complet) }}&background=10b981&color=fff&size=64"
                                         class="w-8 h-8 rounded-full object-cover" alt="{{ $prime->employe->nom_complet }}">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $prime->employe->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">{{ $prime->employe->poste }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                                    {{ $prime->type_prime }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-green-600 text-right font-bold">
                                +{{ number_format($prime->montant, 2) }} DT
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $prime->date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 justify-center">
                                    <a href="{{ route('primes.show', $prime) }}" class="text-gray-400 hover:text-primary transition-colors p-1" title="Voir détails">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('primes.edit', $prime) }}" class="text-gray-400 hover:text-primary transition-colors p-1" title="Modifier">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('primes.destroy', $prime) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="Supprimer"
                                            onclick="return confirm('Supprimer cette prime ?')">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
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
