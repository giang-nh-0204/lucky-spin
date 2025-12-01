<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SpinSession;
use Symfony\Component\HttpFoundation\Response;

class VerifySpinSession
{
    /**
     * Xác thực session token từ header hoặc cookie
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy token từ header hoặc cookie
        $token = $request->header('X-Session-Token')
            ?? $request->cookie('spin_session_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã code để bắt đầu',
                'code' => 'SESSION_REQUIRED',
            ], 401);
        }

        // Tìm session
        $session = SpinSession::where('session_token', $token)->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên không hợp lệ',
                'code' => 'INVALID_SESSION',
            ], 401);
        }

        // Kiểm tra hết hạn
        if (!$session->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đã hết hạn, vui lòng nhập mã mới',
                'code' => 'SESSION_EXPIRED',
            ], 401);
        }

        // Gắn session vào request
        $request->attributes->set('spin_session', $session);

        return $next($request);
    }
}
