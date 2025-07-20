<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\StoreResourceRequest;
use App\Http\Requests\Api\Dashboard\UpdateResourceRequest;
use App\Services\ResourceService;
use App\Traits\ApiResponse;

class ResourceController extends Controller
{
    use ApiResponse;

    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    public function index($sectionId)
    {
        $resources = $this->resourceService->getResourcesForSection($sectionId);
        return $this->success($resources, 'Resources retrieved successfully');
    }

    public function store(StoreResourceRequest $request, $sectionId)
    {
        $resource = $this->resourceService->createResource($sectionId, $request->validated());
        return $this->success($resource, 'Resource created successfully', 201);
    }

    public function update(UpdateResourceRequest $request, $resourceId)
    {
        $resource = $this->resourceService->updateResource($resourceId, $request->validated());
        return $this->success($resource, 'Resource updated successfully');
    }
}
