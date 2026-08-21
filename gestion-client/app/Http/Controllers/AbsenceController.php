<?php

namespace App\Http\Controllers;

use App\Enums\AbsenceStatus;
use App\Models\Absence;
use App\Models\Employe;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource (Employee view - own absences).
     */
    public function index(Request $request)
    {
        $userRole = session('user_role');

        // ROLE_EMPLOYEE : voit uniquement ses absences
        if ($userRole === 'ROLE_EMPLOYEE') {
            $employe = Employe::where('keycloak_id', session('user_id'))->first();
            if (!$employe) {
                return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
            }
            $absences = Absence::with('employe')
                ->where('employe_id', $employe->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('absences.employee.index', compact('absences', 'employe'));
        }
        // ROLE_ADMIN : vue tableau avec toutes les absences
        else {
            $query = Absence::with('employe');

            // Filtres - mapping des valeurs du test vers les valeurs de la base
            if ($request->has('employe_id') && $request->employe_id) {
                $query->where('employe_id', $request->employe_id);
            }
            if ($request->has('type_absence') && $request->type_absence) {
                // Mapping: conge_paye -> Congé annuel
                $typeMap = [
                    'conge_paye' => 'Congé annuel',
                    'maladie' => 'Maladie',
                    'sans_solde' => 'Sans solde',
                    'formation' => 'Formation',
                    'familial' => 'Familial',
                    'autre' => 'Autre',
                ];
                $type = $typeMap[$request->type_absence] ?? $request->type_absence;
                $query->where('type', $type);
            }
            if ($request->has('statut') && $request->statut) {
                // Mapping: en_attente -> pending, approuve -> approved, rejete -> rejected
                $statutMap = [
                    'en_attente' => 'pending',
                    'approuve' => 'approved',
                    'rejete' => 'rejected',
                ];
                $statut = $statutMap[$request->statut] ?? $request->statut;
                $query->where('statut', $statut);
            }
            if ($request->has('date_debut') && $request->date_debut) {
                $query->where('date_debut', '>=', $request->date_debut);
            }
            if ($request->has('date_fin') && $request->date_fin) {
                $query->where('date_fin', '<=', $request->date_fin);
            }

            $allAbsences = $query->orderBy('created_at', 'desc')->get();

            $employes = Employe::where('statut', 'actif')->get();
            $types = ['Congé annuel', 'Maladie', 'Sans solde', 'Formation', 'Familial', 'Autre'];

            return view('absences.admin.index', compact('allAbsences', 'employes', 'types'));
        }
    }

    /**
     * Show the form for creating a new resource (Employee only).
     */
    public function create()
    {
        $this->authorizeEmployee();

        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
        }

        $types = ['Congé annuel', 'Maladie', 'Sans solde', 'Formation', 'Familial', 'Autre'];

        return view('absences.employee.create', compact('types', 'employe'));
    }

    /**
     * Store a newly created resource in storage (Employee only).
     */
    public function store(Request $request)
    {
        $this->authorizeEmployee();

        $request->validate([
            'type' => 'required|in:Congé annuel,Maladie,Sans solde,Formation,Familial,Autre',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:500',
        ]);

        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
        }

        // Calculer le nombre de jours
        $dateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFin = \Carbon\Carbon::parse($request->date_fin);
        $nombreJours = $dateDebut->diffInDays($dateFin) + 1;

        Absence::create([
            'employe_id' => $employe->id,
            'type' => $request->type,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'nombre_jours' => $nombreJours,
            'motif' => $request->motif,
            'statut' => AbsenceStatus::PENDING->value,
        ]);

        return redirect()->route('absences.index')
            ->with('success', 'Demande d\'absence envoyée avec succès');
    }

    /**
     * Approve an absence (Admin only).
     */
    public function approve(Absence $absence)
    {
        $this->authorizeAdmin();

        $absence->update([
            'statut' => AbsenceStatus::APPROVED->value,
            'reponse_at' => now(),
        ]);

        // Créer l'événement dans l'agenda
        \App\Models\Event::create([
            'title' => $absence->type,
            'type' => 'conge', // On classe l'absence comme congé dans l'agenda
            'employe_id' => $absence->employe_id,
            'start_date' => $absence->date_debut,
            'end_date' => $absence->date_fin,
            'description' => $absence->motif,
        ]);

        return redirect()->back()
            ->with('success', 'Absence approuvée');
    }

    /**
     * Reject an absence (Admin only).
     */
    public function reject(Absence $absence)
    {
        $this->authorizeAdmin();

        $absence->update([
            'statut' => AbsenceStatus::REJECTED->value,
            'reponse_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Absence rejetée');
    }

    /**
     * Display the specified absence.
     */
    public function show(Absence $absence)
    {
        $userRole = session('user_role');
        $employe = Employe::where('keycloak_id', session('user_id'))->first();

        // Employees can only view their own absences
        if ($userRole === 'ROLE_EMPLOYEE' && $absence->employe_id !== $employe->id) {
            abort(403, 'Accès non autorisé');
        }

        $absence->load('employe');

        return view('absences.show', compact('absence'));
    }

    /**
     * Show the form for editing the specified absence (Employee only).
     */
    public function edit(Absence $absence)
    {
        $this->authorizeEmployee();

        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if ($absence->employe_id !== $employe->id) {
            abort(403, 'Accès non autorisé');
        }

        if ($absence->statut !== AbsenceStatus::PENDING->value) {
            return redirect()->route('absences.index')->with('error', 'Vous ne pouvez modifier que les absences en attente');
        }

        $types = ['Congé annuel', 'Maladie', 'Sans solde', 'Formation', 'Familial', 'Autre'];

        return view('absences.edit', compact('absence', 'types'));
    }

    /**
     * Update the specified absence (Employee only).
     */
    public function update(Request $request, Absence $absence)
    {
        $this->authorizeEmployee();

        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if ($absence->employe_id !== $employe->id) {
            abort(403, 'Accès non autorisé');
        }

        if ($absence->statut !== AbsenceStatus::PENDING->value) {
            return redirect()->route('absences.index')->with('error', 'Vous ne pouvez modifier que les absences en attente');
        }

        $request->validate([
            'type' => 'required|in:Congé annuel,Maladie,Sans solde,Formation,Familial,Autre',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:500',
        ]);

        // Calculer le nombre de jours
        $dateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFin = \Carbon\Carbon::parse($request->date_fin);
        $nombreJours = $dateDebut->diffInDays($dateFin) + 1;

        $absence->update([
            'type' => $request->type,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'nombre_jours' => $nombreJours,
            'motif' => $request->motif,
        ]);

        return redirect()->route('absences.index')
            ->with('success', 'Absence modifiée avec succès');
    }

    /**
     * Remove the specified absence (Employee only).
     */
    public function destroy(Absence $absence)
    {
        $this->authorizeEmployee();

        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if ($absence->employe_id !== $employe->id) {
            abort(403, 'Accès non autorisé');
        }

        if ($absence->statut !== AbsenceStatus::PENDING->value) {
            return redirect()->route('absences.index')->with('error', 'Vous ne pouvez supprimer que les absences en attente');
        }

        $absence->delete();

        return redirect()->route('absences.index')
            ->with('success', 'Absence supprimée avec succès');
    }

    /**
     * Export absences to CSV (Admin only).
     */
    public function export(Request $request)
    {
        $this->authorizeAdmin();

        $query = Absence::with('employe');

        // Apply same filters as index
        if ($request->has('employe_id') && $request->employe_id) {
            $query->where('employe_id', $request->employe_id);
        }
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        $absences = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="absences_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($absences) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employé', 'Type', 'Date Début', 'Date Fin', 'Nombre de jours', 'Statut', 'Motif']);

            foreach ($absences as $absence) {
                $statut = match($absence->statut) {
                    'pending' => 'En attente',
                    'approved' => 'Approuvé',
                    'rejected' => 'Rejeté',
                    default => $absence->statut,
                };

                fputcsv($file, [
                    $absence->employe->nom_complet,
                    $absence->type,
                    $absence->date_debut->format('d/m/Y'),
                    $absence->date_fin->format('d/m/Y'),
                    $absence->nombre_jours,
                    $statut,
                    $absence->motif ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display absence dashboard statistics (Admin only).
     */
    public function dashboard()
    {
        $this->authorizeAdmin();

        $totalAbsences = Absence::count();
        $absencesEnAttente = Absence::where('statut', AbsenceStatus::PENDING->value)->count();
        $absencesApprouvees = Absence::where('statut', AbsenceStatus::APPROVED->value)->count();
        $absencesRejetees = Absence::where('statut', AbsenceStatus::REJECTED->value)->count();

        return view('absences.dashboard', compact(
            'totalAbsences',
            'absencesEnAttente',
            'absencesApprouvees',
            'absencesRejetees'
        ));
    }

    /**
     * Vérifier que l'utilisateur est admin
     */
    private function authorizeAdmin()
    {
        if (session('user_role') !== 'ROLE_ADMIN') {
            abort(403, 'Accès non autorisé');
        }
    }

    /**
     * Vérifier que l'utilisateur est employé
     */
    private function authorizeEmployee()
    {
        if (session('user_role') !== 'ROLE_EMPLOYEE') {
            abort(403, 'Accès non autorisé');
        }
    }
}
