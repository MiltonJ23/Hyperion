<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Images;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class EventController extends Controller
{

    public function index(Request $request)
    {

        $query = Event::query()->with('user','images');


        if ($request->has('name')) {
            $query->where('event_name', 'like', '%' . $request->input('name') . '%');
        }


        if ($request->has('location')) {
            $query->where('event_location', 'like', '%' . $request->input('location') . '%');
        }


        if ($request->has('date')) {
            $query->whereDate('event_date', $request->input('date'));
        }


        if ($request->has('max_price')) {
            $query->where('event_price', '<=', $request->input('max_price'));
        }


        if ($request->has('sort_by')) {
            $sortColumn = $request->input('sort_by');
            $sortDirection = $request->input('direction', 'asc');
            $sortableColumns = ['event_date', 'event_price', 'event_name'];
            if (in_array($sortColumn, $sortableColumns)) {
                $query->orderBy($sortColumn, $sortDirection);
            }
        } else {

            $query->orderBy('event_date', 'asc');
        }


        $events = $query->paginate(6);

        return response()->json($events);
    }

    /**
     * Store a newly created event in storage.
     * Corresponds to POST /event
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'event_desc' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i',
            'event_venue' => 'required|string|max:200',
            'event_location' => 'nullable|string|max:255',
            'event_status' => 'required|in:Waiting,In Progress,Finished',
            'event_price' => 'required|numeric|min:0',

            'fk_user_id' => 'required|uuid|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $event = Event::create($validator->validated());

        return response()->json($event, 201);
    }





    public function show(string $id)
    {

        $event = Event::with(['user', 'images'])->findOrFail($id);
        return response()->json($event);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        
        if ($event->fk_user_id !== Auth::id()) {
            return response()->json(['error' => 'You are not authorized to edit this event.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'event_name' => 'sometimes|required|string|max:255',
            'event_desc' => 'sometimes|required|string',
            'event_date' => 'sometimes|required|date',
            'event_time' => 'sometimes|required|date_format:H:i',
            'event_venue' => 'sometimes|required|string|max:200',
            'event_location' => 'nullable|string|max:255',
            'event_price' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $event->update($validator->validated());

        return response()->json($event);
    }


    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return response()->json(null, 204);
    }


    public function getImages(string $id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event->images);
    }

    /**
     * Add an image to a specific event.
     * Corresponds to POST /event/{id}/image
     */
    public function addImage(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:30720',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $path = $request->file('image')->store('event_images', 'public');


        $image = Images::create([
            'image_url' => $path
        ]);


        $event->images()->attach($image->image_id);

        return response()->json(['message' => 'Image added successfully.', 'image' => $image], 201);
    }


    public function deleteImage(string $id, string $imageId)
    {
        $event = Event::findOrFail($id);
        $image = Images::findOrFail($imageId);


        $event->images()->detach($image->image_id);


        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }


        $image->delete();

        return response()->json(null, 204);
    }


    public function bookings(string $id)
    {
        $event = Event::findOrFail($id);

        $bookings = $event->bookedByUsers()->get();
        return response()->json($bookings);
    }


    public function book(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user) {
             return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $event = Event::findOrFail($id);
        $event->bookedByUsers()->attach($user->user_id, [
            'status' => 'Confirmed',
            'price_at_booking' => $event->event_price,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Event booked successfully for user ' . $user->name]);
    }


    public function cancel(Request $request, string $id)
    {

        $user = User::find($request->input('user_id')) ?? User::first();

        $event = Event::findOrFail($id);
        $event->bookedByUsers()->detach($user->user_id);

        return response()->json(['message' => 'Booking canceled successfully for user ' . $user->name]);
    }
}
