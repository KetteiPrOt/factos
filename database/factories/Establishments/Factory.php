<?php

namespace Database\Factories\Establishments;

use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;
use App\Models\Establishments\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class Factory extends BaseFactory
{
    protected $model = Model::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => fake()->sentence(),
            'commercial_name' => fake()->name()
        ];
    }
}
