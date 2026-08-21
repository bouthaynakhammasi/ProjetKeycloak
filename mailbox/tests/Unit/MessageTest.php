<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\KeycloakUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test qu'un message peut être créé avec les attributs par défaut.
     */
    public function test_message_can_be_created(): void
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $sender->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'subject' => 'Test Subject',
            'body' => 'Test Body',
            'folder' => 'inbox',
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'subject' => 'Test Subject',
        ]);

        $this->assertEquals($sender->keycloak_id, $message->sender_id);
        $this->assertEquals($recipient->keycloak_id, $message->recipient_id);
        $this->assertEquals('Test Subject', $message->subject);
    }

    /**
     * Test le scope inbox().
     */
    public function test_inbox_scope(): void
    {
        Message::factory()->inbox()->count(3)->create();
        Message::factory()->sent()->count(2)->create();
        Message::factory()->draft()->count(1)->create();

        $inboxMessages = Message::inbox()->get();

        $this->assertCount(3, $inboxMessages);
        $this->assertTrue($inboxMessages->every(fn ($message) => $message->folder === 'inbox'));
    }

    /**
     * Test le scope unread().
     */
    public function test_unread_scope(): void
    {
        Message::factory()->unread()->count(4)->create();
        Message::factory()->read()->count(3)->create();

        $unreadMessages = Message::unread()->get();

        $this->assertCount(4, $unreadMessages);
        $this->assertTrue($unreadMessages->every(fn ($message) => !$message->is_read));
    }

    /**
     * Test le scope starred().
     */
    public function test_starred_scope(): void
    {
        Message::factory()->starred()->count(2)->create();
        Message::factory()->create(['is_starred' => false]);

        $starredMessages = Message::starred()->get();

        $this->assertCount(2, $starredMessages);
        $this->assertTrue($starredMessages->every(fn ($message) => $message->is_starred));
    }

    /**
     * Test le scope inFolder().
     */
    public function test_in_folder_scope(): void
    {
        Message::factory()->inbox()->count(2)->create();
        Message::factory()->sent()->count(3)->create();
        Message::factory()->trash()->count(1)->create();

        $sentMessages = Message::inFolder('sent')->get();

        $this->assertCount(3, $sentMessages);
        $this->assertTrue($sentMessages->every(fn ($message) => $message->folder === 'sent'));
    }

    /**
     * Test le scope forUser().
     */
    public function test_for_user_scope(): void
    {
        $user1 = KeycloakUser::factory()->create();
        $user2 = KeycloakUser::factory()->create();

        Message::factory()->forUser($user1->keycloak_id)->count(3)->create();
        Message::factory()->forUser($user2->keycloak_id)->count(2)->create();

        $user1Messages = Message::forUser($user1->keycloak_id)->get();
        $user2Messages = Message::forUser($user2->keycloak_id)->get();

        $this->assertCount(3, $user1Messages);
        $this->assertCount(2, $user2Messages);
        $this->assertTrue($user1Messages->every(fn ($message) => $message->keycloak_user_id === $user1->keycloak_id));
    }

    /**
     * Test l'attribut isUnread.
     */
    public function test_is_unread_attribute(): void
    {
        $readMessage = Message::factory()->read()->create();
        $unreadMessage = Message::factory()->unread()->create();

        $this->assertFalse($readMessage->is_unread);
        $this->assertTrue($unreadMessage->is_unread);
    }

    /**
     * Test les casts de boolean.
     */
    public function test_boolean_casts(): void
    {
        $message = Message::factory()->create([
            'is_read' => true,
            'is_starred' => false,
        ]);

        $this->assertIsBool($message->is_read);
        $this->assertIsBool($message->is_starred);
        $this->assertTrue($message->is_read);
        $this->assertFalse($message->is_starred);
    }

    /**
     * Test que recipient_id peut être null pour les messages externes.
     */
    public function test_recipient_id_can_be_null(): void
    {
        $message = Message::factory()->create([
            'recipient_id' => null,
            'recipient_email' => 'external@example.com',
        ]);

        $this->assertNull($message->recipient_id);
        $this->assertEquals('external@example.com', $message->recipient_email);
    }

    /**
     * Test l'attribut relativeDate.
     */
    public function test_relative_date_attribute(): void
    {
        $todayMessage = Message::factory()->create([
            'created_at' => now(),
        ]);

        $yesterdayMessage = Message::factory()->create([
            'created_at' => now()->subDay(),
        ]);

        $this->assertIsString($todayMessage->relative_date);
        $this->assertIsString($yesterdayMessage->relative_date);
        $this->assertEquals('Hier', $yesterdayMessage->relative_date);
    }
}