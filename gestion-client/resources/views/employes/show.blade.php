@extends('layouts.app')

@section('title', $employe->prenom . ' ' . $employe->nom)

@section('styles')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandTeal: '#14b8a6',
                        brandEmerald: '#059669',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
@php
    use Carbon\Carbon;

    $ageAnciennete = $employe->date_embauche ? Carbon::parse($employe->date_embauche)->diffInYears(now()) : null;
    $photoUrl = $employe->photo ? asset('storage/' . $employe->photo) : null;
    $initiales = strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1));
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 pb-8 pt-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-b-3xl bg-gradient-to-br from-teal-500 to-emerald-600 px-5 pb-16 pt-4 text-white shadow-lg">
            <div class="relative flex items-center justify-between">
                <a href="{{ route('employes.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-lg font-semibold tracking-wide">Profil</div>
                <div class="w-10"></div>
            </div>

            <div class="mt-8 flex flex-col items-center text-center">
                <div class="relative">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Photo de {{ $employe->prenom }} {{ $employe->nom }}" class="h-32 w-32 rounded-full border-4 border-white object-cover shadow-xl">
                    @else
                        <div class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-white bg-white/20 text-4xl font-bold shadow-xl">
                            {{ $initiales }}
                        </div>
                    @endif
                </div>

                <h1 class="mt-4 text-2xl font-bold">{{ $employe->prenom }} {{ $employe->nom }}</h1>
                <p class="mt-1 text-sm text-white/85">{{ $employe->poste ?? 'Employé' }} · {{ $employe->departement ?? 'Département non défini' }}</p>
            </div>
        </div>

        <div class="-mt-10 rounded-t-3xl bg-white p-5 shadow-lg">
            <div class="grid grid-cols-2 divide-x divide-slate-200 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                <div class="px-4 py-5 text-center">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ancienneté</div>
                    <div class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $ageAnciennete !== null ? $ageAnciennete . ' an' . ($ageAnciennete > 1 ? 's' : '') : 'N/A' }}
                    </div>
                </div>
                <div class="px-4 py-5 text-center">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Statut</div>
                    <div class="mt-2 text-2xl font-bold {{ $employe->statut === 'actif' ? 'text-emerald-600' : 'text-slate-700' }}">
                        {{ ucfirst($employe->statut) }}
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <div class="text-sm text-slate-500">
                    Consultez les informations du profil ci-dessous.
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('employes.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Retour</a>
                    @if(session('user_role') === 'admin')
                        <a href="{{ route('employes.edit', $employe) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Modifier</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="mb-4 flex items-center gap-2 text-slate-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                    </svg>
                    <h5 class="mb-0 text-base font-semibold">Informations personnelles</h5>
                </div>
                <div class="space-y-4 text-sm text-slate-700">
                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Email</div>
                            <div class="font-medium">{{ $employe->email }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.036 3.11a1 1 0 01-.27 1.04l-1.7 1.7a11.042 11.042 0 005.516 5.516l1.7-1.7a1 1 0 011.04-.27l3.11 1.036a1 1 0 01.684.95V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Téléphone</div>
                            <div class="font-medium">{{ $employe->telephone ?? 'Non fourni' }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Date d'embauche</div>
                            <div class="font-medium">{{ $employe->date_embauche?->translatedFormat('d F Y') ?? 'Non fourni' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="mb-4 flex items-center gap-2 text-slate-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z" />
                    </svg>
                    <h5 class="mb-0 text-base font-semibold">Informations professionnelles</h5>
                </div>
                <div class="space-y-4 text-sm text-slate-700">
                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Poste</div>
                            <div class="font-medium">{{ $employe->poste ?? 'Non fourni' }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V7l-7-4-7 4v14h14z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Département</div>
                            <div class="font-medium">{{ $employe->departement ?? 'Non fourni' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="mb-4 flex items-center gap-2 text-slate-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V5m0 14v-3m0 0a7 7 0 100-14 7 7 0 000 14z" />
                    </svg>
                    <h5 class="mb-0 text-base font-semibold">Dates importantes</h5>
                </div>
                <div class="space-y-4 text-sm text-slate-700">
                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Créé le</div>
                            <div class="font-medium">{{ $employe->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="mt-0.5 text-teal-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500">Mise à jour</div>
                            <div class="font-medium">{{ $employe->updated_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
