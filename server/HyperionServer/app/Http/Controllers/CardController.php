<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CardController extends Controller
{

    public function index(Request $request)
    {
        $query = Card::query();


        if ($request->has('provider')) {
            $query->where('bank_provider', 'like', '%' . $request->input('provider') . '%');
        }


        if ($request->has('holder_name')) {
            $query->where('bank_card_holder_name', 'like', '%' . $request->input('holder_name') . '%');
        }

        $cards = $query->get();

        return response()->json($cards);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_card_number' => 'required|string|unique:card,bank_card_number|max:19',
            'bank_card_holder_name' => 'required|string|max:255',
            'bank_card_type' => 'required|in:Debit,Credit,Other',
            'date_peremption' => 'required|date',
            'cvv' => 'required|string|digits:3',
            'bank_provider' => 'required|string|max:20',
            'fk_user_id' => 'required|exists:users,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        DB::beginTransaction();
        try {
            $card = Card::create($request->except('fk_user_id'));

            DB::table('user_card')->insert([
                'fk1_user_id' => $request->input('fk_user_id'),
                'fk2_bank_card_id' => $card->bank_card_id
            ]);
            DB::commit();
            return response()->json($card, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save the card. The operation was rolled back.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(string $id)
    {
        $card = Card::findOrFail($id);
        return response()->json($card);
    }


    public function update(Request $request, string $id)
    {
        $card = Card::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bank_card_holder_name' => 'sometimes|required|string|max:255',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $card->update($validator->validated());

        return response()->json($card);
    }

    /**
     * Remove the specified resource from storage.
     * Corresponds to DELETE /card/{id}
     */
    public function destroy(string $id)
    {
        $card = Card::findOrFail($id);
        $card->users()->detach();
        $card->delete();

        return response()->json(null, 204);
    }


    public function bookings(string $id)
    {
        $card = Card::findOrFail($id);

        return response()->json([
            'message' => 'Logic to retrieve bookings for this card goes here.',
            'card' => $card
        ]);
    }

    /**
     * A custom method to search for cards.
     * Corresponds to GET /card/search
     */
    public function search(Request $request)
    {

        $query = Card::query();

        if ($request->has('provider')) {
            $query->where('bank_provider', 'like', '%' . $request->input('provider') . '%');
        }



        $cards = $query->get();

        return response()->json($cards);
    }
}
