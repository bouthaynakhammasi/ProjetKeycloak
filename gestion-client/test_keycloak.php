<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

echo "=== TEST KEYCLOAK ADMIN API ===\n\n";

// Configuration
$baseUrl = config('services.keycloak.base_url', 'http://localhost:8080');
$realm = config('services.keycloak.realms', 'CompanyRealm');
$clientId = config('services.keycloak.admin_client_id', 'laravel-admin-client');
$clientSecret = config('services.keycloak.admin_client_secret', '');

echo "Configuration:\n";
echo "- Base URL: {$baseUrl}\n";
echo "- Realm: {$realm}\n";
echo "- Client ID: {$clientId}\n";
echo "- Has Secret: " . (!empty($clientSecret) ? 'YES' : 'NO') . "\n";
echo "- Secret Length: " . strlen($clientSecret) . "\n\n";

// Test 1: Récupérer le token admin
echo "TEST 1: Récupération du token admin...\n";
$tokenUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/token";
echo "URL: {$tokenUrl}\n";

$response = Http::asForm()->post($tokenUrl, [
    'grant_type' => 'client_credentials',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
]);

echo "Status: {$response->status()}\n";
echo "Success: " . ($response->successful() ? 'YES' : 'NO') . "\n";

if ($response->failed()) {
    echo "ERROR: Token request failed\n";
    echo "Response: {$response->body()}\n\n";
    exit(1);
}

$token = $response->json('access_token');
echo "Token obtenu (premiers 50 chars): " . substr($token, 0, 50) . "...\n\n";

// Décoder le token pour voir les rôles
$parts = explode('.', $token);
$payload = json_decode(base64_decode($parts[1]), true);

echo "\nTOKEN ROLES:\n";
print_r($payload['realm_access']['roles'] ?? []);
echo "\n";

// Test 2: Lister les rôles disponibles
echo "TEST 2: Liste des rôles disponibles...\n";
$rolesUrl = "{$baseUrl}/admin/realms/{$realm}/roles";
echo "URL: {$rolesUrl}\n";

$response = Http::withToken($token)->get($rolesUrl);

echo "Status: {$response->status()}\n";
echo "Success: " . ($response->successful() ? 'YES' : 'NO') . "\n";

if ($response->successful()) {
    $roles = $response->json();
    echo "Nombre de rôles: " . count($roles) . "\n";
    echo "Rôles disponibles:\n";
    foreach ($roles as $role) {
        echo "  - {$role['name']} (id: {$role['id']})\n";
    }
} else {
    echo "ERROR: Failed to list roles\n";
    echo "Response: {$response->body()}\n\n";
}

// Test 3: Vérifier les rôles spécifiques
echo "\nTEST 3: Vérification des rôles spécifiques...\n";
$requiredRoles = ['ROLE_ADMIN', 'ROLE_EMPLOYEE', 'ROLE_CLIENT'];

foreach ($requiredRoles as $roleName) {
    $roleUrl = "{$baseUrl}/admin/realms/{$realm}/roles/{$roleName}";
    $response = Http::withToken($token)->get($roleUrl);
    
    echo "- {$roleName}: " . ($response->successful() ? 'EXISTS' : 'NOT FOUND') . "\n";
    
    if ($response->failed()) {
        echo "  Status: {$response->status()}\n";
        echo "  Response: {$response->body()}\n";
    }
}

echo "\n=== FIN DU TEST ===\n";
