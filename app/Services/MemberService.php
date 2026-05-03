<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Database\Eloquent\Collection;

class MemberService
{
    /**
     * Mengambil semua daftar anggota beserta data akun usernya
     */
    public function getAllMembers(): Collection
    {
        return Member::with('user')->latest()->get();
    }

    /**
     * Mengambil hanya anggota yang sudah terverifikasi (untuk dropdown pesanan)
     */
    public function getVerifiedMembers(): Collection
    {
        return Member::where('status', 'verified')->get();
    }

    /**
     * Mengubah status anggota menjadi verified (Validasi NIB)
     */
    public function verifyMember(int $memberId): bool
    {
        $member = Member::findOrFail($memberId);
        return $member->update(['status' => 'verified']);
    }

    /**
     * Membatalkan verifikasi atau menolak anggota
     */
    public function rejectMember(int $memberId): bool
    {
        $member = Member::findOrFail($memberId);
        return $member->update(['status' => 'pending']);
    }
}