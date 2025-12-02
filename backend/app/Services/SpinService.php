<?php

namespace App\Services;

use App\Models\Prize;
use App\Models\SpinCode;
use App\Models\SpinSession;
use App\Models\SpinResult;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\SpinException;

class SpinService
{
    /**
     * Số ngày session có hiệu lực
     */
    const SESSION_DAYS = 7;

    /**
     * Số vòng quay tối thiểu
     */
    const MIN_ROTATIONS = 7;

    /**
     * Đổi mã code lấy lượt quay
     */
    public function redeemCode(string $code, string $ipAddress, string $userAgent): array
    {
        $spinCode = SpinCode::where('code', strtoupper(trim($code)))->first();

        if (!$spinCode) {
            throw new SpinException('Mã không tồn tại', 404);
        }

        if (!$spinCode->isValid()) {
            throw new SpinException($spinCode->getInvalidReason(), 400);
        }

        return DB::transaction(function () use ($spinCode, $ipAddress, $userAgent) {
            // Tăng số lần sử dụng code
            $spinCode->incrementUsage();

            // Tạo session mới
            $session = SpinSession::create([
                'session_token' => Str::random(64),
                'code_id' => $spinCode->id,
                'spin_balance' => $spinCode->spins_amount,
                'total_spins' => 0,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'expires_at' => now()->addDays(self::SESSION_DAYS),
            ]);

            return [
                'session_token' => $session->session_token,
                'spin_balance' => $session->spin_balance,
                'expires_at' => $session->expires_at->toIso8601String(),
            ];
        });
    }

    /**
     * Bắt đầu quay (xác định kết quả tại server)
     * @param SpinSession $session
     * @param float $currentRotation - Góc quay hiện tại của wheel (0-360)
     */
    public function startSpin(SpinSession $session, float $currentRotation = 0): array
    {
        if (!$session->isValid()) {
            throw new SpinException('Phiên đã hết hạn', 401);
        }

        if (!$session->hasSpins()) {
            throw new SpinException('Hết lượt quay', 400);
        }

        // Lấy danh sách prizes available
        $prizes = Prize::available()->orderBy('sort_order')->get();

        if ($prizes->isEmpty()) {
            throw new SpinException('Không có giải thưởng', 500);
        }

        return DB::transaction(function () use ($session, $prizes, $currentRotation) {
            // 1. Chọn giải theo xác suất
            $prize = $this->selectPrizeByProbability($prizes);

            // 2. Tính góc quay (có tính đến vị trí hiện tại)
            $targetAngle = $this->calculateTargetAngle($prize, $prizes, $currentRotation);

            // 3. Tạo spin token
            $spinToken = Str::random(64);

            // 4. Trừ lượt quay
            $session->decrementSpin();

            // 5. Giảm stock nếu có
            $prize->decrementStock();

            // 6. Lưu kết quả
            SpinResult::create([
                'session_id' => $session->id,
                'prize_id' => $prize->id,
                'spin_token' => $spinToken,
                'target_angle' => $targetAngle,
                'status' => 'pending',
            ]);

            // 7. Trả về (KHÔNG có prize info - bảo mật)
            return [
                'spin_token' => $spinToken,
                'target_angle' => $targetAngle,
                'remaining_spins' => $session->fresh()->spin_balance,
            ];
        });
    }

    /**
     * Claim kết quả sau khi animation xong
     */
    public function claimResult(string $spinToken, SpinSession $session): array
    {
        $result = SpinResult::where('spin_token', $spinToken)
            ->where('session_id', $session->id)
            ->first();

        if (!$result) {
            throw new SpinException('Không tìm thấy kết quả', 404);
        }

        if (!$result->canClaim()) {
            throw new SpinException('Kết quả đã được nhận hoặc hết hạn', 400);
        }

        $result->markAsClaimed();

        return [
            'prize' => [
                'id' => $result->prize->id,
                'name' => $result->prize->name,
                'price' => $result->prize->price,
                'image' => $result->prize->image,
                'image_url' => $result->prize->image_url,
                'color' => $result->prize->color,
            ],
        ];
    }

    /**
     * Lấy lịch sử quay của session
     */
    public function getHistory(SpinSession $session, int $limit = 20): array
    {
        $results = $session->spinResults()
            ->with('prize:id,name,price,image')
            ->where('status', 'claimed')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $results->map(fn($r) => [
            'prize' => [
                'name' => $r->prize->name,
                'price' => $r->prize->price,
                'image' => $r->prize->image,
                'image_url' => $r->prize->image_url,
            ],
            'claimed_at' => $r->claimed_at->toIso8601String(),
        ])->toArray();
    }

    /**
     * Chọn giải theo xác suất
     */
    private function selectPrizeByProbability($prizes): Prize
    {
        // Normalize probabilities
        $totalProbability = $prizes->sum('probability');

        $random = mt_rand() / mt_getrandmax() * $totalProbability;
        $cumulative = 0;

        foreach ($prizes as $prize) {
            $cumulative += $prize->probability;
            if ($random <= $cumulative) {
                return $prize;
            }
        }

        // Fallback: trả về prize cuối
        return $prizes->last();
    }

    /**
     * Tính góc quay để kim chỉ đúng vào prize
     * Công thức giống frontend demo mode:
     * - targetAngle = 360 - (index * angle + angle/2 + offset)
     * - deltaAngle = targetAngle - currentRotation
     * - spinRotation = 7 vòng + deltaAngle
     *
     * @param Prize $selectedPrize - Giải được chọn
     * @param Collection $prizes - Danh sách giải
     * @param float $currentRotation - Góc quay hiện tại của wheel (0-360)
     */
    private function calculateTargetAngle(Prize $selectedPrize, $prizes, float $currentRotation = 0): float
    {
        $prizeCount = $prizes->count();
        $segmentAngle = 360 / $prizeCount;

        // Tìm index của prize được chọn (dùng values() để reset keys)
        $prizesArray = $prizes->values();
        $prizeIndex = $prizesArray->search(fn($p) => $p->id === $selectedPrize->id);

        // Thêm offset ngẫu nhiên trong segment (±30%)
        $offset = (mt_rand(-30, 30) / 100) * $segmentAngle;

        // Góc để kim chỉ vào giữa segment (vị trí tuyệt đối)
        // Công thức giống frontend: 360 - (index * angle + angle/2 + offset)
        $absoluteTargetAngle = 360 - ($prizeIndex * $segmentAngle + $segmentAngle / 2 + $offset);

        // Normalize về 0-360
        while ($absoluteTargetAngle < 0) {
            $absoluteTargetAngle += 360;
        }
        while ($absoluteTargetAngle >= 360) {
            $absoluteTargetAngle -= 360;
        }

        // Tính delta: góc cần quay từ vị trí hiện tại đến vị trí đích
        // Giống frontend: deltaAngle = targetAngle - currentRotation
        $deltaAngle = $absoluteTargetAngle - $currentRotation;

        // Nếu delta âm, cộng thêm 360 để quay theo chiều dương
        if ($deltaAngle < 0) {
            $deltaAngle += 360;
        }

        // Tổng góc = nhiều vòng quay + delta
        $totalRotations = self::MIN_ROTATIONS * 360;

        return $totalRotations + $deltaAngle;
    }
}
