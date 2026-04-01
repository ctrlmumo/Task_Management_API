<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This is the master seeder — it calls all other seeders.
     * Add any new seeders here as your project grows.
     */
    public function run(): void
    {
        $this->call([
            TaskSeeder::class,
        ]);
    }
}
