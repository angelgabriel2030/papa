<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Numero extends Model
{
    use HasFactory;

    protected $table = 'numeros';

    protected $fillable = [
        'numero_actual',
        'enviado_por',
        'recibido_por',
        'ip_origen',
        'ip_destino',
    ];

    protected $casts = [
        'numero_actual' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}