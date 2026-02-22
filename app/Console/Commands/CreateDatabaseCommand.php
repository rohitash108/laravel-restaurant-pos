<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class CreateDatabaseCommand extends Command
{
    protected $signature = 'db:create-database
                            {--database= : The database name (default: from .env DB_DATABASE)}';

    protected $description = 'Create the MySQL database if it does not exist';

    public function handle(): int
    {
        $database = $this->option('database') ?? env('DB_DATABASE', 'cspos');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        $charset = env('DB_CHARSET', 'utf8mb4');
        $collation = env('DB_COLLATION', 'utf8mb4_unicode_ci');

        try {
            $dsn = "mysql:host={$host};port={$port};charset={$charset}";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec(
                "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}"
            );

            $this->info("Database '{$database}' created successfully (or already exists).");
            return self::SUCCESS;
        } catch (PDOException $e) {
            $this->error('Could not create database: ' . $e->getMessage());
            $this->line('Check your .env: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD');
            return self::FAILURE;
        }
    }
}
