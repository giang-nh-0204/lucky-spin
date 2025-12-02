<script setup lang="ts">
import { ref, onMounted } from 'vue'
import LuckyWheelReal from '@/components/LuckyWheelReal.vue'
import api, { getSessionToken, clearSessionToken } from '@/services/api'

// Session state
const isRealMode = ref(false)
const spinBalance = ref(0)
const showCodeModal = ref(false)
const codeInput = ref('')
const codeError = ref('')
const codeLoading = ref(false)

// Check existing session
onMounted(async () => {
  const token = getSessionToken()
  if (token) {
    try {
      const session = await api.getSession()
      spinBalance.value = session.spin_balance
      isRealMode.value = true
    } catch {
      clearSessionToken()
    }
  }
})

// Redeem code
const redeemCode = async () => {
  if (!codeInput.value.trim()) return
  codeLoading.value = true
  codeError.value = ''
  try {
    const result = await api.redeemCode(codeInput.value.trim())
    spinBalance.value = result.spin_balance
    isRealMode.value = true
    showCodeModal.value = false
    codeInput.value = ''
  } catch (e) {
    codeError.value = e instanceof Error ? e.message : 'Mã không hợp lệ'
  } finally {
    codeLoading.value = false
  }
}

// Logout
const logout = () => {
  clearSessionToken()
  spinBalance.value = 0
  isRealMode.value = false
}

// Paste from clipboard
const pasteCode = async () => {
  try {
    const text = await navigator.clipboard.readText()
    codeInput.value = text.trim().toUpperCase()
  } catch {
    // Clipboard access denied - ignore
  }
}
</script>

<template>
  <div>
    <!-- Top Bar -->
    <div class="fixed top-0 left-0 right-0 z-40 bg-black/80 backdrop-blur-sm border-b border-white/10">
      <div class="container mx-auto px-4 py-2 flex items-center justify-between">
        <!-- Left: Mode indicator -->
        <div class="flex items-center gap-2 sm:gap-3">
          <span
            :class="[
              'px-2 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold whitespace-nowrap',
              isRealMode
                ? 'bg-green-500 text-white'
                : 'bg-yellow-500 text-gray-900'
            ]"
          >
            {{ isRealMode ? '🎰 QUAY THẬT' : '🎮 QUAY THỬ' }}
          </span>
          <span v-if="isRealMode" class="text-white text-xs sm:text-sm whitespace-nowrap">
            Còn <span class="text-yellow-400 font-bold text-sm sm:text-base">{{ spinBalance }}</span> lượt
          </span>
          <span v-else class="text-purple-300 text-[10px] sm:text-xs hidden sm:inline">
            Kết quả không được lưu
          </span>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
          <button
            v-if="!isRealMode"
            @click="showCodeModal = true"
            class="px-3 sm:px-4 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 font-bold text-xs sm:text-sm rounded-full hover:scale-105 transition-transform whitespace-nowrap"
          >
            NHẬP MÃ
          </button>
          <button
            v-else
            @click="logout"
            class="px-3 py-1.5 bg-red-500/20 text-red-300 text-xs sm:text-sm rounded-full hover:bg-red-500/30 whitespace-nowrap"
          >
            Thoát
          </button>
        </div>
      </div>
    </div>

    <!-- Main content with padding for top bar -->
    <div class="pt-10 sm:pt-12">
      <LuckyWheelReal
        :is-real-mode="isRealMode"
        :spin-balance="spinBalance"
        @balance-update="spinBalance = $event"
      />
    </div>

    <!-- Code Modal -->
    <Teleport to="body">
      <div
        v-if="showCodeModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
        @click.self="showCodeModal = false"
      >
        <div class="w-full max-w-md bg-gradient-to-br from-purple-900 to-indigo-900 rounded-2xl p-6 shadow-2xl">
          <h2 class="text-xl sm:text-2xl font-bold text-white text-center mb-2">
            Nhập Mã Code
          </h2>
          <p class="text-purple-200 text-center text-xs sm:text-sm mb-6">
            Nhập mã để nhận lượt quay thật và lưu kết quả
          </p>

          <form @submit.prevent="redeemCode">
            <div class="relative">
              <input
                v-model="codeInput"
                type="text"
                placeholder="VD: LUCKY2024"
                class="w-full px-4 py-3 pr-20 rounded-xl bg-purple-800/50 border-2 border-purple-500/50 text-white text-center text-lg sm:text-xl font-bold uppercase tracking-widest placeholder-purple-400 focus:border-yellow-400 focus:outline-none"
                :disabled="codeLoading"
                autofocus
              />
              <button
                type="button"
                @click="pasteCode"
                class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white text-xs font-medium rounded-lg transition-colors"
                :disabled="codeLoading"
              >
                📋 Dán
              </button>
            </div>

            <p v-if="codeError" class="mt-3 text-red-400 text-center text-sm">
              {{ codeError }}
            </p>

            <button
              type="submit"
              :disabled="codeLoading || !codeInput.trim()"
              class="mt-6 w-full py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 font-bold text-base sm:text-lg rounded-xl shadow-lg hover:scale-105 transition-transform disabled:opacity-50 disabled:hover:scale-100"
            >
              {{ codeLoading ? 'Đang xử lý...' : 'XÁC NHẬN' }}
            </button>
          </form>

          <button
            type="button"
            class="mt-4 w-full py-2 text-purple-300 hover:text-white text-sm"
            @click="showCodeModal = false"
          >
            Tiếp tục quay thử
          </button>

          <div class="mt-4 pt-4 border-t border-purple-700/50">
            <p class="text-purple-300 text-xs text-center">
              💡 Liên hệ admin để nhận mã code
            </p>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
