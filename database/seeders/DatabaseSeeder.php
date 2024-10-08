<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Products\IceTypeSeeder;
use Database\Seeders\Products\VatRateSeeder;
use Database\Seeders\Products\Seeder as ProductSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Receipt\Type as RecieptTypeSeeder;
use Database\Seeders\Establishments\Seeder as EstablishmentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'test_2@example.com',
        ]);

        $this->call([
            VatRateSeeder::class,
            IceTypeSeeder::class,
            ProductSeeder::class,
            RecieptTypeSeeder::class,
            EstablishmentSeeder::class
        ]);
    }
}
