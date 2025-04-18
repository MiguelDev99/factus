<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

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
    

}
