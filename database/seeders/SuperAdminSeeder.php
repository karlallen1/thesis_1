<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        Admin::updateOrCreate(
            ['username' => 'nccadmin'],
            [
                'password' => Hash::make('ncc2025'),
                'role' => 'super_admin',
                'is_seeded' => true,
            ]
        );
    }
}