@extends('layouts.app')

@section('title', 'Détails Absence')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 alert-success">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mb-6">
            <a href="{{ route('absences.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/90 mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour à la liste
            </a>
            <h1 class="text-3xl font-bold text-white">Détails Absence</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Employé</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $absence->employe->nom_complet }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                        <p class="text-lg font-semibold text-gray-900" data-testid="absence-type">{{ $absence->type }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date Début</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $absence->date_debut->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date Fin</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $absence->date_fin->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre de jours</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $absence->nombre_jours }} jour(s)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Statut</label>
                        @if($absence->statut === 'pending')
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">En attente</span>
                        @elseif($absence->statut === 'approved')
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Approuvé</span>
                        @elseif($absence->statut === 'rejected')
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Rejeté</span>
                        @endif
                    </div>
                </div>

                @if($absence->motif)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Motif</label>
                        <p class="text-gray-900" data-testid="absence-motif">{{ $absence->motif }}</p>
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Dates</label>
                    <p class="text-gray-900" data-testid="absence-dates">
                        Du {{ $absence->date_debut->format('d/m/Y') }} au {{ $absence->date_fin->format('d/m/Y') }}
                        ({{ $absence->nombre_jours }} jour(s))
                    </p>
                </div>

                @if($absence->reponse_at)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Réponse le</label>
                        <p class="text-gray-900">{{ $absence->reponse_at->format('d/m/Y à H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
