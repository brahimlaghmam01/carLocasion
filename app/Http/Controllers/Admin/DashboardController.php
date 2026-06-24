<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CarStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Car;
use App\Models\City;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Route the connected user to the appropriate dashboard.
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAgencyAdmin()) {
            return $this->agencyDashboard();
        }

        return $this->superAdminDashboard();
    }

    /**
     * Global, company-wide overview for Super Admins.
     */
    private function superAdminDashboard()
    {
        $currency = config('app.currency_symbol');

        $totalRevenue = Payment::completed()->sum('amount');

        $kpis = [
            'totalRevenue' => $currency . number_format($totalRevenue, 2),
            'totalReservations' => Reservation::count(),
            'totalVehicles' => Car::count(),
            'totalCustomers' => User::clients()->count(),
            'totalCities' => City::count(),
            'totalAgencies' => Agency::count(),
        ];

        // Revenue by agency (top performing agencies)
        $revenueByAgency = Agency::query()
            ->withSum(['payments as revenue' => fn ($q) => $q->where('status', PaymentStatus::COMPLETED)], 'amount')
            ->with('city:id,name')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($agency) => [
                'id' => $agency->id,
                'name' => $agency->name,
                'city' => $agency->city?->name,
                'revenue' => round((float) $agency->revenue, 2),
                'formatted_revenue' => $currency . number_format((float) $agency->revenue, 2),
            ]);

        // Revenue by city
        $revenueByCity = City::query()
            ->with(['agencies' => fn ($q) => $q->withSum(['payments as revenue' => fn ($p) => $p->where('status', PaymentStatus::COMPLETED)], 'amount')])
            ->get()
            ->map(fn ($city) => [
                'name' => $city->name,
                'revenue' => round((float) $city->agencies->sum('revenue'), 2),
            ])
            ->sortByDesc('revenue')
            ->values();

        // Reservations by city
        $reservationsByCity = City::query()
            ->withCount(['agencies'])
            ->with('agencies:id,city_id')
            ->get()
            ->map(function ($city) {
                $agencyIds = $city->agencies->pluck('id');

                return [
                    'name' => $city->name,
                    'reservations' => Reservation::whereIn('agency_id', $agencyIds)->count(),
                ];
            })
            ->sortByDesc('reservations')
            ->values();

        $utilization = $this->vehicleUtilization();

        return Inertia::render('Admin/Dashboard/SuperAdmin', [
            'kpis' => $kpis,
            'revenueByAgency' => $revenueByAgency,
            'topAgencies' => $revenueByAgency->take(5)->values(),
            'revenueByCity' => $revenueByCity,
            'reservationsByCity' => $reservationsByCity,
            'monthlyReservations' => $this->monthlyReservations(),
            'vehicleUtilization' => $utilization,
            'recentActivities' => $this->recentActivities(),
        ]);
    }

    /**
     * Agency-specific overview. Queries are auto-scoped by the agency global scope.
     */
    private function agencyDashboard()
    {
        $currency = config('app.currency_symbol');
        $user = Auth::user();
        $agency = $user->agency()->with('city')->first();

        $totalRevenue = Payment::completed()->sum('amount');

        $kpis = [
            'revenue' => $currency . number_format($totalRevenue, 2),
            'reservations' => Reservation::count(),
            'vehicles' => Car::count(),
            'customers' => User::clients()->where('agency_id', $user->agency_id)->count(),
        ];

        return Inertia::render('Admin/Dashboard/Agency', [
            'agency' => $agency,
            'kpis' => $kpis,
            'monthlyRevenue' => $this->monthlyRevenue(),
            'monthlyReservations' => $this->monthlyReservations(),
            'vehicleAvailability' => $this->vehicleAvailability(),
            'recentActivities' => $this->recentActivities(),
        ]);
    }

    /**
     * Monthly reservation counts for the last 6 months (agency-scoped when applicable).
     */
    private function monthlyReservations(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = Reservation::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Monthly completed revenue for the last 6 months (agency-scoped when applicable).
     */
    private function monthlyRevenue(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = round((float) Payment::completed()
                ->whereYear('processed_at', $month->year)
                ->whereMonth('processed_at', $month->month)
                ->sum('amount'), 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Vehicle availability breakdown (agency-scoped when applicable).
     */
    private function vehicleAvailability(): array
    {
        return [
            'available' => Car::where('status', CarStatus::AVAILABLE)->count(),
            'rented' => Car::whereIn('status', [CarStatus::RENTED, CarStatus::RESERVED])->count(),
            'unavailable' => Car::whereIn('status', [
                CarStatus::MAINTENANCE,
                CarStatus::CLEANING,
                CarStatus::UNAVAILABLE,
                CarStatus::RETIRED,
            ])->count(),
        ];
    }

    /**
     * Global vehicle utilisation rate (rented / total).
     */
    private function vehicleUtilization(): array
    {
        $total = Car::count();
        $rented = Car::whereIn('status', [CarStatus::RENTED, CarStatus::RESERVED])->count();

        return [
            'total' => $total,
            'rented' => $rented,
            'rate' => $total > 0 ? round(($rented / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Recent activity log entries (agency-scoped for Agency Admins).
     */
    private function recentActivities()
    {
        $user = Auth::user();

        return \App\Models\ActivityLog::query()
            ->with('user:id,name')
            ->when($user->isAgencyAdmin(), fn ($q) => $q->where('agency_id', $user->agency_id))
            ->latest()
            ->limit(10)
            ->get(['id', 'user_id', 'action', 'model', 'record_id', 'description', 'created_at'])
            ->map(fn ($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'System',
                'action' => $log->action,
                'model' => class_basename($log->model),
                'record_id' => $log->record_id,
                'description' => $log->description,
                'created_at' => $log->created_at?->diffForHumans(),
            ]);
    }
}
