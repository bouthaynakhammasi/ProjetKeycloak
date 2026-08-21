@extends('layouts.app')

@section('title', 'Agenda RH')

@section('content')
<div class="min-h-screen py-8 page-content" x-data="agenda">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- En-tête principal --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-white/90">Gestion RH</p>
            <div class="flex items-center gap-3">
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-white capitalize">{{ $monthName }}</h1>
                <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-1 shadow-sm">
                    <a href="{{ route('agenda.month', ['year' => $prevDate->year, 'month' => $prevDate->month]) }}"
                       class="p-1 text-gray-600 hover:bg-gray-100 rounded transition" title="Mois précédent">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </a>
                    <a href="{{ route('agenda.index') }}"
                       class="px-3 py-1 text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition" title="Aujourd'hui">
                        Aujourd'hui
                    </a>
                    <a href="{{ route('agenda.month', ['year' => $nextDate->year, 'month' => $nextDate->month]) }}"
                       class="p-1 text-gray-600 hover:bg-gray-100 rounded transition" title="Mois suivant">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <p class="mt-1 text-sm font-medium text-white/80">Gestion du calendrier et des événements RH</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Toggle Semaine / Mois --}}
            <div class="bg-white border border-gray-200 p-1 rounded-xl flex items-center gap-1 text-xs font-medium shadow-sm">
                <button @click="viewMode = 'semaine'"
                        :class="viewMode === 'semaine' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-lg transition-all">
                    Semaine
                </button>
                <button @click="viewMode = 'mois'"
                        :class="viewMode === 'mois' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-lg transition-all">
                    Mois
                </button>
            </div>

            {{-- Bouton Nouvel Événement --}}
            <button @click="modalOpen = true"
                    class="px-5 py-2.5 bg-gradient-to-r from-primary to-primary/90 text-white rounded-xl text-sm font-semibold flex items-center gap-2 hover:from-primary/90 hover:to-primary transition shadow-md">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouvel événement
            </button>
        </div>
    </div>

    {{-- Filtres d'affichage par type --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mr-2">Légende :</span>
        <button @click="selectedType = 'all'" :class="selectedType === 'all' ? 'ring-2 ring-gray-300' : ''" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 transition">
            Tous
        </button>
        <button @click="selectedType = 'conge'" :class="selectedType === 'conge' ? 'ring-2 ring-red-300' : ''" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Congé
        </button>
        <button @click="selectedType = 'formation'" :class="selectedType === 'formation' ? 'ring-2 ring-blue-300' : ''" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Formation
        </button>
        <button @click="selectedType = 'entretien'" :class="selectedType === 'entretien' ? 'ring-2 ring-orange-300' : ''" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition">
            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Entretien
        </button>
        <button @click="selectedType = 'ferie'" :class="selectedType === 'ferie' ? 'ring-2 ring-green-300' : ''" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Férié
        </button>
        <button @click="selectedType = 'reunion'" :class="selectedType === 'reunion' ? 'ring-2 ring-purple-300' : ''" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition">
            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Réunion
        </button>
    </div>

    {{-- Barre de recherche --}}
    <div class="mb-6">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" x-model="searchQuery" placeholder="Rechercher un événement..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
        </div>
    </div>

    {{-- Liste des événements groupés par jour --}}
    <div class="space-y-6">
        @forelse($eventsGrouped as $dateString => $dayEvents)
            @php
                $dayCarbon = \Carbon\Carbon::parse($dateString);
                $isToday = $dayCarbon->isToday();
            @endphp
            <div x-show="shouldShowDay(['{{ implode("','", $dayEvents->pluck('type')->toArray()) }}'])" class="bg-white border {{ $isToday ? 'border-primary/40 ring-2 ring-primary/20' : 'border-gray-200' }} rounded-2xl p-6 shadow-md {{ $isToday ? 'shadow-primary/10' : '' }}">
                {{-- En-tête du jour --}}
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl {{ $isToday ? 'bg-gradient-to-br from-primary to-primary/80 text-white shadow-lg shadow-primary/30' : 'bg-gray-100 text-gray-700' }} flex flex-col items-center justify-center font-bold">
                            <span class="text-[10px] uppercase leading-none tracking-wider">{{ mb_substr($dayCarbon->translatedFormat('D'), 0, 3) }}</span>
                            <span class="text-lg leading-none">{{ $dayCarbon->format('d') }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 capitalize">
                                {{ $dayCarbon->translatedFormat('l j F Y') }}
                            </h3>
                            @if($isToday)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-primary mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                    Aujourd'hui
                                </span>
                            @endif
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">{{ $dayEvents->count() }} événement(s)</span>
                </div>

                {{-- Liste des événements du jour --}}
                <div class="space-y-3">
                    @foreach($dayEvents as $event)
                        <div x-show="shouldShowEvent('{{ $event->type }}', '{{ $event->title }}')" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 bg-gradient-to-r from-gray-50 to-white hover:from-gray-100 hover:to-white transition-all shadow-sm hover:shadow">
                            <div class="flex items-center gap-3">
                                {{-- Pastille de couleur par type --}}
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $event->dot_color }} ring-2 ring-white shadow-sm"></span>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-gray-900">{{ $event->title }}</h4>
                                        @if($event->start_time)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($event->employe)
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                            <i data-lucide="user" class="w-3 h-3 text-gray-400"></i>
                                            <span class="font-medium">{{ $event->employe->nom_complet }}</span>
                                        </p>
                                    @endif

                                    @if($event->description)
                                        <p class="text-xs text-gray-400 mt-1 italic">{{ $event->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                {{-- Badge coloré à droite nommant le type --}}
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold border shadow-sm {{ $event->badge_class }}">
                                    {{ $event->type_label }}
                                </span>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    {{-- Modifier --}}
                                    <button @click="editEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ $event->type }}', '{{ $event->start_date ?? '' }}', '{{ $event->end_date ?? '' }}', '{{ $event->start_time ?? '' }}', '{{ addslashes($event->description ?? '') }}', {{ $event->employe_id ?? 'null' }})" class="p-2 text-gray-400 hover:text-primary rounded-lg hover:bg-primary/10 transition-all hover:shadow" title="Modifier">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>

                                    {{-- Action Supprimer --}}
                                    <form method="POST" action="{{ route('agenda.destroy', $event->id) }}"
                                          onsubmit="return confirm('Voulez-vous vraiment supprimer cet événement ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-all hover:shadow" title="Supprimer">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="calendar" class="w-6 h-6 text-gray-400"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Aucun événement ce mois-ci</h3>
                <p class="text-sm text-gray-500 mt-1 mb-4">Cliquez sur le bouton ci-dessous pour ajouter un événement à l'agenda.</p>
                <button @click="modalOpen = true" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium inline-flex items-center gap-2 hover:bg-primary/90 transition">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Créer un événement
                </button>
            </div>
        @endforelse
    </div>

    {{-- Modal : Modifier un événement --}}
    <div x-show="editModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div @click.outside="editModalOpen = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Modifier l'événement</h3>
                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" x-model="editingEvent?.id">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Titre de l'événement</label>
                    <input type="text" name="title" required x-model="editingEvent?.title" placeholder="ex: Reunion d'équipe"
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Type</label>
                        <select name="type" required x-model="editingEvent?.type" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="reunion">Réunion</option>
                            <option value="conge">Congé</option>
                            <option value="formation">Formation</option>
                            <option value="entretien">Entretien</option>
                            <option value="ferie">Férié</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Employé (optionnel)</label>
                        <select name="employe_id" x-model="editingEvent?.employebId" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="">Tous / Aucun</option>
                            @foreach($employes as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Date début</label>
                        <input type="date" name="start_date" required x-model="editingEvent?.startDate"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Date fin (optionnelle)</label>
                        <input type="date" name="end_date" x-model="editingEvent?.endDate"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Heure (optionnelle)</label>
                        <input type="time" name="start_time" x-model="editingEvent?.startTime"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Détails ou remarques..." x-model="editingEvent?.description"
                              class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="editModalOpen = false"
                            class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90 transition shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal : Ajouter un événement --}}
    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div @click.outside="modalOpen = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Ajouter un événement</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('agenda.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Titre de l'événement</label>
                    <input type="text" name="title" required placeholder="ex: Reunion d'équipe"
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Type</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="reunion">Réunion</option>
                            <option value="conge">Congé</option>
                            <option value="formation">Formation</option>
                            <option value="entretien">Entretien</option>
                            <option value="ferie">Férié</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Employé (optionnel)</label>
                        <select name="employe_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="">Tous / Aucun</option>
                            @foreach($employes as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Date début</label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Date fin (optionnelle)</label>
                        <input type="date" name="end_date"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Heure (optionnelle)</label>
                        <input type="time" name="start_time"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Détails ou remarques..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90 transition shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
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

    document.addEventListener('alpine:init', () => {
        Alpine.data('agenda', () => ({
            modalOpen: false,
            viewMode: 'mois',
            selectedType: 'all',
            searchQuery: '',
            editModalOpen: false,
            editingEvent: null,
            
            shouldShowDay(eventTypes) {
                if (this.selectedType === 'all') return true;
                return eventTypes.includes(this.selectedType);
            },
            
            shouldShowEvent(type, title) {
                const typeMatch = this.selectedType === 'all' || type === this.selectedType;
                const searchMatch = !this.searchQuery || title.toLowerCase().includes(this.searchQuery.toLowerCase());
                return typeMatch && searchMatch;
            },
            
            editEvent(id, title, type, startDate, endDate, startTime, description, employebId) {
                this.editingEvent = {
                    id,
                    title,
                    type,
                    startDate,
                    endDate,
                    startTime,
                    description,
                    employebId
                };
                this.editModalOpen = true;
            }
        }));
    });
</script>
@endsection
