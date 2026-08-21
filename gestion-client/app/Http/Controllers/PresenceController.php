<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    /**
     * Afficher les présences de l'employé connecté.
     */
    public function employee(Request $request)
    {
        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
        }

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $presences = Presence::where('employe_id', $employe->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('presences.employee', compact('presences', 'date'));
    }

    /**
     * Afficher la page de présence du jour (ou d'une date donnée).
     */
    public function index(Request $request)
    {
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $dateFormatted = Carbon::parse($date)->translatedFormat('j F Y');

        // Tous les employés actifs
        $employes = Employe::where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        $totalEmployes = $employes->count();

        // Présences existantes pour la date
        $presences = Presence::with('employe')
            ->forDate($date)
            ->get()
            ->keyBy('employe_id');

        // Construire la liste complète : chaque employé avec son statut
        $listePresence = $employes->map(function ($employe) use ($presences) {
            $presence = $presences->get($employe->id);

            return (object) [
                'employe'         => $employe,
                'heure_connexion' => $presence?->heure_connexion,
                'heure_depart'    => $presence?->heure_depart,
                'statut'          => $presence?->statut ?? 'absent',
                'statut_label'    => $presence?->statut_label ?? 'Absent',
                'badge_class'     => $presence?->badge_class ?? 'bg-red-50 text-red-700 border-red-200',
                'presence_id'     => $presence?->id,
            ];
        });

        $presents = $listePresence->where('statut', 'present')->count()
            + $listePresence->where('statut', 'retard')->count();
        $absents  = $listePresence->where('statut', 'absent')->count();

        return view('presences.index', compact(
            'date',
            'dateFormatted',
            'listePresence',
            'totalEmployes',
            'presents',
            'absents'
        ));
    }

    /**
     * Enregistrer ou mettre à jour la présence d'un employé.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'date'       => 'required|date',
            'statut'     => 'required|in:present,retard,absent',
            'heure_connexion' => 'nullable|date_format:H:i',
            'heure_depart' => 'nullable|date_format:H:i',
        ]);

        Presence::updateOrCreate(
            [
                'employe_id' => $request->employe_id,
                'date'       => $request->date,
            ],
            [
                'statut'          => $request->statut,
                'heure_connexion' => $request->statut !== 'absent' ? $request->heure_connexion : null,
                'heure_depart'    => $request->heure_depart,
                'remarque'        => $request->remarque,
            ]
        );

        return redirect()
            ->route('presences.index', ['date' => $request->date])
            ->with('success', 'Présence mise à jour avec succès.');
    }

    /**
     * Marquer tous les employés sans présence comme absents pour une date.
     */
    public function marquerAbsents(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $date = $request->date;
        $employeIdsAvecPresence = Presence::forDate($date)->pluck('employe_id');

        $employesSansPresence = Employe::where('statut', 'actif')
            ->whereNotIn('id', $employeIdsAvecPresence)
            ->get();

        foreach ($employesSansPresence as $employe) {
            Presence::create([
                'employe_id' => $employe->id,
                'date'       => $date,
                'statut'     => 'absent',
            ]);
        }

        return redirect()
            ->route('presences.index', ['date' => $date])
            ->with('success', $employesSansPresence->count() . ' employé(s) marqué(s) comme absents.');
    }
}
