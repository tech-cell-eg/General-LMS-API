<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\Testimonial;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\TestimonialResource;

class TestimonialController extends Controller
{
    use ApiResponse;

    public function index(){
        $testimonials = Testimonial::with('user')->get();
        return $this->success(TestimonialResource::collection($testimonials), 'Testimonials retrieved successfully');
    }
}
