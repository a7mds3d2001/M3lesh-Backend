<?php

namespace Database\Seeders;

use App\Models\User\Admin;
use App\Models\User\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /*
        * Run the database seeds.
    */
    public function run(): void
    {
        $superAdminRole = Role::where('name_en', 'Super Admin')
            ->where('guard_name', 'admin')
            ->first();

        // Create or update Super Admin user (admin_type = system owner, not editable/deletable)
        $superAdmin = Admin::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'),
                'phone' => '+201026272813',
                'is_active' => true,
                'admin_type' => Admin::TYPE_SUPER_ADMIN,
            ],
        );

        // Assign Super Admin role
        if ($superAdminRole) {
            $superAdmin->syncRoles([$superAdminRole]);
        }
    }
}
