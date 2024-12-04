<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userUtama = User::create([
            'name' => 'Yanuarso',
            'email' => 'yan@mail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10)
        ]);
        $userUtama->syncRoles('superadmin');

        $userAdmin = User::create([
            'name' => 'Babyla',
            'email' => 'babyla@mail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10)
        ]);
        $userAdmin->syncRoles('admin');

        User::factory(5)->create()->each(function ($user) {
            $user->syncRoles('user');
        });
    }
}
