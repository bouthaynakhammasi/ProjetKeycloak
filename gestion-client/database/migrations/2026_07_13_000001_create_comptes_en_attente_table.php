<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comptes_en_attente', function (Blueprint $table) {
            $table->id();
            $table->string('keycloak_id')->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('email')->unique();
            $table->timestamp('date_detection')->useCurrent();
            $table->enum('statut', ['en_attente', 'traite'])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comptes_en_attente');
    }
};
?>
