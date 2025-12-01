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
        $query = SpinResult::with([
            'session:id,ip_address,code_id',
            'session.code:id,code',
            'prize:id,name,price,image'
        ]);

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

        // Search by code
        if ($request->filled('code')) {
            $code = strtoupper(trim($request->code));
            $query->whereHas('session.code', function ($q) use ($code) {
                $q->where('code', 'LIKE', "%{$code}%");
            });
        }

        // Filter by delivery status
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        $results = $query->orderByDesc('created_at')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Cập nhật trạng thái giao hàng
     */
    public function updateDeliveryStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'delivery_status' => 'required|in:pending,delivered',
            'delivery_note' => 'nullable|string|max:500',
        ]);

        $result = SpinResult::findOrFail($id);

        if ($request->delivery_status === 'delivered') {
            $result->markAsDelivered($request->delivery_note);
        } else {
            $result->markAsUndelivered($request->delivery_note);
        }

        return response()->json([
            'success' => true,
            'data' => $result->fresh(['session:id,ip_address,code_id', 'session.code:id,code', 'prize:id,name,price,image']),
        ]);
    }

    /**
     * Cập nhật hàng loạt trạng thái giao hàng
     */
    public function bulkUpdateDeliveryStatus(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:spin_results,id',
            'delivery_status' => 'required|in:pending,delivered',
            'delivery_note' => 'nullable|string|max:500',
        ]);

        $count = SpinResult::whereIn('id', $request->ids)->update([
            'delivery_status' => $request->delivery_status,
            'delivery_note' => $request->delivery_note,
            'delivered_at' => $request->delivery_status === 'delivered' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => ['updated_count' => $count],
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
