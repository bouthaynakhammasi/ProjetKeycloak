@extends('layouts.app')

@section('title', 'Modifier Absence')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('absences.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/90 mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour à la liste
            </a>
            <h1 class="text-3xl font-bold text-white">Modifier Absence</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('absences.update', $absence) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type d'absence</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ $absence->type === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ $absence->date_debut->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            @error('date_debut')
                                <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" name="date_fin" id="date_fin" value="{{ $absence->date_fin->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            @error('date_fin')
                                <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif (optionnel)</label>
                        <textarea name="motif" id="motif" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ $absence->motif }}</textarea>
                        @error('motif')
                            <p class="mt-1 text-sm text-red-600 error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                            Enregistrer les modifications
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
