<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignWeeklyOperationRequest;
use App\Http\Requests\UpdateWeeklyOperationStatusRequest;
use App\Http\Resources\GroupWeeklyOperationResource;
use App\Services\GroupWeeklyOperationService;
use Illuminate\Http\Request;

class GroupWeeklyOperationController extends Controller
{
    protected $service;

    public function __construct(GroupWeeklyOperationService $service)
    {
        $this->service = $service;
    }

    // Assign weekly operation (operations_manager only)
    public function assign(AssignWeeklyOperationRequest $request)
    {
        $assignment = $this->service->assign($request->validated());
        return new GroupWeeklyOperationResource($assignment->load(['group', 'employee', 'assignedBy', 'details']));
    }

    // Update status (operations_staff or operations_manager)
    public function updateStatus(UpdateWeeklyOperationStatusRequest $request, $id)
    {
        $detail = $this->service->updateDetailStatus($request->input('detail_id'), $request->input('status'));
        return response()->json(['message' => 'Status updated', 'detail' => $detail]);
    }

    // List assignments (operations_manager or operations_staff)
    public function index(Request $request)
    {
        $assignments = $this->service->listAssignments($request->all());
        return GroupWeeklyOperationResource::collection($assignments);
    }

    // View assignment details
    public function show($id)
    {
        $assignment = $this->service->getAssignment($id);
        if (!$assignment) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return new GroupWeeklyOperationResource($assignment->load(['group', 'employee', 'assignedBy', 'details']));
    }
}
