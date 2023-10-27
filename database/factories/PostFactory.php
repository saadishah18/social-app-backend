<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::pluck('id')->toArray();
        return [
            'user_id' => fake()->randomElement($users),
            'content' => fake()->realText,
            'client_name' => fake()->name,
            'client_email' => fake()->unique()->safeEmail(),
            'client_signature' => fake()->image,
        ];
    }
}
