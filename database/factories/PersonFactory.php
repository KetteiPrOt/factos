<?php

namespace Database\Factories;

use App\Models\Persons\IdentificationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Persons\Model as Person;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $max_user_id = User::orderBy('id', 'desc')->first()->id;
        $max_identification_type_id = IdentificationType::orderBy('id', 'desc')->first()->id;
        return [
            'identification' => $this->randomNumber(13),
            'social_reason' => fake()->name(),
            'email' => fake()->email(),
            'phone_number' => $this->randomNumber(10),
            'address' => fake()->sentence(),
            'identification_type_id' => fake()->numberBetween(1, $max_identification_type_id),
            'user_id' => fake()->numberBetween(1, $max_user_id),
        ];
    }

    private function randomNumber(int $digits): string
    {
        $number = '';
        for($i = 0; $i < $digits; $i++) $number .= fake()->randomDigit();
        return $number;
    }
}
