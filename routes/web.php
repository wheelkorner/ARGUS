<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\PerfilController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\Admin\AdminUsuarioController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    // Se estiver logado, manda pra home. Se não, manda pro login.
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

// Auth (somente uma vez)
Auth::routes();

Route::get('/pix', [App\Http\Controllers\PixController::class, 'show'])->name('pix.show');


// Páginas de status de acesso (não podem exigir "active")
Route::view('/acesso/pendente', 'acesso.pendente')->name('acesso.pendente');
Route::view('/acesso/bloqueado', 'acesso.bloqueado')->name('acesso.bloqueado');

// HOME agora exige usuário ATIVO
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Área interna (se quiser usar depois)
    Route::get('/area', function () {
        return view('area.index');
    })->name('area.index');
});

// Rotas do usuário logado
Route::middleware(['auth'])->group(function () {

    // API (uso interno do front: watermark, etc.)
    Route::get('/api/me', function (Request $request) {
        return response()->json([
            'email' => auth()->user()->email,
            'ip' => $request->ip(),
        ]);
    })->name('api.me');

    Route::get('/api/server-time', function () {
        return response()->json([
            'server_time' => now()->toIso8601String(),
        ]);
    })->name('api.server.time');

    // Ficha
    Route::get('/ficha/create', [FichaController::class, 'create'])->name('ficha.create');
    Route::get('/ficha', [FichaController::class, 'show'])->name('ficha.show');
    Route::get('/ficha/editar', [FichaController::class, 'edit'])->name('ficha.edit');
    Route::post('/ficha', [FichaController::class, 'store'])->name('ficha.store');

    // Perfil
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::patch('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::patch('/perfil/senha', [PerfilController::class, 'updatePassword'])->name('perfil.password');
});

// Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // LISTAGEM (tela)
    Route::get('/usuarios', [AdminUsuarioController::class, 'index'])
        ->name('admin.usuarios.index');

    // DATATABLE (AJAX) - IMPORTANTE ficar antes do /usuarios/{id}
    Route::get('/usuarios/datatable', [AdminUsuarioController::class, 'datatable'])
        ->name('admin.usuarios.datatable');

    // SHOW
    Route::get('/usuarios/{id}', [AdminUsuarioController::class, 'show'])
        ->name('admin.usuarios.show');

    // AÇÕES
    Route::patch('/usuarios/{id}/ativar', [AdminUsuarioController::class, 'ativar'])
        ->name('admin.usuarios.ativar');

    Route::patch('/usuarios/{id}/bloquear', [AdminUsuarioController::class, 'bloquear'])
        ->name('admin.usuarios.bloquear');

    Route::patch('/usuarios/{id}/instrucoes', [AdminUsuarioController::class, 'instrucoes'])
        ->name('admin.usuarios.instrucoes');
});


Route::middleware('auth')->get('/sair', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('sair');