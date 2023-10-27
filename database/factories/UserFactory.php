<?php

namespace Database\Factories;

use App\Models\Plans;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
//        $plans = Plans::pluck('id')->toArray();
        $roles = ['Admin', 'Employee', 'User'];
        return [
            'user_name' => 'sadmin',
            'email' => 'sadmin@socialblast.us',
            'created_by' => null,
            'email_verified_at' => now(),
            'password' => Hash::make('@Dmin123'), // password
            'role' => 'sadmin',
            'plan_id' => 2,
            'is_approved' => 1,
            'is_active' => 1,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if ($user->id == 1) {
                $user->user_name = 'sadmin';
                $user->email = 'sadmin@dds.com';
                $user->created_by = null;
                $user->save();
            } elseif ($user->id == 2) {
                $user->user_name = 'dealer';
                $user->email = 'delaler1@dds.com';
                $user->created_by = 1;
                $user->save();
            }
        });
    }
}
