<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'username' => $this->faker->username(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make($this->faker->password),
            'role' => User::ROLE_NORMAL,
            'language' => User::LANGUAGE_ENGLISH,
            'theme' => $this->faker->randomElement([User::THEME_LIGHT, User::THEME_DARK]),
            'active' => true
        ];
    }

    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null
        ]);
    }
    public function password($password)
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password)
        ]);
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN
        ]);
    }
}
