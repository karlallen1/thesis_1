<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'username' => 'nccadmin',
            'password' => Hash::make('nccadmin2025'),
            'role' => 'super_admin'
        ]);
    }
}
