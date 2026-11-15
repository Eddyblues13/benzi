<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * An activated customer account.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type' => '0',
            'is_activated' => '1',
            'currency' => '$',
            'a_number' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'account_type' => 'Savings',
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'country' => 'United States',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['user_type' => '1']);
    }

    /**
     * Has registered but not yet entered the emailed activation code.
     */
    public function unactivated(): static
    {
        return $this->state(fn () => [
            'is_activated' => '0',
            'email_verified_at' => null,
            'token' => '4321',
        ]);
    }

    /**
     * Has the admin-issued transfer codes, SSN and a loan limit.
     */
    public function withCodes(): static
    {
        return $this->state(fn () => [
            'otp' => '1111',
            'ccic_code' => '2222',
            'int_code' => '3333',
            'ssn' => '123456789',
            'eligible_loan' => 5000,
        ]);
    }
}
