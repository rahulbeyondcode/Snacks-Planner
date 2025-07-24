<?php

namespace App\Http\Controllers;

use App\Models\SnackSuggestion;
use App\Http\Requests\StoreSnackSuggestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SnackSuggestionController extends Controller
{
    // Employee can suggest a snack
    public function store(StoreSnackSuggestionRequest $request)
    {
        $suggestion = SnackSuggestion::create([
            'user_id' => Auth::id(),
            'snack_name' => $request->validated()['snack_name'],
            'comment' => $request->validated()['comment'] ?? null,
        ]);
        return response()->json($suggestion, 201);
    }

    // List all suggestions (optionally filter by user)
    public function index(Request $request)
    {
        $query = SnackSuggestion::query();
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        return response()->json($query->latest()->get());
    }
}
