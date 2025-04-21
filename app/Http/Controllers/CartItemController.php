<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
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

        // Obtener los ítems del carrito del usuario (relación o consulta directa)
        $carrito = CartItem::with('product')->where('id_user', $user->id)->get();

        if ($carrito->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], 400);
        }

        // Construir los items para la API de Factus
        $items = $carrito->map(function ($item) {
            return [
                "product_code" => $item->product->code ?? 'SIN-CODIGO',
                "description" => $item->product->name ?? 'Producto sin nombre',
                "quantity" => $item->quantity,
                "unit_price" => $item->price, // Usamos el precio del carrito
                "tax_rate" => 0, // Ajusta si manejas IVA
            ];
        });

        $datosFactura = [
            "reference_code" => "FAC-" . now()->format('YmdHis'), // Código único de la factura
            "customer" => [
                "identification_document_id" => 3, // Ej: 3 = Cédula de ciudadanía
                "identification" => "123456789",   // Documento del cliente (puedes poner uno genérico para pruebas)
                "name" => $user->name ?? "Cliente de prueba",
                "email" => $user->email ?? "correo@ejemplo.com",
                "tribute_id" => 21 // Ej: 1 = Régimen común
            ],
            "currency" => "COP",
            "issue_date" => now()->toDateString(),
            "items" => $carrito->map(function ($item) {
                return [
                    "code_reference" => $item->product->code ?? '001',
                    "name" => $item->product->name ?? 'Producto sin nombre',
                    "description" => $item->product->description ?? '',
                    "price" => $item->price,
                    "quantity" => $item->quantity,
                    "discount_rate" => 0,
                    "is_excluded" => 0,                  // true si el producto está exento de IVA
                    "unit_measure_id" => 70,             // 70 = Unidad (revisar catálogo de Factus)
                    "standard_code_id" => 1,             // 999 = Código genérico
                    "tribute_id" => 1,                   // 1 = Régimen común (aplica IVA)
                    "tax_rate" => 0
                ];
            })->toArray()
        ];        

        try {
            $token = $factus->getAccessToken();
            $resultado = $factus->crearFactura($datosFactura, $token);

            if ($resultado['ok']) {
                // Vaciar el carrito después de facturar exitosamente
                CartItem::where('id_user', $user->id)->delete();

                return response()->json([
                    'message' => 'Factura generada correctamente',
                    'data' => $resultado['data']
                ]);
            }

            return response()->json([
                'message' => 'Error al generar la factura',
                'error' => $resultado['error']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error en facturación: '.$e->getMessage());

            return response()->json([
                'message' => 'Error en el servicio de facturación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
