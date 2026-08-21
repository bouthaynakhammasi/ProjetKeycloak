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
        Schema::table('salaires', function (Blueprint $table) {
            // Supprimer les anciens champs s'ils existent
            if (Schema::hasColumn('salaires', 'salaire_base')) {
                $table->dropColumn(['salaire_base', 'prime', 'retenue', 'statut_paiement', 'date_paiement', 'notes']);
            }
        });

        Schema::table('salaires', function (Blueprint $table) {
            // Ajouter les nouveaux champs s'ils n'existent pas
            if (!Schema::hasColumn('salaires', 'salaire_brut')) {
                $table->decimal('salaire_brut', 10, 2)->after('annee');
            }
            if (!Schema::hasColumn('salaires', 'deductions')) {
                $table->decimal('deductions', 10, 2)->after('salaire_brut');
            }
            if (!Schema::hasColumn('salaires', 'fichier_pdf')) {
                $table->string('fichier_pdf')->nullable()->after('salaire_net');
            }

            // Ajouter la contrainte unique si elle n'existe pas
            $constraintName = 'salaires_employe_id_mois_annee_unique';
            $hasConstraint = collect(DB::select("
                SELECT conname FROM pg_constraint
                WHERE conrelid = 'salaires'::regclass
                AND conname = '$constraintName'
            "))->isNotEmpty();

            if (!$hasConstraint) {
                $table->unique(['employe_id', 'mois', 'annee']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaires', function (Blueprint $table) {
            // Supprimer la contrainte unique
            $table->dropUnique(['employe_id', 'mois', 'annee']);

            // Supprimer les nouveaux champs
            $table->dropColumn(['salaire_brut', 'deductions', 'fichier_pdf']);
        });

        Schema::table('salaires', function (Blueprint $table) {
            // Restaurer les anciens champs
            $table->decimal('salaire_base', 10, 2)->after('annee');
            $table->decimal('prime', 10, 2)->after('salaire_base');
            $table->decimal('retenue', 10, 2)->after('prime');
            $table->string('statut_paiement')->default('en_attente')->after('salaire_net');
            $table->date('date_paiement')->nullable()->after('statut_paiement');
            $table->text('notes')->nullable()->after('date_paiement');
        });
    }
};
