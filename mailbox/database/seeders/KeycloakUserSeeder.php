<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KeycloakUser;

class KeycloakUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utiliser les vrais IDs Keycloak des utilisateurs connectés
        // Ces IDs proviennent de la session Keycloak réelle
        
        KeycloakUser::updateOrCreate(
            ['keycloak_id' => '0b91ad6a-34d7-4402-9964-258d1e7af9fa'],
            [
                'name' => 'Bouthaina Khamassi',
                'email' => 'khamassibouthaina2021@gmail.com',
                'role' => 'ROLE_EMPLOYEE',
                'status' => 'active',
                'activated_at' => now(),
            ]
        );

        KeycloakUser::updateOrCreate(
            ['keycloak_id' => 'd3dd9a30-af29-448b-b5b9-4f293e452d57'],
            [
                'name' => 'Bouthaina Khamassi',
                'email' => 'khamassibouthaina@esprit.tn',
                'role' => 'ROLE_EMPLOYEE',
                'status' => 'active',
                'activated_at' => now(),
            ]
        );

        // Garder les utilisateurs de test pour les tests fonctionnels
        KeycloakUser::updateOrCreate(
            ['keycloak_id' => 'admin-123'],
            [
                'name' => 'Administrateur Test',
                'email' => 'admin@test.com',
                'role' => 'ROLE_ADMIN',
                'status' => 'active',
                'activated_at' => now(),
            ]
        );
    }
}
