<?php

namespace App\Http\Controllers;

use App\Models\Salaire;
use App\Models\Employe;
use App\Events\SalaireValidated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function employee(Request $request)
    {
        $employe = Employe::where('keycloak_id', session('user_id'))->first();
        if (!$employe) {
            return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
        }

        $salaires = Salaire::where('employe_id', $employe->id)
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();

        return view('salaires.employee', compact('salaires'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userRole = session('user_role');
        $currentMonth = date('n');
        $currentYear = date('Y');

        // ROLE_EMPLOYEE : voit uniquement ses salaires
        if ($userRole === 'ROLE_EMPLOYEE') {
            $employe = Employe::where('keycloak_id', session('user_id'))->first();
            if (!$employe) {
                return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
            }
            $query = Salaire::where('employe_id', $employe->id);
        }
        // ROLE_ADMIN : voit tous les salaires avec filtres
        else {
            $query = Salaire::with('employe');

            // Filtrage par employé
            if ($request->has('employe_id') && $request->employe_id) {
                $query->where('employe_id', $request->employe_id);
            }

            // Filtrage par mois et année
            if ($request->has('mois') && $request->mois) {
                $query->where('mois', $request->mois);
            }
            if ($request->has('annee') && $request->annee) {
                $query->where('annee', $request->annee);
            }
        }

        $salaires = $query->orderBy('annee', 'desc')->orderBy('mois', 'desc')->get();

        // Stats du mois courant (pour admin)
        $stats = null;
        if ($userRole === 'ROLE_ADMIN') {
            $stats = [
                'nombre_fiches_mois' => Salaire::where('mois', $currentMonth)
                    ->where('annee', $currentYear)
                    ->count(),
                'masse_salariale_mois' => Salaire::where('mois', $currentMonth)
                    ->where('annee', $currentYear)
                    ->sum('salaire_net'),
            ];
        }

        $employes = Employe::where('statut', 'actif')->get();

        return view('salaires.index', compact('salaires', 'employes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAdmin();
        
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('salaires.create', compact('employes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020|max:2100',
            'salaire_brut' => 'required|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        // Vérifier que le salaire n'existe pas déjà pour cette période
        $existing = Salaire::where('employe_id', $request->employe_id)
            ->where('mois', $request->mois)
            ->where('annee', $request->annee)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Un salaire existe déjà pour cet employé pour cette période');
        }

        // Calculer le salaire net (ne pas faire confiance au client)
        $salaireNet = $request->salaire_brut - ($request->deductions ?? 0);

        $salaire = Salaire::create([
            'employe_id' => $request->employe_id,
            'mois' => $request->mois,
            'annee' => $request->annee,
            'salaire_brut' => $request->salaire_brut,
            'deductions' => $request->deductions ?? 0,
            'salaire_net' => $salaireNet,
        ]);

        return redirect()->route('salaires.index')
            ->with('success', 'Salaire créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salaire $salaire)
    {
        $userRole = session('user_role');
        
        // ROLE_EMPLOYEE : ne peut voir que ses propres salaires
        if ($userRole === 'ROLE_EMPLOYEE') {
            $employe = Employe::where('keycloak_id', session('user_id'))->first();
            if (!$employe || $salaire->employe_id !== $employe->id) {
                return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
            }
        }
        
        $salaire->load('employe');
        
        return view('salaires.show', compact('salaire'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salaire $salaire)
    {
        $this->authorizeAdmin();
        
        $salaire->load('employe');
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('salaires.edit', compact('salaire', 'employes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salaire $salaire)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020|max:2100',
            'salaire_base' => 'required|numeric|min:0',
            'prime' => 'nullable|numeric|min:0',
            'retenue' => 'nullable|numeric|min:0',
            'statut_paiement' => 'required|in:en_attente,paye,annule',
            'date_paiement' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier l'unicité si on change la période ou l'employé
        if ($salaire->employe_id != $request->employe_id || 
            $salaire->mois != $request->mois || 
            $salaire->annee != $request->annee) {
            
            $existing = Salaire::where('employe_id', $request->employe_id)
                ->where('mois', $request->mois)
                ->where('annee', $request->annee)
                ->where('id', '!=', $salaire->id)
                ->first();
                
            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Un salaire existe déjà pour cet employé pour cette période');
            }
        }
        
        $salaire->update([
            'employe_id' => $request->employe_id,
            'mois' => $request->mois,
            'annee' => $request->annee,
            'salaire_base' => $request->salaire_base,
            'prime' => $request->prime ?? 0,
            'retenue' => $request->retenue ?? 0,
            'statut_paiement' => $request->statut_paiement,
            'date_paiement' => $request->statut_paiement === 'paye' ? now() : null,
            'notes' => $request->notes,
        ]);
        
        // Recalculer le salaire net
        $salaire->calculerSalaireNet();
        
        return redirect()->route('salaires.index')
            ->with('success', 'Salaire mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salaire $salaire)
    {
        $this->authorizeAdmin();
        
        $salaire->delete();
        
        return redirect()->route('salaires.index')
            ->with('success', 'Salaire supprimé avec succès');
    }
    
    /**
     * Marquer un salaire comme payé
     */
    public function marquerPaye(Salaire $salaire)
    {
        $this->authorizeAdmin();
        
        $salaire->update([
            'statut_paiement' => 'paye',
            'date_paiement' => now(),
        ]);
        
        // Broadcast event pour notification temps réel
        broadcast(new SalaireValidated($salaire));
        
        return redirect()->back()
            ->with('success', 'Salaire marqué comme payé');
    }
    
    /**
     * Générer la fiche de paie PDF
     */
    public function generatePDF(Salaire $salaire)
    {
        $userRole = session('user_role');
        
        // ROLE_EMPLOYEE : ne peut générer que ses propres fiches
        if ($userRole === 'ROLE_EMPLOYEE') {
            $employe = Employe::where('keycloak_id', session('user_id'))->first();
            if (!$employe || $salaire->employe_id !== $employe->id) {
                return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
            }
        }
        
        $salaire->load('employe');
        
        // Générer le PDF avec HTML simple
        $html = view('salaires.pdf', compact('salaire'))->render();
        
        // Utiliser une approche simple avec DomPDF si installé, sinon HTML view
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        
        $filename = 'fiche_paie_' . $salaire->employe->nom_complet . '_' . $salaire->mois . '_' . $salaire->annee . '.pdf';
        
        return $pdf->stream($filename);
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
}
