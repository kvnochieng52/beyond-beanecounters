<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gate for admin access
        Gate::define('is_admin', function ($user) {
            return $user->hasRole('Admin');
        });

        // Define gate for supervisor access
        Gate::define('is_supervisor', function ($user) {
            return $user->hasRole('Supervisor');
        });
    }
}
