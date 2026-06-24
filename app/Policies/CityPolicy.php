<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;

/**
 * Cities are a company-wide resource. Only Super Admins may manage them, which
 * is granted through the Gate::before bypass registered in AppServiceProvider.
 * All other roles are denied here.
 */
class CityPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, City $city): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, City $city): bool
    {
        return false;
    }

    public function delete(User $user, City $city): bool
    {
        return false;
    }
}
