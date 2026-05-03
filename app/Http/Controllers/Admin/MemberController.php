<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    protected MemberService $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Menampilkan halaman daftar anggota (Web/Vuestic)
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Members/Index', [
            'members' => $this->memberService->getAllMembers()
        ]);
    }

    /**
     * API untuk verifikasi NIB Anggota
     */
    public function verify(int $id): JsonResponse
    {
        $this->memberService->verifyMember($id);

        return response()->json([
            'message' => 'Anggota berhasil diverifikasi. NIB dinyatakan valid.'
        ]);
    }
    
    /**
     * API untuk mendapatkan daftar anggota aktif (untuk dropdown input pesanan)
     */
    public function directory(): JsonResponse
    {
        return response()->json([
            'data' => $this->memberService->getVerifiedMembers()
        ]);
    }
}