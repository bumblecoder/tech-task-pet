<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

final class CreateDatabase extends Command
{
    protected $signature = 'db:create';

    protected $description = 'Create the database if it does not exist';

    public function handle(): void
    {
        $database = Config::get('database.connections.mysql.database');

        Config::set('database.connections.mysql.database', null);

        if (!$this->databaseExists($database)) {
            DB::statement("CREATE DATABASE IF NOT EXISTS `$database`");
            $this->info("Database '$database' created successfully.");
        } else {
            $this->info("Database '$database' already exists.");
        }

        Config::set('database.connections.mysql.database', $database);
    }

    protected function databaseExists(string $database): bool
    {
        $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$database]);

        return !empty($result);
    }
}
