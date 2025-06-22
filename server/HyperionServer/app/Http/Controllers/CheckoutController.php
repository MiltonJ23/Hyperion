<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Ticket;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Process the user's cart for checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $cart = $user->carts()->with('events')->first();
        if (!$cart || $cart->events->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }
        
        $eventsInCart = $cart->events;
        
        DB::beginTransaction();
        try {
            foreach ($eventsInCart as $event) {
                
                $booking = new Book();
                $booking->fk1_user_id = $user->user_id;
                $booking->fk2_event_id = $event->event_id;
                $booking->price_at_booking = $event->event_price;
                $booking->status = 'Confirmed';
                $booking->save(); 

                $ticket = new Ticket();
                $ticket->fk_booking_id = $booking->booking_id; 
                $ticket->fk_user_id = $user->user_id;
                $ticket->fk_event_id = $event->event_id;
                $ticket->ticket_code = 'TICKET-' . strtoupper(Str::random(10));
                $ticket->save();
            }

            
            $cart->events()->detach();
            DB::commit();

            return response()->json(['message' => 'Checkout successful! Your tickets have been generated.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred during checkout. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}