<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    // Confirmar compra (checkout): CU-PP-C-001 + Módulo de Pagos
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'direccion_envio'        => 'required|string|min:5|max:255',
                'metodo_pago'            => 'required|in:Tarjeta,Transferencia,Efectivo',
                // El número de tarjeta solo se pide si paga con tarjeta (no se guarda)
                'numero_tarjeta'         => 'required_if:metodo_pago,Tarjeta|nullable|digits:16',
                'productos'              => 'required|array|min:1',
                'productos.*.producto_id' => 'required|integer|exists:productos,id',
                'productos.*.cantidad'   => 'required|integer|min:1',
            ]);

            // Pago simulado con tarjeta: las tarjetas que terminan en 0002 se rechazan (RF-MP-005)
            if ($request->metodo_pago === 'Tarjeta' && str_ends_with($request->numero_tarjeta, '0002')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transacción rechazada. Por favor, intenta con otro método de pago.'
                ], 402);
            }

            // Todo adentro de una transacción: si algo falla, no se guarda nada (RF-PP-003)
            $pedido = DB::transaction(function () use ($request) {
                $total = 0;
                $lineas = [];

                // Última validación de stock antes de confirmar
                foreach ($request->productos as $item) {
                    // lockForUpdate: bloquea el producto hasta terminar, para que dos compras
                    // al mismo tiempo no vendan el mismo stock
                    $producto = Producto::lockForUpdate()->find($item['producto_id']);

                    if ($producto->estado !== 'Activo') {
                        throw new \DomainException("El producto {$producto->nombre} ya no está disponible");
                    }
                    if ($producto->stock < $item['cantidad']) {
                        throw new \DomainException("Solo quedan {$producto->stock} unidades de {$producto->nombre}");
                    }

                    $total += $producto->precio * $item['cantidad'];
                    $lineas[] = ['producto' => $producto, 'cantidad' => $item['cantidad']];
                }

                // Tarjeta aprobada = Pagado. Transferencia o efectivo = Pendiente de Pago
                $pedido = Pedido::create([
                    'user_id'         => $request->user()->id,
                    'estado'          => 'Pendiente',
                    'estado_pago'     => $request->metodo_pago === 'Tarjeta' ? 'Pagado' : 'Pendiente de Pago',
                    'metodo_pago'     => $request->metodo_pago,
                    'total'           => $total,
                    'direccion_envio' => $request->direccion_envio,
                ]);

                foreach ($lineas as $linea) {
                    // Guardo el precio de hoy: si después cambia, el pedido no cambia
                    $pedido->detalles()->create([
                        'producto_id'     => $linea['producto']->id,
                        'cantidad'        => $linea['cantidad'],
                        'precio_unitario' => $linea['producto']->precio,
                    ]);

                    // Descuento el stock (RF-PP-003)
                    $linea['producto']->decrement('stock', $linea['cantidad']);
                }

                return $pedido;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pedido confirmado.',
                'data'    => $pedido->load('detalles.producto')
            ], 201);

        } catch (\DomainException $e) {
            // Problemas de stock: no se creó el pedido
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar el pedido.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Historial del cliente logueado: CU-PP-C-002
    public function misPedidos(Request $request): JsonResponse
    {
        $pedidos = $request->user()->pedidos()
            ->with('detalles.producto')
            ->latest()                       // los más nuevos primero
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $pedidos
        ], 200);
    }
}
