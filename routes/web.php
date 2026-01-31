<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Se estiver logado, manda pra home. Se não, manda pro login.
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

// Auth (somente uma vez)
Auth::routes();

// Páginas de status de acesso (não podem exigir "active")
Route::view('/acesso/pendente', 'acesso.pendente')->name('acesso.pendente');
Route::view('/acesso/bloqueado', 'acesso.bloqueado')->name('acesso.bloqueado');

// HOME agora exige usuário ATIVO
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Área interna (se quiser usar depois)
    Route::get('/area', function () {
        return view('area.index');
    })->name('area.index');
});

// Rotas do usuário logado (ficha pode ser acessível mesmo pendente, se você quiser)
// Se você QUISER bloquear ficha pra pendente, troque middleware para ['auth','active'].
Route::middleware(['auth'])->group(function () {
    Route::get('/ficha/create', [App\Http\Controllers\FichaController::class, 'create'])->name('ficha.create');
    Route::get('/ficha', [App\Http\Controllers\FichaController::class, 'show'])->name('ficha.show');
    Route::get('/ficha/editar', [App\Http\Controllers\FichaController::class, 'edit'])->name('ficha.edit');
    Route::post('/ficha', [App\Http\Controllers\FichaController::class, 'store'])->name('ficha.store');
});

// Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/usuarios', [App\Http\Controllers\Admin\AdminUsuarioController::class, 'index'])
        ->name('admin.usuarios.index');

    Route::get('/usuarios/{id}', [App\Http\Controllers\Admin\AdminUsuarioController::class, 'show'])
        ->name('admin.usuarios.show');

    Route::patch('/usuarios/{id}/ativar', [App\Http\Controllers\Admin\AdminUsuarioController::class, 'ativar'])
        ->name('admin.usuarios.ativar');

    Route::patch('/usuarios/{id}/bloquear', [App\Http\Controllers\Admin\AdminUsuarioController::class, 'bloquear'])
        ->name('admin.usuarios.bloquear');

    Route::patch('/usuarios/{id}/instrucoes', [App\Http\Controllers\Admin\AdminUsuarioController::class, 'instrucoes'])
        ->name('admin.usuarios.instrucoes');
});

Route::middleware('auth')->get('/api/server-time', function () {
    return response()->json([
        'server_time' => now()->toIso8601String(),
    ]);
})->name('api.server.time');
