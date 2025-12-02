<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import adminApi, { type Prize } from '@/services/adminApi'

const prizes = ref<Prize[]>([])
const loading = ref(true)
const error = ref('')
const autoLoading = ref(false)

// Modal
const showModal = ref(false)
const editingPrize = ref<Prize | null>(null)
const formLoading = ref(false)

const form = ref({
  name: '',
  price: 100,
  image: '',
  color: '#FF6B6B',
  probability: 0.05,
  stock: null as number | null,
  is_active: true,
})

const totalProbability = computed(() => {
  return prizes.value.reduce((sum, p) => sum + Number(p.probability), 0)
})

const loadPrizes = async () => {
  loading.value = true
  try {
    prizes.value = await adminApi.getPrizes()
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Lỗi'
  } finally {
    loading.value = false
  }
}

const openModal = (prize?: Prize) => {
  if (prize) {
    editingPrize.value = prize
    form.value = {
      name: prize.name,
      price: prize.price,
      image: prize.image,
      color: prize.color,
      probability: prize.probability,
      stock: prize.stock,
      is_active: prize.is_active,
    }
  } else {
    editingPrize.value = null
    form.value = {
      name: '',
      price: 100,
      image: '',
      color: '#FF6B6B',
      probability: 0.05,
      stock: null,
      is_active: true,
    }
  }
  showModal.value = true
}

const handleSubmit = async () => {
  formLoading.value = true
  try {
    if (editingPrize.value) {
      await adminApi.updatePrize(editingPrize.value.id, form.value)
    } else {
      await adminApi.createPrize({
        ...form.value,
        sort_order: prizes.value.length,
      })
    }
    showModal.value = false
    await loadPrizes()
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  } finally {
    formLoading.value = false
  }
}

const toggleActive = async (prize: Prize) => {
  try {
    await adminApi.updatePrize(prize.id, { is_active: !prize.is_active })
    prize.is_active = !prize.is_active
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  }
}

const deletePrize = async (prize: Prize) => {
  if (!confirm(`Xóa giải "${prize.name}"?`)) return
  try {
    await adminApi.deletePrize(prize.id)
    prizes.value = prizes.value.filter((p) => p.id !== prize.id)
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  }
}

const autoProbability = async () => {
  if (!confirm('Tự động phân phối xác suất theo giá vàng?\n(Giá cao = xác suất thấp)')) return
  autoLoading.value = true
  try {
    await adminApi.autoProbability()
    await loadPrizes()
    alert('Đã phân phối xác suất thành công!')
  } catch (e) {
    alert(e instanceof Error ? e.message : 'Lỗi')
  } finally {
    autoLoading.value = false
  }
}

