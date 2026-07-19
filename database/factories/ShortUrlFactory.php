<?php

namespace Database\Factories;

use App\Models\ShortUrl;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends Factory<ShortUrl>
 */
class ShortUrlFactory extends Factory
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
            'company_id'   => Company::factory(),
            'user_id'      => User::factory(),
            'original_url' => fake()->url(),
            'code'         => Str::random(6),
        ];
    }
}
