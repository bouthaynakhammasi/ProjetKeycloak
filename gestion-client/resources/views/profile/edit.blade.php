@extends('layouts.app')

@section('title', 'bellent HR - Modifier le profil')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Modifier le profil</h1>
            <p class="text-sm text-gray-500 mt-1">Mettez a jour vos informations personnelles et suivez votre progression.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">Une erreur est survenue pendant la sauvegarde.</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col xl:flex-row gap-6">
        <div class="w-full xl:w-[68%] flex flex-col gap-6">
            @if(($isAdminViewer ?? false) && !($isOwnProfile ?? true))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Actions Admin</h2>
                            <p class="text-sm text-gray-500 mt-1">Gestion du compte employé affiché.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('absences.index', ['employe_id' => $employe->id]) }}" class="inline-flex items-center justify-center border border-gray-200 bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 no-underline">
                                Historique absences
                            </a>
                            <a href="{{ route('salaires.index', ['employe_id' => $employe->id]) }}" class="inline-flex items-center justify-center border border-gray-200 bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 no-underline">
                                Historique paie
                            </a>
                            <form action="{{ route('employees.profile.deactivate', ['userId' => $employe->id]) }}" method="POST" onsubmit="return confirm('Confirmer la désactivation du compte ?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors shadow-sm">
                                    Désactiver le compte
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-5">Photo de profil</h2>
                <div class="flex flex-col md:flex-row md:items-center gap-5">
                    <div class="shrink-0">
                        @if($employe->photo)
                            <img src="{{ asset('storage/' . $employe->photo) }}" alt="Photo de profil" class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employe->nom_complet ?: (session('user_name') ?? 'Utilisateur')) }}&background=6366f1&color=ffffff'">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($employe->nom_complet ?: (session('user_name') ?? 'Utilisateur')) }}&background=6366f1&color=ffffff" alt="Photo de profil" class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-sm">
                        @endif
                    </div>
                    <form action="{{ route('employees.profile.photo.update', ['userId' => $employe->id]) }}" method="POST" enctype="multipart/form-data" class="flex-1" data-loading-form>
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="sr-only" required data-photo-input id="profile-photo-input">
                            <label for="profile-photo-input" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 cursor-pointer">
                                Choisir un fichier
                            </label>
                            <button type="submit" class="inline-flex items-center justify-center bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm min-w-[190px]" data-submit-label="Uploader une nouvelle photo">
                                Uploader une nouvelle photo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-gray-900">Informations personnelles</h2>
                    @if(($isOwnProfile ?? false) || ($isAdminViewer ?? false))
                        <button type="button" class="text-sm font-medium text-primary hover:text-primary/80" data-edit-toggle="personal-section">Modifier</button>
                    @endif
                </div>
                <form action="{{ route('employees.profile.personal.update', ['userId' => $employe->id]) }}" method="POST" id="personal-section" data-loading-form>
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Nom complet</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="prenom" value="{{ old('prenom', $employe->prenom) }}" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20" readonly>
                                <input type="text" name="nom" value="{{ old('nom', $employe->nom) }}" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20" readonly>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employe->email) }}" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Telephone</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $employe->telephone) }}" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20" readonly>
                        </div>
                    </div>
                    <div class="mt-4 hidden items-center gap-3" data-edit-actions>
                        <button type="submit" class="inline-flex items-center justify-center bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm" data-submit-label="Enregistrer les informations">
                            Enregistrer les informations
                        </button>
                        <button type="button" class="inline-flex items-center justify-center border border-gray-200 bg-white text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50" data-cancel-edit>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-5">Localisation</h2>
                <form action="{{ route('employees.profile.location.update', ['userId' => $employe->id]) }}" method="POST" data-loading-form>
                    @csrf
                    <input type="text" name="localisation" value="{{ old('localisation', $employe->localisation) }}" placeholder="Ville, pays ou adresse" class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <div class="mt-4 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm" data-submit-label="Enregistrer">
                            Enregistrer
                        </button>
                        <button type="reset" class="inline-flex items-center justify-center border border-gray-200 bg-white text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-gray-900">Bio</h2>
                    @if(($isOwnProfile ?? false) || ($isAdminViewer ?? false))
                        <button type="button" class="text-sm font-medium text-primary hover:text-primary/80" data-edit-toggle="bio-section">Modifier</button>
                    @endif
                </div>
                <form action="{{ route('employees.profile.bio.update', ['userId' => $employe->id]) }}" method="POST" id="bio-section" data-loading-form>
                    @csrf
                    <textarea name="bio" rows="5" class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 resize-y" placeholder="Parlez un peu de vous" readonly>{{ old('bio', $employe->bio) }}</textarea>
                    <div class="mt-4 hidden items-center gap-3" data-edit-actions>
                        <button type="submit" class="inline-flex items-center justify-center bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm" data-submit-label="Enregistrer la bio">
                            Enregistrer la bio
                        </button>
                        <button type="button" class="inline-flex items-center justify-center border border-gray-200 bg-white text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50" data-cancel-edit>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="w-full xl:w-[32%]">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Completer votre profil</h2>

                <div class="flex items-center gap-4 mb-5">
                    <div class="relative w-20 h-20">
                        <svg class="w-20 h-20 -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                            <circle cx="50" cy="50" r="42" class="text-gray-200" stroke="currentColor" stroke-width="9" fill="none"></circle>
                            <circle cx="50" cy="50" r="42" class="text-primary" stroke="currentColor" stroke-width="9" fill="none" stroke-linecap="round" stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $completion / 100) }}"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-sm font-semibold text-gray-900">{{ $completion }}%</div>
                    </div>
                    <p class="text-sm text-gray-500">Votre profil est complete a {{ $completion }}%.</p>
                </div>

                <div class="space-y-2.5">
                    @foreach($checks as $check)
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <div class="flex items-center gap-2">
                                @if($check['done'])
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-50 text-green-600">✓</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-50 text-red-600">✗</span>
                                @endif
                                <span class="{{ $check['done'] ? 'text-gray-900' : 'text-gray-500' }}">{{ $check['label'] }}</span>
                            </div>
                            <span class="text-xs {{ $check['done'] ? 'text-green-600' : 'text-gray-400' }}">+{{ $check['weight'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-edit-toggle]').forEach(function (toggleButton) {
            toggleButton.addEventListener('click', function () {
                const formId = toggleButton.getAttribute('data-edit-toggle');
                const form = document.getElementById(formId);
                if (!form) {
                    return;
                }

                const fields = form.querySelectorAll('input, textarea');
                fields.forEach(function (field) {
                    field.removeAttribute('readonly');
                    field.classList.remove('bg-gray-50');
                    field.classList.add('bg-white');
                });

                const actions = form.querySelector('[data-edit-actions]');
                if (actions) {
                    actions.classList.remove('hidden');
                    actions.classList.add('flex');
                }
            });
        });

        document.querySelectorAll('[data-cancel-edit]').forEach(function (cancelButton) {
            cancelButton.addEventListener('click', function () {
                const form = cancelButton.closest('form');
                if (!form) {
                    return;
                }

                form.reset();

                const fields = form.querySelectorAll('input, textarea');
                fields.forEach(function (field) {
                    field.setAttribute('readonly', 'readonly');
                    field.classList.add('bg-gray-50');
                    field.classList.remove('bg-white');
                });

                const actions = form.querySelector('[data-edit-actions]');
                if (actions) {
                    actions.classList.add('hidden');
                    actions.classList.remove('flex');
                }
            });
        });

        document.querySelectorAll('[data-loading-form]').forEach(function (form) {
            form.addEventListener('submit', function () {
                const submitButton = form.querySelector('button[type="submit"]');
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.classList.add('opacity-70', 'cursor-not-allowed');

                const label = submitButton.getAttribute('data-submit-label') || submitButton.innerText;
                submitButton.innerText = 'Enregistrement...';

                form.dataset.originalSubmitLabel = label;
            });
        });
    });
</script>
@endsection
