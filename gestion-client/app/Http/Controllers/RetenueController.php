<?php

namespace App\Http\Controllers;

use App\Models\Retenue;
use App\Models\Employe;
use Illuminate\Http\Request;

class RetenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();
        
        $query = Retenue::with('employe');
        
        // Filtrage par employé
        if ($request->has('employe_id') && $request->employe_id) {
            $query->where('employe_id', $request->employe_id);
        }
        
        // Filtrage par type de retenue
        if ($request->has('type') && $request->type) {
            $query->where('type_retenue', $request->type);
        }
        
        // Filtrage par période
        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date', '>=', $request->date_debut);
        }
        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date', '<=', $request->date_fin);
        }
        
        $retenues = $query->orderBy('date', 'desc')->get();
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('retenues.index', compact('retenues', 'employes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAdmin();
        
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('retenues.create', compact('employes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type_retenue' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
        
        Retenue::create([
            'employe_id' => $request->employe_id,
            'type_retenue' => $request->type_retenue,
            'montant' => $request->montant,
            'description' => $request->description,
            'date' => $request->date,
        ]);
        
        return redirect()->route('retenues.index')
            ->with('success', 'Retenue créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Retenue $retenue)
    {
        $this->authorizeAdmin();
        
        $retenue->load('employe');
        
        return view('retenues.show', compact('retenue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Retenue $retenue)
    {
        $this->authorizeAdmin();
        
        $retenue->load('employe');
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('retenues.edit', compact('retenue', 'employes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Retenue $retenue)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type_retenue' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
        
        $retenue->update([
            'employe_id' => $request->employe_id,
            'type_retenue' => $request->type_retenue,
            'montant' => $request->montant,
            'description' => $request->description,
            'date' => $request->date,
        ]);
        
        return redirect()->route('retenues.index')
            ->with('success', 'Retenue mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Retenue $retenue)
    {
        $this->authorizeAdmin();
        
        $retenue->delete();
        
        return redirect()->route('retenues.index')
            ->with('success', 'Retenue supprimée avec succès');
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
