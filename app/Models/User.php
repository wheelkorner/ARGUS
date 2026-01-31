<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'whatsapp',
        'cidade',
        'status',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helpers ARGUS
     */
    public function isAdmin(): bool
    {
        return ($this->role ?? 'user') === 'admin';
    }

    public function isAtivo(): bool
    {
        return ($this->status ?? 'pendente') === 'ativo';
    }

    /**
     * Relacionamento (será usado quando criarmos a tabela user_fichas)
     * Mantém aqui pra ficar pronto pro fluxo.
     */
    public function ficha()
    {
        return $this->hasOne(\App\Models\UserFicha::class);
    }

    /**
     * AdminLTE hooks (preservados)
     */
    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        return 'I\'m a nice guy';
    }

    public function adminlte_profile_url()
    {
        return 'profile/username';
    }
}
