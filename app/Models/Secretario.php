<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Secretario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'secretarios';

    protected $fillable = [
        'nome',
        'login',
        'senha',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }
}
