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
        $superAdminRole = Role::firstOrCreate(
            ['name_en' => 'Super Admin', 'name_ar' => 'المدير المسؤول', 'guard_name' => 'admin'],
        );
        $this->setAuditIfNew($superAdminRole);
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->pluck('key'));
    }
}
