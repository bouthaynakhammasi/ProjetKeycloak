<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Vérification Timezone ===\n\n";
echo "Timezone PHP: " . date_default_timezone_get() . "\n";
echo "Timezone Laravel: " . config('app.timezone') . "\n";
echo "Timezone DB: " . config('database.connections.pgsql.timezone') . "\n\n";

echo "=== Test Carbon ===\n";
echo "Carbon::now(): " . \Carbon\Carbon::now() . "\n";
echo "Carbon::today(): " . \Carbon\Carbon::today() . "\n";
echo "Carbon::now()->format('H:i'): " . \Carbon\Carbon::now()->format('H:i') . "\n\n";

echo "=== Test enregistrement présence ===\n";
$testTime = \Carbon\Carbon::now()->format('H:i');
$testDate = \Carbon\Carbon::today()->format('Y-m-d');
echo "Temps test: {$testTime}, Date test: {$testDate}\n\n";

echo "=== Dernières présences ===\n";
$presences = \App\Models\Presence::orderBy('date', 'desc')->limit(5)->get();
foreach ($presences as $presence) {
    echo "Date: {$presence->date}, Arrivée: {$presence->heure_connexion}, Départ: {$presence->heure_depart}\n";
}
