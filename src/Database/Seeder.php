<?php

namespace LunoxHoshizaki\Database;

abstract class Seeder
{
    /**
     * Run the database seeds.
     */
    abstract public function run(): void;

    /**
     * Call another seeder class.
     */
    public function call(string $class): void
    {
        echo "⚡ Seeding: {$class}\n";
        $seeder = new $class();
        $seeder->run();
        echo "✅ Seeded: {$class}\n";
    }
}
