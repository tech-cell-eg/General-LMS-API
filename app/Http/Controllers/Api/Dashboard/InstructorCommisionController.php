<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\InstructorCommissionService;
use App\Traits\ApiResponse;

class InstructorCommisionController extends Controller
{
    use ApiResponse;

    protected $commissionService;

    public function __construct(InstructorCommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    public function index()
    {
        $instructor = auth()->user();
        $stats = $this->commissionService->getCommissionStats($instructor->id);
        $history = $this->commissionService->getCommissionHistory($instructor->id);

        return $this->success([
            'lifetime_stats' => $stats,
            'commission_history' => $history
        ], 'Commission data retrieved successfully');
    }
}