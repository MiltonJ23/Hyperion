<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
   public function show($id)
    {
       
        $bookingDetails = DB::table('book')
            ->join('users as attendee', 'book.fk1_user_id', '=', 'attendee.user_id')
            ->join('event', 'book.fk2_event_id', '=', 'event.event_id')
            ->join('users as organizer', 'event.fk_user_id', '=', 'organizer.user_id')
            ->leftJoin('ticket', 'book.booking_id', '=', 'ticket.fk_booking_id')
            ->leftJoin('event_images', 'event.event_id', '=', 'event_images.fk1_event_id')
            ->leftJoin('image', 'event_images.fk2_image_id', '=', 'image.image_id')
            ->where('book.booking_id', '=', $id)
            ->select(
                'book.booking_id',
                'book.fk1_user_id', 
                'book.status as booking_status',
                'book.price_at_booking',
                'book.created_at as booking_date',
                
                'attendee.name as attendee_name',
                'attendee.email as attendee_email',

                'event.event_id',
                'event.event_name',
                'event.event_desc',
                'event.event_date',
                'event.event_time',
                'event.event_venue',
                'event.event_location',

                'organizer.name as organizer_name',

                'ticket.ticket_code',
                
                'image.image_url'
            )
            ->first();

        if (!$bookingDetails) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        
        if ($bookingDetails->fk1_user_id !== Auth::id()) {
           return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($bookingDetails);
    }
}
