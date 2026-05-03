<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Data Dashboard untuk Admin (Semua UMKM)
     */
    public function adminDashboard(): JsonResponse
    {
        return response()->json([
            'summary' => $this->analyticsService->getAdminSummary(),
            'chart' => $this->analyticsService->getRevenueTrend() // Tanpa ID = Global
        ]);
    }

    /**
     * Data Dashboard untuk Member (Internal UMKM)
     */
    public function memberDashboard(Request $request): JsonResponse
    {
        $memberId = $request->user()->member->id;

        return response()->json([
            'performance' => [
                'avg_shipping_hours' => $this->analyticsService->getShippingPerformance($memberId),
            ],
            'chart' => $this->analyticsService->getRevenueTrend($memberId) // Pakai ID = Spesifik
        ]);
    }
}