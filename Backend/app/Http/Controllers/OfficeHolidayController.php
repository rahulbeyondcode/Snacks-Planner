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
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'account_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only account managers can update office holidays.',
                    [],
                    403
                );
            }

            $data = $request->validated();
            // Convert 'holiday_date' from d-M-Y (UI/API) to Y-m-d (DB)
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }

            $holiday = $this->officeHolidayService->updateHoliday($id, $data);
            if (!$holiday) {
                return apiResponse(
                    false,
                    'Holiday not found',
                    [],
                    404
                );
            }

            return apiResponse(
                true,
                'Holiday updated successfully',
                [
                    'holiday' => new \App\Http\Resources\OfficeHolidayResource($holiday),
                    'active_holidays' => $this->getActiveOfficeHolidaysList()
                ],
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update holiday: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Delete an office holiday
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'account_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only account managers can delete office holidays.',
                    [],
                    403
                );
            }

            $deleted = $this->officeHolidayService->deleteHoliday($id);
            if (!$deleted) {
                return apiResponse(
                    false,
                    'Holiday not found',
                    [],
                    404
                );
            }

            return apiResponse(
                true,
                'Holiday deleted successfully',
                $this->getActiveOfficeHolidaysList(),
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to delete holiday: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
    // List all office holidays (account_manager only sees office holidays)
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user && $user->role->name === 'account_manager') {
                $holidays = $this->officeHolidayService->getOfficeHolidays();
            } else {
                // For shared access, still return all holidays for backward compatibility
                $holidays = $this->officeHolidayService->getAllHolidays();
            }

            return apiResponse(
                true,
                'Office holidays retrieved successfully',
                \App\Http\Resources\OfficeHolidayResource::collection($holidays),
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve office holidays: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Add a new office holiday (pill-style add)
    public function store(StoreOfficeHolidayRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'account_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only account managers can create office holidays.',
                    [],
                    403
                );
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

            return apiResponse(
                true,
                'Holiday created successfully',
                [
                    'holiday' => new \App\Http\Resources\OfficeHolidayResource($holiday),
                    'active_holidays' => $this->getActiveOfficeHolidaysList()
                ],
                201
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to create holiday: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Only account_manager can set a holiday
    public function setHoliday(SetOfficeHolidayRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'account_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only account managers can set office holidays.',
                    [],
                    403
                );
            }

            $validated = $request->validated();

            if ($this->officeHolidayService->isHolidaySet($validated['holiday_date'])) {
                return apiResponse(
                    false,
                    'Holiday already set for this date',
                    [],
                    422
                );
            }

            $holiday = $this->officeHolidayService->setHoliday([
                'user_id' => $user->user_id,
                'holiday_date' => $validated['holiday_date'],
                'description' => $validated['description'] ?? null,
                'type' => \App\Models\OfficeHoliday::TYPE_OFFICE_HOLIDAY,
                'group_id' => null,
            ]);

            return apiResponse(
                true,
                'Holiday set successfully',
                [
                    'holiday' => new \App\Http\Resources\OfficeHolidayResource($holiday),
                    'active_holidays' => $this->getActiveOfficeHolidaysList()
                ],
                201
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to set holiday: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
