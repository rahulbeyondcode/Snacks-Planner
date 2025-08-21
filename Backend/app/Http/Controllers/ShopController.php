<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopPaymentMethod;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Resources\ShopResource;

class ShopController extends Controller
{
    // List all shops
    public function index(Request $request)
    {
        $query = Shop::query();

        // Always include payment methods
        $query->with('paymentMethods');

        $shops = $query->get();
        return ShopResource::collection($shops);
    }

    // Show a single shop
    public function show(Request $request, $id)
    {
        $query = Shop::query();

        // Always include payment methods
        $query->with('paymentMethods');

        $shop = $query->find($id);

        if (!$shop) {
            return response()->notFound(__('Shop not found'));
        }
        return new ShopResource($shop);
    }

    // Create a shop (admin only)
    public function store(StoreShopRequest $request)
    {
        $shop = Shop::create($request->validated());

        // Handle payment methods if provided
        if ($request->has('payment_methods')) {
            $this->attachPaymentMethods($shop, $request->input('payment_methods'));
        }

        // Load the shop with payment methods for response
        $shop->load('paymentMethods');

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

        // Handle payment methods if provided
        if ($request->has('payment_methods')) {
            $this->syncPaymentMethods($shop, $request->input('payment_methods'));
        }

        // Load the shop with payment methods for response
        $shop->load('paymentMethods');

        return (new ShopResource($shop))->response()->setStatusCode(200);
    }

    // Delete a shop (admin only)
    public function destroy($id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->notFound(__('Shop not found'));
        }

        // Delete related payment methods before deleting the shop
        $shop->paymentMethods()->delete();

        $shop->delete();
        return response()->noContent();
    }

    /**
     * Attach payment methods to a shop
     */
    private function attachPaymentMethods(Shop $shop, array $paymentMethods)
    {
        foreach ($paymentMethods as $method) {
            ShopPaymentMethod::create([
                'shop_id' => $shop->shop_id,
                'payment_method' => $method, // This will store 'cash', 'card', 'upi', 'bank_transfer'
            ]);
        }
    }

    /**
     * Sync payment methods for a shop (replace existing with new ones)
     */
    private function syncPaymentMethods(Shop $shop, array $paymentMethods)
    {
        // Delete existing payment methods
        $shop->paymentMethods()->delete();

        // Add new payment methods
        $this->attachPaymentMethods($shop, $paymentMethods);
    }
}
