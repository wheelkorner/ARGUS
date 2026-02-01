<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não estiver logado, deixa o auth resolver
        if (!$user) {
            return $next($request);
        }

        $status = $user->status ?? 'pendente';

        /**
         * =====================================================
         * USUÁRIO BLOQUEADO
         * =====================================================
         * - NÃO desloga
         * - NÃO mata sessão
         * - Só permite PIX e logout
         */
        if ($status === 'bloqueado') {

            // Rotas liberadas para bloqueado
            if (
                $request->is('pix') ||
                $request->is('logout') ||
                $request->is('api/*')
            ) {
                return $next($request);
            }

            return redirect()
                ->route('pix.show')
                ->with('error', 'Sua conta está bloqueada. Regularize o pagamento.');
        }

        /**
         * =====================================================
         * USUÁRIO PENDENTE
         * =====================================================
         * - Continua indo para tela de pendente
         */
        if ($status !== 'ativo') {
            return redirect()->route('acesso.pendente');
        }

        // Usuário ativo → segue normal
        return $next($request);
    }
}
