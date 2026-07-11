<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class KeycloakController extends Controller
{
    /**
     * Redirige l'utilisateur vers la page de connexion Keycloak.
     */
    public function redirect()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * Traite le callback OAuth2 de Keycloak et stocke l'utilisateur en session.
     */
    public function callback()
    {
        try {
            $user = Socialite::driver('keycloak')->user();

            session([
                'keycloak_user' => [
                    'sub'            => $user->getId(),
                    'name'           => $user->getName(),
                    'email'          => $user->getEmail(),
                    'nickname'       => $user->getNickname(),
                    'preferred_username' => $user->getNickname(),
                    'token'          => $user->token,
                    'refresh_token'  => $user->refreshToken,
                ],
            ]);

            return redirect()->route('mailbox.index');
        } catch (\Exception $e) {
            return redirect()->route('keycloak.redirect')
                ->withErrors(['keycloak' => 'Échec de l\'authentification SSO : ' . $e->getMessage()]);
        }
    }

    /**
     * Déconnecte l'utilisateur de l'application et de Keycloak (SSO logout).
     */
    public function logout(Request $request)
    {
        $keycloakUser = session('keycloak_user');
        $request->session()->forget('keycloak_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $realm   = config('services.keycloak.realms');
        $baseUrl = config('services.keycloak.base_url');
        $redirectUri = urlencode(url('/'));

        $logoutUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/logout"
            . "?post_logout_redirect_uri={$redirectUri}"
            . "&client_id=" . config('services.keycloak.client_id');

        return redirect($logoutUrl);
    }
}
