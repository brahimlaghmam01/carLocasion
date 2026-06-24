<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;

/**
 * Agencies are managed by Super Admins (granted via the Gate::before bypass).
 * Agency Admins may only view their own agency.
 */
class AgencyPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Agency $agency): bool
    {
        return $user->isAgencyAdmin() && $user->agency_id === $agency->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Agency $agency): bool
    {
        return false;
    }

    public function delete(User $user, Agency $agency): bool
    {
        return false;
    }
}
