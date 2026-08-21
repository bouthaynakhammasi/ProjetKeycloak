@extends('layouts.app')

@section('title', 'Présence du jour')

@section('content')
<div class="min-h-screen py-8 page-content">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-white">Présence du jour</h1>
            <p class="mt-1 text-sm font-medium text-white/80">{{ $dateFormatted }}</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Date picker --}}
            <form method="GET" action="{{ route('presences.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}"
                    class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white"
                    onchange="this.form.submit()">
            </form>

            {{-- Menu contextuel --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <i data-lucide="more-horizontal" class="w-5 h-5 text-gray-500"></i>
                </button>
                <div x-show="open" @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-50">
                    
                    @if(session('user_role') === 'ROLE_ADMIN')
                    <form method="POST" action="{{ route('presences.marquer-absents') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors text-left">
                            <i data-lucide="user-x" class="w-4 h-4 text-gray-400"></i>
                            Marquer tous absents
                        </button>
                    </form>
                    @endif
                </div>
            </div>
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
    <div class="grid grid-cols-3 gap-4 mb-6">
        {{-- Total --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalEmployes }}</p>
        </div>

        {{-- Présents --}}
        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
            <p class="text-xs font-medium text-green-600 uppercase tracking-wider mb-1">Présents</p>
            <p class="text-3xl font-bold text-green-700">{{ $presents }}</p>
        </div>

        {{-- Absents --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <p class="text-xs font-medium text-red-600 uppercase tracking-wider mb-1">Absents</p>
            <p class="text-3xl font-bold text-red-700">{{ $absents }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Employé</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Arrivée</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Départ</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($listePresence as $item)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($item->employe->photo)
                                <img src="{{ asset('storage/' . $item->employe->photo) }}" alt=""
                                    class="w-8 h-8 rounded-full object-cover border border-gray-200"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($item->employe->nom_complet) }}&background=6366f1&color=ffffff&size=128'">
                            @else
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-sm font-semibold">
                                    {{ mb_substr($item->employe->prenom, 0, 1) }}{{ mb_substr($item->employe->nom, 0, 1) }}
                                </div>
                            @endif
                            <span class="text-sm font-medium text-gray-900">{{ $item->employe->nom_complet }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">
                            {{ $item->heure_connexion ? \Carbon\Carbon::parse($item->heure_connexion)->format('H:i') : '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">
                            {{ $item->heure_depart ? \Carbon\Carbon::parse($item->heure_depart)->format('H:i') : '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if(session('user_role') === 'ROLE_ADMIN')
                            {{-- Admin : formulaire inline pour changer le statut --}}
                            <div class="flex items-center justify-end gap-2" x-data="{ editing: false }">
                                <span x-show="!editing"
                                    @click="editing = true"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border cursor-pointer hover:shadow-sm transition-shadow {{ $item->badge_class }}">
                                    {{ $item->statut_label }}
                                </span>

                                <form x-show="editing" @click.outside="editing = false"
                                    method="POST" action="{{ route('presences.store') }}"
                                    class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="employe_id" value="{{ $item->employe->id }}">
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="time" name="heure_connexion"
                                        value="{{ $item->heure_connexion }}"
                                        class="px-2 py-1 border border-gray-200 rounded-lg text-xs w-24 focus:outline-none focus:ring-1 focus:ring-primary/30">
                                    <input type="time" name="heure_depart"
                                        value="{{ $item->heure_depart }}"
                                        class="px-2 py-1 border border-gray-200 rounded-lg text-xs w-24 focus:outline-none focus:ring-1 focus:ring-primary/30">
                                    <select name="statut"
                                        class="px-2 py-1 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-primary/30">
                                        <option value="present" {{ $item->statut === 'present' ? 'selected' : '' }}>Présent</option>
                                        <option value="retard" {{ $item->statut === 'retard' ? 'selected' : '' }}>Retard</option>
                                        <option value="absent" {{ $item->statut === 'absent' ? 'selected' : '' }}>Absent</option>
                                    </select>
                                    <button type="submit"
                                        class="p-1 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $item->badge_class }}">
                                {{ $item->statut_label }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i data-lucide="users" class="w-10 h-10 text-gray-300"></i>
                            <p class="text-sm text-gray-500">Aucun employé actif trouvé.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
