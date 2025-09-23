<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SetOfficeHolidayRequest;
use App\Http\Requests\StoreOfficeHolidayRequest;
use App\Http\Requests\UpdateOfficeHolidayRequest;
use App\Services\OfficeHolidayServiceInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OfficeHolidayController extends BaseController
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
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $data = $request->validated();
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }

            $holiday = $this->officeHolidayService->updateHoliday($id, $data);
            if (!$holiday) {
                return $this->notFoundResponse('Holiday not found');
            }

            return $this->updatedResponse(new \App\Http\Resources\OfficeHolidayResource($holiday), 'Holiday updated successfully');
        }, 'account_manager', 'Failed to update office holiday');
    }

    // Delete an office holiday
    public function destroy($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            $deleted = $this->officeHolidayService->deleteHoliday($id);
            if (!$deleted) {
                return $this->notFoundResponse('Holiday not found');
            }
            return $this->deletedResponse('Holiday deleted successfully');
        }, 'account_manager', 'Failed to delete office holiday');
    }
    // List all office holidays (account_manager only sees office holidays)
    public function index()
    {
        $user = Auth::user();
        $holidays = $user && $user->role->name === 'account_manager'
            ? $this->officeHolidayService->getOfficeHolidays()
            : $this->officeHolidayService->getAllHolidays();

        return $this->resourceCollectionResponse(\App\Http\Resources\OfficeHolidayResource::collection($holidays));
    }

    // Add a new office holiday (pill-style add)
    public function store(StoreOfficeHolidayRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $data = $request->validated();
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }
            $data['user_id'] = $user->user_id;
            $data['type'] = \App\Models\OfficeHoliday::TYPE_OFFICE_HOLIDAY;
            $data['group_id'] = null;
            $holiday = $this->officeHolidayService->createHoliday($data);

            return $this->createdResponse(new \App\Http\Resources\OfficeHolidayResource($holiday), 'Holiday created successfully');
        }, 'account_manager', 'Failed to create office holiday');
    }

    // Only account_manager can set a holiday
    public function setHoliday(SetOfficeHolidayRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $validated = $request->validated();

            if ($this->officeHolidayService->isHolidaySet($validated['holiday_date'])) {
                return $this->validationErrorResponse('Holiday already set for this date');
            }

            $holiday = $this->officeHolidayService->setHoliday([
                'user_id' => $user->user_id,
                'holiday_date' => $validated['holiday_date'],
                'description' => $validated['description'] ?? null,
                'type' => \App\Models\OfficeHoliday::TYPE_OFFICE_HOLIDAY,
                'group_id' => null,
            ]);

            return $this->createdResponse(new \App\Http\Resources\OfficeHolidayResource($holiday), 'Holiday set successfully');
        }, 'account_manager', 'Failed to set office holiday');
    }
}
