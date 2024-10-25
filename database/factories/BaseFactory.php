<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    protected function randomNumber(int $digits): string
    {
        $number = '';
        for($i = 0; $i < $digits; $i++) $number .= fake()->randomDigit();
        return $number;
    }
}
