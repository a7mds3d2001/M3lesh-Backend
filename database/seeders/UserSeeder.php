<?php

namespace Database\Seeders;

use App\Models\User\User;
use Database\Seeders\Concerns\SeedsWithSuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use SeedsWithSuperAdmin;

    public function run(): void
    {
        $user = User::withTrashed()->updateOrCreate(
            ['phone' => '+201026272813'],
            [
                'email' => 'a7md@user.com',
                'name' => 'Ahmed Saad',
                'image' => 'defaults/user.png',
                'password' => Hash::make('123456'),
                'is_active' => true,
                'deleted_at' => null,
            ],
        );
        $this->setAuditIfNew($user);
    }
}
