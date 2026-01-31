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
     * Tela principal
     */
    public function index()
    {
        return view('admin.usuarios.index');
    }

    /**
     * Endpoint DataTables (server-side puro)
     */
    public function datatable(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $search = $request->input('search.value');

        $status = $request->input('status');
        $role = $request->input('role');

        $base = User::query()->select([
            'id',
            'name',
            'email',
            'whatsapp',
            'cidade',
            'status',
            'role',
            'created_at'
        ]);

        $recordsTotal = (clone $base)->count();

        /* Filtros */
        if ($status) {
            $base->where('status', $status);
        }

        if ($role) {
            $base->where('role', $role);
        }

        if ($search) {
            $base->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->orWhere('cidade', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $base)->count();

        /* Ordenação */
        $cols = [
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'whatsapp',
            4 => 'cidade',
            5 => 'status',
            6 => 'role',
        ];

        $orderCol = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        $orderBy = $cols[$orderCol] ?? 'id';

        $base->orderBy($orderBy, $orderDir);

        /* Paginação */
        $rows = $base
            ->skip($start)
            ->take($length)
            ->get();

        /* Montagem */
        $data = $rows->map(function ($u) {

            $status = match ($u->status) {
                'ativo' => '<span class="badge badge-success">ativo</span>',
                'bloqueado' => '<span class="badge badge-danger">bloqueado</span>',
                default => '<span class="badge badge-warning">pendente</span>',
            };

            $role = $u->role === 'admin'
                ? '<span class="badge badge-info">admin</span>'
                : '<span class="badge badge-secondary">user</span>';

            $acoes = '
                <a href="' . route('admin.usuarios.show', $u->id) . '"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-search"></i> Ver
                </a>
            ';

            return [
                'id' => $u->id,
                'name' => e($u->name),
                'email' => e($u->email),
                'whatsapp' => e($u->whatsapp),
                'cidade' => e($u->cidade),
                'status_badge' => $status,
                'role_badge' => $role,
                'acoes' => $acoes,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /* ============================
       RESTO DO SEU CONTROLLER
       ============================ */

    public function show($id)
    {
        $usuario = User::findOrFail($id);

        $ficha = UserFicha::where('user_id', $usuario->id)->first();

        return view('admin.usuarios.show', compact('usuario', 'ficha'));
    }

    public function ativar($id)
    {
        $usuario = User::findOrFail($id);

        $usuario->status = 'ativo';
        $usuario->save();

        $ficha = UserFicha::where('user_id', $usuario->id)->first();

        if ($ficha && !$ficha->ativado_em) {
            $ficha->ativado_em = now();
            $ficha->save();
        }

        $this->forceLogoutUser($usuario);

        return back()->with('success', 'Usuário ativado.');
    }

    public function bloquear($id)
    {
        $usuario = User::findOrFail($id);

        $usuario->status = 'bloqueado';
        $usuario->save();

        $this->forceLogoutUser($usuario);

        return back()->with('success', 'Usuário bloqueado.');
    }

    public function instrucoes(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $ficha = UserFicha::where('user_id', $usuario->id)->first();

        if (!$ficha) {
            return back()->with('error', 'Usuário sem ficha.');
        }

        $request->validate([
            'instrucoes' => 'nullable|string',
            'limite_ativacao' => 'nullable|date',
        ]);

        $ficha->fill([
            'instrucoes' => $request->instrucoes,
            'limite_ativacao' => $request->limite_ativacao,
            'info_verificada' => $request->has('info_verificada'),
            'documentos_ok' => $request->has('documentos_ok'),
        ])->save();

        return back()->with('success', 'Ficha atualizada.');
    }

    private function forceLogoutUser(User $usuario): void
    {
        DB::table('sessions')
            ->where('user_id', $usuario->id)
            ->delete();

        $usuario->setRememberToken(Str::random(60));
        $usuario->save();
    }
}
