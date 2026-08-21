<?php

namespace Database\Seeders;

use App\Enums\AbsenceStatus;
use App\Models\Absence;
use App\Models\Employe;
use Illuminate\Database\Seeder;

class E2EAbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing test absences to avoid conflicts
        Absence::where(function($query) {
            $query->where('motif', 'like', '%Vacances%')
                  ->orWhere('motif', 'like', '%Grippe%')
                  ->orWhere('motif', 'like', '%Formation%')
                  ->orWhere('motif', 'like', '%Personnel%');
        })->delete();

        // Create test employees if they don't exist
        $employees = [
            [
                'nom' => 'User',
                'prenom' => 'Employee',
                'email' => 'employee@test.com', // Match test employee email
                'keycloak_id' => 'employee-keycloak-id-456', // Match test employee keycloak_id
                'poste' => 'Développeur',
                'departement' => 'IT',
                'date_embauche' => '2023-01-15',
                'statut' => 'actif',
                'conges_payes' => 25,
                'conges_maladie' => 10,
                'heures_recuperation' => 5,
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'marie.martin@test.com',
                'poste' => 'Designer',
                'departement' => 'Marketing',
                'date_embauche' => '2023-03-20',
                'statut' => 'actif',
                'conges_payes' => 25,
                'conges_maladie' => 10,
                'heures_recuperation' => 5,
            ],
        ];

        foreach ($employees as $employeeData) {
            Employe::firstOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );
        }

        // Ensure Keycloak users exist for E2E testing
        \App\Models\KeycloakUser::updateOrCreate(
            ['email' => 'employee@test.com'],
            [
                'keycloak_id' => 'employee-keycloak-id-456',
                'name' => 'Employee User',
                'role' => 'ROLE_EMPLOYEE',
                'status' => 'active',
                'activated_at' => now(),
            ]
        );

        \App\Models\KeycloakUser::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'keycloak_id' => 'admin-keycloak-id-123',
                'name' => 'Admin User',
                'role' => 'ROLE_ADMIN',
                'status' => 'active',
                'activated_at' => now(),
            ]
        );

        // Get the employees for assigning absences
        $employee = Employe::where('email', 'employee@test.com')->first();
        $marie = Employe::where('email', 'marie.martin@test.com')->first();

        if (!$employee || !$marie) {
            $this->command->error('Test employees not found. Please run the seeder again.');
            return;
        }

        // Create multiple pending absences for employee (for edit/delete tests)
        // We need multiple so each test can have its own pending absence
        Absence::create(
            [
                'employe_id' => $employee->id,
                'type' => 'Congé annuel',
                'date_debut' => '2024-02-01',
                'date_fin' => '2024-02-05',
                'nombre_jours' => 5,
                'motif' => 'Vacances famille - pour édition',
                'statut' => AbsenceStatus::PENDING->value,
                'reponse_at' => null,
            ]
        );

        Absence::create(
            [
                'employe_id' => $employee->id,
                'type' => 'Formation',
                'date_debut' => '2024-04-15',
                'date_fin' => '2024-04-16',
                'nombre_jours' => 2,
                'motif' => 'Formation technique - pour suppression',
                'statut' => AbsenceStatus::PENDING->value,
                'reponse_at' => null,
            ]
        );

        Absence::create(
            [
                'employe_id' => $employee->id,
                'type' => 'Maladie',
                'date_debut' => '2024-05-20',
                'date_fin' => '2024-05-21',
                'nombre_jours' => 2,
                'motif' => 'Maladie - supplémentaire',
                'statut' => AbsenceStatus::PENDING->value,
                'reponse_at' => null,
            ]
        );

        // Create approved absence for employee (for "should not edit/delete approved" tests)
        Absence::create(
            [
                'employe_id' => $employee->id,
                'type' => 'Maladie',
                'date_debut' => '2024-01-20',
                'date_fin' => '2024-01-22',
                'nombre_jours' => 3,
                'motif' => 'Grippe',
                'statut' => AbsenceStatus::APPROVED->value,
                'reponse_at' => now()->subDays(10),
            ]
        );

        // Create additional pending absences for admin tests
        Absence::create(
            [
                'employe_id' => $marie->id,
                'type' => 'Maladie',
                'date_debut' => '2024-03-10',
                'date_fin' => '2024-03-12',
                'nombre_jours' => 3,
                'motif' => 'Grippe',
                'statut' => AbsenceStatus::PENDING->value,
                'reponse_at' => null,
            ]
        );

        // Create additional approved absence for admin tests
        Absence::create(
            [
                'employe_id' => $marie->id,
                'type' => 'Congé annuel',
                'date_debut' => '2024-01-20',
                'date_fin' => '2024-01-22',
                'nombre_jours' => 3,
                'motif' => 'Vacances hiver',
                'statut' => AbsenceStatus::APPROVED->value,
                'reponse_at' => now()->subDays(10),
            ]
        );

        // Create rejected absence
        Absence::create(
            [
                'employe_id' => $employee->id,
                'type' => 'Sans solde',
                'date_debut' => '2024-01-05',
                'date_fin' => '2024-01-08',
                'nombre_jours' => 4,
                'motif' => 'Personnel',
                'statut' => AbsenceStatus::REJECTED->value,
                'reponse_at' => now()->subDays(15),
            ]
        );

        $this->command->info('E2E Absence Seeder completed successfully.');
        $this->command->info('Created test employees and absences with different statuses.');
    }
}
