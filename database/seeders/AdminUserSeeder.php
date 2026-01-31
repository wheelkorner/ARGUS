<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@argus.local'],
            [
                'name' => 'Admin ARGUS',
                'whatsapp' => '31999999999',
                'cidade' => 'Sistema',
                'role' => 'admin',
                'status' => 'ativo',
                'password' => Hash::make('root12345'),
            ]
        );
    }
}
