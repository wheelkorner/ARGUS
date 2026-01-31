<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserStateFresh
{
    public function handle(Request $request, Closure $next)
    {
        // Se não tem usuário logado, não faz nada
        if (!Auth::check()) {
            return $next($request);
        }

        $user = $request->user();

        // Chaves que vamos guardar na sessão pra detectar mudança
        $currentState = [
            'status' => (string) ($user->status ?? ''),
            'role' => (string) ($user->role ?? ''),
        ];

        $sessionKey = 'argus_user_state';

        // Se ainda não existe, grava e segue o baile
        if (!$request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, $currentState);
            return $next($request);
        }

        // Se existe, compara
        $storedState = (array) $request->session()->get($sessionKey, []);

        $statusChanged = ($storedState['status'] ?? null) !== $currentState['status'];
        $roleChanged = ($storedState['role'] ?? null) !== $currentState['role'];

        if ($statusChanged || $roleChanged) {
            // Logout forçado
            Auth::logout();

            // Invalida sessão + novo token (segurança)
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redireciona pro login com recado (sem vazar detalhes)
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Sua sessão foi encerrada porque seu acesso foi atualizado. Faça login novamente.',
                ]);
        }

        return $next($request);
    }
}
