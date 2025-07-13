<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;

    protected $table = 'administrador'; // define o nome correto da tabela

    protected $fillable = ['nome', 'email', 'login', 'senha'];
}
