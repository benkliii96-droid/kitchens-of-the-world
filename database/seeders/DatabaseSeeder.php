<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@champ2026'],
            [
                'fullname' => 'Администратор',
                'password' => 'champ2026',
                'role' => User::ROLE_ADMIN,
            ]
        );
    }
}
