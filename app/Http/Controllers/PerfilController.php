<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Tudo que o usuário tem na tabela, exceto campos sensíveis
        $attributes = $user->getAttributes();

        $hidden = [
            'password',
            'remember_token',
            'two_factor_recovery_codes',
            'two_factor_secret',
        ];

        foreach ($hidden as $k) {
            unset($attributes[$k]);
        }

        return view('perfil.show', compact('user', 'attributes'));
    }

    /**
     * Atualiza dados editáveis do perfil.
     * Regra: email e whatsapp NÃO podem ser editados.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'cidade' => ['nullable', 'string', 'max:120'],
        ]);

        $user->name = $request->name;
        $user->cidade = $request->cidade;
        $user->save();

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    /**
     * Troca de senha (segura)
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'senha_atual' => ['required', 'string'],
            'nova_senha' => ['required', 'confirmed', Password::min(8)],
            // exige campos: nova_senha + nova_senha_confirmation
        ]);

        if (!Hash::check($request->senha_atual, $user->password)) {
            return back()->withErrors([
                'senha_atual' => 'Senha atual inválida.',
            ]);
        }

        $user->password = Hash::make($request->nova_senha);

        // invalida "lembrar de mim"
        $user->setRememberToken(Str::random(60));
        $user->save();

        return back()->with('success', 'Senha alterada com sucesso.');
    }
}
