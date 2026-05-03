<?php

namespace App\Services;

use App\Models\Order;
use App\Models\DailySale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Mendapatkan Tren Pendapatan 7 Hari Terakhir (Hybrid: Order + Daily Sales)
     */
    public function getRevenueTrend(?int $memberId = null): array
    {
        $days = 7;
        $labels = [];
        $data = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;

            // 1. Hitung dari Order (Pesanan yang lunas/Selesai)
            $orderRevenue = Order::where('status', Order::STATUS_COMPLETED)
                ->whereDate('created_at', $date)
                ->when($memberId, fn($q) => $q->where('member_id', $memberId))
                ->sum('total_amount');

            // 2. Hitung dari Daily Sale (Input Manual Offline)
            $dailyRevenue = DailySale::where('sale_date', $date)
                ->when($memberId, fn($q) => $q->where('member_id', $memberId))
                ->sum('amount');

            $data[] = (float) ($orderRevenue + $dailyRevenue);
        }

        return ['labels' => $labels, 'datasets' => $data];
    }

    /**
     * Menghitung Performa Kecepatan Pengiriman (Governance Tracking)
     * Rata-rata waktu dari status 'Processing' ke 'Shipped'
     */
    public function getShippingPerformance(int $memberId): float
    {
        // Logika: Mencari selisih waktu antara update status
        // Ini membutuhkan data audit log, namun kita bisa estimasi dari updated_at 
        // pada status Shipped dibanding created_at.
        
        $avgHours = Order::where('member_id', $memberId)
            ->where('status', Order::STATUS_COMPLETED)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time'))
            ->first()->avg_time;

        return round((float) $avgHours, 1);
    }

    /**
     * Ringkasan Statistik untuk Admin (Dashboard Utama)
     */
    public function getAdminSummary(): array
    {
        return [
            'total_active_members' => DB::table('members')->where('status', 'verified')->count(),
            'total_pending_orders' => Order::where('status', Order::STATUS_PENDING_MEMBER)->count(),
            'total_all_revenue' => (float) Order::where('status', Order::STATUS_COMPLETED)->sum('total_amount'),
            'top_members' => DB::table('orders')
                ->join('members', 'orders.member_id', '=', 'members.id')
                ->select('members.business_name', DB::raw('SUM(total_amount) as total'))
                ->where('orders.status', Order::STATUS_COMPLETED)
                ->groupBy('members.business_name')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
        ];
    }
}