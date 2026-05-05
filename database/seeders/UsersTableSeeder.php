<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['id' => 1, 'name' => 'Ch Awais', 'email' => 'awais@example.com', 'password' => Hash::make('password')],
            ['id' => 2, 'name' => 'Mubashar', 'email' => 'mubashar@example.com', 'password' => Hash::make('password')],
            ['id' => 3, 'name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')],
            ['id' => 4, 'name' => 'Noman', 'email' => 'noman@example.com', 'password' => Hash::make('password')],
            ['id' => 5, 'name' => 'Abdul Rehman', 'email' => 'abdulrehman@example.com', 'password' => Hash::make('password')],
            ['id' => 6, 'name' => 'Esha', 'email' => 'esha@example.com', 'password' => Hash::make('password')],
            ['id' => 7, 'name' => 'Mudasr', 'email' => 'mudasr@example.com', 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
