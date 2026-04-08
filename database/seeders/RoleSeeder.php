<?php

namespace Database\Seeders;

use App\Models\User\Role;
use Database\Seeders\Concerns\SeedsWithSuperAdmin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    use SeedsWithSuperAdmin;

    public function run(): void
    {
        // Include `name` so the row satisfies Spatie's legacy `roles.name` column (builder
        // `firstOrCreate` does not use our Role::create() helper that copies name_en → name).
        $superAdminRole = Role::firstOrCreate(
            ['name_en' => 'Super Admin', 'guard_name' => 'admin'],
            [
                'name_ar' => 'المدير المسؤول',
                'name' => 'Super Admin',
            ],
        );
        $this->setAuditIfNew($superAdminRole);
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->pluck('key'));
    }
}
