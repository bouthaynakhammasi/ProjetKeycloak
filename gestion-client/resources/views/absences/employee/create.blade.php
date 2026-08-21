@extends('layouts.app')

@section('title', 'Nouvelle Demande d\'Absence')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('absences.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/90 mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour à la liste
            </a>
            <h1 class="text-3xl font-bold text-white">Nouvelle Demande d'Absence</h1>
        </div>

        <!-- Leave Balance Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Solde de congés</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Congés payés disponibles</p>
                    <p class="text-2xl font-bold text-blue-600" data-testid="solde-conges">{{ $employe->conges_payes ?? 25 }} jours</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Congés maladie</p>
                    <p class="text-2xl font-bold text-green-600">{{ $employe->conges_maladie ?? 0 }} jours</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Heures de récupération</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $employe->heures_recuperation ?? 0 }} heures</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('absences.store') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type d'absence</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            <option value="">Sélectionner un type</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" name="date_debut" id="date_debut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            @error('date_debut')
                                <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            @error('date_fin')
                                <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif (optionnel)</label>
                        <textarea name="motif" id="motif" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                        @error('motif')
                            <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                            Envoyer la demande
                        </button>
                        <a href="{{ route('absences.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
