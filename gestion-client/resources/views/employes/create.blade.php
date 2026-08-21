@extends('layouts.app')

@section('title', 'Créer un employé')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Créer un nouvel employé</h1>
            <p class="text-muted">Remplissez le formulaire ci-dessous pour ajouter un employé.</p>
        </div>
        <a href="{{ route('employes.index') }}" class="btn btn-secondary">Retour à la liste</a>
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
            <form action="{{ route('employes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                               id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                               id="telephone" name="telephone" value="{{ old('telephone') }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="poste" class="form-label">Poste</label>
                        <input type="text" class="form-control @error('poste') is-invalid @enderror" 
                               id="poste" name="poste" value="{{ old('poste') }}">
                        @error('poste')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="departement" class="form-label">Département</label>
                        <input type="text" class="form-control @error('departement') is-invalid @enderror" 
                               id="departement" name="departement" value="{{ old('departement') }}">
                        @error('departement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_embauche" class="form-label">Date d'embauche</label>
                        <input type="date" class="form-control @error('date_embauche') is-invalid @enderror" 
                               id="date_embauche" name="date_embauche" value="{{ old('date_embauche') }}">
                        @error('date_embauche')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="statut" class="form-label">Statut *</label>
                        <select class="form-select @error('statut') is-invalid @enderror" 
                                id="statut" name="statut" required>
                            <option value="">Sélectionner un statut</option>
                            <option value="actif" {{ old('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" class="form-control @error('photo') is-invalid @enderror"
                           id="photo" name="photo" accept="image/*">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Créer l'employé</button>
                        <a href="{{ route('employes.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
