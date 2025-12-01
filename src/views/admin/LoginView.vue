<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import adminApi from '@/services/adminApi'

const router = useRouter()

const email = ref('admin@example.com')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true

  try {
    await adminApi.login(email.value, password.value)
    router.push('/admin')
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Đăng nhập thất bại'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div
    class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800"
  >
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
      <div class="mb-8 text-center">
        <span class="text-5xl">🎰</span>
        <h1 class="mt-4 text-2xl font-bold text-gray-800">Lucky Spin Admin</h1>
        <p class="text-gray-500">Đăng nhập để quản lý</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500/20"
            placeholder="admin@example.com"
          />
        </div>

        <div class="mb-6">
          <label class="mb-2 block text-sm font-medium text-gray-700">Mật khẩu</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500/20"
            placeholder="••••••••"
          />
        </div>

        <p v-if="error" class="mb-4 text-center text-red-500">
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-yellow-500 py-3 font-semibold text-gray-900 transition hover:bg-yellow-400 disabled:opacity-50"
        >
          {{ loading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-500">
        Mặc định: admin@example.com / password
      </p>
    </div>
  </div>
</template>
