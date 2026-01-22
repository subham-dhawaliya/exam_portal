<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full system access',
        ]);

        Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'Student/User access',
        ]);
    }
}
