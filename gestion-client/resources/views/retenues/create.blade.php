@extends('layouts.app')

@section('title', 'Nouvelle Retenue')

@section('content')
<div class="p-6 animate-fadeIn">
    <div class="mb-6">
        <a href="{{ route('retenues.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle Retenue</h1>
            <p class="text-sm text-gray-500 mt-1">Ajoutez une retenue pour un employé</p>
        </div>

        <form method="POST" action="{{ route('retenues.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employé *</label>
                    <select name="employe_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Sélectionner un employé</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">{{ $employe->nom_complet }} - {{ $employe->poste }}</option>
                        @endforeach
                    </select>
                    @error('employe_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de Retenue *</label>
                    <input type="text" name="type_retenue" required placeholder="Ex: Absence, Retard, Avance salaire" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('type_retenue')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant (DT) *</label>
                    <input type="number" name="montant" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('montant')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input type="date" name="date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Créer la Retenue
                </button>
                <a href="{{ route('retenues.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
