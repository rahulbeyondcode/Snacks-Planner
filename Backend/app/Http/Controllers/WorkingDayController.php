<?php

namespace App\Http\Controllers;

use App\Services\WorkingDayService;
use App\Http\Requests\UpdateWorkingDaysRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkingDayController extends Controller
{
    protected $service;

    public function __construct(WorkingDayService $service)
    {
        $this->service = $service;
    }

    // Get current working days
    public function show()
    {
        try {
            $current = $this->service->getCurrent();
            $workingDays = $current ? $current->working_days : [];

            return apiResponse(
                true,
                'Working days retrieved successfully',
                $workingDays,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve working days: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Update working days
    public function update(UpdateWorkingDaysRequest $request)
    {
        try {
            $userId = Auth::id();
            $days = $request->input('working_days');
            $updated = $this->service->update($days, $userId);

            return apiResponse(
                true,
                'Working days updated successfully',
                $updated->working_days,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update working days: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
