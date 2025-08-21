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
    /**
     * Get all active shops with payment methods
     */
    private function getAllActiveShops()
    {
        return Shop::whereNull('deleted_at')
            ->with('paymentMethods')
            ->get();
    }

    // List all shops
    public function index(Request $request)
    {
        $shops = $this->getAllActiveShops();

        return response()->json([
            'success' => true,
            'message' => 'Shops retrieved successfully',
            'data' => ShopResource::collection($shops)
        ]);
    }

    // Show a single shop
    public function show(Request $request, $id)
    {
        $shop = Shop::whereNull('deleted_at')->find($id);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found',
                'data' => null
            ], 404);
        }

        // Get all active shops for response
        $shops = $this->getAllActiveShops();

        return response()->json([
            'success' => true,
            'message' => 'Shop retrieved successfully',
            'data' => ShopResource::collection($shops)
        ]);
    }

    // Create a shop (admin only)
    public function store(StoreShopRequest $request)
    {
        $shop = Shop::create($request->validated());

        // Handle payment methods if provided
        if ($request->has('payment_methods')) {
            $this->attachPaymentMethods($shop, $request->input('payment_methods'));
        }

        // Get all active shops for response (including the newly created one)
        $shops = $this->getAllActiveShops();

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
            'data' => ShopResource::collection($shops)
        ], 201);
    }

    // Update a shop (admin only)
    public function update(UpdateShopRequest $request, $id)
    {
        $shop = Shop::whereNull('deleted_at')->find($id);
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found',
                'data' => null
            ], 404);
        }

        $shop->update($request->validated());

        // Handle payment methods if provided
        if ($request->has('payment_methods')) {
            $this->syncPaymentMethods($shop, $request->input('payment_methods'));
        }

        // Get all active shops for response (including the updated one)
        $shops = $this->getAllActiveShops();

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully',
            'data' => ShopResource::collection($shops)
        ]);
    }

    // Delete a shop (admin only)
    public function destroy($id)
    {
        $shop = Shop::whereNull('deleted_at')->find($id);
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found',
                'data' => null
            ], 404);
        }

        // Delete related payment methods before deleting the shop
        $shop->paymentMethods()->delete();

        $shop->delete();

        // Get remaining active shops after deletion
        $shops = $this->getAllActiveShops();

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully',
            'data' => ShopResource::collection($shops)
        ]);
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
