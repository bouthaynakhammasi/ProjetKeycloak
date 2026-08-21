@extends('layouts.app')

@section('title', 'bellent HR - Tableau de bord Admin')

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

                <p class="text-xs text-purple-600 font-medium mt-0.5">Espace Administrateur</p>
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
            
            <!-- Inbox/Notifications Card - Demandes d'absence en attente -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Tabs -->
                <div class="flex border-b border-gray-100">
                    <button class="px-6 py-4 text-sm font-semibold text-gray-900 border-b-2 border-primary">
                        Demandes en attente ({{ $pendingAbsences->count() }})
                    </button>
                    <div class="flex-1 text-right py-4 px-4">
                        <a href="{{ route('absences.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Voir toutes les absences</a>
                    </div>
                </div>

                <!-- List -->
                <div class="divide-y divide-gray-50">
                    @if($pendingAbsences->isEmpty())
                        <div class="p-8 text-center">
                            <i data-lucide="calendar-check" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                            <p class="text-sm text-gray-500">Aucune demande en attente</p>
                        </div>
                    @else
                        @foreach($pendingAbsences as $absence)
                            <div class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                                @if($absence->employe && $absence->employe->photo)
                                    <img src="{{ asset('storage/' . $absence->employe->photo) }}" class="w-10 h-10 rounded-full object-cover mr-4" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($absence->employe->nom . ' ' . $absence->employe->prenom) }}&background=random'">
                                @elseif($absence->employe)
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($absence->employe->nom . ' ' . $absence->employe->prenom) }}&background=random" class="w-10 h-10 rounded-full object-cover mr-4">
                                @else
                                    <img src="https://ui-avatars.com/api/?name=Utilisateur&background=random" class="w-10 h-10 rounded-full object-cover mr-4">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $absence->employe ? $absence->employe->prenom . ' ' . $absence->employe->nom : 'Employé inconnu' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $absence->type }}</p>
                                </div>
                                <div class="flex items-center gap-3 w-48">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $absence->nombre_jours }} jour(s)</p>
                                        <p class="text-xs text-gray-500">{{ $absence->date_debut->format('d/m') }} - {{ $absence->date_fin->format('d/m') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('absences.reject', $absence) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-full border border-red-200 text-red-500 flex items-center justify-center hover:bg-red-50" title="Refuser">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('absences.approve', $absence) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-full border border-primary/30 text-primary flex items-center justify-center hover:bg-primary/5" title="Approuver">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Request Absence Card - Hidden for Admin as it's employee-specific --}}

        </div>

        <!-- RIGHT COLUMN (~35%) -->
        <div class="w-full lg:w-[35%] flex flex-col gap-6">
            
            {{-- Time Tracking Card - Hidden for Admin as it's employee-specific --}}

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
                    @if($todayAbsences->isEmpty())
                        <div class="p-8 text-center">
                            <i data-lucide="calendar-check" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                            <p class="text-sm text-gray-500">Aucune absence aujourd'hui</p>
                        </div>
                    @else
                        @foreach($todayAbsences as $absence)
                            <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    @if($absence->employe && $absence->employe->photo)
                                        <img src="{{ asset('storage/' . $absence->employe->photo) }}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($absence->employe->nom . ' ' . $absence->employe->prenom) }}&background=random'">
                                    @elseif($absence->employe)
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($absence->employe->nom . ' ' . $absence->employe->prenom) }}&background=random" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name=Utilisateur&background=random" class="w-8 h-8 rounded-full object-cover">
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $absence->employe ? $absence->employe->prenom . ' ' . $absence->employe->nom : 'Employé inconnu' }}</p>
                                        <p class="text-xs text-gray-500">{{ $absence->date_debut->format('d M') }}</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 rounded {{ $absence->type === 'conge' ? 'bg-blue-50 text-blue-500' : ($absence->type === 'maladie' ? 'bg-red-50 text-red-500' : 'bg-orange-50 text-orange-500') }} flex items-center justify-center">
                                    @if($absence->type === 'conge')
                                        <i data-lucide="sun" class="w-3.5 h-3.5"></i>
                                    @elseif($absence->type === 'maladie')
                                        <i data-lucide="briefcase-medical" class="w-3.5 h-3.5"></i>
                                    @else
                                        <i data-lucide="palmtree" class="w-3.5 h-3.5"></i>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
