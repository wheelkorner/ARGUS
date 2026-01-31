<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /**
         * Aliases de middleware (Laravel 12).
         * Aqui substitui o antigo Kernel.php.
         */
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'active' => \App\Http\Middleware\CheckUserActive::class,

            // Novo: garante que se status/role mudar, a sessão cai automaticamente
            'state.fresh' => \App\Http\Middleware\EnsureUserStateFresh::class,
        ]);

        /**
         * Aplica globalmente no grupo "web" (todas rotas web).
         * O middleware é inteligente: só age se estiver logado (Auth::check()).
         */
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureUserStateFresh::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
