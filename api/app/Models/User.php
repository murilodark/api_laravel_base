<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Model: User
 * 
 * Representa o usuário do sistema e gerencia suas permissões e estados.
 * 
 * @author Murilo Dark
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Campos que podem ser preenchidos em massa (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status', // 'ativo', 'inativo', 'pendente'
        'tipo',   // 'root', 'admin', 'gerente', 'vendedor', 'cliente'
        'email_verified_at',
    ];

    /**
     * Atributos que devem ser ocultos em respostas JSON (Segurança)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversão de tipos (Casts)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'string',
            'tipo'              => 'string',
        ];
    }

    // --- SCOPES (Filtros Otimizados) ---
 

    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeRoot($query)
    {
        return $query->where('tipo', 'root');
    }

    public function scopeAdmin($query)
    {
        return $query->where('tipo', 'admin');
    }

    public function scopeGerente($query)
    {
        return $query->where('tipo', 'gerente');
    }
       public function scopeVendedor($query)
    {
        return $query->where('tipo', 'vendedor');
    }
       public function scopeCliente($query)
    {
        return $query->where('tipo', 'cliente');
    }

    // --- MUTATORS & ACCESSORS ---

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    // --- HELPERS DE VERIFICAÇÃO ---

    /**
     * Verifica se o usuário possui status 'ativo'
     */
    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }

    public function isRoot(): bool
    {
        return $this->tipo === 'root';
    }

    public function isAdmin(): bool
    {
        return $this->tipo === 'admin';
    }

    public function isGerente(): bool
    {
        return $this->tipo === 'gerente';
    }

    public function isVendedor(): bool
    {
        return $this->tipo === 'vendedor';
    }

    public function isCliente(): bool
    {
        return $this->tipo === 'cliente';
    }
}
