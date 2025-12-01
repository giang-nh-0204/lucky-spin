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
      <div class="mb-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Tổng lượt quay</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_spins }}</p>
            </div>
            <span class="text-4xl">🎰</span>
          </div>
          <p class="mt-2 text-sm text-green-600">+{{ stats.spins_today }} hôm nay</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Mã Code</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_codes }}</p>
            </div>
            <span class="text-4xl">🎟️</span>
          </div>
          <p class="mt-2 text-sm text-blue-600">{{ stats.active_codes }} đang hoạt động</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Sessions</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_sessions }}</p>
            </div>
            <span class="text-4xl">👥</span>
          </div>
          <p class="mt-2 text-sm text-purple-600">{{ stats.active_sessions }} đang active</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Giải thưởng</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_prizes }}</p>
            </div>
            <span class="text-4xl">🎁</span>
          </div>
          <p class="mt-2 text-sm text-yellow-600">{{ stats.active_prizes }} đang active</p>
        </div>
      </div>

      <!-- Top prizes -->
      <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-6 shadow">
          <h3 class="mb-4 text-lg font-semibold text-gray-800">Top giải trúng nhiều</h3>
          <div class="space-y-3">
            <div
              v-for="(prize, index) in stats.top_prizes"
              :key="prize.id"
              class="flex items-center justify-between rounded-lg bg-gray-50 p-3"
            >
              <div class="flex items-center gap-3">
                <span
                  :class="[
                    'flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold',
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
                  <p class="font-medium text-gray-800">{{ prize.name }}</p>
                  <p class="text-sm text-gray-500">{{ prize.price }} Gold</p>
                </div>
              </div>
              <span class="font-bold text-purple-600">{{ prize.spin_results_count }} lần</span>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow">
          <h3 class="mb-4 text-lg font-semibold text-gray-800">Lượt quay 7 ngày qua</h3>
          <div class="space-y-2">
            <div
              v-for="day in stats.daily_spins"
              :key="day.date"
              class="flex items-center gap-4"
            >
              <span class="w-24 text-sm text-gray-500">{{ day.date }}</span>
              <div class="flex-1">
                <div
                  class="h-6 rounded bg-purple-500"
                  :style="{
                    width: `${(day.count / Math.max(...stats.daily_spins.map((d) => d.count))) * 100}%`,
                  }"
                ></div>
              </div>
              <span class="w-12 text-right font-medium">{{ day.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
