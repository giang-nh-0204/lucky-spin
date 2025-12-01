<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpinCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AdminCodeController extends Controller
{
    /**
     * Danh sách mã code
     */
    public function index(Request $request): JsonResponse
    {
        $query = SpinCode::query()->withCount('sessions');

        // Filter
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $codes = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $codes,
        ]);
    }

    /**
     * Tạo mã code mới
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:spin_codes,code',
            'spins_amount' => 'required|integer|min:1|max:1000',
            'max_uses' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Auto generate code nếu không có
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateUniqueCode();
        } else {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $code = SpinCode::create($validated);

        return response()->json([
            'success' => true,
            'data' => $code,
            'message' => 'Tạo mã thành công',
        ], 201);
    }

    /**
     * Chi tiết mã code
     */
    public function show(SpinCode $code): JsonResponse
    {
        $code->load(['sessions' => function ($q) {
            $q->withCount('spinResults')->latest()->limit(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => $code,
        ]);
    }

    /**
     * Cập nhật mã code
     */
    public function update(Request $request, SpinCode $code): JsonResponse
    {
        $validated = $request->validate([
            'spins_amount' => 'sometimes|integer|min:1|max:1000',
            'max_uses' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
        ]);

        $code->update($validated);

        return response()->json([
            'success' => true,
            'data' => $code,
            'message' => 'Cập nhật thành công',
        ]);
    }

    /**
     * Xóa mã code
     */
    public function destroy(SpinCode $code): JsonResponse
    {
        $code->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mã',
        ]);
    }

    /**
     * Tạo nhiều mã cùng lúc
     */
    public function generateBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'spins_amount' => 'required|integer|min:1|max:1000',
            'prefix' => 'nullable|string|max:10',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
            'note' => 'nullable|string|max:255',
        ]);

        $codes = [];
        $prefix = strtoupper($validated['prefix'] ?? '');

        for ($i = 0; $i < $validated['count']; $i++) {
            $code = SpinCode::create([
                'code' => $prefix . $this->generateUniqueCode(8),
                'spins_amount' => $validated['spins_amount'],
                'max_uses' => $validated['max_uses'] ?? 1,
                'expires_at' => $validated['expires_at'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);
            $codes[] = $code->code;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'codes' => $codes,
                'count' => count($codes),
            ],
            'message' => "Đã tạo {$validated['count']} mã",
        ], 201);
    }

    /**
     * Tạo mã unique
     */
    private function generateUniqueCode(int $length = 10): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (SpinCode::where('code', $code)->exists());

        return $code;
    }
}
