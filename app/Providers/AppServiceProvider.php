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

        // ✅ Novo gate: permite menu/rotas só se NÃO estiver bloqueado
        Gate::define('argus-nao-bloqueado', function ($user) {
            if (!$user)
                return false;
            return ($user->status ?? null) !== 'bloqueado';
        });

        Gate::define('argus-admin', function ($user) {
            if (!$user)
                return false;
            return ($user->role ?? 'user') === 'admin';
        });

        Gate::define('argus-user', function ($user) {
            // Qualquer usuário autenticado pode ver "Minha Ficha"
            // Se você quiser restringir só para ativo, mude aqui.
            return (bool) $user;
        });
    }
}
