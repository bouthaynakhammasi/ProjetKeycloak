@extends('layouts.app')

@section('title', 'Nouveau Salaire')

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
            <h1 class="text-2xl font-bold text-gray-900">Nouveau Salaire</h1>
            <p class="text-sm text-gray-500 mt-1">Créez un nouveau salaire pour un employé</p>
        </div>

        <form method="POST" action="{{ route('salaires.store') }}" class="p-6">
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

            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mois *</label>
                    <select name="mois" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Sélectionner un mois</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    @error('mois')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Année *</label>
                    <input type="number" name="annee" value="{{ date('Y') }}" required min="2020" max="2100" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('annee')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salaire Brut (DT) *</label>
                    <input type="number" name="salaire_brut" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('salaire_brut')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Déductions (DT)</label>
                    <input type="number" name="deductions" min="0" step="0.01" value="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('deductions')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salaire Net (calculé automatiquement)</label>
                    <input type="text" id="salaire_net" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600" value="0.00">
                    <p class="mt-1 text-xs text-gray-500">Salaire net = Salaire brut - Déductions</p>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Créer le Salaire
                </button>
                <a href="{{ route('salaires.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const salaireBrut = document.querySelector('input[name="salaire_brut"]');
        const deductions = document.querySelector('input[name="deductions"]');
        const salaireNet = document.getElementById('salaire_net');

        function calculateNet() {
            const brut = parseFloat(salaireBrut.value) || 0;
            const ded = parseFloat(deductions.value) || 0;
            const net = brut - ded;
            salaireNet.value = net.toFixed(2);
        }

        salaireBrut.addEventListener('input', calculateNet);
        deductions.addEventListener('input', calculateNet);
    });
</script>
@endsection
