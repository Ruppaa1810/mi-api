<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();                                          // número de pedido (RF-PP-002)
            $table->foreignId('user_id')->constrained('users');    // cliente que hizo la compra
            $table->enum('estado', ['Pendiente', 'Procesando', 'Enviado', 'Entregado', 'Cancelado'])->default('Pendiente');
            $table->enum('estado_pago', ['Pagado', 'Pendiente de Pago']);
            $table->enum('metodo_pago', ['Tarjeta', 'Transferencia', 'Efectivo']);
            $table->decimal('total', 10, 2);
            $table->text('direccion_envio');                       // la dirección que usó en esta compra
            $table->timestamps();                                  // created_at = fecha del pedido
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
