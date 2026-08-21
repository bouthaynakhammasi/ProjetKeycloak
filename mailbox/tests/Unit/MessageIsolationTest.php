<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\KeycloakUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test qu'un utilisateur ne peut voir que ses propres messages.
     */
    public function test_user_can_only_see_own_messages(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();

        // Créer des messages pour l'utilisateur A
        Message::factory()->forUser($userA->keycloak_id)->count(5)->create();

        // Créer des messages pour l'utilisateur B
        Message::factory()->forUser($userB->keycloak_id)->count(3)->create();

        // L'utilisateur A ne doit voir que ses 5 messages
        $userAMessages = Message::forUser($userA->keycloak_id)->get();
        $this->assertCount(5, $userAMessages);
        $this->assertTrue($userAMessages->every(fn ($message) => $message->keycloak_user_id === $userA->keycloak_id));

        // L'utilisateur B ne doit voir que ses 3 messages
        $userBMessages = Message::forUser($userB->keycloak_id)->get();
        $this->assertCount(3, $userBMessages);
        $this->assertTrue($userBMessages->every(fn ($message) => $message->keycloak_user_id === $userB->keycloak_id));
    }

    /**
     * Test que l'utilisateur A ne peut pas voir les messages reçus par l'utilisateur B.
     */
    public function test_user_a_cannot_see_user_b_received_messages(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();
        $sender = KeycloakUser::factory()->create();

        // Créer un message reçu par l'utilisateur B
        Message::factory()->create([
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $userB->keycloak_id,
            'recipient_id' => $userB->keycloak_id,
            'recipient_email' => $userB->email,
            'folder' => 'inbox',
        ]);

        // L'utilisateur A ne doit pas voir ce message
        $userAMessages = Message::forUser($userA->keycloak_id)->get();
        $this->assertCount(0, $userAMessages);

        // L'utilisateur B doit voir ce message
        $userBMessages = Message::forUser($userB->keycloak_id)->get();
        $this->assertCount(1, $userBMessages);
    }

    /**
     * Test que l'utilisateur A ne peut pas voir les messages envoyés par l'utilisateur B.
     */
    public function test_user_a_cannot_see_user_b_sent_messages(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        // Créer un message envoyé par l'utilisateur B
        Message::factory()->create([
            'sender_id' => $userB->keycloak_id,
            'keycloak_user_id' => $userB->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
            'folder' => 'sent',
        ]);

        // L'utilisateur A ne doit pas voir ce message
        $userAMessages = Message::forUser($userA->keycloak_id)->get();
        $this->assertCount(0, $userAMessages);

        // L'utilisateur B doit voir ce message
        $userBMessages = Message::forUser($userB->keycloak_id)->get();
        $this->assertCount(1, $userBMessages);
    }

    /**
     * Test l'isolation entre les boîtes de réception.
     */
    public function test_inbox_isolation_between_users(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();
        $sender = KeycloakUser::factory()->create();

        // Créer des messages dans la boîte de réception de l'utilisateur A
        Message::factory()->inbox()->create([
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $userA->keycloak_id,
            'recipient_id' => $userA->keycloak_id,
            'recipient_email' => $userA->email,
        ]);

        // Créer des messages dans la boîte de réception de l'utilisateur B
        Message::factory()->inbox()->create([
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $userB->keycloak_id,
            'recipient_id' => $userB->keycloak_id,
            'recipient_email' => $userB->email,
        ]);

        // Vérifier que chaque utilisateur ne voit que sa propre boîte de réception
        $userAInbox = Message::forUser($userA->keycloak_id)->inbox()->get();
        $userBInbox = Message::forUser($userB->keycloak_id)->inbox()->get();

        $this->assertCount(1, $userAInbox);
        $this->assertCount(1, $userBInbox);
        $this->assertEquals($userA->keycloak_id, $userAInbox->first()->recipient_id);
        $this->assertEquals($userB->keycloak_id, $userBInbox->first()->recipient_id);
    }

    /**
     * Test l'isolation entre les messages envoyés.
     */
    public function test_sent_isolation_between_users(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        // Créer des messages envoyés par l'utilisateur A
        Message::factory()->sent()->create([
            'sender_id' => $userA->keycloak_id,
            'keycloak_user_id' => $userA->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
        ]);

        // Créer des messages envoyés par l'utilisateur B
        Message::factory()->sent()->create([
            'sender_id' => $userB->keycloak_id,
            'keycloak_user_id' => $userB->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
        ]);

        // Vérifier que chaque utilisateur ne voit que ses propres messages envoyés
        $userASent = Message::forUser($userA->keycloak_id)->inFolder('sent')->get();
        $userBSent = Message::forUser($userB->keycloak_id)->inFolder('sent')->get();

        $this->assertCount(1, $userASent);
        $this->assertCount(1, $userBSent);
        $this->assertEquals($userA->keycloak_id, $userASent->first()->sender_id);
        $this->assertEquals($userB->keycloak_id, $userBSent->first()->sender_id);
    }

    /**
     * Test que la modification de l'URL ne permet pas d'accéder aux messages d'un autre utilisateur.
     */
    public function test_url_manipulation_cannot_access_other_user_messages(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();

        // Créer un message pour l'utilisateur B
        $messageForB = Message::factory()->create([
            'keycloak_user_id' => $userB->keycloak_id,
            'recipient_id' => $userB->keycloak_id,
            'recipient_email' => $userB->email,
        ]);

        // Essayer d'accéder au message avec l'ID de l'utilisateur A
        try {
            Message::forUser($userA->keycloak_id)->findOrFail($messageForB->id);
            $this->fail('L\'utilisateur A ne devrait pas pouvoir accéder au message de l\'utilisateur B');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertTrue(true, 'L\'utilisateur A ne peut pas accéder au message de l\'utilisateur B');
        }
    }

    /**
     * Test que les messages sans destinataire valide sont correctement gérés.
     */
    public function test_messages_without_valid_recipient_are_handled(): void
    {
        $user = KeycloakUser::factory()->create();

        // Créer un message avec un destinataire externe (pas dans la base de données)
        $externalMessage = Message::factory()->create([
            'sender_id' => $user->keycloak_id,
            'keycloak_user_id' => $user->keycloak_id,
            'recipient_id' => null,
            'recipient_email' => 'external@example.com',
            'folder' => 'sent',
        ]);

        $this->assertNull($externalMessage->recipient_id);
        $this->assertEquals('external@example.com', $externalMessage->recipient_email);
        $this->assertEquals($user->keycloak_id, $externalMessage->keycloak_user_id);
    }

    /**
     * Test que recipient_id correspond bien au destinataire Keycloak.
     */
    public function test_recipient_id_matches_keycloak_recipient(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $sender->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
        ]);

        $this->assertEquals($recipient->keycloak_id, $message->recipient_id);
        $this->assertEquals($recipient->email, $message->recipient_email);
    }

    /**
     * Test que recipient_email correspond bien à l'email du destinataire.
     */
    public function test_recipient_email_matches_recipient_email(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create(['email' => 'specific@example.com']);

        $message = Message::factory()->create([
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $sender->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
        ]);

        $this->assertEquals('specific@example.com', $message->recipient_email);
        $this->assertEquals($recipient->email, $message->recipient_email);
    }
}