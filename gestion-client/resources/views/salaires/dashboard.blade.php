@extends('layouts.app')

@section('title', 'Dashboard Paie')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Tableau de bord Paie</h1>
            <p class="mt-1 text-sm font-medium text-white/80">Bienvenue {{ $userName }}</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Bouton Export -->
            <a href="{{ route('salaires.index') }}" class="px-4 py-2 bg-primary text-white rounded-lg flex items-center gap-2 hover:bg-primary/90 transition no-underline">
                <i data-lucide="list" class="w-4 h-4"></i>
                Voir tous les salaires
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                    <i data-lucide="{{ $kpi['icon'] }}" class="w-6 h-6 text-gray-600"></i>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $kpi['badge_positive'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    {{ $kpi['badge'] }}
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $kpi['value'] }}</h3>
            <p class="text-slate-400 text-sm mt-1">{{ $kpi['title'] }}</p>
            <p class="text-xs text-slate-400 mt-2">{{ $kpi['vs_last_month'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Graphique et Taux de traitement -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Masse Salariale Totale (2/3) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Évolution de la Masse Salariale</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-2xl font-bold text-gray-900">{{ $salesData['total'] }} DT</span>
                        <span class="text-sm {{ $salesData['variation_positive'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $salesData['variation'] }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        6 derniers mois
                    </span>
                </div>
            </div>
            <!-- Graphique SVG -->
            <div class="relative h-64">
                <svg class="w-full h-full" viewBox="0 0 800 200" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#16a34a;stop-opacity:0.3" />
                            <stop offset="100%" style="stop-color:#16a34a;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                    <!-- Zone remplie -->
                    <path d="M0,150 Q100,100 200,120 T400,80 T600,100 T800,60 L800,200 L0,200 Z" fill="url(#gradient)" />
                    <!-- Courbe pleine -->
                    <path d="M0,150 Q100,100 200,120 T400,80 T600,100 T800,60" fill="none" stroke="#16a34a" stroke-width="3" />
                    <!-- Courbe pointillée -->
                    <path d="M0,160 Q100,130 200,140 T400,110 T600,130 T800,90" fill="none" stroke="#d1d5db" stroke-width="2" stroke-dasharray="5,5" />
                </svg>
            </div>
            <!-- Dates du graphique -->
            @if(isset($chartData) && count($chartData) > 0)
            <div class="flex justify-between text-xs text-gray-400 mt-4 px-2">
                @foreach($chartData as $point)
                    <div>
                        <p class="font-medium text-gray-600 text-center">{{ $point['label'] }}</p>
                        <p class="text-[10px] text-gray-400 text-center">{{ number_format($point['value'], 0, ',', ' ') }} DT</p>
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Taux de traitement (1/3) -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Taux de Traitement</h2>
            <div class="text-center mb-6">
                <span class="text-4xl font-bold text-gray-900">{{ $conversionRate['rate'] }}</span>
                <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold {{ $conversionRate['variation_positive'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    {{ $conversionRate['variation'] }}
                </span>
            </div>
            <!-- Funnel -->
            <div class="space-y-4">
                @foreach($funnel as $step)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $step['step'] }}</span>
                        <span class="font-semibold text-gray-900">{{ $step['value'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $step['percent'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $step['percent'] }}%</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bas de page -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Performance Paie (1/3) -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 text-white">
            <h3 class="text-xl font-bold mb-2">Performances Paie</h3>
            <p class="text-gray-300 text-sm mb-6">Indicateurs clés de gestion des bulletins</p>

            <!-- Jauges circulaires -->
            <div class="flex justify-around">
                <div class="text-center">
                    <svg class="w-20 h-20 transform -rotate-90">
                        <circle cx="40" cy="40" r="35" stroke="#374151" stroke-width="6" fill="none" />
                        <circle cx="40" cy="40" r="35" stroke="#16a34a" stroke-width="6" fill="none"
                            stroke-dasharray="220" stroke-dashoffset="{{ 220 - (220 * $premiumStats['performance'] / 100) }}" />
                    </svg>
                    <p class="text-sm font-semibold mt-2">Paiement</p>
                    <p class="text-xs text-gray-400">{{ $premiumStats['performance'] }}%</p>
                </div>
                <div class="text-center">
                    <svg class="w-20 h-20 transform -rotate-90">
                        <circle cx="40" cy="40" r="35" stroke="#374151" stroke-width="6" fill="none" />
                        <circle cx="40" cy="40" r="35" stroke="#3b82f6" stroke-width="6" fill="none"
                            stroke-dasharray="220" stroke-dashoffset="{{ 220 - (220 * $premiumStats['tasks'] / 100) }}" />
                    </svg>
                    <p class="text-sm font-semibold mt-2">Complétions</p>
                    <p class="text-xs text-gray-400">{{ $premiumStats['tasks'] }}%</p>
                </div>
            </div>
        </div>

        <!-- Derniers Bulletins (2/3) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Derniers Bulletins de Paie</h2>
                    <p class="text-sm text-gray-400">{{ $products->count() }} enregistrements</p>
                </div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3 font-semibold">Employé</th>
                            <th class="pb-3 font-semibold">Période</th>
                            <th class="pb-3 font-semibold">Salaire Brut</th>
                            <th class="pb-3 font-semibold">Déductions</th>
                            <th class="pb-3 font-semibold text-right">Salaire Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product['image'] }}" class="w-8 h-8 rounded-full object-cover border border-gray-200" alt="{{ $product['name'] }}">
                                    <span class="font-medium text-gray-900 text-sm">{{ $product['name'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-600">{{ $product['periode'] }}</td>
                            <td class="py-3 text-sm text-gray-600">{{ $product['salaire_brut'] }} DT</td>
                            <td class="py-3 text-sm text-red-500">-{{ $product['deductions'] }} DT</td>
                            <td class="py-3 text-sm font-semibold text-gray-900 text-right">{{ $product['salaire_net'] }} DT</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm text-gray-400">
                                Aucun bulletin de salaire disponible.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
