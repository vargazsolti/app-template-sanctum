<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserBaseData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoUsersAndBaseDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('user_basedata')->truncate();
        DB::table('model_has_roles')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::transaction(function () {
            $users = [];
            for ($i = 1; $i <= 10; $i++) {
                $users[] = [
                    'name' => "User $i",
                    'email' => strtolower("user{$i}@example.com"),
                    'password' => Hash::make('password'),
                ];
            }
            User::insert($users);

            foreach (User::all() as $index => $user) {
                UserBaseData::create([
                    'user_id' => $user->id,
                    'full_name' => "Full Name $index",
                    'mothers_name' => "Mother $index",
                    'birth_date' => now()->subYears(20 + $index),
                    'birth_place' => "City $index",
                    'id_card_number' => 'ID' . str_pad($index, 6, '0', STR_PAD_LEFT),
                    'social_security_number' => 'SSN' . str_pad($index, 6, '0', STR_PAD_LEFT),
                ]);
            }
        });

        $map = [
            'user1@example.com' => 'admin',
            'user2@example.com' => 'manager',
            'user3@example.com' => 'user',
        ];

        foreach (User::all() as $user) {
            $role = $map[$user->email] ?? 'user';
            $user->assignRole($role);
        }

        \Artisan::call('permission:cache-reset');
    }
}
