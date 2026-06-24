<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;

/**
 * Super Admins and the legacy Admin bypass these checks via Gate::before.
 * Agency Admins may only manage vehicles belonging to their own agency.
 */
class CarPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAgencyAdmin();
    }

    public function view(User $user, Car $car): bool
    {
        return $this->ownsAgency($user, $car);
    }

    public function create(User $user): bool
    {
        return $user->isAgencyAdmin();
    }

    public function update(User $user, Car $car): bool
    {
        return $this->ownsAgency($user, $car);
    }

    public function delete(User $user, Car $car): bool
    {
        return $this->ownsAgency($user, $car);
    }

    private function ownsAgency(User $user, Car $car): bool
    {
        return $user->isAgencyAdmin()
            && $user->agency_id !== null
            && $user->agency_id === $car->agency_id;
    }
}
