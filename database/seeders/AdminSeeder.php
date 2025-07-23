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
            'username' => 'mainadmin',
            'password' => Hash::make('password'), // Use a strong one!
            'role' => 'main_admin'
        ]);
    }
}
