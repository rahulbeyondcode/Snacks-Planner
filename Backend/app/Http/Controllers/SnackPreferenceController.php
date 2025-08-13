<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSnackPreferenceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SnackPreferenceController extends Controller
{
    /**
     * Static snack preference options
     */
    private static array $snackOptions = [
        [
            'value' => 'all_snacks',
            'label' => 'Yes to all snacks'
        ],
        [
            'value' => 'veg_only',
            'label' => 'Veg only'
        ],
        [
            'value' => 'no_snacks',
            'label' => 'No snacks, thanks'
        ],
        [
            'value' => 'veg_but_egg',
            'label' => 'Veg but Egg OK'
        ],
        [
            'value' => 'no_beef',
            'label' => 'Any but no beef'
        ],
        [
            'value' => 'no_chicken',
            'label' => 'Any but no chicken'
        ]
    ];

    /**
     * Check if user has access (all roles except account_manager)
     */
    private function checkAccess(): ?array
    {
        $user = Auth::user();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Authentication required.',
                'data' => [],
                'status' => 401
            ];
        }

        if ($user->role->name === 'account_manager') {
            return [
                'success' => false,
                'message' => 'Access denied. Account managers cannot access snack preferences.',
                'data' => [],
                'status' => 403
            ];
        }

        return null; // Access granted
    }

    /**
     * List all snack preference options with user's current selection
     * GET /api/snack-preferences
     */
    public function index()
    {
        // Check access control
        $accessCheck = $this->checkAccess();
        if ($accessCheck) {
            return response()->json([
                'success' => $accessCheck['success'],
                'message' => $accessCheck['message'],
                'data' => $accessCheck['data']
            ], $accessCheck['status']);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'options' => self::$snackOptions,
                'current_preference' => $user->preference
            ]
        ]);
    }

    /**
     * Update user's snack preference
     * PUT /api/snack-preferences
     */
    public function update(UpdateSnackPreferenceRequest $request)
    {
        // Check access control
        $accessCheck = $this->checkAccess();
        if ($accessCheck) {
            return response()->json([
                'success' => $accessCheck['success'],
                'message' => $accessCheck['message'],
                'data' => $accessCheck['data']
            ], $accessCheck['status']);
        }

        $user = Auth::user();
        $validated = $request->validated();

        // Update user's preference
        User::where('user_id', $user->user_id)
            ->update(['preference' => $validated['preference']]);

        // Get the label for the selected preference
        $selectedOption = collect(self::$snackOptions)
            ->firstWhere('value', $validated['preference']);

        return response()->json([
            'success' => true,
            'message' => 'Snack preference updated successfully.',
            'data' => [
                'preference' => $validated['preference'],
                'label' => $selectedOption['label'] ?? 'Unknown preference'
            ]
        ]);
    }
}
