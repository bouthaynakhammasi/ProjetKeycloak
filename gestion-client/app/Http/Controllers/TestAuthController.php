<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TestAuthController extends Controller
{
    /**
     * Test login endpoint for E2E testing - bypasses Keycloak
     * Only available in testing environment
     */
    public function testLogin(Request $request)
    {
        if (config('app.env') !== 'testing' && config('app.env') !== 'local') {
            return response()->json(['error' => 'Test login only available in testing/local environment'], 403);
        }

        $email = $request->input('email');

        // Mock user data based on email
        $testUsers = [
            'admin@test.com' => [
                'keycloak_id' => 'admin-keycloak-id-123',
                'name' => 'Admin User',
                'role' => 'ROLE_ADMIN',
            ],
            'employee@test.com' => [
                'keycloak_id' => 'employee-keycloak-id-456',
                'name' => 'Employee User',
                'role' => 'ROLE_EMPLOYEE',
            ],
        ];

        if (!isset($testUsers[$email])) {
            return response()->json(['error' => 'Invalid test user'], 400);
        }

        $userData = $testUsers[$email];

        // Ensure the KeycloakUser record exists
        \App\Models\KeycloakUser::updateOrCreate(
            ['email' => $email],
            [
                'keycloak_id' => $userData['keycloak_id'],
                'name' => $userData['name'],
                'role' => $userData['role'],
                'status' => 'active',
                'activated_at' => now(),
            ]
        );

        // Ensure the Employe record exists for employee role
        if ($userData['role'] === 'ROLE_EMPLOYEE') {
            \App\Models\Employe::updateOrCreate(
                ['email' => $email],
                [
                    'keycloak_id' => $userData['keycloak_id'],
                    'nom' => 'User',
                    'prenom' => 'Employee',
                    'poste' => 'Développeur',
                    'departement' => 'IT',
                    'date_embauche' => '2023-01-15',
                    'statut' => 'actif',
                    'conges_payes' => 25,
                    'conges_maladie' => 10,
                    'heures_recuperation' => 5,
                ]
            )
            ;
        }

        // Set session data
        session([
            'user_id' => $userData['keycloak_id'],
            'user_name' => $userData['name'],
            'user_email' => $email,
            'user_role' => $userData['role'],
        ]);

        return response()->json(['success' => true, 'role' => $userData['role']]);
    }
}