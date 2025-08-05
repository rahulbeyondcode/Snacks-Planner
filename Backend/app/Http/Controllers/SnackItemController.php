<?php

namespace App\Http\Controllers;

use App\Models\SnackItem;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSnackItemRequest;
use App\Http\Requests\UpdateSnackItemRequest;

class SnackItemController extends Controller
{
    // List all snack items
    public function index()
    {
        return apiResponse(true, __('success'), SnackItem::all(), 201);
    }

    // Show a single snack item
    public function show($id)
    {
        $item = SnackItem::find($id);
        if (!$item) {
            return apiResponse(true, __('success'), $item, 201);
        }
        return apiResponse(true, __('success'), $item, 201);
    }

    // Create a snack item (admin only)
    public function store(StoreSnackItemRequest $request)
    {
        $item = SnackItem::create($request->validated());
        return apiResponse(true, __('success'), $item, 201);
    }

    // Update a snack item (admin only)
    public function update(UpdateSnackItemRequest $request, $id)
    {
        $item = SnackItem::find($id);
        if (!$item) {
            return apiResponse(true, __('not_found'),  404);
        }
        $item->update($request->validated());
        return apiResponse(true, __('update_msg'), $item, 201);
    }

    // Delete a snack item (admin only)
    public function destroy($id)
    {
        $item = SnackItem::find($id);
        if (!$item) {
            return apiResponse(true, __('not_found'),  404);
        }
        $item->delete();
        return apiResponse(true, __('delete'),  201);
    }
}
