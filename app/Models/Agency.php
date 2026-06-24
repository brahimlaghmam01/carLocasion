<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'city_id',
        'name',
        'address',
        'phone',
        'email',
        'manager_name',
        'status',
    ];

    /**
     * Get the city the agency belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get all users attached to the agency.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the customers (client users) of the agency.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(User::class)->where('role', UserRole::CLIENT);
    }

    /**
     * Get the vehicles owned by the agency.
     */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    /**
     * Get the reservations handled by the agency.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the payments collected by the agency.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
