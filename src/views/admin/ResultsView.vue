<script setup lang="ts">
import { ref, onMounted } from 'vue'
import adminApi, { type SpinResult } from '@/services/adminApi'

const results = ref<SpinResult[]>([])
const loading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

// Filters
const filters = ref({
  status: '',
  from: '',
  to: '',
})

const loadResults = async () => {
  loading.value = true
  try {
    const res = await adminApi.getResults({
      page: currentPage.value,
      status: filters.value.status || undefined,
      from: filters.value.from || undefined,
      to: filters.value.to || undefined,
    })
    results.value = res.data
    if (res.meta) {
      totalPages.value = res.meta.last_page || 1
    }
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Lỗi'
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  loadResults()
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('vi-VN')
}

const statusMap = {
  pending: { label: 'Chờ nhận', class: 'bg-yellow-100 text-yellow-700' },
  claimed: { label: 'Đã nhận', class: 'bg-green-100 text-green-700' },
  expired: { label: 'Hết hạn', class: 'bg-red-100 text-red-700' },
}

onMounted(loadResults)
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold text-gray-800">Kết quả quay</h2>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4 rounded-xl bg-white p-4 shadow">
      <div>
        <label class="mb-1 block text-sm text-gray-600">Trạng thái</label>
        <select
          v-model="filters.status"
          class="rounded-lg border px-3 py-2"
          @change="applyFilters"
        >
          <option value="">Tất cả</option>
          <option value="pending">Chờ nhận</option>
          <option value="claimed">Đã nhận</option>
          <option value="expired">Hết hạn</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm text-gray-600">Từ ngày</label>
        <input
          v-model="filters.from"
          type="date"
          class="rounded-lg border px-3 py-2"
          @change="applyFilters"
        />
      </div>
      <div>
        <label class="mb-1 block text-sm text-gray-600">Đến ngày</label>
        <input
          v-model="filters.to"
          type="date"
          class="rounded-lg border px-3 py-2"
          @change="applyFilters"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-20 text-center text-gray-500">Đang tải...</div>

    <!-- Table -->
    <div v-else class="overflow-hidden rounded-xl bg-white shadow">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Thời gian</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Giải thưởng</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Giá trị</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">IP</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Trạng thái</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="result in results" :key="result.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-600">
              {{ formatDate(result.created_at) }}
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <img
                  v-if="result.prize.image_url"
                  :src="result.prize.image_url"
                  :alt="result.prize.name"
                  class="h-8 w-8 object-contain"
                />
                <span class="font-medium text-gray-800">{{ result.prize.name }}</span>
              </div>
            </td>
            <td class="px-4 py-3 text-center font-medium text-yellow-600">
              {{ result.prize.price }} Gold
            </td>
            <td class="px-4 py-3 text-center text-sm text-gray-500">
              {{ result.session?.ip_address || '-' }}
            </td>
            <td class="px-4 py-3 text-center">
              <span
                :class="[
                  'inline-block rounded-full px-2 py-1 text-xs font-medium',
                  statusMap[result.status]?.class || 'bg-gray-100 text-gray-600',
                ]"
              >
                {{ statusMap[result.status]?.label || result.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="results.length === 0" class="py-12 text-center text-gray-500">
        Không có kết quả nào
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 border-t p-4">
        <button
          :disabled="currentPage <= 1"
          class="rounded bg-gray-100 px-3 py-1 hover:bg-gray-200 disabled:opacity-50"
          @click="currentPage--; loadResults()"
        >
          ←
        </button>
        <span class="text-sm text-gray-600">
          Trang {{ currentPage }} / {{ totalPages }}
        </span>
        <button
          :disabled="currentPage >= totalPages"
          class="rounded bg-gray-100 px-3 py-1 hover:bg-gray-200 disabled:opacity-50"
          @click="currentPage++; loadResults()"
        >
          →
        </button>
      </div>
    </div>
  </div>
</template>
