<?php

namespace Database\Factories\Products;

use App\Models\Products\IceType;
use App\Models\Products\VatRate;
use App\Models\User;
use App\Models\Products\Model as Product;
use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class Factory extends BaseFactory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $max_user_id = User::orderBy('id', 'desc')->first()->id;
        $max_ice_type_id = IceType::orderBy('id', 'desc')->first()->id;
        $max_vat_rate_id = VatRate::orderBy('id', 'desc')->first()->id;
        $ice_applies = fake()->boolean();
        return [
            'code' => fake()->randomNumber(4, true),
            'name' => ucwords(fake()->words(3, true)),
            'price' => fake()->randomFloat(2, 0.01, 999999.99),
            'additional_info' => fake()->boolean() ? fake()->text(255) : null,
            'tourism_vat_applies' => fake()->boolean(),
            'ice_applies' => $ice_applies,
            'user_id' => fake()->numberBetween(1, $max_user_id),
            'ice_type_id' => $ice_applies ? fake()->numberBetween(1, $max_ice_type_id) : null,
            'vat_rate_id' => fake()->numberBetween(1, $max_vat_rate_id)
        ];
    }
}
