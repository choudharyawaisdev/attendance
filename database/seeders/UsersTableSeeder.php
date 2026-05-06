<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users to ensure only these seeded users exist
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $users = [
            ['id' => 1, 'name' => 'Ch Awais', 'email' => 'awais@gmail.com', 'password' => Hash::make('password')],
            ['id' => 2, 'name' => 'Mubashar', 'email' => 'mubashar@gmail.com', 'password' => Hash::make('password')],
            ['id' => 3, 'name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => Hash::make('password')],
            ['id' => 4, 'name' => 'Noman', 'email' => 'noman@gmail.com', 'password' => Hash::make('password')],
            ['id' => 5, 'name' => 'Abdul Rehman', 'email' => 'abdulrehman@gmail.com', 'password' => Hash::make('password')],
            ['id' => 6, 'name' => 'Esha', 'email' => 'esha@gmail.com', 'password' => Hash::make('password')],
            ['id' => 7, 'name' => 'Mudasr', 'email' => 'mudasr@gmail.com', 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
