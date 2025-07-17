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
        // if (!$user || !$user->hasAnyRole(['admin', 'manager'])) {
        //     return response()->json(['message' => 'Forbidden.'], 403);
        // }
        // Continue with logic for allowed roles...
        $shops = Shop::all();
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
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255|unique:shops,shop_name',
            'address' => 'required|string|max:255',
            'phone_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'location' => 'nullable|string|max:255'
        ]);
        $shop = Shop::create($validated);
        return response()->json([
            'success' => true,
            'data' => $shop,
            'message' => 'Shop created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $shop = Shop::find($id);
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
        $shop = Shop::find($id);
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
        $shop = Shop::find($id);
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
