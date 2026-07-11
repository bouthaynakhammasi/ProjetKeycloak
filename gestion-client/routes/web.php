<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Page d'affichage du login (vue uniquement)
Route::get('/', function () {
    return view('login');
})->name('login.page');

Route::get('/login', function () {
    return view('login');
})->name('login');

// Action : redirection vers Keycloak SSO
Route::get('/auth/keycloak/redirect', [AuthController::class, 'login'])->name('keycloak.redirect');

// Callback Keycloak après authentification
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('keycloak.callback');

// Déconnexion
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    if (!session()->has('user_id')) {
        return redirect('/login');
    }

    return view('dashboard');
});
