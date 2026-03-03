<?php

namespace LunoxHoshizaki\Database;

use PDO;
use Exception;

class Migrator
{
    protected PDO $pdo;
    protected string $migrationsPath;

    public function __construct(PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = rtrim($migrationsPath, '/\\');
        
        $this->createMigrationsTableIfNotExists();
    }

    /**
     * Ensure the tracking table exists.
     */
    protected function createMigrationsTableIfNotExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->pdo->exec($sql);
    }

    /**
     * Run all pending migrations.
     */
    public function run(): array
    {
        $executed = [];
        $files = $this->getMigrationFiles();
        
        if (empty($files)) {
            return [];
        }

        $ranMigrations = $this->getRanMigrations();
        $pending = array_diff($files, $ranMigrations);

        if (empty($pending)) {
            return [];
        }

        $batch = $this->getNextBatchNumber();

        foreach ($pending as $file) {
            $this->runMigration($file, $batch);
            $executed[] = $file;
        }

        return $executed;
    }

    /**
     * Get all migration files in the directory.
     */
    protected function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = scandir($this->migrationsPath);
        $migrations = [];
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $migrations[] = $file;
            }
        }

        sort($migrations);
        return $migrations;
    }

    /**
     * Get migrations that have already been run.
     */
    protected function getRanMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations ORDER BY batch ASC, migration ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Determine the next batch number.
     */
    protected function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        $batch = (int) $stmt->fetchColumn();
        return $batch + 1;
    }

    /**
     * Execute a single migration file.
     */
    protected function runMigration(string $file, int $batch): void
    {
        $path = $this->migrationsPath . '/' . $file;
        require_once $path;

        // Parse class name from filename: e.g. 2026_02_22_064438_create_users_table.php -> CreateUsersTable
        $filenameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
        // Strip the timestamp part (first 18 characters typically: YYYY_MM_DD_HHMMSS_)
        $namePart = preg_replace('/^[0-9_]+_/', '', $filenameWithoutExt);
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $namePart)));

        if (!class_exists($className)) {
            throw new Exception("Migration class {$className} not found in {$file}");
        }

        $migration = new $className();
        
        if (!method_exists($migration, 'up')) {
            throw new Exception("Migration class {$className} does not have an 'up' method");
        }

        try {
            // Run the 'up' method
            $migration->up();
            
            // Log it
            $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
            $stmt->execute([$file, $batch]);
            
        } catch (Exception $e) {
            throw new Exception("Migration failed [{$file}]: " . $e->getMessage());
        }
    }
}
