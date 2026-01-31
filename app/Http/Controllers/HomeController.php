<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard inicial pós-login (fluxo ARGUS).
     *
     * - Admin -> /admin/usuarios
     * - Usuário ativo -> /ficha
     * - Pendente/bloqueado -> fallback para telas de acesso
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Segurança extra (normalmente o middleware 'active' já barra antes)
        if (($user->status ?? 'pendente') === 'bloqueado') {
            auth()->logout();
            return redirect()->route('acesso.bloqueado');
        }

        if (($user->status ?? 'pendente') !== 'ativo') {
            auth()->logout();
            return redirect()->route('acesso.pendente');
        }

        // Admin vai direto pro painel
        if (($user->role ?? 'user') === 'admin') {
            return redirect()->route('admin.usuarios.index');
        }

        // Usuário comum ativo vai pra ficha
        return redirect()->route('ficha.show');
    }
}
