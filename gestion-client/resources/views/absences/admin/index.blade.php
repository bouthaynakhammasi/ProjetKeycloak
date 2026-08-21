@extends('layouts.app')

@section('title', 'Gestion des Absences')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
                <h1 id="page-title" class="mt-1 text-3xl font-bold tracking-tight text-white">Absences</h1>
                <p class="mt-1 text-sm font-medium text-white/80">Vue d'ensemble des demandes d'absence</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.href='{{ route('absences.export') }}'" class="inline-flex items-center gap-2 rounded-xl bg-[#7C3AED] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#6D28D9]">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Exporter
                </button>
            </div>
        </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 alert-success">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('absences.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <select name="employe_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tous les employés</option>
                    @foreach($employes as $employe)
                        <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>
                            {{ $employe->nom_complet }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select name="type_absence" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tous les types</option>
                    <option value="conge_paye" {{ request('type_absence') == 'conge_paye' ? 'selected' : '' }}>Congé payé</option>
                    @foreach($types as $type)
                        <option value="{{ strtolower(str_replace(' ', '_', $type)) }}" {{ request('type_absence') == strtolower(str_replace(' ', '_', $type)) ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select name="statut" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="approuve" {{ request('statut') == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Date début">
            </div>
            <div class="flex-1 min-w-[200px]">
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Date fin">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                Filtrer
            </button>
            <a href="{{ route('absences.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Réinitialiser
            </a>
        </form>
    </div>

    <!-- Tableau des absences -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Début</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($allAbsences as $absence)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
                                        {{ substr($absence->employe->nom, 0, 1) }}{{ substr($absence->employe->prenom, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $absence->employe->nom_complet }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->date_debut->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->date_fin->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($absence->statut->value === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                            @elseif($absence->statut->value === 'approved')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approuvé</span>
                            @elseif($absence->statut->value === 'rejected')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejeté</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('absences.show', $absence) }}" class="btn-view text-primary hover:text-primary/90 mr-3">Voir</a>
                            @if($absence->statut->value === 'pending')
                                <form method="POST" action="{{ route('absences.approve', $absence) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-approve text-green-600 hover:text-green-900 mr-3">Approuver</button>
                                </form>
                                <form method="POST" action="{{ route('absences.reject', $absence) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-reject text-red-600 hover:text-red-900">Refuser</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($allAbsences->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucune absence trouvée
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    </div>
    </div>
</div>
@endsection
