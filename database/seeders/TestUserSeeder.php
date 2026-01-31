<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'whatsapp' => '31988887777',
                'cidade' => 'Belo Horizonte',
                'role' => 'user',
                'status' => 'pendente', // importante pra testar ativação
                'password' => Hash::make('root12345'),
            ]
        );
    }
}
