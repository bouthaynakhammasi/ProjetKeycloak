<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Afficher l'agenda personnel de l'employé connecté.
     */
    public function employee($year = null, $month = null)
    {
        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
        }

        $currentDate = Carbon::now();
        $year = $year ? (int) $year : $currentDate->year;
        $month = $month ? (int) $month : $currentDate->month;

        $targetDate = Carbon::createFromDate($year, $month, 1);
        $monthName = $targetDate->translatedFormat('F Y');

        $prevDate = $targetDate->copy()->subMonth();
        $nextDate = $targetDate->copy()->addMonth();

        // Récupérer les événements de l'employé pour le mois
        $events = Event::where('employe_id', $employe->id)
            ->duMois($month, $year)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Groupement par jour (YYYY-MM-DD)
        $eventsGrouped = $events->groupBy(function ($event) {
            return Carbon::parse($event->start_date)->format('Y-m-d');
        });

        return view('agenda.employee', compact(
            'events',
            'eventsGrouped',
            'year',
            'month',
            'monthName',
            'prevDate',
            'nextDate',
            'employe'
        ));
    }

    /**
     * Afficher l'agenda du mois spécifié (ou mois en cours).
     */
    public function index($year = null, $month = null)
    {
        $currentDate = Carbon::now();
        $year = $year ? (int) $year : $currentDate->year;
        $month = $month ? (int) $month : $currentDate->month;

        $targetDate = Carbon::createFromDate($year, $month, 1);
        $monthName = $targetDate->translatedFormat('F Y');

        $prevDate = $targetDate->copy()->subMonth();
        $nextDate = $targetDate->copy()->addMonth();

        // Récupérer les événements du mois
        $events = Event::with('employe')
            ->duMois($month, $year)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Groupement par jour (YYYY-MM-DD)
        $eventsGrouped = $events->groupBy(function ($event) {
            return Carbon::parse($event->start_date)->format('Y-m-d');
        });

        // Employés pour l'assignation dans le formulaire modal
        $employes = Employe::where('statut', 'actif')->orderBy('nom')->get();

        return view('agenda.index', compact(
            'events',
            'eventsGrouped',
            'year',
            'month',
            'monthName',
            'prevDate',
            'nextDate',
            'employes'
        ));
    }

    /**
     * Créer un nouvel événement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:conge,formation,entretien,ferie,reunion',
            'employe_id'  => 'nullable|exists:employes,id',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'start_time'  => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        Event::create([
            'title'       => $request->title,
            'type'        => $request->type,
            'employe_id'  => $request->employe_id ?: null,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date ?: null,
            'start_time'  => $request->start_time ?: null,
            'description' => $request->description ?: null,
        ]);

        $month = Carbon::parse($request->start_date)->month;
        $year = Carbon::parse($request->start_date)->year;

        return redirect()
            ->route('agenda.month', ['year' => $year, 'month' => $month])
            ->with('success', 'Événement ajouté avec succès.');
    }

    /**
     * Supprimer un événement.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $month = Carbon::parse($event->start_date)->month;
        $year = Carbon::parse($event->start_date)->year;

        $event->delete();

        return redirect()
            ->route('agenda.month', ['year' => $year, 'month' => $month])
            ->with('success', 'Événement supprimé avec succès.');
    }
}
