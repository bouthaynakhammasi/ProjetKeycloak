@extends('layouts.app')

@section('title', 'Mes Absences')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Mes Absences</h1>
                <p class="mt-1 text-sm font-medium text-white/80">Historique de vos demandes d'absence</p>
            </div>
            <a href="{{ route('absences.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#7C3AED] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#6D28D9] no-underline">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouvelle demande
            </a>
        </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 alert-success">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Section Soldes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Congés payés -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="umbrella" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-xs text-gray-400">Disponibles</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $employe->conges_payes ?? 25 }}</h3>
            <p class="text-sm text-gray-500">Congés payés (jours)</p>
        </div>

        <!-- Congés maladie -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="heart-pulse" class="w-5 h-5 text-red-600"></i>
                </div>
                <span class="text-xs text-gray-400">Illimité</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $employe->conges_maladie ?? 0 }}</h3>
            <p class="text-sm text-gray-500">Congés maladie (jours)</p>
        </div>

        <!-- Heures de récupération -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-xs text-gray-400">Disponibles</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $employe->heures_recuperation ?? 0 }}</h3>
            <p class="text-sm text-gray-500">Heures de récupération</p>
        </div>
    </div>

    <!-- Section Historique -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" data-testid="absence-history">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Début</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @if($absences->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucune absence trouvée
                        </td>
                    </tr>
                @else
                    @foreach($absences as $absence)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->date_debut->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->date_fin->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($absence->statut === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                @elseif($absence->statut === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approuvé</span>
                                @elseif($absence->statut === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejeté</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('absences.show', $absence) }}" class="btn-view text-primary hover:text-primary/90">Voir</a>
                                @if($absence->statut === 'pending')
                                    <a href="{{ route('absences.edit', $absence) }}" class="btn-edit text-primary hover:text-primary/90 ml-3">Modifier</a>
                                    <form method="POST" action="{{ route('absences.destroy', $absence) }}" class="inline ml-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete text-red-600 hover:text-red-900">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    </div>
    </div>
</div>
@endsection
