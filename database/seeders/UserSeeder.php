<?php

namespace Database\Seeders;

use App\Enums\UserAccountStatusEnum;
use App\Enums\UserGenderEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Reset table and sequences safely across drivers
        Schema::disableForeignKeyConstraints();
        User::truncate(); // Postgres: TRUNCATE ... RESTART IDENTITY CASCADE
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        if (app()->environment('production')) {
            $fixedUsers = [
                [
                    'full_name'         => 'Super Admin',
                    'email'             => 'superadmin@deepgrow.io',
                    'role'              => UserRoleEnum::SUPER_ADMIN->value,
                    'status'            => UserAccountStatusEnum::ACTIVE->value,
                    'password'          => Hash::make('12345678Aa#'),
                    'email_verified_at' => $now,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ],
                [
                    'full_name'         => 'Mobile User 1',
                    'email'             => 'user1@deepgrow.io',
                    'role'              => UserRoleEnum::USER->value,
                    'status'            => UserAccountStatusEnum::ACTIVE->value,
                    'gender'            => UserGenderEnum::MALE->value,
                    'date_of_birth'     => '2000-01-01',
                    'password'          => Hash::make('12345678Aa#'),
                    'email_verified_at' => $now,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ],
            ];
            foreach ($fixedUsers as $data) {
                User::create($data);
            }
            return;
        }

        // Create fixed users FIRST (no explicit id -> they get ids 1..4)
        $fixedUsers = [
            [
                'full_name'         => 'Super Admin',
                'email'             => 'superadmin@deepgrow.io',
                'role'              => UserRoleEnum::SUPER_ADMIN->value,
                'status'            => UserAccountStatusEnum::ACTIVE->value,
                'password'          => Hash::make('12345678Aa#'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'full_name'         => 'Admin User',
                'email'             => 'admin@deepgrow.io',
                'role'              => UserRoleEnum::ADMIN->value,
                'status'            => UserAccountStatusEnum::ACTIVE->value,
                'password'          => Hash::make('12345678Aa#'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'full_name'         => 'Dev User',
                'email'             => 'dev@deepgrow.io',
                'role'              => UserRoleEnum::DEV->value,
                'status'            => UserAccountStatusEnum::ACTIVE->value,
                'password'          => Hash::make('12345678Aa#'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'full_name'         => 'Mobile User 1',
                'email'             => 'user1@deepgrow.io',
                'role'              => UserRoleEnum::USER->value,
                'status'            => UserAccountStatusEnum::ACTIVE->value,
                'gender'            => UserGenderEnum::MALE->value,
                'date_of_birth'     => '2000-01-01',
                'password'          => Hash::make('12345678Aa#'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'full_name'         => 'Mobile User 2',
                'email'             => 'user2@deepgrow.io',
                'role'              => UserRoleEnum::USER->value,
                'status'            => UserAccountStatusEnum::ACTIVE->value,
                'gender'            => UserGenderEnum::MALE->value,
                'date_of_birth'     => '2000-01-01',
                'password'          => Hash::make('12345678Aa#'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        foreach ($fixedUsers as $data) {
            User::create($data);
        }

        // create 5 mobile users with ids 5..9
        User::factory()->count(5)->create([
            'role' => UserRoleEnum::USER->value,
            'password' => Hash::make('12345678Aa#'),
            'status' => UserAccountStatusEnum::ACTIVE->value,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // // Top up to 100 total
        // $remaining = max(0, 100 - User::count());
        // if ($remaining > 0) {
        //     User::factory()->count($remaining)->create();
        // }
    }
}
