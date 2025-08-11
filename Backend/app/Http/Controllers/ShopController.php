<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Resources\ShopResource;

class ShopController extends Controller
{
    // List all shops
    public function index()
    {
        $shops = Shop::select([
            'shop_id',
            'name',
            'address',
            'contact_number'
        ])->get();
        return ShopResource::collection($shops);
    }

    // Show a single shop
    public function show($id)
    {
        $shop = Shop::select([
            'shop_id',
            'name',
            'address',
            'contact_number'
        ])->find($id);

        if (!$shop) {
            return response()->notFound(__('Shop not found'));
        }
        return new ShopResource($shop);
    }

    // Create a shop (admin only)
    public function store(StoreShopRequest $request)
    {
        $shop = Shop::create($request->validated());
        return (new ShopResource($shop))->response()->setStatusCode(201);
    }

    // Update a shop (admin only)
    public function update(UpdateShopRequest $request, $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->notFound(__('Shop not found'));
        }
        $shop->update($request->validated());
        return (new ShopResource($shop))->response()->setStatusCode(200);
    }

    // Delete a shop (admin only)
    public function destroy($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->notFound(__('Shop not found'));
        }
        $shop->delete();
        return response()->noContent();
    }
}
