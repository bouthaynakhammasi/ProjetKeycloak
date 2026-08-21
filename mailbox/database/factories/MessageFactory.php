<?php

namespace Database\Factories;

use App\Models\KeycloakUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sender = KeycloakUser::factory()->create();
        $recipient = KeycloakUser::factory()->create();

        return [
            'sender_id' => $sender->keycloak_id,
            'keycloak_user_id' => $sender->keycloak_id,
            'recipient_id' => $recipient->keycloak_id,
            'recipient_email' => $recipient->email,
            'sender_name' => $sender->name,
            'sender_email' => $sender->email,
            'subject' => fake()->sentence(),
            'body' => fake()->paragraphs(3, true),
            'folder' => fake()->randomElement(['inbox', 'sent', 'drafts', 'trash']),
            'is_read' => fake()->boolean(),
            'is_starred' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the message is in the inbox.
     */
    public function inbox(): static
    {
        return $this->state(fn (array $attributes) => [
            'folder' => 'inbox',
        ]);
    }

    /**
     * Indicate that the message is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'folder' => 'sent',
        ]);
    }

    /**
     * Indicate that the message is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'folder' => 'drafts',
        ]);
    }

    /**
     * Indicate that the message is in trash.
     */
    public function trash(): static
    {
        return $this->state(fn (array $attributes) => [
            'folder' => 'trash',
        ]);
    }

    /**
     * Indicate that the message is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Indicate that the message is starred.
     */
    public function starred(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_starred' => true,
        ]);
    }

    /**
     * Indicate that the message is for a specific user.
     */
    public function forUser(string $keycloakUserId): static
    {
        return $this->state(fn (array $attributes) => [
            'keycloak_user_id' => $keycloakUserId,
        ]);
    }

    /**
     * Indicate that the message is from a specific sender.
     */
    public function fromUser(string $senderId): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $senderId,
        ]);
    }

    /**
     * Indicate that the message is for a specific recipient.
     */
    public function toUser(string $recipientId, string $recipientEmail): static
    {
        return $this->state(fn (array $attributes) => [
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
        ]);
    }
}