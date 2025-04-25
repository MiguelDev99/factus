<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Payment;
use App\Services\FacturacionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CartItemController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'id_product' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        // Obtener el usuario desde el middleware
        $user = $request->get('auth_user');
        $userId = $user->id;

        $cartItem = CartItem::where('id_user', $userId)
                            ->where('id_product', $request->id_product)
                            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->price = $request->price; // 👈 Asegurate de establecerlo
            $cartItem->save();
        } else {
            CartItem::create([
                'id_user' => $userId,
                'id_product' => $request->id_product,
                'quantity' => $request->quantity,
                'price' => $request->price
            ]);
        }

        return response()->json(['message' => 'Producto añadido al carrito']);
    }

    public function getCartItems(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->get('auth_user');
        $userId = $user->id;

        // Obtener los ítems del carrito con la información del producto
        $cartItems = CartItem::with('product')
            ->where('id_user', $userId)
            ->get();

        // Formatear la respuesta para que incluya lo necesario
        $response = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->id_product,
                'product_name' => $item->product->name ?? '',
                'description' => $item->product->description ?? '',
                'image' => $item->product->image ?? null,
                'unit_price' => $item->price,
                'quantity' => $item->quantity,
                'total_price' => $item->price * $item->quantity,
            ];
        });

        return response()->json($response);
    }

    public function removeFromCart($id)
    {
        $user = request()->get('auth_user');
    
        $cartItem = CartItem::where('id', $id)->where('id_user', $user->id)->first();
    
        if (!$cartItem) {
            return response()->json(['message' => 'Producto no encontrado en el carrito'], 404);
        }
    
        $cartItem->delete();
    
        return response()->json(['message' => 'Producto eliminado del carrito']);
    }
    
    public function facturar(Request $request, FacturacionService $factus)
    {
        $user = $request->get('auth_user');

        $carrito = CartItem::with('product')->where('id_user', $user->id)->get();

        if ($carrito->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], 400);
        }

        // Calcular total del carrito
        $total = $carrito->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $referencia = "FAC-" . now()->format('YmdHisv');

        $datosFactura = [
            "numbering_range_id" => 8,
            "reference_code" => $referencia,
            "observation" => "",
            "payment_form" => "1",
            "payment_due_date" => now()->addDays(15)->toDateString(),
            "payment_method_code" => "10",
            "billing_period" => [
                "start_date" => now()->subMonth()->toDateString(),
                "start_time" => "00:00:00",
                "end_date" => now()->toDateString(),
                "end_time" => "23:59:59"
            ],
            "customer" => [
                "identification" => $user->identification,
                "dv" => $user->dv ?? "",
                "company" => $user->company ?? "",
                "trade_name" => $user->trade_name ?? "",
                "names" => $user->name,
                "address" => $user->address ?? "",
                "email" => $user->email,
                "phone" => $user->phone ?? "",
                "legal_organization_id" => $user->legal_organization_id ?? "",
                "tribute_id" => $user->tribute_id ?? "",
                "identification_document_id" => $user->identification_document_id ?? "",
                "municipality_id" => $user->municipality_id ?? ""
            ],
            "items" => $carrito->map(function ($item) {
                return [
                    "code_reference" => $item->product->code ?? '001',
                    "name" => $item->product->name ?? 'Producto sin nombre',
                    "quantity" => $item->quantity,
                    "discount_rate" => 0,
                    "price" => $item->price,
                    "tax_rate" => "0.00",
                    "unit_measure_id" => 70,
                    "standard_code_id" => 1,
                    "is_excluded" => 0,
                    "tribute_id" => 1,
                    "withholding_taxes" => []
                ];
            })->toArray()
        ];

        try {
            $token = $factus->getAccessToken();
            $resultado = $factus->crearFactura($datosFactura, $token);

            if ($resultado['ok']) {
                // Guardar el pago
                Payment::create([
                    'user_id' => $user->id,
                    'payment_method' => $resultado['data']['payment_form']['name'] ?? 'Desconocido',
                    'payment_status' => 'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Limpiar carrito
                CartItem::where('id_user', $user->id)->delete();

                return response()->json([
                    'message' => 'Factura generada correctamente',
                    'data' => [
                        'bill' => [
                            'number' => $resultado['data']['number'] ?? 'Desconocido',
                            'reference_code' => $referencia
                        ]
                    ]
                ]);
            }

            return response()->json([
                'message' => 'Error al generar la factura',
                'error' => $resultado['error'],
                'json_enviado' => $datosFactura
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error en facturación: '.$e->getMessage());

            return response()->json([
                'message' => 'Error en el servicio de facturación',
                'error' => $e->getMessage(),
                'json_enviado' => $datosFactura
            ], 500);
        }
    }


}
