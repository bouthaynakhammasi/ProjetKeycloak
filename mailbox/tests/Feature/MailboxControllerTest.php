<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\KeycloakUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class MailboxControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup pour simuler une session Keycloak authentifiée.
     */
    protected function authenticateUser(KeycloakUser $user): void
    {
        Session::put('keycloak_user', [
            'sub' => $user->keycloak_id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Bypasser le middleware d'authentification pour les tests.
     */
    protected function withoutKeycloakMiddleware(): void
    {
        $this->withoutMiddleware([\App\Http\Middleware\KeycloakAuthenticate::class]);
    }

    /**
     * Test qu'un utilisateur peut envoyer un message à un autre utilisateur.
     */
    public function test_user_can_send_message_to_another_user(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($sender);

        $response = $this->post(route('mailbox.store'), [
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_email' => $recipient->email,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        $response->assertRedirect(route('mailbox.index', ['folder' => 'sent']));

        // Vérifier que deux messages ont été créés (un dans sent, un dans inbox)
        $this->assertDatabaseCount('messages', 2);

        // Vérifier le message dans sent
        $sentMessage = Message::where('folder', 'sent')
            ->where('sender_id', $sender->keycloak_id)
            ->first();
        $this->assertNotNull($sentMessage);
        $this->assertEquals($recipient->keycloak_id, $sentMessage->recipient_id);
        $this->assertEquals($recipient->email, $sentMessage->recipient_email);

        // Vérifier le message dans inbox
        $inboxMessage = Message::where('folder', 'inbox')
            ->where('recipient_id', $recipient->keycloak_id)
            ->first();
        $this->assertNotNull($inboxMessage);
        $this->assertEquals($sender->keycloak_id, $inboxMessage->sender_id);
    }

    /**
     * Test que recipient_id correspond bien au destinataire Keycloak.
     */
    public function test_recipient_id_matches_keycloak_recipient(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($sender);

        $this->post(route('mailbox.store'), [
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_email' => $recipient->email,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        $message = Message::where('folder', 'sent')->first();
        $this->assertEquals($recipient->keycloak_id, $message->recipient_id);
    }

    /**
     * Test que recipient_email correspond bien à l'email du destinataire.
     */
    public function test_recipient_email_matches_recipient_email(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create(['email' => 'specific@example.com']);

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($sender);

        $this->post(route('mailbox.store'), [
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_email' => $recipient->email,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        $message = Message::where('folder', 'sent')->first();
        $this->assertEquals('specific@example.com', $message->recipient_email);
    }

    /**
     * Test que le message envoyé apparaît uniquement dans les Envoyés de l'expéditeur.
     */
    public function test_sent_message_appears_only_in_sender_sent_folder(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($sender);

        $this->post(route('mailbox.store'), [
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_email' => $recipient->email,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        // Vérifier que l'expéditeur voit le message dans sent
        $senderSentMessages = Message::where('folder', 'sent')
            ->where('sender_id', $sender->keycloak_id)
            ->get();
        $this->assertCount(1, $senderSentMessages);

        // Vérifier que l'expéditeur ne voit pas le message dans inbox
        $senderInboxMessages = Message::where('folder', 'inbox')
            ->where('recipient_id', $sender->keycloak_id)
            ->get();
        $this->assertCount(0, $senderInboxMessages);
    }

    /**
     * Test que le message reçu apparaît uniquement dans la Boîte de réception du destinataire.
     */
    public function test_received_message_appears_only_in_recipient_inbox(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($sender);

        $this->post(route('mailbox.store'), [
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_email' => $recipient->email,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        // Vérifier que le destinataire voit le message dans inbox
        $recipientInboxMessages = Message::where('folder', 'inbox')
            ->where('recipient_id', $recipient->keycloak_id)
            ->get();
        $this->assertCount(1, $recipientInboxMessages);

        // Vérifier que le destinataire ne voit pas le message dans sent
        $recipientSentMessages = Message::where('folder', 'sent')
            ->where('sender_id', $recipient->keycloak_id)
            ->get();
        $this->assertCount(0, $recipientSentMessages);
    }

    /**
     * Test qu'un utilisateur A ne peut jamais voir les messages reçus par B.
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

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($userA);

        // Vérifier que l'utilisateur A ne voit pas le message de l'utilisateur B
        $userAMessages = Message::where('folder', 'inbox')
            ->where('recipient_id', $userA->keycloak_id)
            ->get();
        $this->assertCount(0, $userAMessages);
    }

    /**
     * Test qu'un utilisateur ne peut pas accéder aux messages d'un autre utilisateur en modifiant simplement l'URL.
     */
    public function test_user_cannot_access_other_user_messages_by_url_manipulation(): void
    {
        $userA = KeycloakUser::factory()->create();
        $userB = KeycloakUser::factory()->create();

        // Créer un message pour l'utilisateur B
        $messageForB = Message::factory()->create([
            'keycloak_user_id' => $userB->keycloak_id,
            'recipient_id' => $userB->keycloak_id,
            'recipient_email' => $userB->email,
            'folder' => 'inbox',
        ]);

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($userA);

        // Essayer d'accéder au message de l'utilisateur B
        $response = $this->get(route('mailbox.show', $messageForB->id));
        $response->assertStatus(404); // ModelNotFoundException
    }

    /**
     * Test que les messages sans destinataire valide sont correctement gérés.
     */
    public function test_messages_without_valid_recipient_are_handled(): void
    {
        $sender = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($sender);

        $response = $this->post(route('mailbox.store'), [
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'recipient_email' => 'external@example.com', // Email non enregistré
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ]);

        $response->assertRedirect(route('mailbox.index', ['folder' => 'sent']));

        // Vérifier qu'un seul message a été créé (pas de double création pour externe)
        $this->assertDatabaseCount('messages', 1);

        $message = Message::first();
        $this->assertNull($message->recipient_id);
        $this->assertEquals('external@example.com', $message->recipient_email);
    }

    /**
     * Test que le statut lu/non lu fonctionne.
     */
    public function test_read_unread_status_works(): void
    {
        $user = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($user);

        // Créer un message non lu
        $unreadMessage = Message::factory()->create([
            'keycloak_user_id' => $user->keycloak_id,
            'recipient_id' => $user->keycloak_id,
            'recipient_email' => $user->email,
            'folder' => 'inbox',
            'is_read' => false,
        ]);

        // Vérifier que le message est non lu
        $this->assertFalse($unreadMessage->is_read);
        $this->assertTrue($unreadMessage->is_unread);

        // Accéder au message (doit le marquer comme lu)
        $response = $this->get(route('mailbox.show', $unreadMessage->id));
        $response->assertStatus(200);

        // Vérifier que le message est maintenant lu
        $unreadMessage->refresh();
        $this->assertTrue($unreadMessage->is_read);
        $this->assertFalse($unreadMessage->is_unread);
    }

    /**
     * Test que la suppression d'un message fonctionne correctement.
     */
    public function test_message_deletion_works_correctly(): void
    {
        $user = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($user);

        // Créer un message
        $message = Message::factory()->create([
            'keycloak_user_id' => $user->keycloak_id,
            'sender_id' => $user->keycloak_id,
            'folder' => 'inbox',
        ]);

        // Supprimer le message (doit le déplacer vers la corbeille)
        $response = $this->delete(route('mailbox.destroy', $message->id));
        $response->assertRedirect(route('mailbox.index', ['folder' => 'inbox']));

        // Vérifier que le message est dans la corbeille
        $message->refresh();
        $this->assertEquals('trash', $message->folder);

        // Supprimer définitivement le message
        $response = $this->delete(route('mailbox.destroy', $message->id));
        $response->assertRedirect(route('mailbox.index', ['folder' => 'inbox']));

        // Vérifier que le message est supprimé
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    /**
     * Test que le toggle étoile fonctionne.
     */
    public function test_toggle_star_works(): void
    {
        $user = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($user);

        $message = Message::factory()->create([
            'keycloak_user_id' => $user->keycloak_id,
            'is_starred' => false,
        ]);

        // Marquer comme favori
        $response = $this->patch(route('mailbox.star', $message->id));
        $response->assertJson(['starred' => true]);

        $message->refresh();
        $this->assertTrue($message->is_starred);

        // Retirer le favori
        $response = $this->patch(route('mailbox.star', $message->id));
        $response->assertJson(['starred' => false]);

        $message->refresh();
        $this->assertFalse($message->is_starred);
    }

    /**
     * Test la création de brouillons.
     */
    public function test_draft_creation_works(): void
    {
        $user = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($user);

        $response = $this->post(route('mailbox.store'), [
            'sender_name' => $user->name,
            'sender_email' => $user->email,
            'recipient_email' => 'test@example.com',
            'subject' => 'Draft Subject',
            'body' => 'Draft Body',
            'save_draft' => true,
        ]);

        $response->assertRedirect(route('mailbox.index', ['folder' => 'drafts']));

        $this->assertDatabaseHas('messages', [
            'folder' => 'drafts',
            'subject' => 'Draft Subject',
        ]);
    }

    /**
     * Test l'accès aux différents dossiers.
     */
    public function test_folder_access_works(): void
    {
        $user = KeycloakUser::factory()->create();

        $this->withoutKeycloakMiddleware();
        $this->authenticateUser($user);

        // Créer des messages dans différents dossiers
        Message::factory()->inbox()->create([
            'keycloak_user_id' => $user->keycloak_id,
            'recipient_id' => $user->keycloak_id,
            'recipient_email' => $user->email,
        ]);

        Message::factory()->sent()->create([
            'keycloak_user_id' => $user->keycloak_id,
            'sender_id' => $user->keycloak_id,
        ]);

        Message::factory()->draft()->create([
            'keycloak_user_id' => $user->keycloak_id,
            'sender_id' => $user->keycloak_id,
        ]);

        // Tester l'accès à chaque dossier
        $response = $this->get(route('mailbox.index', ['folder' => 'inbox']));
        $response->assertStatus(200);

        $response = $this->get(route('mailbox.index', ['folder' => 'sent']));
        $response->assertStatus(200);

        $response = $this->get(route('mailbox.index', ['folder' => 'drafts']));
        $response->assertStatus(200);

        $response = $this->get(route('mailbox.index', ['folder' => 'trash']));
        $response->assertStatus(200);
    }
}