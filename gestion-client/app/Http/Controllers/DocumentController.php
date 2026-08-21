<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userRole = session('user_role');

        // ROLE_EMPLOYEE : voit uniquement ses documents
        if ($userRole === 'ROLE_EMPLOYEE') {
            $employe = Employe::where('keycloak_id', session('user_id'))->first();
            if (!$employe) {
                return redirect()->route('dashboard')->with('error', 'Employé non trouvé');
            }
            $query = Document::with('employe')->where('employe_id', $employe->id);
        }
        // ROLE_ADMIN : voit tous les documents avec filtres
        else {
            $query = Document::with('employe');

            // Filtrage par employé
            if ($request->has('employe_id') && $request->employe_id) {
                $query->where('employe_id', $request->employe_id);
            }

            // Filtrage par type
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }
        }

        $documents = $query->orderBy('uploaded_at', 'desc')->paginate(5)->withQueryString();
        $employes = Employe::where('statut', 'actif')->get();

        $types = ['Contrat', 'Diplôme', 'CNI', 'Certificat médical', 'Autre'];

        return view('documents.index', compact('documents', 'employes', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAdmin();

        $employes = Employe::where('statut', 'actif')->get();
        $types = ['Contrat', 'Diplôme', 'CNI', 'Certificat médical', 'Autre'];

        return view('documents.create', compact('employes', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type' => 'required|in:Contrat,Diplôme,CNI,Certificat médical,Autre',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5 Mo
        ]);

        // Stocker le fichier
        $path = $request->file('fichier')->store('documents', 'public');

        // Créer l'entrée en base
        Document::create([
            'employe_id' => $request->employe_id,
            'type' => $request->type,
            'fichier' => $path,
            'uploaded_at' => now(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document ajouté avec succès');
    }

    /**
     * Download the specified document.
     */
    public function download(Document $document)
    {
        $userRole = session('user_role');

        // ROLE_EMPLOYEE : ne peut télécharger que ses propres documents
        if ($userRole === 'ROLE_EMPLOYEE') {
            $employe = Employe::where('keycloak_id', session('user_id'))->first();
            if (!$employe || $document->employe_id !== $employe->id) {
                return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
            }
        }

        if (!Storage::disk('public')->exists($document->fichier)) {
            return redirect()->back()->with('error', 'Fichier introuvable');
        }

        return Storage::disk('public')->download($document->fichier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorizeAdmin();

        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($document->fichier)) {
            Storage::disk('public')->delete($document->fichier);
        }

        // Supprimer l'entrée en base
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé avec succès');
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
