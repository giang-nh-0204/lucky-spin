<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { clearAdminToken } from '@/services/adminApi'

const router = useRouter()
const route = useRoute()
const sidebarOpen = ref(true)
const mobileMenuOpen = ref(false)
const isMobile = ref(false)

const menuItems = [
  { name: 'Dashboard', path: '/admin', icon: '📊' },
  { name: 'Mã Code', path: '/admin/codes', icon: '🎟️' },
  { name: 'Giải thưởng', path: '/admin/prizes', icon: '🎁' },
  { name: 'Kết quả', path: '/admin/results', icon: '📋' },
]

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
  if (isMobile.value) {
    sidebarOpen.value = false
  }
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})

const isActive = (path: string) => {
  if (path === '/admin') {
    return route.path === '/admin'
  }
  return route.path.startsWith(path)
}

const navigateTo = (path: string) => {
  router.push(path)
  if (isMobile.value) {
    mobileMenuOpen.value = false
  }
}

const logout = () => {
  clearAdminToken()
  router.push('/admin/login')
}
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Mobile overlay -->
    <div
      v-if="mobileMenuOpen && isMobile"
      class="fixed inset-0 z-40 bg-black/50 md:hidden"
      @click="mobileMenuOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col bg-gray-900 text-white transition-all duration-300 md:relative',
        isMobile
          ? mobileMenuOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64'
          : sidebarOpen ? 'w-64' : 'w-20',
      ]"
    >
      <!-- Logo -->
      <div class="flex h-14 items-center justify-between border-b border-gray-800 px-4 md:h-16 md:justify-center md:px-0">
        <span class="text-lg font-bold text-yellow-400 md:text-xl">
          <span v-if="sidebarOpen || isMobile">🎰 Lucky Spin</span>
          <span v-else>🎰</span>
        </span>
        <button
          v-if="isMobile"
          class="text-gray-400 hover:text-white md:hidden"
          @click="mobileMenuOpen = false"
        >
          ✕
        </button>
      </div>

      <!-- Menu -->
      <nav class="mt-4 flex-1 md:mt-6">
        <button
          v-for="item in menuItems"
          :key="item.path"
          :class="[
            'flex w-full items-center px-4 py-3 transition-colors md:px-6',
            isActive(item.path)
              ? 'bg-yellow-500 text-gray-900'
              : 'text-gray-300 hover:bg-gray-800',
          ]"
          @click="navigateTo(item.path)"
        >
          <span class="text-xl">{{ item.icon }}</span>
          <span v-if="sidebarOpen || isMobile" class="ml-3">{{ item.name }}</span>
        </button>
      </nav>

      <!-- Toggle & Logout -->
      <div class="border-t border-gray-800 p-3 md:p-4">
        <button
          v-if="!isMobile"
          class="mb-2 flex w-full items-center justify-center rounded bg-gray-800 py-2 text-gray-300 hover:bg-gray-700"
          @click="sidebarOpen = !sidebarOpen"
        >
          {{ sidebarOpen ? '◀' : '▶' }}
        </button>
        <button
          class="flex w-full items-center justify-center rounded bg-red-600 py-2 text-white hover:bg-red-700"
          @click="logout"
        >
          <span v-if="sidebarOpen || isMobile">Đăng xuất</span>
          <span v-else>🚪</span>
        </button>
      </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 overflow-auto">
      <!-- Header -->
      <header class="flex h-14 items-center justify-between bg-white px-3 shadow md:h-16 md:px-6">
        <div class="flex items-center gap-3">
          <!-- Mobile menu button -->
          <button
            v-if="isMobile"
            class="rounded bg-gray-100 p-2 text-gray-600 hover:bg-gray-200 md:hidden"
            @click="mobileMenuOpen = true"
          >
            ☰
          </button>
          <h1 class="text-base font-semibold text-gray-800 md:text-xl">
            {{ menuItems.find((i) => isActive(i.path))?.name || 'Admin' }}
          </h1>
        </div>
        <a
          href="/"
          target="_blank"
          class="rounded bg-purple-600 px-3 py-1.5 text-xs text-white hover:bg-purple-700 md:px-4 md:py-2 md:text-sm"
        >
          Xem trang chủ
        </a>
      </header>

      <!-- Page content -->
      <div class="p-3 md:p-6">
        <router-view />
      </div>
    </main>
  </div>
</template>
