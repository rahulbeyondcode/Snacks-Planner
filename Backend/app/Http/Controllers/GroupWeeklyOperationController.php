<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignWeeklyOperationRequest;
use App\Http\Requests\UpdateWeeklyOperationStatusRequest;
use App\Http\Resources\GroupWeeklyOperationResource;
use App\Services\GroupWeeklyOperationService;
use Illuminate\Http\Request;

class GroupWeeklyOperationController extends BaseController
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
        return $this->createdResponse(new GroupWeeklyOperationResource($assignment->load(['group', 'employee', 'assignedBy', 'details'])));
    }

    // Update status (operation or operations_manager)
    public function updateStatus(UpdateWeeklyOperationStatusRequest $request, $id)
    {
        $detail = $this->service->updateDetailStatus($request->input('detail_id'), $request->input('status'));
        return $this->updatedResponse($detail, 'Status updated');
    }

    // List assignments (operations_manager or operation)
    public function index(Request $request)
    {
        $assignments = $this->service->listAssignments($request->all());
        return $this->resourceCollectionResponse(GroupWeeklyOperationResource::collection($assignments));
    }

    // View assignment details
    public function show($id)
    {
        $assignment = $this->service->getAssignment($id);
        if (!$assignment) {
            return $this->notFoundResponse('Not found');
        }
        return new GroupWeeklyOperationResource($assignment->load(['group', 'employee', 'assignedBy', 'details']));
    }
}
