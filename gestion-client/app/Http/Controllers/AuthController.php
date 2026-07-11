<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Redirige l'utilisateur vers la page de connexion Keycloak.
     */
    public function login(Request $request)
    {
        try {
            if ($request->has('redirect')) {
                session(['url.intended' => $request->get('redirect')]);
            }

            return Socialite::driver('keycloak')
                ->redirectUrl(config('services.keycloak.redirect'))
                ->redirect();
        } catch (Exception $e) {
            Log::error('Keycloak redirect error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Impossible de contacter le serveur d\'authentification : ' . $e->getMessage());
        }
    }

    /**
     * Gère le retour de Keycloak après authentification.
     */
    public function callback(Request $request)
    {
        // 1. Vérifier si Keycloak a renvoyé une erreur dans l'URL
        if ($request->has('error')) {
            $errorDescription = $request->query('error_description', 'L\'authentification a été annulée ou a échoué.');
            return redirect()->route('login')->with('error', 'Erreur Keycloak : ' . $errorDescription);
        }

        try {
            $user = Socialite::driver('keycloak')->user();

            if (!$user || !$user->getId()) {
                return redirect()->route('login')->with('error', 'Utilisateur introuvable ou jeton invalide.');
            }

            session([
                'user_id'    => $user->getId(),
                'user_name'  => $user->getName() ?? $user->getNickname() ?? 'Utilisateur',
                'user_email' => $user->getEmail(),
                'id_token'   => $user->token,
            ]);

            $redirectTo = session('url.intended', '/dashboard');
            session()->forget('url.intended');

            return redirect($redirectTo);

        } catch (InvalidStateException $e) {
            Log::error('Keycloak InvalidStateException: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Session expirée, veuillez réessayer.');

        } catch (Exception $e) {
            Log::error('Keycloak callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Erreur lors de la récupération des données utilisateur : ' . $e->getMessage());
        }
    }

    /**
     * Déconnecte l'utilisateur de la session locale et de Keycloak (SSO).
     */
    public function logout()
    {
        try {
            // Générer l'URL de déconnexion globale Keycloak
            $logoutUrl = Socialite::driver('keycloak')->getLogoutUrl(
                url('/login'), // URI de redirection après déconnexion Keycloak
                config('services.keycloak.client_id')
            );
        } catch (Exception $e) {
            // En cas d'erreur de configuration, on retombe sur une déconnexion locale simple
            $logoutUrl = '/login';
        }

        // Vider la session locale de l'application
        session()->flush();

        return redirect($logoutUrl);
    }
}
