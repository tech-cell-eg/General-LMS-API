<?php

namespace App\Services;

use App\Models\Resource;

class SectionResourceService
{
    public function getResourcesForSection(int $sectionId): array
    {
        return Resource::where('lesson_id', $sectionId)
            ->get(['id', 'type', 'title', 'description', 'url_or_path as upload_file'])
            ->toArray();
    }

    public function createResource(int $sectionId, array $validatedData): Resource
    {
        return Resource::create([
            'lesson_id' => $sectionId,
            'type' => $validatedData['type'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'url_or_path' => $validatedData['upload_file'],
            'thumbnail_url' => $validatedData['upload_thumbnail'] ?? null
        ]);
    }

    public function updateResource(int $resourceId, array $validatedData): Resource
    {
        $resource = Resource::findOrFail($resourceId);
        $resource->update([
            'type' => $validatedData['type'] ?? $resource->type,
            'title' => $validatedData['title'] ?? $resource->title,
            'description' => $validatedData['description'] ?? $resource->description,
            'url_or_path' => $validatedData['upload_file'] ?? $resource->url_or_path,
            'thumbnail_url' => $validatedData['upload_thumbnail'] ?? $resource->thumbnail_url
        ]);
        return $resource;
    }
}
