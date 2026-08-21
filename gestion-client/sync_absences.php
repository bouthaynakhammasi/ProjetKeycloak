<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$absences = \App\Models\Absence::where('statut', 'approved')->get();
foreach($absences as $abs) {
    \App\Models\Event::updateOrCreate(
        [
            'employe_id' => $abs->employe_id,
            'start_date' => $abs->date_debut,
            'end_date' => $abs->date_fin
        ],
        [
            'title' => $abs->type,
            'type' => 'conge',
            'description' => $abs->motif
        ]
    );
}
echo "Sync OK";
