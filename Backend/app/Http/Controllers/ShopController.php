<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // List all shops
    public function index()
    {
        return response()->json(Shop::all());
    }

    // Show a single shop
    public function show($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($shop);
    }

    // Create a shop (admin only)
    public function store(\App\Http\Requests\StoreShopRequest $request)
    {
        $shop = Shop::create($request->validated());
        return response()->json($shop, 201);
    }

    // Update a shop (admin only)
    public function update(\App\Http\Requests\UpdateShopRequest $request, $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $shop->update($request->validated());
        return response()->json($shop);
    }

    // Delete a shop (admin only)
    public function destroy($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $shop->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
