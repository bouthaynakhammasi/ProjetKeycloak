@extends('layouts.app')

@section('title', 'Mes Présences')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/90">Mon Espace</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Mes Présences</h1>
                <p class="mt-1 text-sm font-medium text-white/80">Historique de mes présences</p>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Date picker --}}
                <form method="GET" action="{{ route('presences.employee') }}" class="flex items-center gap-2">
                    <input type="date" id="presence-date" name="date" value="{{ $date ?? '' }}"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white"
                        onchange="this.form.submit()">
                </form>
            </div>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            {{-- Total --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total</p>
                <p class="text-3xl font-bold text-gray-900">{{ $presences->count() }}</p>
            </div>

            {{-- Présents --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <p class="text-xs font-medium text-green-600 uppercase tracking-wider mb-1">Présents</p>
                <p class="text-3xl font-bold text-green-700">{{ $presences->where('statut', 'present')->count() }}</p>
            </div>

            {{-- Retards --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                <p class="text-xs font-medium text-yellow-600 uppercase tracking-wider mb-1">Retards</p>
                <p class="text-3xl font-bold text-yellow-700">{{ $presences->where('statut', 'retard')->count() }}</p>
            </div>

            {{-- Absents --}}
            <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                <p class="text-xs font-medium text-red-600 uppercase tracking-wider mb-1">Absents</p>
                <p class="text-3xl font-bold text-red-700">{{ $presences->where('statut', 'absent')->count() }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Heure d'arrivée</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Heure de départ</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($presences as $presence)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($presence->date)->translatedFormat('j F Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                {{ $presence->heure_connexion ? \Carbon\Carbon::parse($presence->heure_connexion)->format('H:i') : '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                {{ $presence->heure_depart ? \Carbon\Carbon::parse($presence->heure_depart)->format('H:i') : '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $presence->badge_class }}">
                                {{ $presence->statut_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i data-lucide="calendar-x" class="w-12 h-12 stroke-1 mb-2"></i>
                                <p class="text-sm font-medium">Aucune présence enregistrée</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection