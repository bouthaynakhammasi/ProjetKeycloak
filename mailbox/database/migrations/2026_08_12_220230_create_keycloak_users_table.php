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
        Schema::create('keycloak_users', function (Blueprint $table) {
            $table->id();
            $table->string('keycloak_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keycloak_users');
    }
};
