<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KeycloakAuthenticate
{
    /**
     * Vérifie que l'utilisateur Keycloak est bien authentifié via la session SSO.
     * Si ce n'est pas le cas, redirige vers la page de connexion Keycloak.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('keycloak_user')) {
            return redirect()->route('keycloak.redirect');
        }

        return $next($request);
    }
}
