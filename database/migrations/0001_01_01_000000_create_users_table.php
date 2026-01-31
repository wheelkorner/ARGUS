<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Cadastro inicial (ARGUS)
            $table->string('name', 255);            // Nome Completo
            $table->string('whatsapp', 30);         // WhatsApp (OBRIGATÓRIO)
            $table->string('cidade', 120);          // Cidade (OBRIGATÓRIO)

            // Login
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();

            // Controle de acesso
            $table->enum('status', ['pendente', 'ativo', 'bloqueado'])
                ->default('pendente')
                ->index();

            $table->enum('role', ['user', 'admin'])
                ->default('user')
                ->index();

            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 255)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // SESSIONS (sem foreign key)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            // Sem FK (regra do projeto)
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
