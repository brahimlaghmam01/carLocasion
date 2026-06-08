<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomePagesController extends Controller
{
    public function index()
    {
        $homeCars = Car::whereIn('id', [6, 8, 13, 17, 23, 29])
            ->select('id', 'make', 'model', 'year', 'price_per_day', 'description', 'fuel_type', 'car_img')
            ->orderBy('year', 'asc')
            ->limit(30)
            ->get()
            ->each(function ($car) {
                // Ensure frontend receives image_url even when we only selected car_img.
                $car->image_url = $car->image_url ?? (
                    !empty($car->car_img)
                        ? Storage::url($car->car_img)
                        : asset('images/car-default.jpg')
                );
            });

        return inertia('Welcome', compact('homeCars'));
    }

    public function fleet(Request $request)
    {
        $query = Car::where('status', CarStatus::AVAILABLE)
            ->select('id', 'make', 'model', 'year', 'price_per_day', 'description', 'fuel_type', 'car_img');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('make', 'like', "%{$searchTerm}%")
                    ->orWhere('model', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Make filter
        if ($request->filled('make')) {
            $query->where('make', $request->make);
        }

        // Fuel type filter
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Year filter
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        $cars = $query
            ->with(['files' => function ($q) {
                $q->where('collection', 'image');
            }])
            ->paginate(10)
            ->withQueryString();

        $cars->getCollection()->transform(function ($car) {
            // Car model has getImageUrlAttribute(), but because we only selected some columns,
            // make sure we still provide image_url for the frontend.
            $car->image_url = $car->image_url ?? (
                !empty($car->car_img) ? Storage::url($car->car_img) : asset('images/car-default.jpg')
            );

            return $car;
        });

        // Get filter options
        $makes = Car::where('status', CarStatus::AVAILABLE)
            ->distinct()
            ->pluck('make')
            ->toArray();

        $fuelTypes = Car::where('status', CarStatus::AVAILABLE)
            ->distinct()
            ->pluck('fuel_type')
            ->toArray();

        $years = Car::where('status', CarStatus::AVAILABLE)
            ->distinct()
            ->pluck('year')
            ->toArray();

        $filters = $request->only(['search', 'make', 'fuel_type', 'min_price', 'max_price', 'year']);

        return inertia('Fleet', compact('cars', 'makes', 'fuelTypes', 'years', 'filters'));
    }

    public function about()
    {
        return inertia('About');
    }

    public function contact()
    {
        return inertia('Contact');
    }

    public function guestContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'guest_name' => $request->name,
            'guest_email' => $request->email,
            'subject' => $request->subject,
        ]);

        $ticket->messages()->create([
            'message' => $request->message,
        ]);

        return redirect()->route('contact')->with('success', 'Message sent successfully!');
    }
}
