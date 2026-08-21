@extends('layouts.app')

@section('title', 'Modifier le Salaire')

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
            <h1 class="text-2xl font-bold text-gray-900">Modifier le Salaire</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $salaire->employe->nom_complet }} - {{ $salaire->nom_mois }} {{ $salaire->annee }}</p>
        </div>

        <form method="POST" action="{{ route('salaires.update', $salaire) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employé *</label>
                    <select name="employe_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Sélectionner un employé</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}" {{ $salaire->employe_id == $employe->id ? 'selected' : '' }}>
                                {{ $employe->nom_complet }} - {{ $employe->poste }}
                            </option>
                        @endforeach
                    </select>
                    @error('employe_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mois *</label>
                    <select name="mois" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $salaire->mois == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('mois')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Année *</label>
                    <input type="number" name="annee" value="{{ $salaire->annee }}" required min="2020" max="2100" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('annee')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salaire Base (DT) *</label>
                    <input type="number" name="salaire_base" value="{{ $salaire->salaire_base }}" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('salaire_base')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prime (DT)</label>
                    <input type="number" name="prime" value="{{ $salaire->prime }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('prime')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retenue (DT)</label>
                    <input type="number" name="retenue" value="{{ $salaire->retenue }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('retenue')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut de Paiement *</label>
                    <select name="statut_paiement" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="en_attente" {{ $salaire->statut_paiement === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="paye" {{ $salaire->statut_paiement === 'paye' ? 'selected' : '' }}>Payé</option>
                        <option value="annule" {{ $salaire->statut_paiement === 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                    @error('statut_paiement')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('notes', $salaire->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Mettre à jour
                </button>
                <a href="{{ route('salaires.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