onMounted(loadPrizes)
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-4 md:mb-6">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold text-gray-800 md:text-xl">Quản lý Giải thưởng</h2>
          <p class="text-xs text-gray-500 md:text-sm">
            Tổng xác suất:
            <span :class="totalProbability === 1 ? 'text-green-600' : 'text-red-600'">
              {{ (totalProbability * 100).toFixed(2) }}%
            </span>
            <span v-if="totalProbability !== 1" class="text-red-600">(nên = 100%)</span>
          </p>
        </div>
        <div class="flex w-full gap-2 md:w-auto">
          <button
            :disabled="autoLoading"
            class="flex-1 rounded-lg bg-purple-500 px-3 py-2 text-xs font-medium text-white hover:bg-purple-400 disabled:opacity-50 md:flex-none md:px-4 md:text-sm"
            @click="autoProbability"
          >
            {{ autoLoading ? '...' : '⚖️ Chia %' }}
          </button>
          <button
            class="flex-1 rounded-lg bg-yellow-500 px-3 py-2 text-xs font-medium text-gray-900 hover:bg-yellow-400 md:flex-none md:px-4 md:text-sm"
            @click="openModal()"
          >
            + Thêm giải
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="py-20 text-center text-gray-500">Đang tải...</div>

    <!-- Grid -->
    <div v-else class="grid gap-3 md:gap-4 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="prize in prizes"
        :key="prize.id"
        :class="[
          'relative rounded-xl bg-white p-4 shadow transition hover:shadow-lg',
          !prize.is_active && 'opacity-50',
        ]"
      >
        <!-- Color bar -->
        <div
          class="absolute left-0 top-0 h-full w-1 rounded-l-xl"
          :style="{ backgroundColor: prize.color }"
        ></div>

        <div class="flex gap-4">
          <!-- Image -->
          <div
            class="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100"
            :style="{ backgroundColor: prize.color + '20' }"
          >
            <img
              v-if="prize.image_url"
              :src="prize.image_url"
              :alt="prize.name"
              class="h-12 w-12 object-contain"
            />
            <span v-else class="text-2xl">🎁</span>
          </div>

          <!-- Info -->
          <div class="flex-1">
            <h3 class="font-semibold text-gray-800">{{ prize.name }}</h3>
            <p class="text-lg font-bold text-yellow-600">{{ prize.price }} Gold</p>
            <div class="mt-1 flex flex-wrap gap-2 text-xs">
              <span class="text-gray-500">Xác suất: {{ (prize.probability * 100).toFixed(2) }}%</span>
              <span
                v-if="prize.stock !== null"
                :class="[
                  'rounded px-1.5 py-0.5 font-medium',
                  prize.stock === 0 ? 'bg-red-100 text-red-600' :
                  prize.stock <= 5 ? 'bg-orange-100 text-orange-600' :
                  'bg-green-100 text-green-600'
                ]"
              >
                Còn: {{ prize.stock }}
              </span>
              <span v-else class="rounded bg-blue-100 px-1.5 py-0.5 font-medium text-blue-600">
                Không giới hạn
              </span>
            </div>
            <p v-if="prize.spin_results_count" class="mt-1 text-xs text-purple-600">
              Đã trúng {{ prize.spin_results_count }} lần
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-3 flex justify-end gap-2">
          <button
            class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-600 hover:bg-blue-200"
            @click="openModal(prize)"
          >
            Sửa
          </button>
          <button
            :class="[
              'rounded px-2 py-1 text-xs',
              prize.is_active
                ? 'bg-red-100 text-red-600 hover:bg-red-200'
                : 'bg-green-100 text-green-600 hover:bg-green-200',
            ]"
            @click="toggleActive(prize)"
          >
            {{ prize.is_active ? 'Tắt' : 'Bật' }}
          </button>
          <button
            class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200"
            @click="deletePrize(prize)"
          >
            Xóa
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showModal = false"
      >
        <div class="max-h-[90vh] w-full max-w-md overflow-auto rounded-xl bg-white p-6 shadow-xl">
          <h3 class="mb-4 text-lg font-semibold">
            {{ editingPrize ? 'Sửa giải thưởng' : 'Thêm giải thưởng' }}
          </h3>

          <form @submit.prevent="handleSubmit">
            <div class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">Tên giải</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full rounded-lg border px-3 py-2"
              />
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Giá (Gold)</label>
                <input
                  v-model.number="form.price"
                  type="number"
                  min="0"
                  required
                  class="w-full rounded-lg border px-3 py-2"
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Xác suất</label>
                <input
                  v-model.number="form.probability"
                  type="number"
                  min="0.0001"
                  max="1"
                  step="0.0001"
                  required
                  class="w-full rounded-lg border px-3 py-2"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">Đường dẫn ảnh</label>
              <input
                v-model="form.image"
                type="text"
                class="w-full rounded-lg border px-3 py-2"
                placeholder="/images/prize.png"
              />
            </div>

            <div class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">Màu sắc</label>
              <div class="flex gap-2">
                <input v-model="form.color" type="color" class="h-10 w-14 cursor-pointer rounded border" />
                <input
                  v-model="form.color"
                  type="text"
                  class="flex-1 rounded-lg border px-3 py-2"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="mb-1 block text-sm font-medium text-gray-700">
                Số lượng (để trống = vô hạn)
              </label>
              <input
                v-model.number="form.stock"
                type="number"
                min="0"
                placeholder="Không giới hạn"
                class="w-full rounded-lg border px-3 py-2"
              />
            </div>

            <div class="mb-4 flex items-center gap-2">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4"
              />
              <label for="is_active" class="text-sm text-gray-700">Đang hoạt động</label>
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
                {{ formLoading ? 'Đang lưu...' : 'Lưu' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
