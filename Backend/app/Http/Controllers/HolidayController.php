<?php

namespace App\Http\Controllers;

use App\Models\Holiday;

use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasAnyRole(['admin', 'manager'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $holidays = Holiday::all();
        return response()->json([
            'success' => true,
            'data' => $holidays
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Holiday created (stub).']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $holiday = Holiday::find($id);
        if (!$holiday) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $holiday
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $holiday = Holiday::find($id);
        if (!$holiday) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $holiday->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $holiday
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $holiday = Holiday::find($id);
        if (!$holiday) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $holiday->delete();
        return response()->json([
            'success' => true,
            'message' => 'Holiday deleted.'
        ]);
    }
}
