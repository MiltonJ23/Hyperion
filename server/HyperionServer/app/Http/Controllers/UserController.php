<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();


        if ($request->has('name')) {
            $searchName = $request->input('name');
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        $users = $query->get();

        return response()->json($users);
    }


     public function cards(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $cards = DB::table('card')
            ->join('user_card', 'card.bank_card_id', '=', 'user_card.fk2_bank_card_id')
            ->where('user_card.fk1_user_id', $id)
            ->select('card.*')
            ->get();


        return response()->json($cards);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json($user, 201);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Could not create user. A database error occurred.',
                'sql_error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(string $id)
    {

        $user = User::findOrFail($id);
        return response()->json($user);
    }


    public function events(string $id)
    {
        if (Auth::id() !== $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {

            $events = DB::table('event')
                ->where('fk_user_id', $id)
                ->latest('event_date')
                ->get();


            foreach ($events as $event) {

                $event->images = DB::table('image')
                    ->join('event_images', 'image.image_id', '=', 'event_images.fk2_image_id')
                    ->where('event_images.fk1_event_id', $event->event_id)
                    ->get();

                $event->booked_by_users_count = DB::table('book')
                    ->where('fk2_event_id', $event->event_id)
                    ->count();
            }

            return response()->json($events);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'A critical database error occurred.',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


    public function update(Request $request, User $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:30',
            'email' => 'sometimes|required|email|max:70|unique:users,email,' . $id->user_id . ',user_id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'age' => 'nullable|integer',
            'sex' => 'nullable|in:Male,Female,Other',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $id->update($validator->validated());

        return response()->json($id);
    }

    /**
     * Update the user's profile image.
     */
    public function updateImage(Request $request, User $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:30720',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('image')) {
            if ($id->profile_image_url) {
                Storage::disk('public')->delete($id->profile_image_url);
            }

            $path = $request->file('image')->store('profile_images', 'public');
            $id->profile_image_url = $path;
            $id->save();
        }

        return response()->json($id);
    }


    public function destroy(string $id)
    {

        $user = User::findOrFail($id);
        $user->delete();


        return response()->json(null, 204);
    }

    /**
     * Get all bookings for a specific user.
     * Corresponds to GET /user/{id}/bookings
     */
    public function bookings(string $id)
    {
        $user = User::findOrFail($id);
        
        
        $bookings = $user->bookedEvents()->with('images')->get();
        
        return response()->json($bookings);
    }

    /**
     * Get a specific booking for a user and event.
     * Corresponds to GET /user/{userId}/event/{eventId}/booking
     */
    public function eventBooking(string $userId, string $eventId)
    {
        $user = User::findOrFail($userId);
        $booking = $user->bookedEvents()->where('event.event_id', $eventId)->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        return response()->json($booking);
    }

    /**
     * Get bookings associated with a specific user and card.
     * This logic assumes a relationship exists.
     * Corresponds to GET /user/{userId}/card/{cardId}/booking
     */
    public function cardBooking(string $userId, string $cardId)
    {

        $user = User::with('cards')->findOrFail($userId);
        $card = $user->cards()->where('card.bank_card_id', $cardId)->first();

        if (!$card) {
            return response()->json(['message' => 'Card not found for this user.'], 404);
        }

        $bookings = "Logic to find bookings for user " . $userId . " with card " . $cardId . " goes here.";

        return response()->json($bookings);
    }
}
