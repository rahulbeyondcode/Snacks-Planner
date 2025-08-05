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
        $user = Auth::user();
        if (!$user || $user->role->name !== 'snack_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Get user's group
        $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
        if (!$groupMember) {
            return response()->json(['message' => 'User is not a snack manager in any group'], 400);
        }

        // Get year and month from request parameters
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $noSnacksDays = $this->officeHolidayService->getNoSnacksDaysForGroup(
            $groupMember->group_id,
            $year,
            $month
        );

        return OfficeHolidayResource::collection($noSnacksDays);
    }

    /**
     * Store a new no snacks day
     */
    public function store(StoreNoSnacksDayRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'snack_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Get user's group
        $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
        if (!$groupMember) {
            return response()->json(['message' => 'User is not a snack manager in any group'], 400);
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

        return response()->json([
            'message' => 'No snacks day created successfully',
            'data' => new OfficeHolidayResource($noSnacksDay),
            'active_no_snacks_days' => $this->getActiveNoSnacksDaysList($groupMember->group_id)
        ], 201);
    }

    /**
     * Update a no snacks day
     */
    public function update(UpdateNoSnacksDayRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'snack_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Get user's group
        $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
        if (!$groupMember) {
            return response()->json(['message' => 'User is not a snack manager in any group'], 400);
        }

        // Check if the no snacks day belongs to the user's group
        $noSnacksDay = OfficeHoliday::where('holiday_id', $id)
            ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
            ->where('group_id', $groupMember->group_id)
            ->first();

        if (!$noSnacksDay) {
            return response()->json(['message' => 'No snacks day not found or not accessible'], 404);
        }

        $data = $request->validated();

        // Convert date format
        if (isset($data['holiday_date'])) {
            $data['holiday_date'] = Carbon::createFromFormat('d-M-Y', $data['holiday_date'])->format('Y-m-d');
        }

        $updated = $this->officeHolidayService->updateHoliday($id, $data);

        if (!$updated) {
            return response()->json(['message' => 'No snacks day not found'], 404);
        }

        return response()->json([
            'message' => 'No snacks day updated successfully',
            'data' => new OfficeHolidayResource($updated),
            'active_no_snacks_days' => $this->getActiveNoSnacksDaysList($groupMember->group_id)
        ]);
    }

    /**
     * Delete a no snacks day
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'snack_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Get user's group
        $groupMember = $user->groupMembers()->where('role_id', \App\Models\Role::SNACK_MANAGER)->first();
        if (!$groupMember) {
            return response()->json(['message' => 'User is not a snack manager in any group'], 400);
        }

        // Check if the no snacks day belongs to the user's group
        $noSnacksDay = OfficeHoliday::where('holiday_id', $id)
            ->where('type', OfficeHoliday::TYPE_NO_SNACKS_DAY)
            ->where('group_id', $groupMember->group_id)
            ->first();

        if (!$noSnacksDay) {
            return response()->json(['message' => 'No snacks day not found or not accessible'], 404);
        }

        $deleted = $this->officeHolidayService->deleteHoliday($id);

        if (!$deleted) {
            return response()->json(['message' => 'No snacks day not found'], 404);
        }

        return response()->json([
            'message' => 'No snacks day deleted successfully',
            'active_no_snacks_days' => $this->getActiveNoSnacksDaysList($groupMember->group_id)
        ]);
    }
}
