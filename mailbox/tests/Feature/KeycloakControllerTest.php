<?php

namespace Tests\Feature;

use App\Models\KeycloakUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class KeycloakControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que la redirection vers Keycloak fonctionne.
     */
    public function test_redirect_to_keycloak_login(): void
    {
        $response = $this->get(route('keycloak.redirect'));
        $response->assertRedirect();
    }

    /**
     * Test que la déconnexion fonctionne correctement.
     */
    public function test_logout_works(): void
    {
        $user = KeycloakUser::factory()->create();

        Session::put('keycloak_user', [
            'sub' => $user->keycloak_id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $response = $this->post(route('keycloak.logout'));
        $response->assertRedirect('/');

        // Vérifier que la session a été invalidée
        $this->assertNull(Session::get('keycloak_user'));
    }

    /**
     * Test que l'utilisateur non authentifié est redirigé vers Keycloak.
     */
    public function test_unauthenticated_user_redirected_to_keycloak(): void
    {
        $response = $this->get(route('mailbox.index'));
        $response->assertRedirect(route('keycloak.redirect'));
    }

    /**
     * Test que l'utilisateur authentifié peut accéder à la boîte de réception.
     */
    public function test_authenticated_user_can_access_mailbox(): void
    {
        $user = KeycloakUser::factory()->create();

        Session::put('keycloak_user', [
            'sub' => $user->keycloak_id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->withoutMiddleware([\App\Http\Middleware\KeycloakAuthenticate::class]);

        $response = $this->get(route('mailbox.index'));
        $response->assertStatus(200);
    }
}