<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubGroupRequest;
use App\Http\Requests\UpdateSubGroupRequest;
use App\Http\Resources\SubGroupResource;
use App\Services\SubGroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SubGroupController extends BaseController
{
    protected $subGroupService;

    public function __construct(SubGroupServiceInterface $subGroupService)
    {
        $this->subGroupService = $subGroupService;
    }

    /**
     * List all sub groups with optional filters
     */
    public function index(Request $request)
    {
        try {
            $subGroups = $this->subGroupService->listSubGroups();

            if (! $subGroups) {
                return $this->notFoundResponse(__('sub_group.sub_group_not_found'));
            }

            return $this->resourceCollectionResponse(SubGroupResource::collection($subGroups));
        } catch (\Exception $e) {
            return Response::internalServerError(__('messages.error'));
        }
    }

    /**
     * Show sub group details
     */
    public function show($id)
    {
        try {
            $subGroup = $this->subGroupService->getSubGroup($id);

            if (! $subGroup) {
                return $this->notFoundResponse(__('sub_group.sub_group_not_found'));
            }

            return new SubGroupResource($subGroup);
        } catch (\Exception $e) {
            return Response::internalServerError(__('messages.error'));
        }
    }

    /**
     * Create new sub group
     */
    public function store(StoreSubGroupRequest $request)
    {
        try {
            $validated = $request->validated();

            $subGroup = $this->subGroupService->createSubGroup($validated);
            if (is_object($subGroup) && method_exists($subGroup, 'getStatusCode') && $subGroup->getStatusCode() == 422) {
                return $this->validationErrorResponse($subGroup->getData()->message);
            } elseif (! $subGroup) {
                return $this->notFoundResponse(__('sub_group.group_not_found'));
            }

            return $this->createdResponse(new SubGroupResource($subGroup));
        } catch (\Exception $e) {
            return Response::internalServerError(__('messages.error'));
        }
    }

    /**
     * Update sub group
     */
    public function update(UpdateSubGroupRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $subGroup = $this->subGroupService->updateSubGroup($id, $validated);

            if (is_object($subGroup) && method_exists($subGroup, 'getStatusCode') && $subGroup->getStatusCode() == 422) {
                return $this->validationErrorResponse($subGroup->getData()->message);
            } elseif (! $subGroup) {
                return $this->notFoundResponse(__('sub_group.group_not_found'));
            }

            return $this->updatedResponse(new SubGroupResource($subGroup));
        } catch (\Exception $e) {
            return Response::internalServerError(__('messages.error'));
        }
    }

    /**
     * Delete sub group
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->subGroupService->deleteSubGroup($id);
            if (! $deleted) {
                return $this->notFoundResponse(__('sub_group.sub_group_not_found'));
            }

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return Response::internalServerError(__('messages.error'));
        }
    }
}
