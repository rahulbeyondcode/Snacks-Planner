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
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validated();
        // Convert 'holiday_date' from d-M-Y (UI/API) to Y-m-d (DB)
        if (isset($data['holiday_date'])) {
            $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
        }
        $holiday = $this->officeHolidayService->updateHoliday($id, $data);
        if (!$holiday) {
            return response()->json(['message' => 'Holiday not found'], 404);
        }

        return response()->json([
            'message' => 'Holiday updated successfully',
            'data' => new \App\Http\Resources\OfficeHolidayResource($holiday),
            'active_holidays' => $this->getActiveOfficeHolidaysList()
        ]);
    }

    // Delete an office holiday
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $deleted = $this->officeHolidayService->deleteHoliday($id);
        if (!$deleted) {
            return response()->json(['message' => 'Holiday not found'], 404);
        }

        return response()->json([
            'message' => 'Holiday deleted successfully',
            'active_holidays' => $this->getActiveOfficeHolidaysList()
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
        return \App\Http\Resources\OfficeHolidayResource::collection($holidays);
    }

    // Add a new office holiday (pill-style add)
    public function store(StoreOfficeHolidayRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
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
            'message' => 'Holiday created successfully',
            'data' => new \App\Http\Resources\OfficeHolidayResource($holiday),
            'active_holidays' => $this->getActiveOfficeHolidaysList()
        ], 201);
    }

    // Only account_manager can set a holiday
    public function setHoliday(SetOfficeHolidayRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validated();

        if ($this->officeHolidayService->isHolidaySet($validated['holiday_date'])) {
            return response()->json(['message' => 'Holiday already set for this date'], 422);
        }

        $holiday = $this->officeHolidayService->setHoliday([
            'user_id' => $user->user_id,
            'holiday_date' => $validated['holiday_date'],
            'description' => $validated['description'] ?? null,
            'type' => \App\Models\OfficeHoliday::TYPE_OFFICE_HOLIDAY,
            'group_id' => null,
        ]);

        return response()->json([
            'message' => 'Holiday set successfully',
            'data' => new \App\Http\Resources\OfficeHolidayResource($holiday),
            'active_holidays' => $this->getActiveOfficeHolidaysList()
        ], 201);
    }
}
