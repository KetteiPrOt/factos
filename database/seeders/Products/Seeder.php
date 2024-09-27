<?php

namespace Database\Seeders\Products;

use App\Models\Products\Model as Product;
use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(90)->create();
    }
}
