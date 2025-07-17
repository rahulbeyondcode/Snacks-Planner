<?php

namespace App\Http\Controllers;

use App\Models\SnackItem;

use Illuminate\Http\Request;

class SnackItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasAnyRole(['admin', 'manager'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $snackItems = SnackItem::all();
        return response()->json([
            'success' => true,
            'data' => $snackItems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'snack_name' => 'required|string|max:255|unique:snack_items,snack_name',
            'snack_description' => 'nullable|string|max:255',            
            'snack_size' => 'required|string|max:255'            
            // Add other fields and rules as needed
        ]);

        $snackItem = SnackItem::create($validated);

        return response()->json([
            'success' => true,
            'data' => $snackItem,
            'message' => 'Snack item created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $snackItem = SnackItem::find($id);
        if (!$snackItem) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $snackItem
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $snackItem = SnackItem::find($id);
       
        if (!$snackItem) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'snack_name' => 'required|string|max:255|unique:snack_items,snack_name,' . $id,
            'snack_description' => 'nullable|string|max:255',            
            'snack_size' => 'required|string|max:255|in:small,medium,large' 
        ]);

        $snackItem->update($validated);

        return response()->json([
            'success' => true,
            'data' => $snackItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $snackItem = SnackItem::find($id);
        if (!$snackItem) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $snackItem->delete();
        return response()->json([
            'success' => true,
            'message' => 'SnackItem deleted.'
        ]);
    }
}
