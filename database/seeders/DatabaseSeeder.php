<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mariposa@papilia.net'],
            [
                'name' => 'Papilia',
                'password' => Hash::make('12345678'),
                'can_create_users' => true,
                'can_manage_events' => true,
                'can_access_trash' => true,
                'can_manage_animations' => true,
            ]
        );
    }
}
