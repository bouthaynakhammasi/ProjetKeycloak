<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeycloakController;
use App\Http\Controllers\MailboxController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Authentification Keycloak (SSO) ──────────────────────────────────────────
Route::get('/auth/redirect', [KeycloakController::class, 'redirect'])->name('keycloak.redirect');
Route::get('/auth/callback', [KeycloakController::class, 'callback'])->name('keycloak.callback');
Route::post('/auth/logout',  [KeycloakController::class, 'logout'])->name('keycloak.logout');

// ─── Page d'accueil → redirige vers la boîte mail ─────────────────────────────
Route::get('/', function () {
    if (session()->has('keycloak_user')) {
        return redirect()->route('mailbox.index');
    }
    return redirect()->route('keycloak.redirect');
});

// ─── Application Mailbox (protégée par SSO) ───────────────────────────────────
Route::middleware('keycloak.auth')->group(function () {

    // Liste des messages (dossier via ?folder=inbox|sent|drafts|trash)
    Route::get('/mailbox', [MailboxController::class, 'index'])->name('mailbox.index');

    // Lecture d'un message
    Route::get('/mailbox/{id}', [MailboxController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('mailbox.show');

    // Composer un nouveau message
    Route::get('/mailbox/compose', [MailboxController::class, 'compose'])->name('mailbox.compose');

    // Enregistrer un message (envoi ou brouillon)
    Route::post('/mailbox', [MailboxController::class, 'store'])->name('mailbox.store');

    // Toggle étoile (AJAX)
    Route::patch('/mailbox/{id}/star', [MailboxController::class, 'toggleStar'])
        ->where('id', '[0-9]+')
        ->name('mailbox.star');

    // Supprimer / mettre à la corbeille
    Route::delete('/mailbox/{id}', [MailboxController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('mailbox.destroy');
});
