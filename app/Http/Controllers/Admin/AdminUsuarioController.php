<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFicha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminUsuarioController extends Controller
{
    /**
     * Lista usuários
     */
    public function index()
    {
        $usuarios = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Exibe usuário + ficha
     */
    public function show($id)
    {
        $usuario = User::findOrFail($id);

        $ficha = UserFicha::where('user_id', $usuario->id)->first();

        return view('admin.usuarios.show', compact('usuario', 'ficha'));
    }

    /**
     * Ativar usuário
     */
    public function ativar($id)
    {
        $usuario = User::findOrFail($id);

        $usuario->status = 'ativo';
        $usuario->save();

        // Só seta ativado_em se a ficha existir (não cria ficha vazia!)
        $ficha = UserFicha::where('user_id', $usuario->id)->first();
        if ($ficha && !$ficha->ativado_em) {
            $ficha->ativado_em = now();
            $ficha->save();
        }

        // FORÇA logout em qualquer dispositivo (sessions + remember me)
        $this->forceLogoutUser($usuario);

        return redirect()
            ->back()
            ->with('success', 'Usuário ativado e desconectado (sessões reiniciadas).');
    }

    /**
     * Bloquear usuário
     */
    public function bloquear($id)
    {
        $usuario = User::findOrFail($id);

        $usuario->status = 'bloqueado';
        $usuario->save();

        // FORÇA logout em qualquer dispositivo (sessions + remember me)
        $this->forceLogoutUser($usuario);

        return redirect()
            ->back()
            ->with('success', 'Usuário bloqueado e desconectado.');
    }

    /**
     * Atualiza instruções e validações
     */
    public function instrucoes(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Aqui o Admin PODE criar ficha (mas tem que preencher nome_monitorado se for obrigatório no banco)
        // Então: NÃO criar ficha automaticamente aqui se sua tabela exige nome_monitorado.
        // Se você quiser permitir que admin crie ficha, precisa ajustar migration pra permitir null/default.
        $ficha = UserFicha::where('user_id', $usuario->id)->first();

        if (!$ficha) {
            return redirect()
                ->back()
                ->with('error', 'Este usuário ainda não possui ficha. Peça para ele preencher primeiro.');
        }

        $request->validate([
            'instrucoes' => 'nullable|string',
            'limite_ativacao' => 'nullable|date',
        ]);

        $ficha->instrucoes = $request->instrucoes;
        $ficha->limite_ativacao = $request->limite_ativacao;

        $ficha->info_verificada = $request->has('info_verificada');
        $ficha->documentos_ok = $request->has('documentos_ok');

        $ficha->save();

        return redirect()
            ->back()
            ->with('success', 'Ficha atualizada.');
    }

    /**
     * FORÇA logout do usuário em qualquer dispositivo:
     * - apaga sessions do driver "database"
     * - invalida "remember me" trocando remember_token
     */
    private function forceLogoutUser(User $usuario): void
    {
        DB::table('sessions')->where('user_id', $usuario->id)->delete();

        // Invalida cookie "lembrar de mim"
        $usuario->setRememberToken(Str::random(60));
        $usuario->save();
    }
}
