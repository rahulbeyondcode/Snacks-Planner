<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;

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
        return apiResponse(true, __('messages.success'), $shops, 200);
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
            return apiResponse(false, __('messages.not_found'), null, 404);
        }
        return apiResponse(true, __('messages.success'), $shop, 200);
    }

    // Create a shop (admin only)
    public function store(StoreShopRequest $request)
    {
        $shop = Shop::create($request->validated());
        return apiResponse(true, __('messages.success'), $shop, 201);
    }

    // Update a shop (admin only)
    public function update(UpdateShopRequest $request, $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return apiResponse(false, __('messages.not_found'), null, 404);
        }
        $shop->update($request->validated());
        return apiResponse(true, __('messages.success'), $shop, 200);
    }

    // Delete a shop (admin only)
    public function destroy($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return apiResponse(false, __('messages.not_found'), null, 404);
        }
        $shop->delete();
        return apiResponse(true, __('messages.success'), null, 200);
    }
}
