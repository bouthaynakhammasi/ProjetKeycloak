@extends('layouts.app')

@section('title', 'Dashboard Absences')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Dashboard Absences</h1>
            <p class="mt-1 text-sm font-medium text-white/80">Statistiques des demandes d'absence</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900" data-testid="total-absences">{{ $totalAbsences }}</h3>
                <p class="text-sm text-gray-500">Total des absences</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900" data-testid="absences-en-attente">{{ $absencesEnAttente }}</h3>
                <p class="text-sm text-gray-500">En attente</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900" data-testid="absences-approuvees">{{ $absencesApprouvees }}</h3>
                <p class="text-sm text-gray-500">Approuvées</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900" data-testid="absences-rejetees">{{ $absencesRejetees }}</h3>
                <p class="text-sm text-gray-500">Rejetées</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Actions rapides</h2>
            <div class="flex gap-4">
                <a href="{{ route('absences.index') }}" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Voir toutes les absences
                </a>
                <a href="{{ route('absences.export') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Exporter les données
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
