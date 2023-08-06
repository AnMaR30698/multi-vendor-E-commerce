<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addItem(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer;
        $cart = $customer->cart;

        $product = Products::find($request->product_id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $quantity = $request->input('quantity', 1);
        $price = $product->price * $quantity;
        $existingItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($existingItem) {
            // If the item already exists in the cart, update the quantity and price
            $existingItem->quantity += $quantity;
            $existingItem->price += $price;
            $existingItem->save();
        } else {
            // Create a new cart item
            $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart',
            'cartItems' => $cart->cartItems
            ], Response::HTTP_OK);
    }

    public function getCartItems()
    {
        $user = auth()->user();
        $customer = $user->customer;
        $cart = $customer->cart;
        $cartItems = $cart->cartItems;

        return response()->json(['cart_items' => $cartItems], 200);
    }
    public function updateCartItem(Request $request,  $cartItem_id)
    {
        $user = auth()->user();
        $customer = $user->customer;
        $cart = $customer->cart;
        $cartItem = CartItem::where('id',$cartItem_id)->first();
        if(!$cartItem){
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found'
                ], Response::HTTP_NOT_FOUND);
        }
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
        $product = Products::find($cartItem->product_id);
        if(!$product){
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
        }
        $cartItem->quantity = $request->input('quantity', 1);
        $cartItem->price = $product->price * $cartItem->quantity; // Calculate the new price
        $cartItem->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated',
            'cartItem' => $cartItem
        ], 200);
    }
    public function deleteCartItem(CartItem $cartItem)
    {
        $user = auth()->user();
        $customer = $user->customer;
        $cart = $customer->cart;

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item deleted'], 200);
    }
}
