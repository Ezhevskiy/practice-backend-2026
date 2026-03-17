<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('admin123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Админ создан или уже существует (email: admin@example.com / пароль: admin123)');
    }
}