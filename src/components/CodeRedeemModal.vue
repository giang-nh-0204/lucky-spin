<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  loading?: boolean
  error?: string | null
}>()

const emit = defineEmits<{
  (e: 'submit', code: string): void
  (e: 'close'): void
}>()

const code = ref('')

const handleSubmit = () => {
  if (code.value.trim()) {
    emit('submit', code.value.trim().toUpperCase())
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
      @click.self="emit('close')"
    >
      <div
        class="mx-4 w-full max-w-md rounded-2xl bg-gradient-to-br from-purple-900 to-indigo-900 p-6 shadow-2xl"
      >
        <h2 class="mb-4 text-center text-2xl font-bold text-white">
          Nhập Mã Code
        </h2>

        <p class="mb-6 text-center text-purple-200">
          Nhập mã code để nhận lượt quay may mắn
        </p>

        <form @submit.prevent="handleSubmit">
          <input
            v-model="code"
            type="text"
            placeholder="VD: LUCKY2024"
            class="w-full rounded-xl border-2 border-purple-500/50 bg-purple-800/50 px-4 py-3 text-center text-xl font-bold uppercase tracking-widest text-white placeholder-purple-400 focus:border-yellow-400 focus:outline-none"
            :disabled="loading"
            autofocus
          />

          <p v-if="error" class="mt-3 text-center text-red-400">
            {{ error }}
          </p>

          <button
            type="submit"
            :disabled="loading || !code.trim()"
            class="mt-6 w-full rounded-xl bg-gradient-to-r from-yellow-400 to-orange-500 py-3 text-lg font-bold text-purple-900 shadow-lg transition-all hover:scale-105 hover:shadow-yellow-500/50 disabled:opacity-50 disabled:hover:scale-100"
          >
            <span v-if="loading">Đang xử lý...</span>
            <span v-else>XÁC NHẬN</span>
          </button>
        </form>

        <button
          type="button"
          class="mt-4 w-full py-2 text-purple-300 hover:text-white"
          @click="emit('close')"
        >
          Đóng
        </button>
      </div>
    </div>
  </Teleport>
</template>
