<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeycloakTokenValidator
{
    /**
     * Valide un token via l'endpoint d'introspection Keycloak.
     *
     * @param string $token Le token à valider (access_token ou id_token)
     * @return bool True si le token est valide, false sinon
     */
    public function validateToken(string $token): bool
    {
        try {
            $baseUrl = config('services.keycloak.base_url');
            $realm = config('services.keycloak.realms');
            $clientId = config('services.keycloak.client_id');
            $clientSecret = config('services.keycloak.client_secret');

            $introspectionUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/token/introspect";

            $response = Http::asForm()->post($introspectionUrl, [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'token' => $token,
            ]);

            if (!$response->successful()) {
                Log::warning('Keycloak token introspection failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            $data = $response->json();

            // Le token est valide si 'active' est true
            return isset($data['active']) && $data['active'] === true;

        } catch (\Exception $e) {
            Log::error('Keycloak token validation error', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Valide un token avec cache pour éviter trop de requêtes.
     *
     * @param string $token Le token à valider
     * @param int $ttl Durée de cache en secondes (défaut: 60)
     * @return bool True si le token est valide, false sinon
     */
    public function validateTokenWithCache(string $token, int $ttl = 60): bool
    {
        $cacheKey = 'keycloak_token_valid_' . md5($token);

        // Si en cache, utiliser la valeur
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey) === true;
        }

        // Sinon, valider via Keycloak
        $isValid = $this->validateToken($token);

        // Mettre en cache
        cache()->put($cacheKey, $isValid, $ttl);

        return $isValid;
    }

    /**
     * Invalide le cache d'un token (utile lors de la déconnexion).
     *
     * @param string $token Le token à invalider du cache
     * @return void
     */
    public function invalidateTokenCache(string $token): void
    {
        $cacheKey = 'keycloak_token_valid_' . md5($token);
        cache()->forget($cacheKey);
    }
}
