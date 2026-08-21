@extends('layouts.app')

@section('title', 'Gestion des comptes - Admin')

@section('styles')
<style>
    @keyframes fadeIn {
        0%  { opacity: 0; transform: translateY(8px); }
        100%{ opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
</style>
@endsection

@section('content')
<div class="p-6 animate-fadeIn">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-blue-200">Gestion RH</p>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-white">Gestion des comptes utilisateurs</h1>
            <p class="mt-2 text-base text-blue-100 opacity-90">Validez, attribuez des rôles ou rejetez les demandes d'accès</p>
        </div>
        <div class="flex items-center gap-3">
            @php
                $totalPending  = $pendingUsers->count();
                $totalActive   = $activeUsers->count();
                $totalRejected = $rejectedUsers->count();
            @endphp
            @if($totalPending > 0)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 px-3 py-1.5 text-sm font-semibold text-white shadow-lg">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                {{ $totalPending }} en attente
            </span>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500 px-3 py-1.5 text-sm font-semibold text-white shadow-lg">
                {{ $totalActive }} actifs
            </span>
            @if($totalRejected > 0)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500 px-3 py-1.5 text-sm font-semibold text-white shadow-lg">
                {{ $totalRejected }} rejetés
            </span>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-green-500 shrink-0"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ===== COMPTES EN ATTENTE ===== --}}
    <section class="mb-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
            </div>
            <h2 class="text-base font-semibold text-gray-900">Comptes en attente de validation</h2>
            @if($totalPending > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-amber-500 text-white rounded-full">{{ $totalPending }}</span>
            @endif
        </div>

        @if($pendingUsers->isEmpty())
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-dashed border-green-200 p-6 text-center">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-green-500"></i>
                </div>
                <p class="text-base font-semibold text-green-800">Aucun compte en attente</p>
                <p class="text-sm text-green-600 mt-2">Tous les comptes ont été traités</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @foreach($pendingUsers as $user)
                        <div class="flex items-center gap-4 p-3 hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent transition-all duration-200 group">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->prenom . ' ' . $user->nom) }}&background=f59e0b&color=fff&size=64"
                                 class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-amber-100 group-hover:ring-amber-200 transition-all" alt="{{ $user->prenom }} {{ $user->nom }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->prenom }} {{ $user->nom }}</p>
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                            </div>
                            <div class="text-xs text-gray-400 hidden md:block w-36 shrink-0">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button onclick="openValidateModal('{{ $user->id }}', '{{ addslashes($user->prenom . ' ' . $user->nom) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 text-white rounded-lg text-xs font-semibold hover:bg-green-600 hover:shadow-md transition-all duration-200">
                                    <i data-lucide="user-check" class="w-3 h-3"></i> Valider
                                </button>
                                <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 hover:shadow-md transition-all duration-200"
                                        onclick="return confirm('Rejeter le compte de {{ addslashes($user->prenom . ' ' . $user->nom) }} ? Cette action est irréversible.')">
                                        <i data-lucide="x" class="w-3 h-3"></i> Refuser
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ===== COMPTES ACTIFS ===== --}}
    <section class="mb-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-green-600"></i>
            </div>
            <h2 class="text-base font-semibold text-gray-900">Comptes actifs</h2>
            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-green-500 text-white rounded-full">{{ $totalActive }}</span>
        </div>

        @if($activeUsers->isEmpty())
            <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl border-2 border-dashed border-gray-200 p-6 text-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="w-5 h-5 text-gray-400"></i>
                </div>
                <p class="text-base font-semibold text-gray-700">Aucun compte actif pour le moment.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @foreach($activeUsers as $user)
                        <div class="flex items-center gap-4 p-3 hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent transition-all duration-200 group {{ $user->status === 'inactive' ? 'opacity-50' : '' }}">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->status === 'inactive' ? '9ca3af' : '10b981' }}&color=fff&size=64"
                                 class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-green-100 group-hover:ring-green-200 transition-all" alt="{{ $user->name }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <span id="role-badge-{{ $user->id }}">
                                    @if($user->role === 'ROLE_ADMIN')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-500 text-white rounded-full text-xs font-semibold shadow-sm">
                                            <i data-lucide="shield" class="w-3 h-3"></i> Administrateur
                                        </span>
                                    @elseif($user->role === 'ROLE_CLIENT')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-500 text-white rounded-full text-xs font-semibold shadow-sm">
                                            <i data-lucide="briefcase" class="w-3 h-3"></i> Client
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-500 text-white rounded-full text-xs font-semibold shadow-sm">
                                            <i data-lucide="user" class="w-3 h-3"></i> Employé
                                        </span>
                                    @endif
                                </span>
                                <button onclick="openRoleModal('{{ $user->id }}', '{{ $user->role ?? 'ROLE_EMPLOYEE' }}')"
                                    class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center" title="Modifier le rôle">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <span id="status-badge-{{ $user->id }}">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-500 text-white rounded-full text-xs font-semibold shadow-sm">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i> Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-400 text-white rounded-full text-xs font-semibold shadow-sm">
                                            <i data-lucide="x-circle" class="w-3 h-3"></i> Inactif
                                        </span>
                                    @endif
                                </span>
                                <button onclick="toggleStatus('{{ $user->id }}')"
                                    class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center" title="{{ $user->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                    <i data-lucide="{{ $user->status === 'active' ? 'toggle-left' : 'toggle-right' }}" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            <div class="text-xs text-gray-400 hidden md:block w-36 shrink-0">
                                Activé le {{ $user->activated_at ? $user->activated_at->format('d/m/Y') : '-' }}
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center" title="Voir détails">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-amber-500 hover:text-white transition-all duration-200 flex items-center justify-center"
                                        title="Réinitialiser le mot de passe"
                                        onclick="return confirm('Réinitialiser le mot de passe de {{ addslashes($user->name) }} ?')">
                                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center"
                                        title="Supprimer"
                                        onclick="return confirm('Supprimer le compte de {{ addslashes($user->name) }} ? Cette action est irréversible.')">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ===== COMPTES REJETÉS ===== --}}
    <section>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                <i data-lucide="x-circle" class="w-3.5 h-3.5 text-red-600"></i>
            </div>
            <h2 class="text-base font-semibold text-gray-900">Comptes rejetés</h2>
            @if($totalRejected > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-red-500 text-white rounded-full">{{ $totalRejected }}</span>
            @endif
        </div>

        @if($rejectedUsers->isEmpty())
            <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl border-2 border-dashed border-red-200 p-6 text-center">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-400"></i>
                </div>
                <p class="text-base font-semibold text-red-700">Aucun compte rejeté.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @foreach($rejectedUsers as $user)
                        <div class="flex items-center gap-4 p-3 hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent transition-all duration-200 group opacity-75">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ef4444&color=fff&size=64"
                                 class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-red-100 group-hover:ring-red-200 transition-all grayscale" alt="{{ $user->name }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-700 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400 truncate mt-0.5">{{ $user->email }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-500 text-white rounded-full text-xs font-semibold shadow-sm shrink-0">
                                <i data-lucide="ban" class="w-3 h-3"></i> Rejeté
                            </span>
                            <div class="shrink-0">
                                <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 text-white rounded-lg text-xs font-semibold hover:bg-amber-600 hover:shadow-md transition-all duration-200">
                                        <i data-lucide="refresh-cw" class="w-3 h-3"></i> Réactiver
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

</div>

{{-- Modale de validation --}}
<div id="validateModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-fadeIn">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Valider le compte</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Vous allez valider le compte de <span id="validateUserName" class="font-semibold text-gray-900"></span>.
                Choisissez le rôle à attribuer :
            </p>
            <form method="POST" action="" id="validateForm">
                @csrf
                <input type="hidden" name="role" id="selectedRole" value="ROLE_EMPLOYEE">
                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="role_select" value="ROLE_EMPLOYEE" checked
                            class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                            onchange="document.getElementById('selectedRole').value = 'ROLE_EMPLOYEE'">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">Employé</span>
                            <p class="text-xs text-gray-500">Accès aux fonctionnalités employé</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="role_select" value="ROLE_ADMIN"
                            class="w-4 h-4 text-primary border-gray-300 focus:ring-primary"
                            onchange="document.getElementById('selectedRole').value = 'ROLE_ADMIN'">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">Administrateur</span>
                            <p class="text-xs text-gray-500">Accès complet à l'administration</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="role_select" value="ROLE_CLIENT"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-600"
                            onchange="document.getElementById('selectedRole').value = 'ROLE_CLIENT'">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">Client</span>
                            <p class="text-xs text-gray-500">Accès client limité</p>
                        </div>
                    </label>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeValidateModal()"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Real-time updates with Laravel Echo
if (typeof Echo !== 'undefined') {
    Echo.channel('admin-users')
        .listen('.user.registered', (e) => {
            console.log('New user registered:', e);
            addPendingUserToTable(e);
            updateCounts();
        })
        .listen('.user.validated', (e) => {
            console.log('User validated:', e);
            removePendingUserFromTable(e.email);
            addActiveUserToTable(e);
            updateCounts();
        })
        .listen('.user.rejected', (e) => {
            console.log('User rejected:', e);
            removePendingUserFromTable(e.email);
            addRejectedUserToTable(e);
            updateCounts();
        });
}

// Fonction pour ajouter un utilisateur en attente au tableau
function addPendingUserToTable(user) {
    const pendingTable = document.querySelector('.divide-y.divide-gray-50');
    if (!pendingTable) return;

    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-4 p-3 hover:bg-gray-50/50 transition-colors animate-fadeIn';
    newRow.dataset.email = user.email;
    newRow.innerHTML = `
        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.prenom + ' ' + user.nom)}&background=f59e0b&color=fff&size=64"
             class="w-9 h-9 rounded-full object-cover shrink-0" alt="${user.prenom} ${user.nom}">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 truncate">${user.prenom} ${user.nom}</p>
            <p class="text-xs text-gray-500 truncate">${user.email}</p>
        </div>
        <div class="text-xs text-gray-400 hidden md:block w-36 shrink-0">
            ${new Date(user.created_at).toLocaleString('fr-FR')}
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="openValidateModal('${user.id}', '${(user.prenom + ' ' + user.nom).replace(/'/g, "\\'")}')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-semibold hover:bg-green-100 transition-colors">
                <i data-lucide="user-check" class="w-3 h-3"></i> Valider
            </button>
            <form method="POST" action="/admin/users/${user.id}/reject" class="inline-block">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors"
                    onclick="return confirm('Rejeter le compte de ${(user.prenom + ' ' + user.nom).replace(/'/g, "\\'")} ? Cette action est irréversible.')">
                    <i data-lucide="x" class="w-3 h-3"></i> Refuser
                </button>
            </form>
        </div>
    `;

    pendingTable.insertBefore(newRow, pendingTable.firstChild);
    
    // Re-initialiser les icônes Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Fonction pour supprimer un utilisateur en attente du tableau
function removePendingUserFromTable(email) {
    const pendingTable = document.querySelector('.divide-y.divide-gray-50');
    if (!pendingTable) return;

    const row = pendingTable.querySelector(`[data-email="${email}"]`);
    if (row) {
        row.remove();
    }
}

// Fonction pour ajouter un utilisateur actif au tableau
function addActiveUserToTable(user) {
    const activeTable = document.querySelectorAll('.divide-y.divide-gray-50')[1];
    if (!activeTable) return;

    const roleLabel = user.role === 'ROLE_ADMIN' ? 'Administrateur' : 
                      user.role === 'ROLE_CLIENT' ? 'Client' : 'Employé';
    
    const roleClass = user.role === 'ROLE_ADMIN' ? 'bg-primary/10 text-primary border-primary/20' :
                      user.role === 'ROLE_CLIENT' ? 'bg-blue-50 text-blue-700 border-blue-200' :
                      'bg-green-50 text-green-700 border-green-200';
    
    const roleIcon = user.role === 'ROLE_ADMIN' ? 'shield' :
                     user.role === 'ROLE_CLIENT' ? 'briefcase' : 'user';

    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-4 p-3 hover:bg-gray-50/50 transition-colors animate-fadeIn';
    newRow.dataset.email = user.email;
    newRow.innerHTML = `
        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=10b981&color=fff&size=64"
             class="w-9 h-9 rounded-full object-cover shrink-0" alt="${user.name}">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 truncate">${user.name}</p>
            <p class="text-xs text-gray-500 truncate">${user.email}</p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 ${roleClass} rounded-full text-xs font-semibold">
                <i data-lucide="${roleIcon}" class="w-3 h-3"></i> ${roleLabel}
            </span>
            <button onclick="openRoleModal('${user.id}', '${user.role ?? 'ROLE_EMPLOYEE'}')"
                class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center" title="Modifier le rôle">
                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                <i data-lucide="check-circle" class="w-3 h-3"></i> Actif
            </span>
            <button onclick="toggleStatus('${user.id}')"
                class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center" title="Désactiver">
                <i data-lucide="toggle-left" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        <div class="text-xs text-gray-400 hidden md:block w-36 shrink-0">
            Activé le ${new Date(user.activated_at).toLocaleDateString('fr-FR')}
        </div>
        <div class="flex items-center gap-1 shrink-0">
            <a href="/admin/users/${user.id}"
                class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center" title="Voir détails">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
            </a>
            <form method="POST" action="/admin/users/${user.id}/reset-password" class="inline-block">
                @csrf
                <button type="submit"
                    class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-amber-500 hover:text-white transition-all duration-200 flex items-center justify-center"
                title="Réinitialiser le mot de passe"
                onclick="return confirm('Réinitialiser le mot de passe de ${user.name.replace(/'/g, "\\'")} ?')">
                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                </button>
            </form>
            <form method="POST" action="/admin/users/${user.id}" class="inline-block">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit"
                    class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center"
                title="Supprimer"
                onclick="return confirm('Supprimer le compte de ${user.name.replace(/'/g, "\\'")} ? Cette action est irréversible.')">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                </button>
            </form>
        </div>
    `;

    activeTable.insertBefore(newRow, activeTable.firstChild);
    
    // Re-initialiser les icônes Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Fonction pour ajouter un utilisateur rejeté au tableau
function addRejectedUserToTable(user) {
    const rejectedTable = document.querySelectorAll('.divide-y.divide-gray-50')[2];
    if (!rejectedTable) return;

    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-4 p-4 hover:bg-gray-50/50 transition-colors opacity-75 animate-fadeIn';
    newRow.dataset.email = user.email;
    newRow.innerHTML = `
        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=ef4444&color=fff&size=64"
             class="w-10 h-10 rounded-full object-cover shrink-0 grayscale" alt="${user.name}">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-700 truncate">${user.name}</p>
            <p class="text-xs text-gray-400 truncate">${user.email}</p>
        </div>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 border border-red-200 rounded-full text-xs font-semibold shrink-0">
            <i data-lucide="ban" class="w-3 h-3"></i> Rejeté
        </span>
        <div class="shrink-0">
            <form method="POST" action="/admin/users/${user.id}/reactivate" class="inline-block">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-semibold hover:bg-amber-100 transition-colors">
                    <i data-lucide="refresh-cw" class="w-3 h-3"></i> Réactiver
                </button>
            </form>
        </div>
    `;

    rejectedTable.insertBefore(newRow, rejectedTable.firstChild);
    
    // Re-initialiser les icônes Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Fonction pour mettre à jour les compteurs
function updateCounts() {
    const pendingCount = document.querySelectorAll('.divide-y.divide-gray-50')[0]?.children.length || 0;
    const activeCount = document.querySelectorAll('.divide-y.divide-gray-50')[1]?.children.length || 0;
    const rejectedCount = document.querySelectorAll('.divide-y.divide-gray-50')[2]?.children.length || 0;

    // Mettre à jour les badges
    const pendingBadge = document.querySelector('.bg-amber-50.text-amber-700');
    if (pendingBadge) {
        pendingBadge.innerHTML = `<span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> ${pendingCount} en attente`;
    }

    const activeBadge = document.querySelector('.bg-green-50.text-green-700');
    if (activeBadge) {
        activeBadge.textContent = `${activeCount} actifs`;
    }

    const rejectedBadge = document.querySelector('.bg-red-50.text-red-700');
    if (rejectedBadge) {
        rejectedBadge.textContent = `${rejectedCount} rejetés`;
    }
}

function openValidateModal(userId, userName) {
    document.getElementById('validateUserName').textContent = userName;
    document.getElementById('validateForm').action = '/admin/users/' + userId + '/validate';
    document.getElementById('validateModal').classList.remove('hidden');
    document.getElementById('validateModal').classList.add('flex');
}

function closeValidateModal() {
    document.getElementById('validateModal').classList.add('hidden');
    document.getElementById('validateModal').classList.remove('flex');
}

function openRoleModal(userId, currentRole) {
    document.getElementById('currentRole').value = currentRole;
    document.getElementById('roleForm').action = '/admin/users/' + userId + '/update-role';
    
    // Sélectionner le rôle actuel
    const radios = document.getElementsByName('role');
    radios.forEach(radio => {
        radio.checked = (radio.value === currentRole);
    });
    
    document.getElementById('roleModal').classList.remove('hidden');
    document.getElementById('roleModal').classList.add('flex');
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
    document.getElementById('roleModal').classList.remove('flex');
}

function toggleStatus(userId) {
    if (confirm('Êtes-vous sûr de vouloir changer le statut de ce compte ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/users/' + userId + '/toggle-status';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

{{-- Modale de modification de rôle --}}
<div id="roleModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-fadeIn">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <i data-lucide="edit-2" class="w-5 h-5 text-primary"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Modifier le rôle</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Choisissez le nouveau rôle pour cet utilisateur :</p>
            <form method="POST" action="{{ route('admin.users.update-role', ':id') }}" id="roleForm">
                @csrf
                <input type="hidden" name="current_role" id="currentRole" value="">
                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="role" value="ROLE_EMPLOYEE"
                            class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">Employé</span>
                            <p class="text-xs text-gray-500">Accès aux fonctionnalités employé</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="role" value="ROLE_ADMIN"
                            class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">Administrateur</span>
                            <p class="text-xs text-gray-500">Accès complet à l'administration</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="role" value="ROLE_CLIENT"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-600">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">Client</span>
                            <p class="text-xs text-gray-500">Accès client limité</p>
                        </div>
                    </label>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRoleModal()"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
