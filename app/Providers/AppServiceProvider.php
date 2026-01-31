<?php

namespace App\Providers;

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
        /**
         * Gates do ARGUS (usados pelo AdminLTE via 'can' no menu).
         */
        Gate::define('argus-admin', function ($user) {
            return ($user->role ?? 'user') === 'admin';
        });

        Gate::define('argus-user', function ($user) {
            // Qualquer usuário autenticado pode ver "Minha Ficha"
            // Se você quiser restringir só para ativo, mude aqui.
            return isset($user);
        });
    }
}
