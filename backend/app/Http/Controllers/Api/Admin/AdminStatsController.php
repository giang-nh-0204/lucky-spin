<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use App\Models\SpinCode;
use App\Models\SpinSession;
use App\Models\SpinResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    /**
     * Thống kê tổng quan
     */
    public function index(): JsonResponse
    {
        $stats = [
            // Tổng quan
            'total_codes' => SpinCode::count(),
            'active_codes' => SpinCode::where('is_active', true)->count(),
            'total_sessions' => SpinSession::count(),
            'active_sessions' => SpinSession::where('expires_at', '>', now())->count(),

            // Lượt quay
            'total_spins' => SpinResult::count(),
            'spins_today' => SpinResult::whereDate('created_at', today())->count(),
            'spins_this_week' => SpinResult::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            // Giải thưởng
            'total_prizes' => Prize::count(),
            'active_prizes' => Prize::where('is_active', true)->count(),

            // Top giải trúng nhiều nhất
            'top_prizes' => Prize::withCount('spinResults')
                ->orderByDesc('spin_results_count')
                ->limit(5)
                ->get(['id', 'name', 'price', 'spin_results_count']),

            // Phân bố giải theo ngày (7 ngày gần nhất)
            'daily_spins' => SpinResult::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Danh sách kết quả quay
     */
    public function results(Request $request): JsonResponse
    {
        $query = SpinResult::with(['session:id,ip_address', 'prize:id,name,price,image']);

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Filter by prize
        if ($request->filled('prize_id')) {
            $query->where('prize_id', $request->prize_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $results = $query->orderByDesc('created_at')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Thống kê sử dụng mã code
     */
    public function codeUsage(Request $request): JsonResponse
    {
        $codes = SpinCode::withCount('sessions')
            ->withSum('sessions', 'total_spins')
            ->orderByDesc('used_count')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $codes,
        ]);
    }
}
