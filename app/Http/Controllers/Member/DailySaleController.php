<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\DailySaleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DailySaleController extends Controller
{
    protected DailySaleService $dailySaleService;

    public function __construct(DailySaleService $dailySaleService)
    {
        $this->dailySaleService = $dailySaleService;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $memberId = $request->user()->member->id;
            $sale = $this->dailySaleService->reportSale($memberId, $validated);

            return response()->json([
                'message' => 'Laporan omzet berhasil disimpan.',
                'data' => $sale
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $memberId = $request->user()->member->id;
        $summary = $this->dailySaleService->getWeeklySummary($memberId);

        return response()->json(['data' => $summary]);
    }
}