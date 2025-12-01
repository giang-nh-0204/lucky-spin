import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Public - Vòng quay
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/SpinPage.vue'),
    },

    // Admin routes
    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('@/views/admin/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/DashboardView.vue'),
        },
        {
          path: 'codes',
          name: 'admin-codes',
          component: () => import('@/views/admin/CodesView.vue'),
        },
        {
          path: 'prizes',
          name: 'admin-prizes',
          component: () => import('@/views/admin/PrizesView.vue'),
        },
        {
          path: 'results',
          name: 'admin-results',
          component: () => import('@/views/admin/ResultsView.vue'),
        },
      ],
    },
  ],
})

// Navigation guard
router.beforeEach((to, _from, next) => {
  const token = localStorage.getItem('admin_token')

  if (to.meta.requiresAuth && !token) {
    next({ name: 'admin-login' })
  } else if (to.meta.guest && token) {
    next({ name: 'admin-dashboard' })
  } else {
    next()
  }
})

export default router
