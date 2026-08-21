@extends('layouts.app')

@section('title', 'bellent HR - Mon Espace')

@section('content')
<div class="p-6">
    <!-- Welcome Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            @if(session('user_photo'))
                <img src="{{ asset('storage/' . session('user_photo')) }}" alt="User Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'Utilisateur') }}&background=random'">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'Utilisateur') }}&background=6366f1&color=ffffff&size=128" alt="User Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
            @endif
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Bonjour {{ session('user_name') ?? 'Utilisateur' }} !</h1>
                <p class="text-xs text-purple-600 font-medium mt-0.5">Espace Employé</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-gray-500 text-sm font-medium">
                {{ now()->formatLocalized('%A %d %B') }}
            </div>
            
            {{-- Notifications --}}
            @php
                $pendingAbsencesCount = \App\Models\Absence::where('employe_id', $employee->id ?? null)
                    ->where('statut', 'pending')
                    ->count();
            @endphp
            <div class="relative">
                <button class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors relative">
                    <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                    @if($pendingAbsencesCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                            {{ $pendingAbsencesCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- 2-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-6">
        
        <!-- LEFT COLUMN (~65%) -->
        <div class="w-full lg:w-[65%] flex flex-col gap-6">
            
            <!-- Welcome Info Banner -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl shadow-sm p-6 text-white">
                <h2 class="text-lg font-semibold mb-2">Bienvenue sur votre espace personnel</h2>
                <p class="text-sm text-purple-100 leading-relaxed">
                    Ici, vous pouvez effectuer vos demandes de congés, suivre vos heures de travail, et consulter votre agenda. Pour toute question, veuillez contacter le service RH.
                </p>
            </div>

            <!-- Request Absence Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Effectuer une demande d'absence</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <a href="{{ route('absences.create') }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Congés payés</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ $employee->conges_payes ?? 0 }} jours disponibles</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                        </div>
                    </a>
                    <a href="{{ route('absences.create') }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                <i data-lucide="briefcase-medical" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Congés maladies</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Illimité</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                        </div>
                    </a>
                    <a href="{{ route('absences.create') }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center text-teal-500">
                                <i data-lucide="heart-pulse" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Heures de récup</span>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $hours = floor($employee->heures_recuperation / 60);
                                $minutes = $employee->heures_recuperation % 60;
                            @endphp
                            <span class="text-xs text-gray-500">{{ $hours }}h {{ $minutes }}m disponibles</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Personal Absences Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Mes dernières demandes</h2>
                    <a href="{{ route('absences.index') }}" class="text-xs text-primary hover:text-primary/80 font-medium">Voir tout</a>
                </div>
                @if($myAbsences->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                        <i data-lucide="inbox" class="w-12 h-12 stroke-1 mb-2"></i>
                        <p class="text-sm font-medium">Aucune demande en cours d'instruction</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($myAbsences as $absence)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $absence->type === 'conge' ? 'bg-blue-50 text-blue-500' : ($absence->type === 'maladie' ? 'bg-red-50 text-red-500' : 'bg-orange-50 text-orange-500') }} flex items-center justify-center">
                                        @if($absence->type === 'conge')
                                            <i data-lucide="sun" class="w-4 h-4"></i>
                                        @elseif($absence->type === 'maladie')
                                            <i data-lucide="briefcase-medical" class="w-4 h-4"></i>
                                        @else
                                            <i data-lucide="palmtree" class="w-4 h-4"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $absence->type }}</p>
                                        <p class="text-xs text-gray-500">{{ $absence->date_debut->format('d/m/Y') }} - {{ $absence->date_fin->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $absence->badge_class }}">
                                    {{ $absence->statut_label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Notifications Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Notifications</h2>
                @php
                    $pendingAbsences = \App\Models\Absence::where('employe_id', $employee->id)
                        ->where('statut', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();
                @endphp
                @if($pendingAbsences->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                        <i data-lucide="bell" class="w-12 h-12 stroke-1 mb-2"></i>
                        <p class="text-sm font-medium">Aucune notification</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($pendingAbsences as $absence)
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-yellow-100 bg-yellow-50">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">Demande en attente</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $absence->type }} - {{ $absence->date_debut->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- RIGHT COLUMN (~35%) -->
        <div class="w-full lg:w-[35%] flex flex-col gap-6">
            
            <!-- Time Tracking Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Présence du jour</h2>
                
                @if($todayPresence)
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Statut</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $todayPresence->badge_class }}">
                                {{ $todayPresence->statut_label }}
                            </span>
                        </div>
                        
                        @if($todayPresence->heure_connexion)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Heure d'arrivée</span>
                            <span class="font-medium">{{ $todayPresence->heure_connexion }}</span>
                        </div>
                        @endif
                        
                        @if($todayPresence->remarque)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Remarque</span>
                            <span class="font-medium text-gray-900">{{ $todayPresence->remarque }}</span>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-6 text-gray-400">
                        <i data-lucide="clock" class="w-10 h-10 stroke-1 mb-2"></i>
                        <p class="text-sm font-medium">Aucune présence enregistrée aujourd'hui</p>
                    </div>
                @endif

                <div class="border-t border-gray-100 pt-4">
                    <a href="{{ route('presences.index') }}" class="text-sm text-primary hover:text-primary/80 font-medium flex items-center gap-2">
                        Voir mes présences
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
