<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeycloakUser;
use App\Models\CompteEnAttente;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications admin :
     * comptes en attente de validation.
     */
    public function index()
    {
        $pendingUsers = KeycloakUser::pending()
            ->orderByDesc('created_at')
            ->get();

        $comptesEnAttente = CompteEnAttente::where('statut', CompteEnAttente::STATUT_EN_ATTENTE)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.notifications.index', compact('pendingUsers', 'comptesEnAttente'));
    }
}
