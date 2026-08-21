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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->string('type'); // congé annuel, maladie, sans solde, etc.
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_jours')->default(1);
            $table->text('motif')->nullable();
            $table->enum('statut', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('reponse_at')->nullable();
            $table->timestamps();

            $table->index(['employe_id', 'statut']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
