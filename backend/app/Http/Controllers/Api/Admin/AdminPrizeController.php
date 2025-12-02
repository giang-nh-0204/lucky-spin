<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminPrizeController extends Controller
{
    /**
     * Danh sách giải thưởng (có probability)
     */
    public function index(): JsonResponse
    {
        $prizes = Prize::orderBy('sort_order')
            ->withCount('spinResults')
            ->get()
            ->makeVisible('probability');

        return response()->json([
            'success' => true,
            'data' => $prizes,
        ]);
    }

    /**
     * Tạo giải thưởng mới
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'probability' => 'required|numeric|min:0.0001|max:1',
            'stock' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ]);

        $prize = Prize::create($validated);

        return response()->json([
            'success' => true,
            'data' => $prize->makeVisible('probability'),
            'message' => 'Tạo giải thưởng thành công',
        ], 201);
    }

    /**
     * Chi tiết giải thưởng
     */
    public function show(Prize $prize): JsonResponse
    {
        $prize->loadCount('spinResults');

        return response()->json([
            'success' => true,
            'data' => $prize->makeVisible('probability'),
        ]);
    }

    /**
     * Cập nhật giải thưởng
     */
    public function update(Request $request, Prize $prize): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|integer|min:0',
            'image' => 'nullable|string|max:255',
            'color' => 'sometimes|string|max:20',
            'probability' => 'sometimes|numeric|min:0.0001|max:1',
            'is_active' => 'sometimes|boolean',
            'stock' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ]);

        $prize->update($validated);

        return response()->json([
            'success' => true,
            'data' => $prize->makeVisible('probability'),
            'message' => 'Cập nhật thành công',
        ]);
    }

    /**
     * Xóa giải thưởng
     */
    public function destroy(Prize $prize): JsonResponse
    {
        // Không xóa nếu đã có kết quả
        if ($prize->spinResults()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa giải đã có người trúng',
            ], 400);
        }

        $prize->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa giải thưởng',
        ]);
    }

    /**
     * Sắp xếp lại thứ tự
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:prizes,id',
            'orders.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['orders'] as $item) {
            Prize::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã sắp xếp lại',
        ]);
    }

    /**
     * Tự động phân phối xác suất theo giá vàng (giá cao = xác suất thấp)
     */
    public function autoDistributeProbability(): JsonResponse
    {
        // Lấy các giải đang active và còn stock
        $prizes = Prize::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->orderBy('price', 'desc')
            ->get();

        if ($prizes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có giải thưởng nào đủ điều kiện',
            ], 400);
        }

        // Tính tổng nghịch đảo giá (1/price) để làm trọng số
        $totalInversePrice = $prizes->sum(fn($p) => 1 / max($p->price, 1));

        // Phân phối xác suất: giải giá thấp = xác suất cao
        foreach ($prizes as $prize) {
            $inversePrice = 1 / max($prize->price, 1);
            $probability = round($inversePrice / $totalInversePrice, 4);

            // Đảm bảo xác suất tối thiểu 0.0001
            $probability = max($probability, 0.0001);

            $prize->update(['probability' => $probability]);
        }

        // Normalize lại để tổng = 1
        $prizes->fresh();
        $total = Prize::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->sum('probability');

        if ($total > 0 && $total != 1) {
            foreach ($prizes as $prize) {
                $normalized = round($prize->probability / $total, 4);
                $prize->update(['probability' => max($normalized, 0.0001)]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã phân phối xác suất theo giá vàng',
        ]);
    }
}
