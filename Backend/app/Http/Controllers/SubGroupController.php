<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubGroupRequest;
use App\Http\Requests\UpdateSubGroupRequest;
use App\Http\Resources\SubGroupResource;
use App\Services\SubGroupServiceInterface;
use Illuminate\Http\Request;

class SubGroupController extends Controller
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
                return response()->notFound(__('sub_group.sub_group_not_found'));
            }

            return SubGroupResource::collection($subGroups);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
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
                return response()->notFound(__('sub_group.sub_group_not_found'));
            }

            return new SubGroupResource($subGroup);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
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

            if ($subGroup instanceof JsonResponse && $subGroup->getStatusCode() == 422) {
                return response()->unprocessableEntity($subGroup->getData()->message);
            } elseif (! $subGroup) {
                return response()->notFound(__('sub_group.group_not_found'));
            }

            return new SubGroupResource($subGroup);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
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

            if ($subGroup instanceof JsonResponse && $subGroup->getStatusCode() == 422) {
                return response()->unprocessableEntity($subGroup->getData()->message);
            } elseif (! $subGroup) {
                return response()->notFound(__('sub_group.group_not_found'));
            }

            return new SubGroupResource($subGroup);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
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
                return response()->notFound(__('sub_group.sub_group_not_found'));
            }

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
        }
    }
}
