<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\UpdateSectionSeoRequest;
use App\Services\SectionSeoService;
use App\Traits\ApiResponse;

class SectionSeoController extends Controller
{
    use ApiResponse;

    protected $seoService;

    public function __construct(SectionSeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function show($sectionId)
    {
        $seo = $this->seoService->getOrCreateSeo($sectionId);
        return $this->success($seo, 'SEO data retrieved successfully');
    }

    public function update(UpdateSectionSeoRequest $request, $sectionId)
    {
        $seo = $this->seoService->updateOrCreateSeo($sectionId, $request->validated());
        return $this->success($seo, 'SEO updated successfully');
    }
}
