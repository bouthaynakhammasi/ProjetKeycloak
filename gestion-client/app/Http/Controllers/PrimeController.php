<?php

namespace App\Http\Controllers;

use App\Models\Prime;
use App\Models\Employe;
use Illuminate\Http\Request;

class PrimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();
        
        $query = Prime::with('employe');
        
        // Filtrage par employé
        if ($request->has('employe_id') && $request->employe_id) {
            $query->where('employe_id', $request->employe_id);
        }
        
        // Filtrage par type de prime
        if ($request->has('type') && $request->type) {
            $query->where('type_prime', $request->type);
        }
        
        // Filtrage par période
        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date', '>=', $request->date_debut);
        }
        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date', '<=', $request->date_fin);
        }
        
        $primes = $query->orderBy('date', 'desc')->get();
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('primes.index', compact('primes', 'employes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAdmin();
        
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('primes.create', compact('employes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type_prime' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
        
        Prime::create([
            'employe_id' => $request->employe_id,
            'type_prime' => $request->type_prime,
            'montant' => $request->montant,
            'description' => $request->description,
            'date' => $request->date,
        ]);
        
        return redirect()->route('primes.index')
            ->with('success', 'Prime créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prime $prime)
    {
        $this->authorizeAdmin();
        
        $prime->load('employe');
        
        return view('primes.show', compact('prime'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prime $prime)
    {
        $this->authorizeAdmin();
        
        $prime->load('employe');
        $employes = Employe::where('statut', 'actif')->get();
        
        return view('primes.edit', compact('prime', 'employes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prime $prime)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type_prime' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
        
        $prime->update([
            'employe_id' => $request->employe_id,
            'type_prime' => $request->type_prime,
            'montant' => $request->montant,
            'description' => $request->description,
            'date' => $request->date,
        ]);
        
        return redirect()->route('primes.index')
            ->with('success', 'Prime mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prime $prime)
    {
        $this->authorizeAdmin();
        
        $prime->delete();
        
        return redirect()->route('primes.index')
            ->with('success', 'Prime supprimée avec succès');
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
