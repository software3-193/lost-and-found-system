
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lostfound.test'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('Admin@12345'),
                'role' => User::ROLE_ADMIN,
            ]
        );
    }
}
