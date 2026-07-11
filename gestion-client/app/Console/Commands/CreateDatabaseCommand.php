<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDOException;

class CreateDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database specified in the configuration if it does not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connectionName = Config::get('database.default');
        $database = Config::get("database.connections.{$connectionName}.database");
        $charset = Config::get("database.connections.{$connectionName}.charset", 'utf8mb4');
        $collation = Config::get("database.connections.{$connectionName}.collation", 'utf8mb4_unicode_ci');

        if (!$database) {
            $this->error('Aucune base de données configurée.');
            return 1;
        }

        if ($database === env('DB_DATABASE', 'forge') && $connectionName === 'sqlite') {
            // Pour sqlite, c'est un fichier
            $this->error('Cette commande est prévue pour MySQL/PostgreSQL, pas pour SQLite.');
            return 1;
        }

        try {
            // Retirer temporairement le nom de la base pour se connecter au serveur MySQL globalement
            Config::set("database.connections.{$connectionName}.database", null);
            
            DB::purge($connectionName);
            
            // Exécuter la requête
            $query = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET $charset COLLATE $collation;";
            DB::connection($connectionName)->statement($query);

            $this->info("Succès : La base de données '{$database}' a été créée (ou existe déjà).");

            // Rétablir la configuration
            Config::set("database.connections.{$connectionName}.database", $database);
            DB::purge($connectionName);

        } catch (PDOException $e) {
            $this->error('Erreur de connexion MySQL : Accès refusé ou serveur injoignable.');
            $this->error('Détails techniques : ' . $e->getMessage());
            $this->error('Veuillez vérifier que XAMPP (ou votre serveur MySQL) est bien démarré et que les identifiants dans .env sont corrects.');
            return 1;
        }

        return 0;
    }
}
