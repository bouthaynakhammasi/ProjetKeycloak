<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class EmployeController extends Controller
{
    protected function authorizeAction(string $action, ?Employe $employe = null)
    {
        $role = session('user_role');
        $isAdmin = in_array($role, ['admin', 'ROLE_ADMIN'], true);
        $isEmployee = in_array($role, ['employe', 'ROLE_EMPLOYEE'], true);
        $isClient = in_array($role, ['client', 'ROLE_CLIENT'], true);

        if ($action === 'viewAny') {
            // Tous les rôles authentifiés peuvent voir la liste
            return $isAdmin || $isEmployee || $isClient;
        }

        if (in_array($action, ['create', 'store', 'edit', 'update', 'destroy'], true)) {
            // Seuls les admins peuvent créer, modifier, supprimer
            return $isAdmin;
        }

        if ($action === 'show') {
            if ($isAdmin) {
                return true;
            }

            // Les employés peuvent voir leur propre fiche
            if ($isEmployee && $employe) {
                return $employe->keycloak_id === session('user_id');
            }

            // Les clients peuvent accéder aux infos publiques
            if ($isClient) {
                return true;
            }
        }

        return false;
    }

    protected function denyAccess()
    {
        return Redirect::to('/dashboard')->with('error', 'Accès refusé : vous n\'êtes pas autorisé à réaliser cette action.');
    }

    public function index(Request $request)
    {
        if (! $this->authorizeAction('viewAny')) {
            return $this->denyAccess();
        }

        $query = Employe::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($name = $request->query('name')) {
            $query->where(function ($q) use ($name) {
                $q->where('nom', 'like', "%{$name}%")
                    ->orWhere('prenom', 'like', "%{$name}%");
            });
        }

        if ($email = $request->query('email')) {
            $query->where('email', 'like', "%{$email}%");
        }

        if ($departement = $request->query('departement')) {
            $query->where('departement', 'like', "%{$departement}%");
        }

        if ($statut = $request->query('statut')) {
            $query->where('statut', $statut);
        }

        $employes = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $connectedEmploye = null;
        if (session('user_id')) {
            $connectedEmploye = Employe::where('keycloak_id', session('user_id'))->first();
        }
        if (!$connectedEmploye && session('user_email')) {
            $connectedEmploye = Employe::where('email', session('user_email'))->first();
        }

        return view('employes.index', [
            'employes' => $employes,
            'search' => $search,
            'name' => $request->query('name'),
            'email' => $request->query('email'),
            'departement' => $departement,
            'statut' => $statut,
            'connectedEmploye' => $connectedEmploye,
        ]);
    }

    public function panel(Employe $employe)
    {
        if (! $this->authorizeAction('show', $employe)) {
            abort(403, 'Accès refusé');
        }

        $employe->load(['salaires', 'primes', 'retenues']);
        $absences = \App\Models\Absence::where('employe_id', $employe->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('employes.partials.panel', [
            'employe' => $employe,
            'absences' => $absences,
        ]);
    }

    public function create()
    {
        if (! $this->authorizeAction('create')) {
            return $this->denyAccess();
        }

        return view('employes.create');
    }

    public function store(StoreEmployeRequest $request)
    {
        if (! $this->authorizeAction('store')) {
            return $this->denyAccess();
        }

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employes', 'public');
        }

        Employe::create($data);

        return redirect()->route('employes.index')->with('success', 'Employé créé avec succès.');
    }

    public function show(Employe $employe)
    {
        if (! $this->authorizeAction('show', $employe)) {
            return $this->denyAccess();
        }

        return view('employes.show', [
            'employe' => $employe,
        ]);
    }

    public function edit(Employe $employe)
    {
        if (! $this->authorizeAction('edit')) {
            return $this->denyAccess();
        }

        return view('employes.edit', [
            'employe' => $employe,
        ]);
    }

    public function update(UpdateEmployeRequest $request, Employe $employe)
    {
        if (! $this->authorizeAction('update')) {
            return $this->denyAccess();
        }

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($employe->photo) {
                Storage::disk('public')->delete($employe->photo);
            }

            $data['photo'] = $request->file('photo')->store('employes', 'public');
        }

        $employe->update($data);

        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('employes.partials.panel', ['employe' => $employe]);
        }

        return redirect()->route('employes.show', $employe)->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(Employe $employe)
    {
        if (! $this->authorizeAction('destroy')) {
            return $this->denyAccess();
        }

        $employe->delete();

        return redirect()->route('employes.index')->with('success', 'Employé supprimé avec succès.');
    }
}
