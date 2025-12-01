<script setup lang="ts">
import { ref, onMounted } from 'vue'
import adminApi, { type SpinCode } from '@/services/adminApi'

const codes = ref<SpinCode[]>([])
const loading = ref(true)
const error = ref('')

// Modal state
const showModal = ref(false)
const modalMode = ref<'single' | 'batch'>('single')
const formLoading = ref(false)

// Form data
const form = ref({
  code: '',
  spins_amount: 5,
  max_uses: 1,
  note: '',
  expires_at: '',
  // Batch
  count: 10,
  prefix: '',
})

const loadCodes = async () => {
  loading.value = true
  try {
    const res = await adminApi.getCodes()
    codes.value = res.data
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Lỗi'
  } finally {
    loading.value = false
  }
}

const openModal = (mode: 'single' | 'batch') => {
  modalMode.value = mode
  form.value = {
    code: '',
    spins_amount: 5,
    max_uses: 1,
    note: '',
    expires_at: '',
    count: 10,
    prefix: '',
  }
  showModal.value = true
}

const handleSubmit = async () => {
  formLoading.value = true
  try {
    if (modalMode.value === 'single') {
      await adminApi.createCode({
        code: form.value.code || undefined,
        spins_amount: form.value.spins_amount,
        max_uses: form.value.max_uses || undefined,
        note: form.value.note || undefined,
        expires_at: form.value.expires_at || undefined,
      })
    } else {
      await adminApi.generateBatchCodes({
        count: form.value.count,
        spins_amount: form.value.spins_amount,
        prefix: form.value.prefix || undefined,
        max_uses: form.value.max_uses || undefined,
        expires_at: form.value.expires_at || undefined,
      })
    }
    showModal.value = false
    await loadCodes()
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  } finally {
    formLoading.value = false
  }
}

const toggleActive = async (code: SpinCode) => {
  try {
    await adminApi.updateCode(code.id, { is_active: !code.is_active })
    code.is_active = !code.is_active
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  }
}

const deleteCode = async (code: SpinCode) => {
  if (!confirm(`Xóa mã ${code.code}?`)) return
  try {
    await adminApi.deleteCode(code.id)
    codes.value = codes.value.filter((c) => c.id !== code.id)
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  }
}

const copyCode = (code: string) => {
  navigator.clipboard.writeText(code)
  alert('Đã copy: ' + code)
}

onMounted(loadCodes)
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-xl font-semibold text-gray-800">Quản lý Mã Code</h2>
      <div class="flex gap-2">
        <button
          class="rounded-lg bg-yellow-500 px-4 py-2 font-medium text-gray-900 hover:bg-yellow-400"
          @click="openModal('single')"
        >
          + Tạo mã
        </button>
        <button
          class="rounded-lg bg-purple-600 px-4 py-2 font-medium text-white hover:bg-purple-700"
          @click="openModal('batch')"
        >
          + Tạo nhiều mã
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-20 text-center text-gray-500">Đang tải...</div>

    <!-- Table -->
    <div v-else class="overflow-hidden rounded-xl bg-white shadow">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Mã Code</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Lượt quay</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Đã dùng</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Giới hạn</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Trạng thái</th>
            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="code in codes" :key="code.id" class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <code class="rounded bg-gray-100 px-2 py-1 font-mono font-bold text-purple-600">
                  {{ code.code }}
                </code>
                <button
                  class="text-gray-400 hover:text-gray-600"
                  title="Copy"
                  @click="copyCode(code.code)"
                >
                  📋
                </button>
              </div>
              <p v-if="code.note" class="mt-1 text-xs text-gray-500">{{ code.note }}</p>
            </td>
            <td class="px-4 py-3 text-center font-medium text-yellow-600">
              {{ code.spins_amount }}
            </td>
            <td class="px-4 py-3 text-center">
              {{ code.used_count }}
            </td>
            <td class="px-4 py-3 text-center">
              {{ code.max_uses || '∞' }}
            </td>
            <td class="px-4 py-3 text-center">
              <span
                :class="[
                  'inline-block rounded-full px-2 py-1 text-xs font-medium',
                  code.is_active
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700',
                ]"
              >
                {{ code.is_active ? 'Hoạt động' : 'Tắt' }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <button
                :class="[
                  'mr-2 rounded px-2 py-1 text-xs',
                  code.is_active
                    ? 'bg-red-100 text-red-600 hover:bg-red-200'
                    : 'bg-green-100 text-green-600 hover:bg-green-200',
                ]"
                @click="toggleActive(code)"
              >
                {{ code.is_active ? 'Tắt' : 'Bật' }}
              </button>
              <button
                class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200"
                @click="deleteCode(code)"
              >
                Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="codes.length === 0" class="py-12 text-center text-gray-500">
        Chưa có mã code nào
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showModal = false"
      >
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 class="mb-4 text-lg font-semibold">
            {{ modalMode === 'single' ? 'Tạo mã code' : 'Tạo nhiều mã code' }}
          </h3>

          <form @submit.prevent="handleSubmit">
            <!-- Single mode: custom code -->
            <div v-if="modalMode === 'single'" class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">
                Mã code (để trống = tự động)
              </label>
              <input
                v-model="form.code"
                type="text"
                class="w-full rounded-lg border px-3 py-2 uppercase"
                placeholder="VD: LUCKY2024"
              />
            </div>

            <!-- Batch mode: count & prefix -->
            <div v-if="modalMode === 'batch'" class="mb-4 grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Số lượng</label>
                <input
                  v-model.number="form.count"
                  type="number"
                  min="1"
                  max="100"
                  class="w-full rounded-lg border px-3 py-2"
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Prefix</label>
                <input
                  v-model="form.prefix"
                  type="text"
                  class="w-full rounded-lg border px-3 py-2 uppercase"
                  placeholder="VD: VIP"
                />
              </div>
            </div>

            <!-- Common fields -->
            <div class="mb-4 grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Số lượt quay</label>
                <input
                  v-model.number="form.spins_amount"
                  type="number"
                  min="1"
                  class="w-full rounded-lg border px-3 py-2"
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Giới hạn sử dụng
                </label>
                <input
                  v-model.number="form.max_uses"
                  type="number"
                  min="1"
                  class="w-full rounded-lg border px-3 py-2"
                  placeholder="Không giới hạn"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">Hết hạn</label>
              <input
                v-model="form.expires_at"
                type="datetime-local"
                class="w-full rounded-lg border px-3 py-2"
              />
            </div>

            <div v-if="modalMode === 'single'" class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">Ghi chú</label>
              <input
                v-model="form.note"
                type="text"
                class="w-full rounded-lg border px-3 py-2"
                placeholder="VD: Mã cho sự kiện ABC"
              />
            </div>

            <div class="flex justify-end gap-2">
              <button
                type="button"
                class="rounded-lg px-4 py-2 text-gray-600 hover:bg-gray-100"
                @click="showModal = false"
              >
                Hủy
              </button>
              <button
                type="submit"
                :disabled="formLoading"
                class="rounded-lg bg-yellow-500 px-4 py-2 font-medium text-gray-900 hover:bg-yellow-400 disabled:opacity-50"
              >
                {{ formLoading ? 'Đang tạo...' : 'Tạo' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
