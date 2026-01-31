<?php

namespace App\Http\Controllers;

use App\Models\UserFicha;
use Illuminate\Http\Request;

class FichaController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $ficha = UserFicha::where('user_id', $user->id)->first();

        return view('ficha.show', compact('ficha'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        $ficha = UserFicha::where('user_id', $user->id)->first();

        // Se não existir ficha, manda pra tela de criar/preencher
        if (!$ficha) {
            return redirect()->route('ficha.create');
        }

        return view('ficha.edit', compact('ficha'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $ficha = UserFicha::where('user_id', $user->id)->first();

        // Se já existe, não cria outra: manda editar
        if ($ficha) {
            return redirect()->route('ficha.edit');
        }

        return view('ficha.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Você confirmou: "sim obrigatorios"
        $request->validate([
            'nome_monitorado' => ['required', 'string', 'max:255'],
            'instagram_monitorado' => ['required', 'string', 'max:255'],
            'whatsapp_monitorado' => ['required', 'string', 'max:30'],
            'parentesco' => ['required', 'string', 'max:120'],
            'observacoes' => ['required', 'string'],
        ], [
            'nome_monitorado.required' => 'Nome monitorado é obrigatório.',
            'instagram_monitorado.required' => 'Instagram monitorado é obrigatório.',
            'whatsapp_monitorado.required' => 'WhatsApp monitorado é obrigatório.',
            'parentesco.required' => 'Parentesco é obrigatório.',
            'observacoes.required' => 'Observações é obrigatório.',
        ]);

        // Usuário NÃO edita info_verificada/documentos_ok/instrucoes
        UserFicha::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nome_monitorado' => $request->nome_monitorado,
                'instagram_monitorado' => $request->instagram_monitorado,
                'whatsapp_monitorado' => $request->whatsapp_monitorado,
                'parentesco' => $request->parentesco,
                'observacoes' => $request->observacoes,
            ]
        );

        return redirect()
            ->route('ficha.show')
            ->with('success', 'Ficha salva com sucesso.');
    }
}
