<script setup lang="ts">
/**
 * Ví dụ tích hợp LuckyWheel với Backend Laravel
 * File này demo cách sử dụng composable useSpin
 */
import { ref } from 'vue'
import { useSpin } from '@/composables/useSpin'
import CodeRedeemModal from '@/components/CodeRedeemModal.vue'

const {
  prizes,
  session,
  isLoading,
  error,
  hasSession,
  spinBalance,
  canSpin,
  redeemCode,
  startSpin,
  claimResult,
  logout,
} = useSpin()

// UI State
const showCodeModal = ref(false)
const isSpinning = ref(false)
const rotation = ref(0)
const currentPrize = ref<{ name: string; price: number; image: string } | null>(null)
const showResult = ref(false)
const currentSpinToken = ref<string | null>(null)

// Xử lý đổi mã code
const handleRedeemCode = async (code: string) => {
  const result = await redeemCode(code)
  if (result.success) {
    showCodeModal.value = false
  }
}

// Xử lý quay
const handleSpin = async () => {
  if (!canSpin.value || isSpinning.value) return

  isSpinning.value = true

  // 1. Gọi API để lấy góc quay (kết quả đã được xác định tại server)
  const result = await startSpin()

  if (!result.success) {
    isSpinning.value = false
    alert(result.message)
    return
  }

  // 2. Lưu spin token để claim sau
  currentSpinToken.value = result.spinToken!

  // 3. Animate wheel theo góc từ server
  rotation.value += result.targetAngle!

  // 4. Sau khi animation xong (8 giây), claim kết quả
  setTimeout(async () => {
    if (currentSpinToken.value) {
      const claimRes = await claimResult(currentSpinToken.value)

      if (claimRes.success) {
        currentPrize.value = claimRes.prize!
        showResult.value = true
      }
    }

    isSpinning.value = false
    currentSpinToken.value = null
  }, 8000)
}

// Đóng modal kết quả
const closeResult = () => {
  showResult.value = false
  currentPrize.value = null
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 to-indigo-900 p-4">
    <!-- Header với thông tin session -->
    <div class="mx-auto mb-6 max-w-md">
      <div
        v-if="hasSession"
        class="flex items-center justify-between rounded-xl bg-white/10 px-4 py-3"
      >
        <div>
          <p class="text-sm text-purple-300">Lượt quay còn lại</p>
          <p class="text-2xl font-bold text-yellow-400">{{ spinBalance }}</p>
        </div>
        <button
          class="rounded-lg bg-red-500/20 px-4 py-2 text-red-300 hover:bg-red-500/30"
          @click="logout"
        >
          Thoát
        </button>
      </div>

      <button
        v-else
        class="w-full rounded-xl bg-gradient-to-r from-yellow-400 to-orange-500 py-4 text-lg font-bold text-purple-900"
        @click="showCodeModal = true"
      >
        NHẬP MÃ CODE ĐỂ BẮT ĐẦU
      </button>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="py-20 text-center text-white">
      Đang tải...
    </div>

    <!-- Wheel (simplified) -->
    <div v-else class="flex flex-col items-center">
      <!-- SVG Wheel ở đây - copy từ LuckyWheel.vue gốc -->
      <div
        class="relative h-80 w-80 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 shadow-2xl"
        :style="{
          transform: `rotate(${rotation}deg)`,
          transition: isSpinning ? 'transform 8s cubic-bezier(0.17, 0.67, 0.02, 1)' : 'none',
        }"
      >
        <!-- Wheel segments -->
        <div class="absolute inset-4 rounded-full bg-white/20">
          <p class="flex h-full items-center justify-center text-white">
            {{ prizes.length }} giải thưởng
          </p>
        </div>
      </div>

      <!-- Spin button -->
      <button
        :disabled="!canSpin || isSpinning"
        class="mt-8 rounded-2xl bg-gradient-to-r from-yellow-400 to-orange-500 px-12 py-4 text-2xl font-bold text-purple-900 shadow-lg transition-all hover:scale-105 disabled:opacity-50 disabled:hover:scale-100"
        @click="handleSpin"
      >
        <span v-if="isSpinning">ĐANG QUAY...</span>
        <span v-else-if="!hasSession">NHẬP MÃ CODE</span>
        <span v-else-if="spinBalance <= 0">HẾT LƯỢT</span>
        <span v-else>QUAY NGAY!</span>
      </button>
    </div>

    <!-- Code Redeem Modal -->
    <CodeRedeemModal
      :show="showCodeModal"
      :loading="isLoading"
      :error="error"
      @submit="handleRedeemCode"
      @close="showCodeModal = false"
    />

    <!-- Result Modal -->
    <Teleport to="body">
      <div
        v-if="showResult && currentPrize"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @click.self="closeResult"
      >
        <div class="mx-4 w-full max-w-md rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 p-8 text-center shadow-2xl">
          <h2 class="mb-4 text-3xl font-bold text-purple-900">
            🎉 CHÚC MỪNG!
          </h2>
          <p class="mb-2 text-xl text-purple-800">Bạn đã trúng:</p>
          <p class="mb-4 text-3xl font-bold text-purple-900">
            {{ currentPrize.name }}
          </p>
          <p class="mb-6 text-2xl font-bold text-yellow-800">
            {{ currentPrize.price }} Gold
          </p>
          <button
            class="rounded-xl bg-purple-900 px-8 py-3 text-lg font-bold text-white hover:bg-purple-800"
            @click="closeResult"
          >
            TIẾP TỤC
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>
