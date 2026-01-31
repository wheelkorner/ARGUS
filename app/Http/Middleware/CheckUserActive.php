<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não tiver logado, deixa o "auth" tratar.
        if (!$user) {
            return $next($request);
        }

        $status = $user->status ?? 'pendente';

        if ($status === 'bloqueado') {
            $this->forceLogout($request, (int) $user->id);
            return redirect()->route('acesso.bloqueado');
        }

        if ($status !== 'ativo') {
            $this->forceLogout($request, (int) $user->id);
            return redirect()->route('acesso.pendente');
        }

        return $next($request);
    }

    /**
     * Logout completo: encerra a sessão atual e remove sessões do usuário.
     * (Isso protege contra “sessão zumbi” em DB session driver.)
     */
    private function forceLogout(Request $request, int $userId): void
    {
        try {
            // Mata todas as sessões do usuário (driver database)
            DB::table('sessions')->where('user_id', $userId)->delete();
        } catch (\Throwable $e) {
            // Se der algum problema, pelo menos desloga a sessão atual.
        }

        Auth::logout();

        // Invalida a sessão atual e troca token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
