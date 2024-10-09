<?php

namespace Database\Seeders\Persons;

use App\Models\Persons\Model as Person;
use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::factory()->count(90)->create();
    }
}
