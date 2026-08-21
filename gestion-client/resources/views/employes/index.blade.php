@extends('layouts.app')

@section('title', 'Employés')


@section('content')
@php
    use App\Models\Employe;

    $totalEmployes = Employe::count();
    $totalActifs = Employe::where('statut', 'actif')->count();
    $totalInactifs = Employe::where('statut', 'inactif')->count();
    $isAdmin = session('user_role') === 'ROLE_ADMIN' || session('user_role') === 'admin';
@endphp


<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Liste des employés</h1>
                </div>

            <div class="flex items-center gap-3 self-start sm:self-auto">
                @if($isAdmin)
                    <a href="{{ route('employes.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#7C3AED] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#6D28D9] no-underline">
                        <span class="text-base leading-none">+</span>
                        Ajouter un employé
                    </a>
                @endif
            </div>
        </div>

        <!-- 4 KPI Cards (Clone exact d'Image 4) -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Card 1: Total Employés -->
            <div class="rounded-2xl bg-white p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
                <div class="text-xs font-semibold text-slate-400">Total Employés</div>
                <div class="mt-3 flex items-baseline justify-between">
                    <div class="text-3xl font-bold text-slate-900">{{ $totalEmployes }}</div>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">4% ↑</span>
                </div>
            </div>

            <!-- Card 2: Répartition -->
            <div class="rounded-2xl bg-white p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
                <div class="text-xs font-semibold text-slate-400">Répartition Statuts</div>
                <div class="mt-3 flex items-center justify-between">
                    <div class="text-3xl font-bold text-slate-900">{{ $totalActifs }}</div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> {{ $totalActifs }}
                        <span class="w-2 h-2 rounded-full bg-slate-300 ml-1"></span> {{ $totalInactifs }}
                    </div>
                </div>
            </div>

            <!-- Card 3: Ratio -->
            <div class="rounded-2xl bg-white p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
                <div class="text-xs font-semibold text-slate-400">Nouveaux / Total</div>
                <div class="mt-3 flex items-baseline justify-between">
                    <div class="text-3xl font-bold text-slate-900">1 / {{ $totalEmployes }}</div>
                    <span class="text-xs text-slate-400 font-medium">Ce mois</span>
                </div>
            </div>

            <!-- Card 4: Membres Actifs avec Avatars Stack -->
            <div class="rounded-2xl bg-white p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
                <div class="text-xs font-semibold text-slate-400">Membres Actifs</div>
                <div class="mt-3 flex items-center justify-between">
                    <div class="text-3xl font-bold text-slate-900">{{ $totalActifs }} <span class="text-xs font-medium text-slate-400">now</span></div>
                    <div class="flex -space-x-2 overflow-hidden">
                        @foreach($employes->take(4) as $emp)
                            @if($emp->photo)
                                <img class="inline-block h-7 w-7 rounded-full ring-2 ring-white object-cover" src="{{ asset('storage/' . $emp->photo) }}" alt="">
                            @else
                                <div class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-800 ring-2 ring-white text-[10px] font-bold text-white uppercase">
                                    {{ substr($emp->prenom, 0, 1) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col xl:flex-row gap-6 items-start" x-data="{ selectedId: null, panelHtml: '', loading: false, fetchPanel(id) { this.selectedId = id; this.loading = true; fetch(`/employees/${id}/panel`).then(res => res.text()).then(html => { this.panelHtml = html; this.loading = false; }); } }">
            <div class="w-full xl:flex-1 bg-white shadow-sm border border-slate-100 rounded-3xl overflow-hidden">
            <!-- Entête style onglets (Tabs) -->
            <div class="px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-6 w-full">
                    <h2 class="text-base font-bold text-slate-800">Tous les employés</h2>
                    
                    <form method="GET" action="{{ route('employees.index') }}" class="flex items-center gap-4 ml-auto">
                        <div class="relative flex items-center">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Recherche rapide..." class="w-48 pl-8 pr-3 py-1.5 text-xs bg-slate-50/80 border border-slate-200/80 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#7C3AED] focus:border-[#7C3AED] placeholder:text-slate-400 transition-colors">
                        </div>
                        <select name="statut" class="py-1.5 pl-2 pr-6 text-xs font-semibold text-slate-600 bg-transparent border-transparent focus:border-transparent focus:ring-0 cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.2rem top 50%; background-size: 0.65rem auto;" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Membres Actifs</option>
                            <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Membres Inactifs</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto border-t border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th scope="col" class="px-6 py-1.5 font-bold">Employé</th>
                            <th scope="col" class="px-6 py-1.5 font-bold">Poste / Département</th>
                            <th scope="col" class="px-6 py-1.5 font-bold">Date d'embauche</th>
                            <th scope="col" class="px-6 py-1.5 font-bold">Statut</th>
                            <th scope="col" class="px-6 py-1.5 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- Ligne profil Admin --}}
                        @if($isAdmin && $connectedEmploye)
                        <tr class="hover:bg-violet-50/40 transition-colors group bg-violet-50/20 cursor-pointer" @click="fetchPanel({{ $connectedEmploye->id }})">
                            <td class="px-6 py-1.5">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        @if($connectedEmploye->photo)
                                            <img src="{{ asset('storage/' . $connectedEmploye->photo) }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-violet-200">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#7C3AED] to-violet-400 text-xs font-semibold text-white">
                                                {{ strtoupper(substr($connectedEmploye->prenom, 0, 1) . substr($connectedEmploye->nom, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm">{{ $connectedEmploye->nom }} {{ $connectedEmploye->prenom }} <span class="ml-1 text-[9px] uppercase font-bold text-violet-700 bg-violet-100 px-1.5 py-0.5 rounded">Vous</span></div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $connectedEmploye->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-1.5">
                                <div class="font-medium text-slate-800 text-sm">{{ $connectedEmploye->poste ?? 'Administrateur' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $connectedEmploye->departement ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-1.5 text-slate-600 text-sm">
                                {{ $connectedEmploye->date_embauche ? $connectedEmploye->date_embauche->format('d M, Y') : '-' }}
                            </td>
                            <td class="px-6 py-1.5">
                                <span class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold uppercase {{ ($connectedEmploye->statut ?? 'actif') === 'actif' ? 'bg-lime-100 text-lime-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($connectedEmploye->statut ?? 'Actif') }}
                                </span>
                            </td>
                            <td class="px-6 py-1.5 text-right">
                                <button type="button" @click.stop="fetchPanel({{ $connectedEmploye->id }})" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#7C3AED] hover:text-[#6D28D9] transition-colors uppercase tracking-wider focus:outline-none">
                                    Voir profil
                                </button>
                            </td>
                        </tr>
                        @endif

                        @forelse($employes as $employe)
                        <tr class="hover:bg-violet-50/40 transition-colors group cursor-pointer" @click="fetchPanel({{ $employe->id }})">
                            <td class="px-6 py-1.5">
                                <div class="flex items-center gap-3">
                                    @if($employe->photo)
                                        <img src="{{ asset('storage/' . $employe->photo) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                                    @else
                                        @php
                                            $seed = crc32($employe->nom . $employe->prenom);
                                            $palette = ['bg-slate-800', 'bg-indigo-600', 'bg-emerald-600', 'bg-blue-600', 'bg-amber-600'];
                                            $avatarClass = $palette[$seed % count($palette)];
                                            $initiales = strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1));
                                        @endphp
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold text-white {{ $avatarClass }}">
                                            {{ $initiales }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm">{{ $employe->nom }} {{ $employe->prenom }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $employe->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-1.5">
                                <div class="font-medium text-slate-800 text-sm">{{ $employe->poste ?? 'Employé' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $employe->departement ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-1.5 text-slate-600 text-sm">
                                {{ $employe->date_embauche ? $employe->date_embauche->format('d M, Y') : '-' }}
                            </td>
                            <td class="px-6 py-1.5">
                                <span class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold uppercase {{ $employe->statut === 'actif' ? 'bg-lime-100 text-lime-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($employe->statut ?? 'inconnu') }}
                                </span>
                            </td>
                            <td class="px-6 py-1.5 text-right">
                                <button type="button" @click.stop="fetchPanel({{ $employe->id }})" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#7C3AED] hover:text-[#6D28D9] transition-colors uppercase tracking-wider focus:outline-none">
                                    Voir profil
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">
                                Aucun employé trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Colonne de droite : Panneau de détails -->
        <div class="w-full xl:flex-1 shrink-0">
            <!-- Placeholder -->
            <div x-show="!selectedId" class="bg-white border border-slate-200/80 shadow-2xs rounded-2xl p-6 text-center flex flex-col items-center justify-center h-[calc(100vh-120px)] sticky top-24">
                <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h3 class="text-[11px] font-bold text-slate-800 tracking-tight">Sélectionnez un employé</h3>
                <p class="text-[10px] font-medium text-slate-400 mt-1 max-w-[180px]">Cliquez pour voir les détails.</p>
            </div>
            
            <!-- Loading -->
            <div x-show="loading && selectedId" class="bg-white border border-slate-200/80 shadow-2xs rounded-2xl p-6 text-center flex flex-col items-center justify-center h-[calc(100vh-120px)] sticky top-24">
                <svg class="animate-spin h-5 w-5 text-slate-900 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="text-[11px] font-semibold text-slate-600 mt-2">Chargement...</p>
            </div>
            
            <!-- Content (Panel HTML) -->
            <div x-show="!loading && panelHtml !== ''" x-html="panelHtml"></div>
        </div>
        </div>

        <div class="mt-8 flex justify-center">
            {{ $employes->links() }}
        </div>
    </div>
</div>
@endsection
