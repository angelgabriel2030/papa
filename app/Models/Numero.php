<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Numero extends Model
{
    use HasFactory;

    protected $table = 'numeros';

    protected $fillable = [
        'ip_envia',
        'ip_recibe',
        'numero_enviado',
        'numero_acumulado',
        'estado',
    ];

    public function scopePendientePara($query, string $ip)
    {
        return $query->where('ip_recibe', $ip)
                     ->where('estado', 'pendiente');
    }

    public function marcarRecibido(): void
    {
        $this->estado = 'recibido';
        $this->save();
    }
}