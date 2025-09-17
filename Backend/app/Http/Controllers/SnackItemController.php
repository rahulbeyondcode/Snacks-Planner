<?php

namespace App\Http\Controllers;

use App\Models\SnackItem;
use App\Models\SnackShopMapping;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSnackItemRequest;
use App\Http\Requests\UpdateSnackItemRequest;
use App\Http\Resources\SnackItemResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SnackItemController extends Controller
{

    // Get all snacks with their shop mappings
    public function index()
    {
        try {
            // Get all snack items with their shop mappings
            $snacks = SnackItem::with(['shopMappings.shop'])
                ->whereHas('shopMappings')
                ->get()
                ->flatMap(function ($snack) {
                    // Create an entry for each shop mapping
                    return $snack->shopMappings->map(function ($mapping) use ($snack) {
                        return [
                            'snack_item_id' => $snack->snack_item_id,
                            'snack_name' => $snack->name . ' - ' . $mapping->shop->name,
                            'description' => $snack->description,
                            'shop_id' => $mapping->shop_id,
                            'shop_name' => $mapping->shop->name,
                            'snack_price' => $mapping->snack_price,
                            'is_available' => $mapping->is_available,
                        ];
                    });
                });

            return response()->json([
                'success' => true,
                'message' => 'Snacks retrieved successfully',
                'data' => $snacks
            ], 200);
        } catch (\Exception $e) {
            return Response::internalServerError(__('Failed to retrieve snacks'));
        }
    }

    // Show a single snack item
    public function show($id)
    {
        $item = SnackItem::select([
            'snack_item_id',
            'name',
            'description'
        ])->find($id);

        if (!$item) {
            return response()->notFound(__('Snack Item not found'));
        }
        return new SnackItemResource($item);
    }

    // Create a snack item (admin only)
    public function store(StoreSnackItemRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Create the snack item
            $snackData = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null
            ];

            $item = SnackItem::create($snackData);

            // Create the shop mapping
            SnackShopMapping::create([
                'snack_item_id' => $item->snack_item_id,
                'shop_id' => $validated['shop_id'],
                'snack_price' => $validated['snack_price'],
                'is_available' => $validated['is_available'] ?? true,
            ]);

            DB::commit();

            // Load the item with shop mapping for response
            $item->load('shopMappings.shop');

            return (new SnackItemResource($item))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollback();
            return Response::internalServerError(__('Failed to create snack item'));
        }
    }

    // Update a snack item (admin only)
    public function update(UpdateSnackItemRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $item = SnackItem::find($id);
            if (!$item) {
                return response()->notFound(__('Snack Item not found'));
            }

            $validated = $request->validated();

            // Update the snack item
            $snackData = [];
            if (isset($validated['name'])) {
                $snackData['name'] = $validated['name'];
            }
            if (isset($validated['description'])) {
                $snackData['description'] = $validated['description'];
            }

            if (!empty($snackData)) {
                $item->update($snackData);
            }

            // Update or create shop mapping if shop_id is provided
            if (isset($validated['shop_id'])) {
                $mappingData = [
                    'snack_item_id' => $item->snack_item_id,
                    'shop_id' => $validated['shop_id'],
                    'snack_price' => $validated['snack_price'],
                    'is_available' => $validated['is_available'] ?? true,
                ];

                // Check if mapping already exists for this shop
                $existingMapping = SnackShopMapping::where('snack_item_id', $item->snack_item_id)
                    ->where('shop_id', $validated['shop_id'])
                    ->first();

                if ($existingMapping) {
                    // Update existing mapping
                    $existingMapping->update([
                        'snack_price' => $validated['snack_price'],
                        'is_available' => $validated['is_available'] ?? $existingMapping->is_available,
                    ]);
                } else {
                    // Create new mapping
                    SnackShopMapping::create($mappingData);
                }
            }

            DB::commit();

            // Load the item with shop mappings for response
            $item->load('shopMappings.shop');

            return (new SnackItemResource($item))->response()->setStatusCode(200);
        } catch (\Exception $e) {
            DB::rollback();
            return Response::internalServerError(__('Failed to update snack item'));
        }
    }

    // Delete a snack item (admin only)
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $item = SnackItem::find($id);

            if (!$item) {
                return response()->notFound(__('Snack Item not found'));
            }

            $item->shopMappings()->delete();
            $item->delete();
            DB::commit();

            return apiResponse(true, __('delete'), null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return Response::internalServerError(__('Failed to delete snack item'));
        }
    }
}
