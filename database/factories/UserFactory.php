<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRoleEnum;
use App\Enums\UserAccountStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'uuid' => 'UID' . $this->faker->unique()->numberBetween(10000, 99999),
            'full_name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRoleEnum::ADMIN,
            'status' => UserAccountStatusEnum::ACTIVE,
            'language' => 'en',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function user(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => UserRoleEnum::USER,
            'status' => UserAccountStatusEnum::ACTIVE,
        ]);
    }
}
