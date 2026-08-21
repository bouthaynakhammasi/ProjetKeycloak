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
        // Modifier l'enum statut pour utiliser les valeurs françaises
        DB::statement("ALTER TABLE absences ALTER COLUMN statut TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE absences ADD CONSTRAINT check_statut CHECK (statut IN ('en_attente', 'approuvee', 'refusee'))");

        // Mettre à jour les données existantes
        DB::statement("UPDATE absences SET statut = 'en_attente' WHERE statut = 'pending'");
        DB::statement("UPDATE absences SET statut = 'approuvee' WHERE statut = 'approved'");
        DB::statement("UPDATE absences SET statut = 'refusee' WHERE statut = 'rejected'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux valeurs anglaises
        DB::statement("UPDATE absences SET statut = 'pending' WHERE statut = 'en_attente'");
        DB::statement("UPDATE absences SET statut = 'approved' WHERE statut = 'approuvee'");
        DB::statement("UPDATE absences SET statut = 'rejected' WHERE statut = 'refusee'");

        DB::statement("ALTER TABLE absences DROP CONSTRAINT check_statut");
        DB::statement("ALTER TABLE absences ALTER COLUMN statut TYPE VARCHAR(20)");
    }
};
