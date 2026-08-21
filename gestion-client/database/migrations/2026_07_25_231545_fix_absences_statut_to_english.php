<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les données existantes de français vers anglais
        DB::statement("UPDATE absences SET statut = 'pending' WHERE statut = 'en_attente'");
        DB::statement("UPDATE absences SET statut = 'approved' WHERE statut = 'approuvee'");
        DB::statement("UPDATE absences SET statut = 'rejected' WHERE statut = 'refusee'");

        // Mettre à jour la contrainte CHECK pour utiliser les valeurs anglaises
        DB::statement("ALTER TABLE absences DROP CONSTRAINT IF EXISTS check_statut");
        DB::statement("ALTER TABLE absences ADD CONSTRAINT check_statut CHECK (statut IN ('pending', 'approved', 'rejected'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux valeurs françaises
        DB::statement("UPDATE absences SET statut = 'en_attente' WHERE statut = 'pending'");
        DB::statement("UPDATE absences SET statut = 'approuvee' WHERE statut = 'approved'");
        DB::statement("UPDATE absences SET statut = 'refusee' WHERE statut = 'rejected'");

        // Revenir à la contrainte CHECK française
        DB::statement("ALTER TABLE absences DROP CONSTRAINT IF EXISTS check_statut");
        DB::statement("ALTER TABLE absences ADD CONSTRAINT check_statut CHECK (statut IN ('en_attente', 'approuvee', 'refusee'))");
    }
};
