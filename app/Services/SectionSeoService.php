<?php

namespace App\Services;

use App\Models\SectionSeo;

class SectionSeoService
{
    public function getOrCreateSeo(int $sectionId): SectionSeo
    {
        return SectionSeo::firstOrCreate(
            ['section_id' => $sectionId],
            ['description' => '', 'ppt_title' => '']
        );
    }

    public function updateOrCreateSeo(int $sectionId, array $validatedData): SectionSeo
    {
        return SectionSeo::updateOrCreate(
            ['section_id' => $sectionId],
            $validatedData
        );
    }
}
