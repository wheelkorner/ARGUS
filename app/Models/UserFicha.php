<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFicha extends Model
{
    use HasFactory;

    protected $table = 'user_fichas';

    protected $fillable = [
        'user_id',

        'nome_monitorado',
        'instagram_monitorado',
        'whatsapp_monitorado',

        'parentesco',
        'observacoes',

        'info_verificada',
        'documentos_ok',

        'instrucoes',
        'ativado_em',
        'limite_ativacao',
    ];

    protected $casts = [
        'info_verificada' => 'boolean',
        'documentos_ok' => 'boolean',
        'ativado_em' => 'datetime',
        'limite_ativacao' => 'date',
    ];

    /**
     * Campo calculado: "Processo"
     * Retorna o tempo desde a ativação em dias e horas.
     */
    public function getProcessoAttribute(): ?string
    {
        if (!$this->ativado_em) {
            return null;
        }

        // Diferença total em horas desde a ativação
        $totalHoras = $this->ativado_em->diffInHours(now());

        $dias = intdiv($totalHoras, 24);
        $horas = $totalHoras % 24;

        // Ex: "3 dias e 5 horas"
        return "{$dias} dias e {$horas} horas";
    }
}
