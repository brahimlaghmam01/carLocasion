<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

/**
 * Super Admins and the legacy Admin bypass these checks via Gate::before.
 * Agency Admins may only manage reservations belonging to their own agency.
 */
class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAgencyAdmin();
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $this->ownsAgency($user, $reservation);
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $this->ownsAgency($user, $reservation);
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $this->ownsAgency($user, $reservation);
    }

    private function ownsAgency(User $user, Reservation $reservation): bool
    {
        return $user->isAgencyAdmin()
            && $user->agency_id !== null
            && $user->agency_id === $reservation->agency_id;
    }
}
