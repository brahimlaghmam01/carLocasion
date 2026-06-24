<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Agency;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superRoleId = Role::where('slug', 'super-admin')->value('id');
        $agencyRoleId = Role::where('slug', 'agency-admin')->value('id');
        $clientRoleId = Role::where('slug', 'client')->value('id');
        $firstAgency = Agency::query()->orderBy('id')->first();

        // Super Admin (company-wide)
        User::query()->updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('00000000'),
                'role' => UserRole::SUPER_ADMIN,
                'role_id' => $superRoleId,
                'agency_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Legacy admin user (kept for backwards compatibility)
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('00000000'),
                'role' => UserRole::ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Agency Admin (scoped to the first agency)
        User::query()->updateOrCreate(
            ['email' => 'agency@example.com'],
            [
                'name' => 'Agency Admin',
                'password' => Hash::make('00000000'),
                'role' => UserRole::AGENCY_ADMIN,
                'role_id' => $agencyRoleId,
                'agency_id' => $firstAgency?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Client user
        User::query()->updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Client User',
                'password' => Hash::make('00000000'),
                'role' => UserRole::CLIENT,
                'role_id' => $clientRoleId,
                'agency_id' => $firstAgency?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
