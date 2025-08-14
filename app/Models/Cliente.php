<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Cliente extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nome_completo',
        'telefone',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function pedidos()
    {
        return $this->hasMany(\App\Models\Pedido::class);
    }

}
