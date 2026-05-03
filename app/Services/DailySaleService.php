<?php

namespace App\Services;

use App\Models\DailySale;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DailySaleService
{
    /**
     * Input omzet harian dengan validasi tanggal
     */
    public function reportSale(int $memberId, array $data): DailySale
    {
        $saleDate = Carbon::parse($data['sale_date']);

        // 1. Validasi: Tidak boleh input tanggal di masa depan
        if ($saleDate->isFuture()) {
            throw new \Exception("Anda tidak bisa mencatat penjualan untuk masa depan.");
        }

        // 2. Validasi: Maksimal input tanggal 3 hari yang lalu (Mencegah manipulasi data lama)
        if ($saleDate->diffInDays(now()) > 3) {
            throw new \Exception("Batas waktu pelaporan maksimal 3 hari setelah transaksi.");
        }

        // 3. Update jika sudah ada, atau buat baru (Upsert)
        return DailySale::updateOrCreate(
            ['member_id' => $memberId, 'sale_date' => $data['sale_date']],
            ['amount' => $data['amount'], 'notes' => $data['notes'] ?? null]
        );
    }

    /**
     * Mengambil rangkuman omzet member untuk grafik
     */
    public function getWeeklySummary(int $memberId): Collection
    {
        return DailySale::where('member_id', $memberId)
            ->where('sale_date', '>=', now()->subDays(7))
            ->orderBy('sale_date', 'ASC')
            ->get();
    }
}