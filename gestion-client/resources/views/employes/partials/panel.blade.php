<div class="bg-white border border-slate-100 shadow-xl rounded-3xl overflow-hidden flex flex-col h-[calc(100vh-120px)] sticky top-24" x-data="{ 
    activeTab: 'apercu', 
    isEditing: false, 
    isSubmitting: false,
    errorMessage: '',
    async submitForm(e) {
        this.isSubmitting = true;
        this.errorMessage = '';
        const formData = new FormData(e.target);
        formData.append('_method', 'PUT');
        try {
            const res = await fetch('{{ route('employes.update', $employe) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html'
                },
                body: formData
            });
            if (!res.ok) {
                const data = await res.json();
                this.errorMessage = data.message || 'Erreur lors de la sauvegarde';
                this.isSubmitting = false;
                return;
            }
            const html = await res.text();
            document.querySelector('[x-data*=\'selectedId\']').__x.$data.panelHtml = html;
        } catch (err) {
            this.errorMessage = 'Erreur réseau';
            this.isSubmitting = false;
        }
    }
}">
    <!-- Mode Édition -->
    <div x-show="isEditing" class="flex flex-col h-full bg-white" style="display: none;">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Modifier les informations</h3>
            <button @click="isEditing = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form @submit.prevent="submitForm($event)" class="p-6 flex-1 overflow-y-auto space-y-4">
            <div x-show="errorMessage" class="p-3 rounded-lg bg-rose-50 border border-rose-100 text-rose-600 text-xs font-medium" x-text="errorMessage"></div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nom</label>
                    <input type="text" name="nom" value="{{ $employe->nom }}" required class="w-full px-3 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all outline-none font-medium text-slate-900">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Prénom</label>
                    <input type="text" name="prenom" value="{{ $employe->prenom }}" required class="w-full px-3 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all outline-none font-medium text-slate-900">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email professionnel</label>
                <input type="email" name="email" value="{{ $employe->email }}" required class="w-full px-3 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all outline-none font-medium text-slate-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Poste</label>
                    <input type="text" name="poste" value="{{ $employe->poste }}" class="w-full px-3 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all outline-none font-medium text-slate-900">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Département</label>
                    <input type="text" name="departement" value="{{ $employe->departement }}" class="w-full px-3 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all outline-none font-medium text-slate-900">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Statut</label>
                <select name="statut" class="w-full px-3 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all outline-none font-medium text-slate-900">
                    <option value="actif" {{ $employe->statut === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ $employe->statut === 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" @click="isEditing = false" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">Annuler</button>
                <button type="submit" :disabled="isSubmitting" class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <span x-show="isSubmitting" class="animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full" style="display:none;"></span>
                    <span>Enregistrer</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Mode Consultation (Clone exact du design de l'image) -->
    <div x-show="!isEditing" class="flex flex-col h-full bg-white">
        <!-- 1. Tabs Pills Tout en Haut -->
        <div class="px-6 pt-5 pb-3 flex items-center gap-2 overflow-x-auto border-b border-slate-100 shrink-0">
            <button @click="activeTab = 'apercu'" :class="activeTab === 'apercu' ? 'bg-orange-500 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-slate-600 font-medium'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all focus:outline-none shrink-0">
                Overview
            </button>
            <button @click="activeTab = 'presence'" :class="activeTab === 'presence' ? 'bg-orange-500 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-slate-600 font-medium'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all focus:outline-none shrink-0">
                Présence
            </button>
            <button @click="activeTab = 'conges'" :class="activeTab === 'conges' ? 'bg-orange-500 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-slate-600 font-medium'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all focus:outline-none shrink-0">
                Congés
            </button>
            <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'bg-orange-500 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-slate-600 font-medium'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all focus:outline-none shrink-0">
                Documents
            </button>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- 2. Header avec Photo Carrée Arrondie + Nom + Actions -->
            <div class="flex gap-4 items-start">
                @if($employe->photo)
                    <img src="{{ asset('storage/' . $employe->photo) }}" alt="" class="w-24 h-24 rounded-2xl object-cover shadow-sm shrink-0">
                @else
                    @php
                        $seed = crc32($employe->nom . $employe->prenom);
                        $palette = ['bg-slate-800', 'bg-indigo-600', 'bg-emerald-600', 'bg-blue-600', 'bg-violet-600'];
                        $avatarClass = $palette[$seed % count($palette)];
                        $initiales = strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1));
                    @endphp
                    <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-xl font-bold text-white shadow-sm shrink-0 {{ $avatarClass }}">
                        {{ $initiales }}
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-slate-900 truncate">{{ $employe->nom }} {{ $employe->prenom }}</h3>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-lime-100 text-lime-700 uppercase">{{ $employe->statut === 'actif' ? 'Actif' : 'Inactif' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="font-medium">Active now</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-400">Visited today</span>
                    </div>

                    <!-- Actions rapides inline -->
                    <div class="mt-3 flex items-center gap-2 text-xs font-semibold">
                        <a href="{{ route('absences.create', ['employe_id' => $employe->id]) }}" class="text-slate-700 hover:text-orange-500 flex items-center gap-1 no-underline">
                            <span class="text-slate-400">+</span> Absence
                        </a>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="isEditing = true" class="text-slate-700 hover:text-orange-500 focus:outline-none">
                            Edit
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. Rangee de Stats à 3 Colonnes -->
            <div class="grid grid-cols-3 gap-2 py-4 border-y border-slate-100 text-left">
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Absences</div>
                    <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $absences->count() }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Solde Congés</div>
                    <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $employe->conges_payes ?? 25 }} jrs</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Heures Sup.</div>
                    <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $employe->heures_recuperation ?? 0 }} h</div>
                </div>
            </div>

            <!-- TAB APERÇU -->
            <div x-show="activeTab === 'apercu'" class="space-y-5">
                <!-- Section Requests -->
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        <h4 class="text-xs font-bold text-slate-800">Requests</h4>
                        <span class="bg-orange-500 text-white text-[9px] font-bold px-1.5 py-0.2 rounded uppercase">New</span>
                    </div>

                    @if($absences->where('statut', 'pending')->count() > 0)
                        @foreach($absences->where('statut', 'pending') as $pendingAbsence)
                            <div class="bg-orange-50/60 border border-orange-100/80 rounded-xl p-3 flex items-center justify-between text-xs mb-2">
                                <span class="font-semibold text-orange-950">{{ ucfirst($pendingAbsence->type) }}</span>
                                <span class="text-orange-600 font-medium">{{ $pendingAbsence->date_debut?->format('d/m') }} — {{ $pendingAbsence->date_fin?->format('d/m') }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-orange-50/50 border border-orange-100/60 rounded-xl p-3.5 flex items-center justify-between text-xs">
                            <span class="font-semibold text-orange-900">Demande de congé annuel</span>
                            <span class="text-orange-600 font-medium">9:00 - 17:00</span>
                        </div>
                    @endif
                </div>

                <!-- Section Next Appointments / Événements -->
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        <h4 class="text-xs font-bold text-slate-800">Next Appointments</h4>
                        <span class="bg-indigo-500 text-white text-[9px] font-bold px-1.5 py-0.2 rounded-full">2</span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="bg-orange-50/50 border border-orange-100/60 rounded-xl p-3.5 flex items-center justify-between">
                            <span class="font-semibold text-orange-900">Entretien d'évaluation annuel</span>
                            <span class="text-orange-600 font-medium">9:00 - 10:00</span>
                        </div>
                        <div class="bg-orange-50/30 border border-orange-100/40 rounded-xl p-3.5 flex items-center justify-between">
                            <span class="font-semibold text-orange-900">Point hebdomadaire équipe</span>
                            <span class="text-orange-600 font-medium">11:00 - 11:30</span>
                        </div>
                    </div>
                </div>

                <!-- Section Notes (Jaune soft) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-bold text-slate-800">Notes</h4>
                        <span class="text-xs font-semibold text-slate-400 hover:text-slate-600 cursor-pointer">+ Add note</span>
                    </div>
                    <div class="bg-amber-100/70 border border-amber-200/50 rounded-xl p-3.5 text-xs text-amber-900 leading-relaxed font-medium">
                        {{ $employe->bio ?? 'Employé très impliqué. Membre actif du département ' . ($employe->departement ?? 'général') . '.' }}
                    </div>
                </div>
            </div>

            <!-- TAB PRÉSENCE -->
            <div x-show="activeTab === 'presence'" class="space-y-4" style="display: none;">
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs space-y-2">
                    <div class="flex justify-between font-semibold text-slate-800">
                        <span>Localisation</span>
                        <span>{{ $employe->localisation ?? 'Siège principal' }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-slate-800">
                        <span>Email</span>
                        <span class="text-indigo-600">{{ $employe->email }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-slate-800">
                        <span>Téléphone</span>
                        <span>{{ $employe->telephone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- TAB CONGÉS -->
            <div x-show="activeTab === 'conges'" class="space-y-3 text-xs" style="display: none;">
                <h4 class="font-bold text-slate-800">Historique complet des congés</h4>
                @forelse($absences as $absence)
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex justify-between items-center">
                        <div>
                            <div class="font-bold text-slate-800">{{ ucfirst($absence->type) }}</div>
                            <div class="text-slate-400 text-[11px]">{{ $absence->date_debut?->format('d/m/Y') }} — {{ $absence->date_fin?->format('d/m/Y') }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $absence->badge_class }}">{{ $absence->statut_label }}</span>
                    </div>
                @empty
                    <div class="p-3 text-slate-400 text-center">Aucune absence enregistrée.</div>
                @endforelse
            </div>

            <!-- TAB DOCUMENTS -->
            <div x-show="activeTab === 'documents'" class="space-y-3 text-xs" style="display: none;">
                <h4 class="font-bold text-slate-800">Fiches de paie & contrats</h4>
                @forelse($employe->salaires as $salaire)
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex justify-between items-center">
                        <span class="font-semibold text-slate-800">Fiche_Paie_{{ $salaire->mois }}_{{ $salaire->annee }}.pdf</span>
                        <a href="{{ route('salaires.pdf', $salaire) }}" class="text-orange-500 font-bold hover:underline">PDF</a>
                    </div>
                @empty
                    <div class="p-3 text-slate-400 text-center">Aucun document archivé.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
