<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->string('localisation')->nullable()->after('telephone');
            $table->text('bio')->nullable()->after('photo');
            $table->boolean('notifications_actives')->default(false)->after('bio');
            $table->string('coordonnees_bancaires')->nullable()->after('notifications_actives');
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn(['localisation', 'bio', 'notifications_actives', 'coordonnees_bancaires']);
        });
    }
};
