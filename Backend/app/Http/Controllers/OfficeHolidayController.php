<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SetOfficeHolidayRequest;
use App\Http\Requests\StoreOfficeHolidayRequest;
use App\Http\Requests\UpdateOfficeHolidayRequest;
use App\Services\OfficeHolidayServiceInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OfficeHolidayController extends Controller
{
    protected $officeHolidayService;

    public function __construct(OfficeHolidayServiceInterface $officeHolidayService)
    {
        $this->officeHolidayService = $officeHolidayService;
    }

    /**
     * Helper method to get active office holidays list
     */
    private function getActiveOfficeHolidaysList()
    {
        $holidays = $this->officeHolidayService->getOfficeHolidays();
        return \App\Http\Resources\OfficeHolidayResource::collection($holidays);
    }

    // Update an office holiday
    public function update(UpdateOfficeHolidayRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only account managers can update office holidays.',
                'data' => []
            ], 403);
        }

        $data = $request->validated();
        // Convert 'holiday_date' from d-M-Y (UI/API) to Y-m-d (DB)
        if (isset($data['holiday_date'])) {
            $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
        }

        $holiday = $this->officeHolidayService->updateHoliday($id, $data);
        if (!$holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Holiday updated successfully',
            'data' => new \App\Http\Resources\OfficeHolidayResource($holiday)
        ]);
    }

    // Delete an office holiday
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only account managers can delete office holidays.',
                'data' => []
            ], 403);
        }

        $deleted = $this->officeHolidayService->deleteHoliday($id);
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Holiday deleted successfully',
            'data' => []
        ]);
    }
    // List all office holidays (account_manager only sees office holidays)
    public function index()
    {
        $user = Auth::user();
        if ($user && $user->role->name === 'account_manager') {
            $holidays = $this->officeHolidayService->getOfficeHolidays();
        } else {
            // For shared access, still return all holidays for backward compatibility
            $holidays = $this->officeHolidayService->getAllHolidays();
        }

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\OfficeHolidayResource::collection($holidays)
        ]);
    }

    // Add a new office holiday (pill-style add)
    public function store(StoreOfficeHolidayRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only account managers can create office holidays.',
                'data' => []
            ], 403);
        }

        $data = $request->validated();
        // Convert 'holiday_date' from d-M-Y (UI/API) to Y-m-d (DB)
        if (isset($data['holiday_date'])) {
            $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
        }

        $data['user_id'] = $user->user_id;
        $data['type'] = \App\Models\OfficeHoliday::TYPE_OFFICE_HOLIDAY; // Set type for office holidays
        $data['group_id'] = null; // Office holidays are not group-specific
        $holiday = $this->officeHolidayService->createHoliday($data);

        return response()->json([
            'success' => true,
            'message' => 'Holiday created successfully',
            'data' => new \App\Http\Resources\OfficeHolidayResource($holiday)
        ], 201);
    }

    // Only account_manager can set a holiday
    public function setHoliday(SetOfficeHolidayRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only account managers can set office holidays.',
                'data' => []
            ], 403);
        }

        $validated = $request->validated();

        if ($this->officeHolidayService->isHolidaySet($validated['holiday_date'])) {
            return response()->json([
                'success' => false,
                'message' => 'Holiday already set for this date',
                'data' => []
            ], 422);
        }

        $holiday = $this->officeHolidayService->setHoliday([
            'user_id' => $user->user_id,
            'holiday_date' => $validated['holiday_date'],
            'description' => $validated['description'] ?? null,
            'type' => \App\Models\OfficeHoliday::TYPE_OFFICE_HOLIDAY,
            'group_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Holiday set successfully',
            'data' => new \App\Http\Resources\OfficeHolidayResource($holiday)
        ], 201);
    }
}