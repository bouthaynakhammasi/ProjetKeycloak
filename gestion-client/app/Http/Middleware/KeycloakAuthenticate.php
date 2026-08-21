<?php

namespace App\Http\Middleware;

use App\Services\KeycloakTokenValidator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KeycloakAuthenticate
{
    /**
     * Vérifie que l'utilisateur Keycloak est bien authentifié via la session SSO.
     * Valide également que le token est toujours valide auprès de Keycloak.
     * Si ce n'est pas le cas, redirige vers la page de connexion Keycloak.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que la session existe
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $idToken = session('id_token');

        // Si pas d'ID Token, rediriger vers login
        if (!$idToken) {
            session()->flush();
            return redirect()->route('login');
        }

        // Valider le token auprès de Keycloak (avec cache court pour SLO rapide)
        $validator = app(KeycloakTokenValidator::class);
        if (!$validator->validateTokenWithCache($idToken, 5)) {
            // Token invalide ou expiré, détruire la session et rediriger
            session()->flush();
            return redirect()->route('login');
        }

        return $next($request);
    }
}
