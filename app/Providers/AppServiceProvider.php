<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('argus-admin', function ($user) {
            return ($user->role ?? 'user') === 'admin';
        });

        Gate::define('argus-bloqueado', function ($user) {
            return ($user->status ?? null) === 'bloqueado';
        });

        Gate::define('argus-nao-bloqueado', function ($user) {
            return ($user->status ?? null) !== 'bloqueado';
        });

        // Se você quiser manter "argus-user", deixa ele já bloqueando bloqueado também:
        Gate::define('argus-user', function ($user) {
            return isset($user) && ($user->status ?? null) !== 'bloqueado';
        });
    }
}
