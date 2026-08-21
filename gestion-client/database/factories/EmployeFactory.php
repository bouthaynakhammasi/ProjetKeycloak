<?php

namespace Database\Factories;

use App\Models\Employe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employe>
 */
class EmployeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employe::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'keycloak_id' => $this->faker->uuid(),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->phoneNumber(),
            'localisation' => $this->faker->city(),
            'bio' => $this->faker->sentence(),
            'notifications_actives' => true,
            'coordonnees_bancaires' => json_encode([
                'iban' => $this->faker->iban('FR'),
                'bic' => $this->faker->swiftBicNumber(),
            ]),
            'poste' => $this->faker->randomElement(['Développeur', 'Designer', 'Manager', 'Comptable', 'RH']),
            'departement' => $this->faker->randomElement(['IT', 'Marketing', 'Finance', 'RH', 'Operations']),
            'date_embauche' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'statut' => 'actif',
            'photo' => null,
            'conges_payes' => 25,
            'conges_maladie' => 10,
            'heures_recuperation' => 5,
        ];
    }
}
