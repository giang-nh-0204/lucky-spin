<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use Illuminate\Http\JsonResponse;

class PrizeController extends Controller
{
    /**
     * Lấy danh sách giải thưởng (public)
     * GET /api/prizes
     */
    public function index(): JsonResponse
    {
        $prizes = Prize::available()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'price', 'image', 'color']);

        return response()->json([
            'success' => true,
            'data' => $prizes,
        ]);
    }
}
