<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employe', 'approver', 'event']);

        if ($request->has('status') && in_array($request->status, ['en_attente', 'accepte', 'refuse'])) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        // Si l'application utilise des vues, remplacer la réponse json par view()
        if ($request->wantsJson()) {
            return response()->json($leaveRequests);
        }
        
        // Vue par défaut (à créer si besoin)
        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type_conge' => 'required|in:annuel,maladie,exceptionnel',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
            'motif'      => 'nullable|string'
        ]);

        $leaveRequest = LeaveRequest::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Demande de congé créée avec succès', 'data' => $leaveRequest], 201);
        }

        return back()->with('success', 'Demande de congé créée avec succès.');
    }

    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'en_attente') {
            if ($request->wantsJson()) return response()->json(['error' => 'La demande est déjà traitée'], 400);
            return back()->with('error', 'La demande est déjà traitée.');
        }

        // Récupérer l'employé admin (si applicable)
        $adminEmploye = \App\Models\Employe::where('keycloak_id', session('user_id'))->first();
        $adminId = $adminEmploye ? $adminEmploye->id : null;

        $leaveRequest->approuver($adminId);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Demande approuvée et ajoutée à l\'agenda', 'data' => $leaveRequest]);
        }

        return back()->with('success', 'Demande approuvée et ajoutée à l\'agenda.');
    }

    public function reject(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'en_attente') {
            if ($request->wantsJson()) return response()->json(['error' => 'La demande est déjà traitée'], 400);
            return back()->with('error', 'La demande est déjà traitée.');
        }

        $leaveRequest->status = 'refuse';
        $leaveRequest->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Demande refusée', 'data' => $leaveRequest]);
        }

        return back()->with('success', 'Demande refusée.');
    }
}
