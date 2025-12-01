<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import adminApi, { type SpinResult } from '@/services/adminApi'

const results = ref<SpinResult[]>([])
const loading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

// Selection for bulk actions
const selectedIds = ref<number[]>([])
const selectAll = ref(false)

// Filters
const filters = ref({
  status: '',
  delivery_status: '',
  from: '',
  to: '',
  code: '',
})

// Delivery note modal
const showNoteModal = ref(false)
const noteModalData = ref<{ ids: number[]; status: 'pending' | 'delivered'; note: string }>({
  ids: [],
  status: 'delivered',
  note: '',
})

const loadResults = async () => {
  loading.value = true
  selectedIds.value = []
  selectAll.value = false
  try {
    const res = await adminApi.getResults({
      page: currentPage.value,
      status: filters.value.status || undefined,
      delivery_status: filters.value.delivery_status || undefined,
      from: filters.value.from || undefined,
      to: filters.value.to || undefined,
      code: filters.value.code || undefined,
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

const deliveryStatusMap = {
  pending: { label: 'Chưa gửi', class: 'bg-orange-100 text-orange-700' },
  delivered: { label: 'Đã gửi', class: 'bg-blue-100 text-blue-700' },
}

// Toggle select all
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedIds.value = results.value.map((r) => r.id)
  } else {
    selectedIds.value = []
  }
}

// Check if all are selected
const isAllSelected = computed(() => {
  return results.value.length > 0 && selectedIds.value.length === results.value.length
})

// Update single delivery status
const updateDeliveryStatus = async (id: number, status: 'pending' | 'delivered') => {
  try {
    await adminApi.updateDeliveryStatus(id, { delivery_status: status })
    await loadResults()
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Có lỗi xảy ra')
  }
}

// Open modal for bulk update
const openBulkUpdateModal = (status: 'pending' | 'delivered') => {
  if (selectedIds.value.length === 0) {
    alert('Vui lòng chọn ít nhất một kết quả')
    return
  }
  noteModalData.value = {
    ids: [...selectedIds.value],
    status,
    note: '',
  }
  showNoteModal.value = true
}

// Confirm bulk update
const confirmBulkUpdate = async () => {
  try {
    await adminApi.bulkUpdateDeliveryStatus({
      ids: noteModalData.value.ids,
      delivery_status: noteModalData.value.status,
      delivery_note: noteModalData.value.note || undefined,
    })
    showNoteModal.value = false
    await loadResults()
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Có lỗi xảy ra')
  }
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
        <label class="mb-1 block text-sm text-gray-600">Tìm theo mã</label>
        <input
          v-model="filters.code"
          type="text"
          placeholder="Nhập mã..."
          class="rounded-lg border px-3 py-2 uppercase"
          @keyup.enter="applyFilters"
        />
      </div>
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
        <label class="mb-1 block text-sm text-gray-600">Giao hàng</label>
        <select
          v-model="filters.delivery_status"
          class="rounded-lg border px-3 py-2"
          @change="applyFilters"
        >
          <option value="">Tất cả</option>
          <option value="pending">Chưa gửi</option>
          <option value="delivered">Đã gửi</option>
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
      <div class="flex items-end">
        <button
          class="rounded-lg bg-purple-600 px-4 py-2 text-white hover:bg-purple-700"
          @click="applyFilters"
        >
          Tìm kiếm
        </button>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedIds.length > 0" class="mb-4 flex items-center gap-4 rounded-lg bg-blue-50 p-3">
      <span class="text-sm text-blue-700">Đã chọn {{ selectedIds.length }} kết quả</span>
      <button
        class="rounded bg-blue-600 px-3 py-1 text-sm text-white hover:bg-blue-700"
        @click="openBulkUpdateModal('delivered')"
      >
        Đánh dấu đã gửi
      </button>
      <button
        class="rounded bg-orange-500 px-3 py-1 text-sm text-white hover:bg-orange-600"
        @click="openBulkUpdateModal('pending')"
      >
        Đánh dấu chưa gửi
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-20 text-center text-gray-500">Đang tải...</div>

    <!-- Table -->
    <div v-else class="overflow-hidden rounded-xl bg-white shadow">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="w-10 px-4 py-3">
              <input
                v-model="selectAll"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300"
                @change="toggleSelectAll"
              />
            </th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Thời gian</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Mã</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Giải thưởng</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Giá trị</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Trạng thái</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Giao hàng</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="result in results" :key="result.id" class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <input
                v-model="selectedIds"
                :value="result.id"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300"
              />
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">
              {{ formatDate(result.created_at) }}
            </td>
            <td class="px-4 py-3">
              <span class="rounded bg-purple-100 px-2 py-1 font-mono text-xs font-medium text-purple-700">
                {{ result.session?.code?.code || '-' }}
              </span>
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
            <td class="px-4 py-3 text-center">
              <div class="flex items-center justify-center gap-2">
                <span
                  :class="[
                    'inline-block rounded-full px-2 py-1 text-xs font-medium',
                    deliveryStatusMap[result.delivery_status]?.class || 'bg-gray-100 text-gray-600',
                  ]"
                >
                  {{ deliveryStatusMap[result.delivery_status]?.label || result.delivery_status }}
                </span>
                <button
                  v-if="result.delivery_status === 'pending'"
                  class="rounded bg-blue-500 px-2 py-1 text-xs text-white hover:bg-blue-600"
                  title="Đánh dấu đã gửi"
                  @click="updateDeliveryStatus(result.id, 'delivered')"
                >
                  ✓
                </button>
                <button
                  v-else
                  class="rounded bg-orange-400 px-2 py-1 text-xs text-white hover:bg-orange-500"
                  title="Đánh dấu chưa gửi"
                  @click="updateDeliveryStatus(result.id, 'pending')"
                >
                  ↩
                </button>
              </div>
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

    <!-- Bulk Update Modal -->
    <div
      v-if="showNoteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showNoteModal = false"
    >
      <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">
          {{ noteModalData.status === 'delivered' ? 'Đánh dấu đã gửi' : 'Đánh dấu chưa gửi' }}
        </h3>
        <p class="mb-4 text-sm text-gray-600">
          Cập nhật {{ noteModalData.ids.length }} kết quả
        </p>
        <div class="mb-4">
          <label class="mb-1 block text-sm text-gray-600">Ghi chú (tùy chọn)</label>
          <textarea
            v-model="noteModalData.note"
            rows="3"
            class="w-full rounded-lg border px-3 py-2"
            placeholder="Nhập ghi chú..."
          ></textarea>
        </div>
        <div class="flex justify-end gap-2">
          <button
            class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300"
            @click="showNoteModal = false"
          >
            Hủy
          </button>
          <button
            class="rounded-lg bg-purple-600 px-4 py-2 text-white hover:bg-purple-700"
            @click="confirmBulkUpdate"
          >
            Xác nhận
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
