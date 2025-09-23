<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreNoSnacksDayRequest;
use App\Http\Requests\UpdateNoSnacksDayRequest;
use App\Services\OfficeHolidayServiceInterface;
use App\Http\Resources\OfficeHolidayResource;
use App\Models\OfficeHoliday;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoSnacksDayController extends BaseController
{
    protected $officeHolidayService;

    public function __construct(OfficeHolidayServiceInterface $officeHolidayService)
    {
        $this->officeHolidayService = $officeHolidayService;
    }

    /**
     * Helper method to get active no snacks days for user's group
     */
    private function getActiveNoSnacksDaysList($groupId)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $noSnacksDays = $this->officeHolidayService->getNoSnacksDaysForGroup(
            $groupId,
            $currentYear,
            $currentMonth
        );

        return OfficeHolidayResource::collection($noSnacksDays);
    }

    /**
     * List no snacks days for the snack manager's group
     */
    public function index(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            if ($user->role->name !== 'snack_manager') {
                return $this->forbiddenResponse('Access denied. Only snack managers can view no snacks days.');
            }

            $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return $this->errorResponse('User is not a snack manager in any group', [], 400);
            }

            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);

            $noSnacksDays = $this->officeHolidayService->getNoSnacksDaysForGroup(
                $groupMember->group_id,
                $year,
                $month
            );

            return $this->resourceCollectionResponse(OfficeHolidayResource::collection($noSnacksDays));
        }, null, 'Failed to retrieve no snacks days');
    }

    /**
     * Store a new no snacks day
     */
    public function store(StoreNoSnacksDayRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            if ($user->role->name !== 'snack_manager') {
                return $this->forbiddenResponse('Access denied. Only snack managers can create no snacks days.');
            }

            $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return $this->errorResponse('User is not a snack manager in any group', [], 400);
            }

            $data = $request->validated();
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }

            $data['user_id'] = $user->user_id;
            $data['type'] = OfficeHoliday::TYPE_NO_SNACKS_DAY;
            $data['group_id'] = $groupMember->group_id;

            $noSnacksDay = $this->officeHolidayService->createHoliday($data);

            return $this->createdResponse(new OfficeHolidayResource($noSnacksDay), 'No snacks day created successfully');
        }, null, 'Failed to create no snacks day');
    }

    /**
     * Update a no snacks day
     */
    public function update(UpdateNoSnacksDayRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            if ($user->role->name !== 'snack_manager') {
                return $this->forbiddenResponse('Access denied. Only snack managers can update no snacks days.');
            }

            $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return $this->errorResponse('User is not a snack manager in any group', [], 400);
            }

            $noSnacksDay = OfficeHoliday::where('holiday_id', $id)
                ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
                ->where('group_id', $groupMember->group_id)
                ->first();

            if (!$noSnacksDay) {
                return $this->notFoundResponse('No snacks day not found or not accessible');
            }

            $data = $request->validated();
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }

            $updated = $this->officeHolidayService->updateHoliday($id, $data);
            if (!$updated) {
                return $this->notFoundResponse('No snacks day not found');
            }

            return $this->updatedResponse(new OfficeHolidayResource($updated), 'No snacks day updated successfully');
        }, null, 'Failed to update no snacks day');
    }

    /**
     * Delete a no snacks day
     */
    public function destroy($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            if ($user->role->name !== 'snack_manager') {
                return $this->forbiddenResponse('Access denied. Only snack managers can delete no snacks days.');
            }

            $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return $this->errorResponse('User is not a snack manager in any group', [], 400);
            }

            $noSnacksDay = OfficeHoliday::where('holiday_id', $id)
                ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
                ->where('group_id', $groupMember->group_id)
                ->first();

            if (!$noSnacksDay) {
                return $this->notFoundResponse('No snacks day not found or not accessible');
            }

            $deleted = $this->officeHolidayService->deleteHoliday($id);
            if (!$deleted) {
                return $this->notFoundResponse('No snacks day not found');
            }

            return $this->deletedResponse('No snacks day deleted successfully');
        }, null, 'Failed to delete no snacks day');
    }
}
