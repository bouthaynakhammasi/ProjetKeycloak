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
        Schema::table('employes', function (Blueprint $table) {
            $table->integer('conges_payes')->default(25); // Jours de congés payés disponibles
            $table->integer('conges_maladie')->default(0); // Jours de maladie (illimité, mais peut être suivi)
            $table->integer('heures_recuperation')->default(0); // Heures de récupération disponibles
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn(['conges_payes', 'conges_maladie', 'heures_recuperation']);
        });
    }
};
