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

// ─── Route /login pour correspondre à la config Keycloak ───────────────────────
Route::get('/login', function () {
    if (session()->has('keycloak_user')) {
        // Si l'utilisateur est déjà authentifié, rediriger vers la mailbox
        return redirect()->route('mailbox.index');
    }
    // Sinon, rediriger vers Keycloak pour authentification
    return redirect()->route('keycloak.redirect');
})->name('login');

// ─── Page d'accueil publique (landing page) ───────────────────────────────────
Route::get('/', function () {
    if (session()->has('keycloak_user')) {
        // Si l'utilisateur est déjà authentifié, rediriger vers la mailbox
        return redirect()->route('mailbox.index');
    }
    // Sinon, afficher la landing page
    return view('landing');
})->name('landing');

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

    // Répondre à un message
    Route::get('/mailbox/{id}/reply', [MailboxController::class, 'compose'])
        ->where('id', '[0-9]+')
        ->name('mailbox.reply');

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
