<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopPaymentMethod;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Resources\ShopResource;

class ShopController extends BaseController
{
    /**
     * Find an active shop by ID
     */
    private function findActiveShop(int $id): ?Shop
    {
        return Shop::whereNull('deleted_at')->find($id);
    }

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
        return $this->executeWithAuth(function ($user) {
            $shops = $this->getAllActiveShops();
            return $this->resourceCollectionResponse(ShopResource::collection($shops));
        }, ['account_manager', 'snack_manager', 'operation'], 'Failed to retrieve shops');
    }

    // Show a single shop
    public function show(Request $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            $shop = $this->findActiveShop($id);

            if (!$shop) {
                return $this->notFoundResponse('Shop not found');
            }

            // Get all active shops for response
            $shops = $this->getAllActiveShops();

            return $this->resourceCollectionResponse(ShopResource::collection($shops));
        }, ['account_manager', 'snack_manager', 'operation'], 'Failed to retrieve shop');
    }

    // Create a shop (admin only)
    public function store(StoreShopRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $shop = Shop::create($request->validated());

            // Handle payment methods if provided
            if ($request->has('payment_methods')) {
                $this->attachPaymentMethods($shop, $request->input('payment_methods'));
            }

            // Get all active shops for response (including the newly created one)
            $shops = $this->getAllActiveShops();

            return $this->createdResponse(
                ShopResource::collection($shops),
                'Shop created successfully'
            );
        }, 'account_manager', 'Failed to create shop');
    }

    // Update a shop (admin only)
    public function update(UpdateShopRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $shop = $this->findActiveShop($id);
            if (!$shop) {
                return $this->notFoundResponse('Shop not found');
            }

            $shop->update($request->validated());

            // Handle payment methods if provided
            if ($request->has('payment_methods')) {
                $this->syncPaymentMethods($shop, $request->input('payment_methods'));
            }

            // Get all active shops for response (including the updated one)
            $shops = $this->getAllActiveShops();

            return $this->updatedResponse(
                ShopResource::collection($shops),
                'Shop updated successfully'
            );
        }, 'account_manager', 'Failed to update shop');
    }

    // Delete a shop (admin only)
    public function destroy($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            $shop = $this->findActiveShop($id);
            if (!$shop) {
                return $this->notFoundResponse('Shop not found');
            }

            // Delete related payment methods before deleting the shop
            $shop->paymentMethods()->delete();

            $shop->delete();

            // Get remaining active shops after deletion
            $shops = $this->getAllActiveShops();

            return $this->successResponse(
                'Shop deleted successfully',
                ShopResource::collection($shops)
            );
        }, 'account_manager', 'Failed to delete shop');
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
