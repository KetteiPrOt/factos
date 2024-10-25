<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\User;
use Database\Seeders\Products\IceTypeSeeder;
use Database\Seeders\Products\VatRateSeeder;
use Database\Seeders\Products\Seeder as ProductSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Receipt\Type as RecieptTypeSeeder;
use Database\Seeders\Establishments\Seeder as EstablishmentSeeder;
use Database\Seeders\Persons\IdentificationTypeSeeder;
use Database\Seeders\Persons\Seeder as PersonSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Define if we create fake data in the database.
     */
    private bool $fakeData = true;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Predefined tables by SRI
        $this->call([
            VatRateSeeder::class,
            IceTypeSeeder::class,
            RecieptTypeSeeder::class,
            IdentificationTypeSeeder::class,
        ]);

        // Fake data for testing
        if($this->fakeData){
            $user0 = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            Certificate::create(['user_id' => $user0->id]);

            $user1 = User::factory()->create([
                'name' => 'Test User 2',
                'email' => 'test_2@example.com',
            ]);
            Certificate::create(['user_id' => $user1->id]);

            $this->call([
                ProductSeeder::class,
                EstablishmentSeeder::class,
                PersonSeeder::class,
            ]);
        }
    }
}
