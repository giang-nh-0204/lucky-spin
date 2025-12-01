<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpinService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SpinController extends Controller
{
    public function __construct(
        private SpinService $spinService
    ) {}

    /**
     * Đổi mã code lấy lượt quay
     * POST /api/redeem-code
     */
    public function redeemCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $result = $this->spinService->redeemCode(
            $request->input('code'),
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Lấy thông tin session hiện tại
     * GET /api/session
     */
    public function getSession(Request $request): JsonResponse
    {
        $session = $request->get('spin_session');

        return response()->json([
            'success' => true,
            'data' => [
                'spin_balance' => $session->spin_balance,
                'total_spins' => $session->total_spins,
                'expires_at' => $session->expires_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Bắt đầu quay
     * POST /api/spin/start
     */
    public function startSpin(Request $request): JsonResponse
    {
        $session = $request->get('spin_session');
        $currentRotation = floatval($request->input('current_rotation', 0));

        $result = $this->spinService->startSpin($session, $currentRotation);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Claim kết quả
     * POST /api/spin/claim/{spinToken}
     */
    public function claimResult(Request $request, string $spinToken): JsonResponse
    {
        $session = $request->get('spin_session');

        $result = $this->spinService->claimResult($spinToken, $session);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Lịch sử quay
     * GET /api/spin/history
     */
    public function history(Request $request): JsonResponse
    {
        $session = $request->get('spin_session');

        $history = $this->spinService->getHistory($session);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}
