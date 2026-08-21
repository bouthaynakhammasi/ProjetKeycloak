<?php

namespace Tests\Unit;

use App\Models\KeycloakUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeycloakUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test qu'un utilisateur Keycloak peut être créé avec les attributs par défaut.
     */
    public function test_keycloak_user_can_be_created(): void
    {
        $user = KeycloakUser::factory()->create([
            'keycloak_id' => 'test-keycloak-id',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('keycloak_users', [
            'keycloak_id' => 'test-keycloak-id',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('test-keycloak-id', $user->keycloak_id);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /**
     * Test la méthode isPending().
     */
    public function test_is_pending_method(): void
    {
        $pendingUser = KeycloakUser::factory()->pending()->create();
        $activeUser = KeycloakUser::factory()->active()->create();

        $this->assertTrue($pendingUser->isPending());
        $this->assertFalse($activeUser->isPending());
    }

    /**
     * Test la méthode isActive().
     */
    public function test_is_active_method(): void
    {
        $activeUser = KeycloakUser::factory()->active()->create();
        $pendingUser = KeycloakUser::factory()->pending()->create();

        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($pendingUser->isActive());
    }

    /**
     * Test la méthode isRejected().
     */
    public function test_is_rejected_method(): void
    {
        $rejectedUser = KeycloakUser::factory()->rejected()->create();
        $activeUser = KeycloakUser::factory()->active()->create();

        $this->assertTrue($rejectedUser->isRejected());
        $this->assertFalse($activeUser->isRejected());
    }

    /**
     * Test l'attribut roleLabel.
     */
    public function test_role_label_attribute(): void
    {
        $adminUser = KeycloakUser::factory()->admin()->create();
        $employeeUser = KeycloakUser::factory()->employee()->create();
        $noRoleUser = KeycloakUser::factory()->create(['role' => null]);

        $this->assertEquals('Administrateur', $adminUser->role_label);
        $this->assertEquals('Employé', $employeeUser->role_label);
        $this->assertEquals('Aucun rôle', $noRoleUser->role_label);
    }

    /**
     * Test le scope pending().
     */
    public function test_pending_scope(): void
    {
        KeycloakUser::factory()->active()->count(3)->create();
        KeycloakUser::factory()->pending()->count(2)->create();

        $pendingUsers = KeycloakUser::pending()->get();

        $this->assertCount(2, $pendingUsers);
        $this->assertTrue($pendingUsers->every(fn ($user) => $user->status === 'pending'));
    }

    /**
     * Test le scope active().
     */
    public function test_active_scope(): void
    {
        KeycloakUser::factory()->active()->count(5)->create();
        KeycloakUser::factory()->pending()->count(3)->create();

        $activeUsers = KeycloakUser::active()->get();

        $this->assertCount(5, $activeUsers);
        $this->assertTrue($activeUsers->every(fn ($user) => $user->status === 'active'));
    }

    /**
     * Test que l'email est unique.
     */
    public function test_email_must_be_unique(): void
    {
        KeycloakUser::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        KeycloakUser::factory()->create([
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test que le keycloak_id est unique.
     */
    public function test_keycloak_id_must_be_unique(): void
    {
        KeycloakUser::factory()->create([
            'keycloak_id' => 'unique-keycloak-id',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        KeycloakUser::factory()->create([
            'keycloak_id' => 'unique-keycloak-id',
        ]);
    }
}