<?php

namespace Database\Seeders;

use App\Models\Prize;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /**
     * Seed giải thưởng từ frontend hiện tại
     */
    public function run(): void
    {
        // Dữ liệu từ frontend LuckyWheel.vue
        // Ảnh lưu tại backend/public/images/
        $prizes = [
            ['name' => 'Cao băng bạo', 'price' => 120, 'image' => '/images/cao-bang-bao.jpg', 'color' => '#FF6B6B', 'probability' => 0.12],
            ['name' => 'Cao bí hựu', 'price' => 200, 'image' => '/images/cao-bi-huu.jpg', 'color' => '#4ECDC4', 'probability' => 0.09],
            ['name' => 'Cao di hồn', 'price' => 100, 'image' => '/images/cao-di-hon.jpg', 'color' => '#45B7D1', 'probability' => 0.12],
            ['name' => 'Cao huyết bạo', 'price' => 750, 'image' => '/images/cao-huyet-bao.jpg', 'color' => '#F7DC6F', 'probability' => 0.02],
            ['name' => 'Cao linh đông', 'price' => 500, 'image' => '/images/cao-linh-dong.jpg', 'color' => '#BB8FCE', 'probability' => 0.03],
            ['name' => 'Cao mãnh kích', 'price' => 750, 'image' => '/images/cao-manh-kich.jpg', 'color' => '#85C1E2', 'probability' => 0.02],
            ['name' => 'Cao ngưng thần', 'price' => 450, 'image' => '/images/cao-ngung-than.jpg', 'color' => '#F8B739', 'probability' => 0.04],
            ['name' => 'Cao cường thân', 'price' => 450, 'image' => '/images/cao-cuong-than.jpg', 'color' => '#52C469', 'probability' => 0.04],
            ['name' => 'Cao phản chấn', 'price' => 300, 'image' => '/images/cao-phan-chan.jpg', 'color' => '#FF8C94', 'probability' => 0.06],
            ['name' => 'Cao phản kích', 'price' => 300, 'image' => '/images/cao-phan-kich.jpg', 'color' => '#A8E6CF', 'probability' => 0.06],
            ['name' => 'Cao phụ thân', 'price' => 150, 'image' => '/images/cao-phu-than.jpg', 'color' => '#FFD3B6', 'probability' => 0.10],
            ['name' => 'Cao tá lực', 'price' => 150, 'image' => '/images/cao-ta-luc.jpg', 'color' => '#FFAAA5', 'probability' => 0.10],
            ['name' => 'Cao thế sát', 'price' => 600, 'image' => '/images/cao-the-sat.jpg', 'color' => '#FF8B94', 'probability' => 0.025],
            ['name' => 'Cao thị huyết', 'price' => 100, 'image' => '/images/cao-thi-huyet.jpg', 'color' => '#A8D8EA', 'probability' => 0.12],
            ['name' => 'Cao thuấn ảnh', 'price' => 400, 'image' => '/images/cao-thuan-anh.jpg', 'color' => '#AA96DA', 'probability' => 0.05],
            ['name' => 'Cao nội lực', 'price' => 600, 'image' => '/images/cao-noi-luc.jpg', 'color' => '#FCBAD3', 'probability' => 0.025],
            ['name' => 'Cao cộng sinh', 'price' => 300, 'image' => '/images/cao-cong-sinh.jpg', 'color' => '#FFFFD2', 'probability' => 0.06],
            ['name' => 'Cao nhục tường', 'price' => 100, 'image' => '/images/cao-nhuc-tuong.jpg', 'color' => '#A0CED9', 'probability' => 0.12],
            ['name' => 'Cao Huyết tế', 'price' => 4500, 'image' => '/images/cao-huyet-te.jpg', 'color' => '#FFC09F', 'probability' => 0.001], // Legendary
        ];

        foreach ($prizes as $index => $prize) {
            Prize::create([
                ...$prize,
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}
