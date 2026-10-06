<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
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
        // Pagination view custom (tanpa Tailwind)
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        // Gate untuk halaman admin (settings & user management)
        Gate::define('admin-only', function (User $user) {
            return $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN]);
        });
    }
}
