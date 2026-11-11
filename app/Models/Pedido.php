<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['user_id', 'estado', 'estado_pago', 'metodo_pago', 'total', 'direccion_envio'];

    protected $casts = [
        'total' => 'float',
    ];

    // El cliente que hizo el pedido
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Los productos del pedido (una fila por producto)
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
