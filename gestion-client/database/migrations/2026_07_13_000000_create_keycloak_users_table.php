<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keycloak_users', function (Blueprint $table) {
            $table->id();
            $table->string('keycloak_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['ROLE_ADMIN', 'ROLE_EMPLOYEE'])->nullable();
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keycloak_users');
    }
};
