<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'rohitindiadev@gmail.com'],
            [
                'name'        => 'Rohit (Owner)',
                'password'    => Hash::make('12345678'),
                'role'        => 'super_admin',
                'admin_level' => 'owner',
                'restaurant_id' => null,
                'status'      => 'active',
            ]
        );

        $this->command->info('Owner admin rohitindiadev@gmail.com created/updated.');
    }
}
