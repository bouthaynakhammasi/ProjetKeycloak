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
        if (!session()->has('keycloak_user')) {
            return redirect()->route('keycloak.redirect');
        }

        $keycloakUser = session('keycloak_user');
        $idToken = $keycloakUser['id_token'] ?? null;

        // Si pas d'ID Token, rediriger vers login
        if (!$idToken) {
            session()->forget('keycloak_user');
            return redirect()->route('keycloak.redirect');
        }

        // Valider le token auprès de Keycloak (avec cache court pour SLO rapide)
        $validator = app(KeycloakTokenValidator::class);
        if (!$validator->validateTokenWithCache($idToken, 5)) {
            // Token invalide ou expiré, détruire la session et rediriger
            session()->forget('keycloak_user');
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('keycloak.redirect');
        }

        return $next($request);
    }
}
