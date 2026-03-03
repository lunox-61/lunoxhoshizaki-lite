<?php

namespace Database\Seeders;

use LunoxHoshizaki\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
    }
}
