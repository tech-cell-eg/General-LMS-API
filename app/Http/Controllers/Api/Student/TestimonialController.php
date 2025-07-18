<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\TestimonialResource;
use App\Models\Testimonial;
use App\Traits\ApiResponse;

class TestimonialController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $testimonials = Testimonial::with('user')->get();

        return $this->success(TestimonialResource::collection($testimonials), 'Testimonials retrieved successfully');
    }
}
