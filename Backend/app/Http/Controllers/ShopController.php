<?php

namespace App\Http\Controllers;

use App\Models\Shop;

use Illuminate\Http\Request;

class ShopController extends Controller
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
        // Continue with logic for allowed roles...
        $shops = \App\Models\Shop::all();
        return response()->json([
            'success' => true,
            'data' => $shops
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $shop = \App\Models\Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $shop = \App\Models\Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $shop->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $shop = \App\Models\Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $shop->delete();
        return response()->json([
            'success' => true,
            'message' => 'Shop deleted.'
        ]);
    }
}
