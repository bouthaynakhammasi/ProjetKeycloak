@extends('layouts.app')

@section('title', 'bellent HR - Tableau de bord')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    
        <!-- Welcome Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                @if(session('user_photo'))
                    <img src="{{ asset('storage/' . session('user_photo')) }}" alt="User Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'Utilisateur') }}&background=random'">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(session('user_name') ?? 'Utilisateur') }}&background=6366f1&color=ffffff&size=128" alt="User Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                @endif
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-white/90">Tableau de bord</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Bonjour {{ session('user_name') ?? 'Utilisateur' }} !</h1>
                </div>
            </div>
            
            <div class="text-gray-500 text-sm font-medium">
                {{ now()->formatLocalized('%A %d %B') }}
            </div>
        </div>

    <!-- 2-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-6">
        
        <!-- LEFT COLUMN (~65%) -->
        <div class="w-full lg:w-[65%] flex flex-col gap-6">
            
            <!-- Inbox/Notifications Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Tabs -->
                <div class="flex border-b border-gray-100">
                    <button class="px-6 py-4 text-sm font-semibold text-gray-900 border-b-2 border-primary">
                        Boîte de réception (7)
                    </button>
                    <button class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Notifications (2)
                    </button>
                    <div class="flex-1 text-right py-4 px-4">
                        <a href="#" class="text-xs text-gray-400 hover:text-gray-600">Guide d'utilisation</a>
                    </div>
                </div>

                <!-- List -->
                <div class="divide-y divide-gray-50">
                    <!-- Row 1 -->
                    <div class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <img src="{{ asset('assets/img/team/team-2.jpg') }}" class="w-10 h-10 rounded-full object-cover mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Joseph+Bousquet&background=random'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">Joseph Bousquet</p>
                            <p class="text-xs text-gray-500 truncate">Coordinateur marketing</p>
                        </div>
                        <div class="flex items-center gap-3 w-48">
                            <div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                                <i data-lucide="heart-pulse" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Jours de récup</p>
                                <p class="text-xs text-gray-500">1 jour</p>
                            </div>
                        </div>
                        <div class="w-32 flex flex-col items-center justify-center">
                            <div class="text-center">
                                <p class="text-sm font-bold">23</p>
                                <p class="text-[10px] text-gray-500 uppercase">Oct 24</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full border border-red-200 text-red-500 flex items-center justify-center hover:bg-red-50"><i data-lucide="x" class="w-4 h-4"></i></button>
                            <button class="w-8 h-8 rounded-full border border-primary/30 text-primary flex items-center justify-center hover:bg-primary/5"><i data-lucide="check" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <img src="{{ asset('assets/img/team/team-3.jpg') }}" class="w-10 h-10 rounded-full object-cover mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Alix+Hector&background=random'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">Alix Hector</p>
                            <p class="text-xs text-gray-500 truncate">Présidente des ventes</p>
                        </div>
                        <div class="flex items-center gap-3 w-48">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Congés payés</p>
                                <p class="text-xs text-gray-500">2 jours</p>
                            </div>
                        </div>
                        <div class="w-32 flex items-center justify-center gap-2">
                            <div class="text-center">
                                <p class="text-sm font-bold">23</p>
                                <p class="text-[10px] text-gray-500 uppercase">Oct 24</p>
                            </div>
                            <i data-lucide="arrow-right" class="w-3 h-3 text-gray-300"></i>
                            <div class="text-center">
                                <p class="text-sm font-bold">24</p>
                                <p class="text-[10px] text-gray-500 uppercase">Oct 24</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full border border-red-200 text-red-500 flex items-center justify-center hover:bg-red-50"><i data-lucide="x" class="w-4 h-4"></i></button>
                            <button class="w-8 h-8 rounded-full border border-primary/30 text-primary flex items-center justify-center hover:bg-primary/5"><i data-lucide="check" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <img src="{{ asset('assets/img/team/team-4.jpg') }}" class="w-10 h-10 rounded-full object-cover mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Yolande+Leloup&background=random'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">Yolande Leloup</p>
                            <p class="text-xs text-gray-500 truncate">Aide-soignante</p>
                        </div>
                        <div class="flex items-center gap-3 w-48">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 shrink-0">
                                <i data-lucide="plane" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Déplacements et vo...</p>
                                <p class="text-xs text-gray-500">21 octobre</p>
                            </div>
                        </div>
                        <div class="w-32 flex items-center justify-center">
                            <p class="text-sm font-medium">375,95 EUR</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full border border-red-200 text-red-500 flex items-center justify-center hover:bg-red-50"><i data-lucide="x" class="w-4 h-4"></i></button>
                            <button class="w-8 h-8 rounded-full border border-primary/30 text-primary flex items-center justify-center hover:bg-primary/5"><i data-lucide="check" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                    
                    <!-- Row 4 -->
                    <div class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <img src="{{ asset('assets/img/team/team-1.jpg') }}" class="w-10 h-10 rounded-full object-cover mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Mehdi+Laurent&background=random'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">Mehdi Laurent</p>
                            <p class="text-xs text-gray-500 truncate">Dresseur de chiens</p>
                        </div>
                        <div class="flex items-center gap-3 w-48">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Formations</p>
                                <p class="text-xs text-gray-500">20 octobre</p>
                            </div>
                        </div>
                        <div class="w-32 flex items-center justify-center">
                            <p class="text-sm font-medium">375,95 EUR</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full border border-red-200 text-red-500 flex items-center justify-center hover:bg-red-50"><i data-lucide="x" class="w-4 h-4"></i></button>
                            <button class="w-8 h-8 rounded-full border border-primary/30 text-primary flex items-center justify-center hover:bg-primary/5"><i data-lucide="check" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Request Absence Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Effectuer une demande d'absence</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                                <i data-lucide="sun" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Congés payés</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">15 jours disponibles</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                        </div>
                    </a>
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
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
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center text-teal-500">
                                <i data-lucide="heart-pulse" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Heures de récup</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">12h 48m disponibles</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN (~35%) -->
        <div class="w-full lg:w-[35%] flex flex-col gap-6">
            
            <!-- Time Tracking Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Suivi du temps de travail</h2>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Prévu</span>
                        <span class="font-medium">8h 00m</span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-50 text-gray-600 border border-gray-200 text-xs font-medium">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                ENREGISTRÉES
                            </span>
                        </div>
                        <span class="font-medium">4h 00m</span>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-600 border border-blue-200 text-xs font-medium">
                                <i data-lucide="sun" class="w-3 h-3"></i>
                                CONGÉS PAYÉS
                            </span>
                        </div>
                        <span class="font-medium">4h 00m</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 mb-4">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-bold text-gray-900">8h 00m</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Nombre d'heures restantes</span>
                        <span class="text-gray-500">- 1h 00m</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button class="text-sm text-gray-400 hover:text-gray-600 font-medium">
                        + Ajouter une pause
                    </button>
                    <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                        Démarrer
                    </button>
                </div>
            </div>

            <!-- Absences Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Absences</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Aujourd'hui</p>
                    </div>
                    <div class="flex gap-1">
                        <button class="w-7 h-7 flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 rounded">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button class="w-7 h-7 flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 rounded">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-50">
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/img/team/team-2.jpg') }}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Jessica+Landry&background=random'">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Jessica Landry</p>
                                <p class="text-xs text-gray-500">16 août</p>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded bg-red-50 text-red-500 flex items-center justify-center">
                            <i data-lucide="briefcase-medical" class="w-3.5 h-3.5"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/img/team/team-3.jpg') }}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Victor+Bonnet&background=random'">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Victor Bonnet</p>
                                <p class="text-xs text-gray-500">16 août</p>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center">
                            <i data-lucide="sun" class="w-3.5 h-3.5"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/img/team/team-4.jpg') }}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Mathieu+Bourdon&background=random'">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Mathieu Bourdon</p>
                                <p class="text-xs text-gray-500">16 août</p>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded bg-orange-50 text-orange-500 flex items-center justify-center">
                            <i data-lucide="palmtree" class="w-3.5 h-3.5"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/img/team/team-1.jpg') }}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Marie+Bourgandin&background=random'">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Marie Bourgandin</p>
                                <p class="text-xs text-gray-500">16 août</p>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center">
                            <i data-lucide="sun" class="w-3.5 h-3.5"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </div>
</div>
@endsection
