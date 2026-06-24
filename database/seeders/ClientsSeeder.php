<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Role;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientRoleId = Role::where('slug', 'client')->value('id');
        $agencyIds = Agency::query()->orderBy('id')->pluck('id')->all();
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Robert Miller',
                'email' => 'robert.miller@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Chris Taylor',
                'email' => 'chris.taylor@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => true,
            ],
            [
                'name' => 'Amanda White',
                'email' => 'amanda.white@example.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::CLIENT,
                'is_active' => false, // Inactive user
            ],
        ];

        foreach ($users as $index => $userData) {
            $userData['role_id'] = $clientRoleId;
            $userData['agency_id'] = $agencyIds !== []
                ? $agencyIds[$index % count($agencyIds)]
                : null;

            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

    }
}
