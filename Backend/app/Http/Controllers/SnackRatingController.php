<?php

namespace App\Http\Controllers;

use App\Models\SnackRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSnackRatingRequest;

class SnackRatingController extends Controller
{
    // Employee can rate a snack
    public function store(StoreSnackRatingRequest $request)
    {
        $validated = $request->validated();
        $rating = SnackRating::create([
            'user_id' => Auth::id(),
            'snack_item_id' => $validated['snack_item_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);
        return response()->json($rating, 201);
    }

    // List all ratings for a snack item
    public function index(Request $request)
    {
        $query = SnackRating::query();
        if ($request->has('snack_item_id')) {
            $query->where('snack_item_id', $request->snack_item_id);
        }
        return response()->json($query->latest()->get());
    }
}
