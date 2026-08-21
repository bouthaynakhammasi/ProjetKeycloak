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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->enum('type_conge', ['annuel', 'maladie', 'exceptionnel']);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->text('motif')->nullable();
            $table->enum('status', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->foreignId('approuve_par')->nullable()->constrained('employes')->onDelete('set null');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
