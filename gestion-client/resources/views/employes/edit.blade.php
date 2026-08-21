@extends('layouts.app')

@section('title', 'Modifier ' . $employe->prenom . ' ' . $employe->nom)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Modifier l'employé</h1>
            <p class="text-muted">{{ $employe->prenom }} {{ $employe->nom }}</p>
        </div>
        <a href="{{ route('employes.show', $employe) }}" class="btn btn-secondary">Retour</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('employes.update', $employe) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom', $employe->nom) }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                               id="prenom" name="prenom" value="{{ old('prenom', $employe->prenom) }}" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $employe->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                               id="telephone" name="telephone" value="{{ old('telephone', $employe->telephone) }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="poste" class="form-label">Poste</label>
                        <input type="text" class="form-control @error('poste') is-invalid @enderror" 
                               id="poste" name="poste" value="{{ old('poste', $employe->poste) }}">
                        @error('poste')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="departement" class="form-label">Département</label>
                        <input type="text" class="form-control @error('departement') is-invalid @enderror" 
                               id="departement" name="departement" value="{{ old('departement', $employe->departement) }}">
                        @error('departement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_embauche" class="form-label">Date d'embauche</label>
                        <input type="date" class="form-control @error('date_embauche') is-invalid @enderror" 
                               id="date_embauche" name="date_embauche" value="{{ old('date_embauche', $employe->date_embauche?->format('Y-m-d')) }}">
                        @error('date_embauche')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="statut" class="form-label">Statut *</label>
                        <select class="form-select @error('statut') is-invalid @enderror" 
                                id="statut" name="statut" required>
                            <option value="">Sélectionner un statut</option>
                            <option value="actif" {{ old('statut', $employe->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut', $employe->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Photo actuelle</label>
                    <div>
                        @if($employe->photo)
                            <img src="{{ asset('storage/' . $employe->photo) }}" alt="Photo de {{ $employe->prenom }} {{ $employe->nom }}"
                                 style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary text-white"
                                 style="width: 96px; height: 96px; border-radius: 50%;">
                                {{ strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Nouvelle photo</label>
                    <input type="file" class="form-control @error('photo') is-invalid @enderror"
                           id="photo" name="photo" accept="image/*">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="{{ route('employes.show', $employe) }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
