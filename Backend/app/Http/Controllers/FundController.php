<?php

namespace App\Http\Controllers;

use App\Models\Fund;

use Illuminate\Http\Request;

class FundController extends Controller
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
        $funds = Fund::all();
        return response()->json([
            'success' => true,
            'data' => $funds
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Fund created (stub).']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fund = Fund::find($id);
        if (!$fund) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $fund
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $fund = Fund::find($id);
        if (!$fund) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $fund->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $fund
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fund = Fund::find($id);
        if (!$fund) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $fund->delete();
        return response()->json([
            'success' => true,
            'message' => 'Fund deleted.'
        ]);
    }

    /**
     * Get the balance of the money pool.
     */
    public function balance()
    {
        return response()->json([
            'success' => true,
            'message' => 'Balance (stub).'
        ]);
    }
}
