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
use App\Models\Role;

class NoSnacksDayController extends Controller
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
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'snack_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers can view no snacks days.',
                    [],
                    403
                );
            }

            // Get user's group
            $groupMember = $user->groupMembers()->where('role_id', Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return apiResponse(
                    false,
                    'User is not a snack manager in any group',
                    [],
                    400
                );
            }

            // Get year and month from request parameters
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);

            $noSnacksDays = $this->officeHolidayService->getNoSnacksDaysForGroup(
                $groupMember->group_id,
                $year,
                $month
            );

            return apiResponse(
                true,
                'No snacks days retrieved successfully',
                OfficeHolidayResource::collection($noSnacksDays),
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve no snacks days: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Store a new no snacks day
     */
    public function store(StoreNoSnacksDayRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'snack_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers can create no snacks days.',
                    [],
                    403
                );
            }

            // Get user's group
            $groupMember = $user->groupMembers()->where('role_id', Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return apiResponse(
                    false,
                    'User is not a snack manager in any group',
                    [],
                    400
                );
            }

            $data = $request->validated();

            // Convert date format
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }

            $data['user_id'] = $user->user_id;
            $data['type'] = OfficeHoliday::TYPE_NO_SNACKS_DAY;
            $data['group_id'] = $groupMember->group_id;

            $noSnacksDay = $this->officeHolidayService->createHoliday($data);

            return apiResponse(
                true,
                'No snacks day created successfully',
                [
                    'no_snacks_day' => new OfficeHolidayResource($noSnacksDay),
                    'active_no_snacks_days' => $this->getActiveNoSnacksDaysList($groupMember->group_id)
                ],
                201
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to create no snacks day: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Update a no snacks day
     */
    public function update(UpdateNoSnacksDayRequest $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'snack_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers can update no snacks days.',
                    [],
                    403
                );
            }

            // Get user's group
            $groupMember = $user->groupMembers()->where('role_id', Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return apiResponse(
                    false,
                    'User is not a snack manager in any group',
                    [],
                    400
                );
            }

            // Check if the no snacks day belongs to the user's group
            $noSnacksDay = OfficeHoliday::where('holiday_id', $id)
                ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
                ->where('group_id', $groupMember->group_id)
                ->first();

            if (!$noSnacksDay) {
                return apiResponse(
                    false,
                    'No snacks day not found or not accessible',
                    [],
                    404
                );
            }

            $data = $request->validated();

            // Convert date format
            if (isset($data['holiday_date'])) {
                $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
            }

            $updated = $this->officeHolidayService->updateHoliday($id, $data);

            if (!$updated) {
                return apiResponse(
                    false,
                    'No snacks day not found',
                    [],
                    404
                );
            }

            return apiResponse(
                true,
                'No snacks day updated successfully',
                [
                    'no_snacks_day' => new OfficeHolidayResource($updated),
                    'active_no_snacks_days' => $this->getActiveNoSnacksDaysList($groupMember->group_id)
                ],
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update no snacks day: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    /**
     * Delete a no snacks day
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'snack_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers can delete no snacks days.',
                    [],
                    403
                );
            }

            // Get user's group
            $groupMember = $user->groupMembers()->where('role_id', Role::SNACK_MANAGER)->first();
            if (!$groupMember) {
                return apiResponse(
                    false,
                    'User is not a snack manager in any group',
                    [],
                    400
                );
            }

            // Check if the no snacks day belongs to the user's group
            $noSnacksDay = OfficeHoliday::where('holiday_id', $id)
                ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
                ->where('group_id', $groupMember->group_id)
                ->first();

            if (!$noSnacksDay) {
                return apiResponse(
                    false,
                    'No snacks day not found or not accessible',
                    [],
                    404
                );
            }

            $deleted = $this->officeHolidayService->deleteHoliday($id);

            if (!$deleted) {
                return apiResponse(
                    false,
                    'No snacks day not found',
                    [],
                    404
                );
            }

            return apiResponse(
                true,
                'No snacks day deleted successfully',
                $this->getActiveNoSnacksDaysList($groupMember->group_id),
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to delete no snacks day: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
