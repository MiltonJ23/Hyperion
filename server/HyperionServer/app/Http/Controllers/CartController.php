<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Event;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->user_id, 'cart_state' => 'active'],
            ['cart_total_price' => 0]
        );
        return response()->json($cart->load('events'));
    }

    public function add(Request $request, Event $event)
    {
        $user = $request->user();
        
        $cart = Cart::firstOrCreate(['user_id' => $user->user_id, 'cart_state' => 'active']);

        if (!$cart->events()->where('event_id', $event->event_id)->exists()) {
            $cart->events()->attach($event->event_id);
        }
        $cart->cart_total_price = $cart->events()->sum('event_price');
        $cart->save();
        return response()->json($cart->load('events'));
    }

    /**
     * Remove an event from the user's active cart.
     */
    public function remove(Request $request, Event $event)
    {
        $user = $request->user();
        $cart = $user->carts()->where('cart_state', 'active')->first();

        if ($cart) {
            $cart->events()->detach($event->event_id);
            $cart->cart_total_price = $cart->events()->sum('event_price');
            $cart->save();
        }

        return response()->json($cart->load('events'));
    }
}