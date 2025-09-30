<?php

namespace App\Http\Controllers;

use App\Services\WorkingDayService;
use App\Http\Requests\UpdateWorkingDaysRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkingDayController extends BaseController
{
    protected $service;

    public function __construct(WorkingDayService $service)
    {
        $this->service = $service;
    }

    // Get current working days
    public function show()
    {
        $current = $this->service->getCurrent();
        $workingDays = $current ? $current->working_days : [];

        return $this->successResponse('success', $workingDays);
    }

    // Update working days
    public function update(UpdateWorkingDaysRequest $request)
    {
        $userId = Auth::id();
        $days = $request->input('working_days');
        $updated = $this->service->updateWorkingDays($days, $userId);

        return $this->updatedResponse($updated->working_days, 'Working days updated successfully');
    }
}
