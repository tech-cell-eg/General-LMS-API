<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\CategoryCollection;
use App\Http\Resources\Api\Student\CategoryResource as StudentCategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::withCount('courses')->orderBy('courses_count', 'desc')->get();

        return $this->success(
            StudentCategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    public function show($id)
    {
        $category = Category::withCount('courses')->find($id);

        if (! $category) {
            return $this->error('Category not found', 404);
        }

        return $this->success(
            new StudentCategoryResource($category),
            'Category retrieved successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    |  Please use invokable class for this method...
    |  exist an error in CategoryCollection...
    |--------------------------------------------------------------------------
    */
    public function popular()
    {
        $categories = Category::withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->limit(5)
            ->get();

        return $this->success(
            new CategoryCollection($categories),
            'Popular categories retrieved successfully'
        );
    }
}
