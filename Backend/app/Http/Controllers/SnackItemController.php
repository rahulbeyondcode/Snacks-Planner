<?php

namespace App\Http\Controllers;

use App\Models\SnackItem;
use App\Models\SnackShopMapping;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSnackItemRequest;
use App\Http\Requests\UpdateSnackItemRequest;
use Illuminate\Support\Facades\DB;

class SnackItemController extends Controller
{
    // List all snack items
    public function index()
    {
        $snackItems = SnackItem::select([
            'snack_item_id',
            'name', 
            'description',
            'price'
        ])->get();
        
        return apiResponse(true, __('success'), $snackItems, 201);
    }

    // Show a single snack item
    public function show($id)
    {
        $item = SnackItem::select([
            'snack_item_id',
            'name', 
            'description',
            'price'
        ])->find($id);
        
        if (!$item) {
            return apiResponse(false, __('not_found'), null, 404);
        }
        return apiResponse(true, __('success'), $item, 200);
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
            
            return apiResponse(true, __('success'), $item, 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return apiResponse(false, 'Failed to create snack item: ' . $e->getMessage(), null, 500);
        }
    }

    // Update a snack item (admin only)
    public function update(UpdateSnackItemRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $item = SnackItem::find($id);
            if (!$item) {
                return apiResponse(false, __('not_found'), null, 404);
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
            
            return apiResponse(true, __('update_msg'), $item, 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            return apiResponse(false, 'Failed to update snack item: ' . $e->getMessage(), null, 500);
        }
    }

    // Delete a snack item (admin only)
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $item = SnackItem::find($id);

            if (!$item) {
                return apiResponse(false, __('not_found'), null, 404);
            }            
           
            $item->shopMappings()->delete();            
            $item->delete();            
            DB::commit();
            
            return apiResponse(true, __('delete'), null, 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            return apiResponse(false, 'Failed to delete snack item: ' . $e->getMessage(), null, 500);
        }
    }
}
