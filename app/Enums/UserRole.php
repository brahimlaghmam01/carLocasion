<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case AGENCY_ADMIN = 'agency_admin';
    case ADMIN = 'admin';
    case CLIENT = 'client';

    /**
     * Human readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::AGENCY_ADMIN => 'Agency Admin',
            self::ADMIN => 'Admin',
            self::CLIENT => 'Client',
        };
    }

    /**
     * Roles that can access the administration area.
     *
     * @return array<int, self>
     */
    public static function adminRoles(): array
    {
        return [self::SUPER_ADMIN, self::AGENCY_ADMIN, self::ADMIN];
    }
}
