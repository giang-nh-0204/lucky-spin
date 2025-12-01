<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { clearAdminToken } from '@/services/adminApi'

const router = useRouter()
const route = useRoute()
const sidebarOpen = ref(true)

const menuItems = [
  { name: 'Dashboard', path: '/admin', icon: '📊' },
  { name: 'Mã Code', path: '/admin/codes', icon: '🎟️' },
  { name: 'Giải thưởng', path: '/admin/prizes', icon: '🎁' },
  { name: 'Kết quả', path: '/admin/results', icon: '📋' },
]

const isActive = (path: string) => {
  if (path === '/admin') {
    return route.path === '/admin'
  }
  return route.path.startsWith(path)
}

const logout = () => {
  clearAdminToken()
  router.push('/admin/login')
}
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside
      :class="[
        'relative flex flex-col bg-gray-900 text-white transition-all duration-300',
        sidebarOpen ? 'w-64' : 'w-20',
      ]"
    >
      <!-- Logo -->
      <div class="flex h-16 items-center justify-center border-b border-gray-800">
        <span v-if="sidebarOpen" class="text-xl font-bold text-yellow-400">
          🎰 Lucky Spin
        </span>
        <span v-else class="text-2xl">🎰</span>
      </div>

      <!-- Menu -->
      <nav class="mt-6 flex-1">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          :class="[
            'flex items-center px-6 py-3 transition-colors',
            isActive(item.path)
              ? 'bg-yellow-500 text-gray-900'
              : 'text-gray-300 hover:bg-gray-800',
          ]"
        >
          <span class="text-xl">{{ item.icon }}</span>
          <span v-if="sidebarOpen" class="ml-3">{{ item.name }}</span>
        </router-link>
      </nav>

      <!-- Toggle & Logout -->
      <div class="border-t border-gray-800 p-4">
        <button
          class="mb-2 flex w-full items-center justify-center rounded bg-gray-800 py-2 text-gray-300 hover:bg-gray-700"
          @click="sidebarOpen = !sidebarOpen"
        >
          {{ sidebarOpen ? '◀' : '▶' }}
        </button>
        <button
          class="flex w-full items-center justify-center rounded bg-red-600 py-2 text-white hover:bg-red-700"
          @click="logout"
        >
          <span v-if="sidebarOpen">Đăng xuất</span>
          <span v-else>🚪</span>
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 overflow-auto">
      <!-- Header -->
      <header class="flex h-16 items-center justify-between bg-white px-6 shadow">
        <h1 class="text-xl font-semibold text-gray-800">
          {{ menuItems.find((i) => isActive(i.path))?.name || 'Admin' }}
        </h1>
        <a
          href="/"
          target="_blank"
          class="rounded bg-purple-600 px-4 py-2 text-sm text-white hover:bg-purple-700"
        >
          Xem trang chủ
        </a>
      </header>

      <!-- Page content -->
      <div class="p-6">
        <router-view />
      </div>
    </main>
  </div>
</template>
