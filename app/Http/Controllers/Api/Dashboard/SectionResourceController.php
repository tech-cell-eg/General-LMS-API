<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\StoreSectionResourceRequest;
use App\Http\Requests\Api\Dashboard\UpdateSectionResourceRequest;
use App\Services\SectionResourceService;
use App\Traits\ApiResponse;

class SectionResourceController extends Controller
{
    use ApiResponse;

    protected $resourceService;

    public function __construct(SectionResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    public function index($sectionId)
    {
        $resources = $this->resourceService->getResourcesForSection($sectionId);
        return $this->success($resources, 'Resources retrieved successfully');
    }

    public function store(StoreSectionResourceRequest $request, $sectionId)
    {
        $resource = $this->resourceService->createResource($sectionId, $request->validated());
        return $this->success($resource, 'Resource created successfully', 201);
    }

    public function update(UpdateSectionResourceRequest $request, $resourceId)
    {
        $resource = $this->resourceService->updateResource($resourceId, $request->validated());
        return $this->success($resource, 'Resource updated successfully');
    }
}
