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
        return response()->json(['success' => true, 'message' => 'Snack item created (stub).']);
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
        $snackItem->update($request->all());
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
