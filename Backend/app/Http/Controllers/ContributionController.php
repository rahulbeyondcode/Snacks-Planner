<?php

namespace App\Http\Controllers;

use App\Models\Contribution;

use Illuminate\Http\Request;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasAnyRole(['admin', 'manager', 'operations'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $contributions = Contribution::with('user')->get();
        $contributions = $contributions->map(function ($item) {
            $data = $item->toArray();
            $data['user_name'] = $item->user ? $item->user->name : null;
            return $data;
        });
        return response()->json([
            'success' => true,
            'data' => $contributions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);
        $contribution = Contribution::create($validated);
        return response()->json([
            'success' => true,
            'data' => $contribution
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contribution = Contribution::with('user')->find($id);
        if (!$contribution) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $data = $contribution->toArray();
        $data['user_name'] = $contribution->user ? $contribution->user->name : null;
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $contribution = Contribution::find($id);
        if (!$contribution) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'date' => 'sometimes|date',
            'status' => 'sometimes|string',
            'remarks' => 'nullable|string',
        ]);
        $contribution->update($validated);
        return response()->json([
            'success' => true,
            'data' => $contribution
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contribution = Contribution::find($id);
        if (!$contribution) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $contribution->delete();
        return response()->json([
            'success' => true,
            'message' => 'Contribution deleted.'
        ]);
    }

    /**
     * Mark a contribution as paid.
     */
    public function markPaid($contribution)
    {
        $contribution = Contribution::find($contribution);
        if (!$contribution) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $contribution->status = 'paid';
        $contribution->save();
        return response()->json([
            'success' => true,
            'data' => $contribution
        ]);
    }

    /**
     * Mark a contribution as unpaid.
     */
    public function markUnpaid($contribution)
    {
        $contribution = Contribution::find($contribution);
        if (!$contribution) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $contribution->status = 'unpaid';
        $contribution->save();
        return response()->json([
            'success' => true,
            'data' => $contribution
        ]);
    }

    /**
     * Search contributions.
     */
    public function search(Request $request)
    {
        $query = Contribution::with('user');
        $hasFilter = false;

        // if ($request->has('status')) {
        //     $query->where('status', $request->input('status'));
        //     $hasFilter = true;
        // }
        // if ($request->has('user_id')) {
        //     $query->where('user_id', $request->input('user_id'));
        //     $hasFilter = true;
        // }
        if ($request->has('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('user_name') . '%');
            });
            $hasFilter = true;
        }

        if (!$hasFilter) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $results = $query->get()->map(function ($item) {
            $data = $item->toArray();
            $data['user_name'] = $item->user ? $item->user->name : null;
            return $data;
        });
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Summary of contributions.
     */
    public function summary(Request $request)
    {
        $total = Contribution::count();
        $paid = Contribution::where('status', 'paid')->count();
        $unpaid = Contribution::where('status', 'unpaid')->count();
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'paid' => $paid,
                'unpaid' => $unpaid
            ]
        ]);
    }
}