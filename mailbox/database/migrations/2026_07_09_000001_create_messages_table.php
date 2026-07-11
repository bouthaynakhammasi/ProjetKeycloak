<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('keycloak_user_id');          // 'sub' du token Keycloak (destinataire)
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('subject');
            $table->text('body');
            $table->enum('folder', ['inbox', 'sent', 'drafts', 'trash'])->default('inbox');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->timestamps();

            $table->index(['keycloak_user_id', 'folder']);
            $table->index(['keycloak_user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
