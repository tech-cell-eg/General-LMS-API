<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\CategoryCollection;
use App\Http\Resources\Api\Student\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;

class PopularCategoriesController extends Controller
{
    use ApiResponse;

    public function __invoke()
    {
        $categories = Category::withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->limit(5)
            ->get();

        return $this->success(
            new CategoryResource($categories),
            'Popular categories retrieved successfully'
        );
    }
}