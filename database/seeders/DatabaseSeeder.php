<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProcessSeeder::class,
            ContactSeeder::class,
            PropertySeeder::class,
            NotificationTemplateSeeder::class,
        ]);
    }
}
