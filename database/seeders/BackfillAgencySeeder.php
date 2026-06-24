<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Car;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

/**
 * Assigns an agency to legacy records created before the multi-agency upgrade.
 * Cars are distributed across agencies; reservations and payments inherit the
 * agency of their related car. Safe to run repeatedly (only touches null rows).
 */
class BackfillAgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencyIds = Agency::query()->orderBy('id')->pluck('id')->all();

        if ($agencyIds === []) {
            return;
        }

        // Distribute cars without an agency across the available agencies.
        Car::query()->whereNull('agency_id')->orderBy('id')->get()
            ->each(function (Car $car, int $index) use ($agencyIds) {
                $car->forceFill(['agency_id' => $agencyIds[$index % count($agencyIds)]])->save();
            });

        // Reservations inherit the agency of their car.
        Reservation::query()->whereNull('agency_id')->with('car:id,agency_id')->get()
            ->each(function (Reservation $reservation) {
                if ($reservation->car?->agency_id) {
                    $reservation->forceFill(['agency_id' => $reservation->car->agency_id])->save();
                }
            });

        // Payments inherit the agency of their reservation.
        Payment::query()->whereNull('agency_id')->with('reservation:id,agency_id')->get()
            ->each(function (Payment $payment) {
                if ($payment->reservation?->agency_id) {
                    $payment->forceFill(['agency_id' => $payment->reservation->agency_id])->save();
                }
            });
    }
}
