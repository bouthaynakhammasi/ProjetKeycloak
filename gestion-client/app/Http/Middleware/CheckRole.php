<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Vérifier si l'utilisateur est authentifié via Keycloak
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $userRole = session('user_role');

        // 2. Si l'utilisateur n'a aucun rôle, rediriger vers la page d'attente
        if (empty($userRole)) {
            // Éviter une boucle infinie de redirection si la requête cible déjà /pending
            if ($request->is('pending')) {
                return $next($request);
            }
            return redirect()->route('pending');
        }

        // 3. Vérifier si le rôle de l'utilisateur fait partie des rôles autorisés
        if (!in_array($userRole, $roles, true)) {
            abort(403, 'Accès interdit : vous n\'avez pas le rôle requis pour accéder à cette ressource.');
        }

        return $next($request);
    }
}
