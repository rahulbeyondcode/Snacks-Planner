<?php

namespace App\Http\Controllers;

use App\Models\SnackItem;
use Illuminate\Http\Request;

class SnackItemController extends Controller
{
    // List all snack items
    public function index()
    {
        return response()->json(SnackItem::all());
    }

    // Show a single snack item
    public function show($id)
    {
        $item = SnackItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($item);
    }

    // Create a snack item (admin only)
    public function store(\App\Http\Requests\StoreSnackItemRequest $request)
    {
        $item = SnackItem::create($request->validated());
        return response()->json($item, 201);
    }

    // Update a snack item (admin only)
    public function update(\App\Http\Requests\UpdateSnackItemRequest $request, $id)
    {
        $item = SnackItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $item->update($request->validated());
        return response()->json($item);
    }

    // Delete a snack item (admin only)
    public function destroy($id)
    {
        $item = SnackItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $item->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
