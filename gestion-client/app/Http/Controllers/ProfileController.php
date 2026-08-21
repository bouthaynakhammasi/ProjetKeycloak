<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $connected = $this->resolveConnectedEmploye();

        if (!$connected) {
            return redirect()->route('dashboard')->with('error', 'Profil introuvable.');
        }

        return redirect()->route('employees.profile.show', ['userId' => $connected->id]);
    }

    public function show(int $userId)
    {
        $connected = $this->resolveConnectedEmploye();
        $target = Employe::find($userId);
        $isAdmin = $this->isAdmin();

        if (!$target) {
            return redirect()->route('employees.index')->with('error', 'Profil introuvable.');
        }

        if (!$isAdmin && !$connected) {
            return redirect()->route('employees.index')->with('error', 'Profil introuvable.');
        }

        $isOwnProfile = $connected ? (int) $connected->id === (int) $target->id : false;

        if (!$isAdmin && !$isOwnProfile) {
            return redirect()->route('employees.index')->with('error', 'Acces refuse.');
        }

        [$checks, $completion] = $this->buildCompletionData($target);

        return view('profile.edit', [
            'employe' => $target,
            'checks' => $checks,
            'completion' => $completion,
            'isOwnProfile' => $isOwnProfile,
            'isAdminViewer' => $isAdmin,
            'connectedEmploye' => $connected,
        ]);
    }

    public function updatePhoto(Request $request, int $userId)
    {
        $target = $this->resolveTargetForUpdate($userId);

        if (!$target) {
            return back()->with('error', 'Profil introuvable.');
        }

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'photo.required' => 'Veuillez selectionner une image avant de valider.',
            'photo.image' => 'Le fichier choisi doit etre une image valide.',
            'photo.mimes' => 'Format non supporte. Utilisez uniquement JPG ou PNG.',
            'photo.max' => 'L\'image ne doit pas depasser 5 Mo.',
        ]);

        if ($target->photo && Storage::disk('public')->exists($target->photo)) {
            Storage::disk('public')->delete($target->photo);
        }

        $path = $validated['photo']->store('profiles', 'public');

        $target->update([
            'photo' => $path,
        ]);

        if ($this->isConnectedEmploye($target)) {
            session(['user_photo' => $path]);
        }

        return back()->with('success', 'Photo de profil mise a jour avec succes.');
    }

    public function updatePersonal(Request $request, int $userId)
    {
        $target = $this->resolveTargetForUpdate($userId);

        if (!$target) {
            return back()->with('error', 'Profil introuvable.');
        }

        $validated = $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employes,email,' . $target->id],
            'telephone' => ['nullable', 'string', 'max:30'],
        ]);

        $target->update($validated);

        if ($this->isConnectedEmploye($target)) {
            session([
                'user_name' => trim($target->prenom . ' ' . $target->nom),
                'user_email' => $target->email,
            ]);
        }

        return back()->with('success', 'Informations personnelles enregistrees avec succes.');
    }

    public function updateLocation(Request $request, int $userId)
    {
        $target = $this->resolveTargetForUpdate($userId);

        if (!$target) {
            return back()->with('error', 'Profil introuvable.');
        }

        $validated = $request->validate([
            'localisation' => ['nullable', 'string', 'max:255'],
        ]);

        $target->update($validated);

        return back()->with('success', 'Localisation enregistree avec succes.');
    }

    public function updateBio(Request $request, int $userId)
    {
        $target = $this->resolveTargetForUpdate($userId);

        if (!$target) {
            return back()->with('error', 'Profil introuvable.');
        }

        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $target->update($validated);

        return back()->with('success', 'Biographie enregistree avec succes.');
    }

    public function deactivate(int $userId)
    {
        if (!$this->isAdmin()) {
            return back()->with('error', 'Acces refuse.');
        }

        $target = Employe::find($userId);
        if (!$target) {
            return back()->with('error', 'Profil introuvable.');
        }

        $target->update(['statut' => 'inactif']);

        return back()->with('success', 'Compte desactive avec succes.');
    }

    public function updateCurrentPhoto(Request $request)
    {
        $connected = $this->resolveConnectedEmploye();
        if (!$connected) {
            return back()->with('error', 'Profil introuvable.');
        }

        return $this->updatePhoto($request, (int) $connected->id);
    }

    public function updateCurrentPersonal(Request $request)
    {
        $connected = $this->resolveConnectedEmploye();
        if (!$connected) {
            return back()->with('error', 'Profil introuvable.');
        }

        return $this->updatePersonal($request, (int) $connected->id);
    }

    public function updateCurrentLocation(Request $request)
    {
        $connected = $this->resolveConnectedEmploye();
        if (!$connected) {
            return back()->with('error', 'Profil introuvable.');
        }

        return $this->updateLocation($request, (int) $connected->id);
    }

    public function updateCurrentBio(Request $request)
    {
        $connected = $this->resolveConnectedEmploye();
        if (!$connected) {
            return back()->with('error', 'Profil introuvable.');
        }

        return $this->updateBio($request, (int) $connected->id);
    }

    private function resolveConnectedEmploye(): ?Employe
    {
        $userId = session('user_id');
        $email = session('user_email');

        $employe = null;

        if (!empty($userId)) {
            $employe = Employe::where('keycloak_id', $userId)->first();
        }

        if (!$employe && !empty($email)) {
            $employe = Employe::where('email', $email)->first();
        }

        return $employe;
    }

    private function resolveTargetForUpdate(int $userId): ?Employe
    {
        $connected = $this->resolveConnectedEmploye();
        $target = Employe::find($userId);

        if (!$target) {
            return null;
        }

        if ($this->isAdmin()) {
            return $target;
        }

        if (!$connected) {
            return null;
        }

        if (!$this->isAdmin() && !$this->isConnectedEmploye($target)) {
            return null;
        }

        return $target;
    }

    private function isConnectedEmploye(Employe $employe): bool
    {
        $connected = $this->resolveConnectedEmploye();

        return $connected && (int) $connected->id === (int) $employe->id;
    }

    private function isAdmin(): bool
    {
        return in_array(session('user_role'), ['ROLE_ADMIN', 'admin'], true);
    }

    private function buildCompletionData(Employe $employe): array
    {
        $checks = [
            [
                'label' => 'Configurer le compte',
                'weight' => 10,
                'done' => !empty($employe->keycloak_id),
            ],
            [
                'label' => 'Uploader la photo',
                'weight' => 15,
                'done' => !empty($employe->photo),
            ],
            [
                'label' => 'Infos personnelles',
                'weight' => 25,
                'done' => !empty($employe->nom) && !empty($employe->prenom) && !empty($employe->email) && !empty($employe->telephone),
            ],
            [
                'label' => 'Localisation',
                'weight' => 15,
                'done' => !empty($employe->localisation),
            ],
            [
                'label' => 'Biographie',
                'weight' => 15,
                'done' => !empty($employe->bio),
            ],
            [
                'label' => 'Notifications',
                'weight' => 10,
                'done' => (bool) $employe->notifications_actives,
            ],
            [
                'label' => 'Coordonnees bancaires',
                'weight' => 10,
                'done' => !empty($employe->coordonnees_bancaires),
            ],
        ];

        $completion = collect($checks)
            ->filter(fn ($item) => $item['done'])
            ->sum('weight');

        return [$checks, $completion];
    }
}
