<?php

namespace Database\Factories;

use App\Enums\AbsenceStatus;
use App\Models\Absence;
use App\Models\Employe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Absence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 5) . ' days');

        return [
            'employe_id' => Employe::factory(),
            'type' => $this->faker->randomElement(['conge_paye', 'maladie', 'sans_solde', 'formation']),
            'date_debut' => $startDate->format('Y-m-d'),
            'date_fin' => $endDate->format('Y-m-d'),
            'nombre_jours' => $startDate->diff($endDate)->days + 1,
            'motif' => $this->faker->randomElement(['Vacances', 'Maladie', 'Formation', 'Famille', 'Personnel']),
            'statut' => AbsenceStatus::PENDING->value,
            'reponse_at' => null,
        ];
    }

    /**
     * Indicate that the absence is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => AbsenceStatus::PENDING->value,
            'reponse_at' => null,
        ]);
    }

    /**
     * Indicate that the absence is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => AbsenceStatus::APPROVED->value,
            'reponse_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the absence is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => AbsenceStatus::REJECTED->value,
            'reponse_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
