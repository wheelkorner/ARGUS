<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('user_fichas', function (Blueprint $table) {

            $table->id();

            // Dono da ficha (sem FK)
            $table->unsignedBigInteger('user_id')->index();

            // Dados monitorados
            $table->string('nome_monitorado', 255);
            $table->string('instagram_monitorado', 255)->nullable();
            $table->string('whatsapp_monitorado', 30)->nullable();

            // Relação
            $table->string('parentesco', 120)->nullable();

            // Observações
            $table->text('observacoes')->nullable();

            // Verificações
            $table->boolean('info_verificada')->default(false);
            $table->boolean('documentos_ok')->default(false);

            // Admin (vai editar depois)
            $table->text('instrucoes')->nullable();
            $table->timestamp('ativado_em')->nullable();
            $table->date('limite_ativacao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_fichas');
    }
};
