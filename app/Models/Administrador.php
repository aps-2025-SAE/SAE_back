<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Administrador extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'administrador'; // define o nome correto da tabela

    protected $fillable = ['nome', 'email', 'login', 'senha'];

    protected $hidden = ['senha'];
}
