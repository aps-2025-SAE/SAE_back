<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
    'cliente_id',
    'evento_id',
    'data_solicitada',
    'horario',
    'endereco',
    'quantidade_pessoas',
    'status',
    'motivo_recusa',
    'secretario_id',
    'avaliado_em',
    ];

    protected $casts = [
        'data_solicitada' => 'date:Y-m-d',
        'avaliado_em' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function evento()
    {
        return $this->belongsTo(\App\Models\Evento::class);
    }

    public function secretario()
    {
        return $this->belongsTo(Secretario::class);
    }
}
