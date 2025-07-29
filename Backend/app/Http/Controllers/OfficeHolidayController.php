<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SetOfficeHolidayRequest;
use App\Http\Requests\StoreOfficeHolidayRequest;
use App\Http\Requests\UpdateOfficeHolidayRequest;
use App\Services\OfficeHolidayServiceInterface;
use Illuminate\Support\Facades\Auth;

class OfficeHolidayController extends Controller
{
    // Update an office holiday
    public function update(UpdateOfficeHolidayRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $holiday = $this->officeHolidayService->updateHoliday($id, $request->validated());
        if (!$holiday) {
            return response()->json(['message' => 'Holiday not found'], 404);
        }
        return new \App\Http\Resources\OfficeHolidayResource($holiday);
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
        return response()->json(['message' => 'Holiday deleted successfully']);
    }
    // List all office holidays
    public function index()
    {
        $holidays = $this->officeHolidayService->getAllHolidays();
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
        $data['user_id'] = $user->user_id;
        $holiday = $this->officeHolidayService->createHoliday($data);
        return new \App\Http\Resources\OfficeHolidayResource($holiday);
    }
    protected $officeHolidayService;

    public function __construct(OfficeHolidayServiceInterface $officeHolidayService)
    {
        $this->officeHolidayService = $officeHolidayService;
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
        ]);

        return (new \App\Http\Resources\OfficeHolidayResource($holiday))->response()->setStatusCode(201);
    }
}
