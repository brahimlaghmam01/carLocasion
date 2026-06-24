<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Seed the RBAC roles. Idempotent via updateOrCreate on the unique slug.
     */
    public function run(): void
    {
        $roles = [
            ['slug' => 'super-admin', 'name' => 'Super Admin', 'description' => 'Full, company-wide access.'],
            ['slug' => 'agency-admin', 'name' => 'Agency Admin', 'description' => 'Manages a single agency.'],
            ['slug' => 'client', 'name' => 'Client', 'description' => 'Customer account.'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
