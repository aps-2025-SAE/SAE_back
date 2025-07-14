<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'tipo',
        'valor',
        'descricao',
        'data_inicio',
        'data_fim',
        'numOfertasDiarias',
    ];
}
