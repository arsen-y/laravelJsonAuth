<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Тестовый Пользователь1',
            'email' => 'test1@example.com',
            'password' => Hash::make('Password123'), 
        ]);

        User::create([
            'name' => 'Тестовый Пользователь2',
            'email' => 'test2@example.com',
            'password' => Hash::make('Password123'), 
        ]);

        User::create([
            'name' => 'Тестовый Пользователь3',
            'email' => 'test3@example.com',
            'password' => Hash::make('Password123'),
        ]);

        
    }
}
