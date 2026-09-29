<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Tom Hallett', 'password' => 'password'],
        );

        $this->call([
            CatalogSeeder::class,
            OrderSeeder::class,
            SiteSeeder::class,
        ]);
    }
}
