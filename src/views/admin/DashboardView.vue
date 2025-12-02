<script setup lang="ts">
import { ref, onMounted } from 'vue'
import adminApi, { type AdminStats } from '@/services/adminApi'

const stats = ref<AdminStats | null>(null)
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    stats.value = await adminApi.getStats()
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Không thể tải dữ liệu'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <!-- Loading -->
    <div v-if="loading" class="py-20 text-center text-gray-500">Đang tải...</div>

    <!-- Error -->
    <div v-else-if="error" class="rounded-lg bg-red-50 p-4 text-red-600">
      {{ error }}
    </div>

    <!-- Stats -->
    <div v-else-if="stats">
      <!-- Overview cards -->
      <div class="mb-6 grid grid-cols-2 gap-3 md:mb-8 md:gap-6 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-3 shadow md:p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-500 md:text-sm">Tổng lượt quay</p>
              <p class="text-xl font-bold text-gray-900 md:text-3xl">{{ stats.total_spins }}</p>
            </div>
            <span class="text-2xl md:text-4xl">🎰</span>
          </div>
          <p class="mt-1 text-xs text-green-600 md:mt-2 md:text-sm">+{{ stats.spins_today }} hôm nay</p>
        </div>

        <div class="rounded-xl bg-white p-3 shadow md:p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-500 md:text-sm">Mã Code</p>
              <p class="text-xl font-bold text-gray-900 md:text-3xl">{{ stats.total_codes }}</p>
            </div>
            <span class="text-2xl md:text-4xl">🎟️</span>
          </div>
          <p class="mt-1 text-xs text-blue-600 md:mt-2 md:text-sm">{{ stats.active_codes }} hoạt động</p>
        </div>

        <div class="rounded-xl bg-white p-3 shadow md:p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-500 md:text-sm">Sessions</p>
              <p class="text-xl font-bold text-gray-900 md:text-3xl">{{ stats.total_sessions }}</p>
            </div>
            <span class="text-2xl md:text-4xl">👥</span>
          </div>
          <p class="mt-1 text-xs text-purple-600 md:mt-2 md:text-sm">{{ stats.active_sessions }} active</p>
        </div>

        <div class="rounded-xl bg-white p-3 shadow md:p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-gray-500 md:text-sm">Giải thưởng</p>
              <p class="text-xl font-bold text-gray-900 md:text-3xl">{{ stats.total_prizes }}</p>
            </div>
            <span class="text-2xl md:text-4xl">🎁</span>
          </div>
          <p class="mt-1 text-xs text-yellow-600 md:mt-2 md:text-sm">{{ stats.active_prizes }} active</p>
        </div>
      </div>

      <!-- Top prizes -->
      <div class="grid gap-4 md:gap-6 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow md:p-6">
          <h3 class="mb-3 text-base font-semibold text-gray-800 md:mb-4 md:text-lg">Top giải trúng nhiều</h3>
          <div class="space-y-2 md:space-y-3">
            <div
              v-for="(prize, index) in stats.top_prizes"
              :key="prize.id"
              class="flex items-center justify-between rounded-lg bg-gray-50 p-2 md:p-3"
            >
              <div class="flex items-center gap-2 md:gap-3">
                <span
                  :class="[
                    'flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold md:h-8 md:w-8 md:text-sm',
                    index === 0
                      ? 'bg-yellow-400 text-yellow-900'
                      : index === 1
                        ? 'bg-gray-300 text-gray-700'
                        : index === 2
                          ? 'bg-orange-300 text-orange-800'
                          : 'bg-gray-200 text-gray-600',
                  ]"
                >
                  {{ index + 1 }}
                </span>
                <div>
                  <p class="text-sm font-medium text-gray-800 md:text-base">{{ prize.name }}</p>
                  <p class="text-xs text-gray-500 md:text-sm">{{ prize.price }} Gold</p>
                </div>
              </div>
              <span class="text-sm font-bold text-purple-600 md:text-base">{{ prize.spin_results_count }}</span>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-4 shadow md:p-6">
          <h3 class="mb-3 text-base font-semibold text-gray-800 md:mb-4 md:text-lg">Lượt quay 7 ngày qua</h3>
          <div class="space-y-2">
            <div
              v-for="day in stats.daily_spins"
              :key="day.date"
              class="flex items-center gap-2 md:gap-4"
            >
              <span class="w-16 text-xs text-gray-500 md:w-24 md:text-sm">{{ day.date }}</span>
              <div class="flex-1">
                <div
                  class="h-4 rounded bg-purple-500 md:h-6"
                  :style="{
                    width: `${(day.count / Math.max(...stats.daily_spins.map((d) => d.count))) * 100}%`,
                  }"
                ></div>
              </div>
              <span class="w-8 text-right text-sm font-medium md:w-12">{{ day.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
