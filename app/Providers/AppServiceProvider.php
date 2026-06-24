<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin (and the legacy full-access Admin) bypass every policy check.
        Gate::before(function (User $user, string $ability) {
            if (in_array($user->role, [UserRole::SUPER_ADMIN, UserRole::ADMIN], true)) {
                return true;
            }

            return null;
        });
    }
}
