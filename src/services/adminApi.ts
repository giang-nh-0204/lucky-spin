/**
 * Admin API Service
 */

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// Token management
export const getAdminToken = (): string | null => {
  return localStorage.getItem('admin_token')
}

export const setAdminToken = (token: string): void => {
  localStorage.setItem('admin_token', token)
}

export const clearAdminToken = (): void => {
  localStorage.removeItem('admin_token')
}

// HTTP Client
async function request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const token = getAdminToken()

  const headers: HeadersInit = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
    ...options.headers,
  }

  const response = await fetch(`${API_BASE}${endpoint}`, {
    ...options,
    headers,
  })

  const data = await response.json()

  if (!response.ok) {
    if (response.status === 401) {
      clearAdminToken()
      window.location.href = import.meta.env.BASE_URL + 'admin/login'
    }
    throw new Error(data.message || 'Có lỗi xảy ra')
  }

  return data
}

// Types
export interface AdminStats {
  total_codes: number
  active_codes: number
  total_sessions: number
  active_sessions: number
  total_spins: number
  spins_today: number
  spins_this_week: number
  total_prizes: number
  active_prizes: number
  top_prizes: Array<{ id: number; name: string; price: number; spin_results_count: number }>
  daily_spins: Array<{ date: string; count: number }>
}

export interface SpinCode {
  id: number
  code: string
  spins_amount: number
  max_uses: number | null
  used_count: number
  note: string | null
  expires_at: string | null
  is_active: boolean
  created_at: string
  sessions_count?: number
}

export interface Prize {
  id: number
  name: string
  price: number
  image: string
  image_url: string // URL đầy đủ từ server
  color: string
  probability: number
  is_active: boolean
  stock: number | null
  sort_order: number
  spin_results_count?: number
}

export interface SpinResult {
  id: number
  spin_token: string
  target_angle: number
  status: 'pending' | 'claimed' | 'expired'
  delivery_status: 'pending' | 'delivered'
  delivery_note: string | null
  delivered_at: string | null
  created_at: string
  claimed_at: string | null
  session: {
    id: number
    ip_address: string
    code?: { id: number; code: string }
  }
  prize: { id: number; name: string; price: number; image: string; image_url: string }
}

// API Functions
export const adminApi = {
  // Auth
  async login(email: string, password: string): Promise<{ token: string }> {
    const res = await request<{ success: boolean; data: { token: string } }>('/admin/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    })
    setAdminToken(res.data.token)
    return res.data
  },

  async logout(): Promise<void> {
    try {
      await request('/admin/logout', { method: 'POST' })
    } finally {
      clearAdminToken()
    }
  },

  // Dashboard
  async getStats(): Promise<AdminStats> {
    const res = await request<{ success: boolean; data: AdminStats }>('/admin/stats')
    return res.data
  },

  // Codes
  async getCodes(page = 1): Promise<{ data: SpinCode[]; meta: any }> {
    const res = await request<{ success: boolean; data: { data: SpinCode[]; meta: any } }>(
      `/admin/codes?page=${page}`
    )
    return res.data
  },

  async createCode(data: {
    code?: string
    spins_amount: number
    max_uses?: number
    note?: string
    expires_at?: string
  }): Promise<SpinCode> {
    const res = await request<{ success: boolean; data: SpinCode }>('/admin/codes', {
      method: 'POST',
      body: JSON.stringify(data),
    })
    return res.data
  },

  async generateBatchCodes(data: {
    count: number
    spins_amount: number
    prefix?: string
    max_uses?: number
    expires_at?: string
  }): Promise<{ codes: string[]; count: number }> {
    const res = await request<{ success: boolean; data: { codes: string[]; count: number } }>(
      '/admin/codes/generate-batch',
      {
        method: 'POST',
        body: JSON.stringify(data),
      }
    )
    return res.data
  },

  async updateCode(id: number, data: Partial<SpinCode>): Promise<SpinCode> {
    const res = await request<{ success: boolean; data: SpinCode }>(`/admin/codes/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    })
    return res.data
  },

  async deleteCode(id: number): Promise<void> {
    await request(`/admin/codes/${id}`, { method: 'DELETE' })
  },

  // Prizes
  async getPrizes(): Promise<Prize[]> {
    const res = await request<{ success: boolean; data: Prize[] }>('/admin/prizes')
    return res.data
  },

  async createPrize(data: Omit<Prize, 'id' | 'spin_results_count'>): Promise<Prize> {
    const res = await request<{ success: boolean; data: Prize }>('/admin/prizes', {
      method: 'POST',
      body: JSON.stringify(data),
    })
    return res.data
  },

  async updatePrize(id: number, data: Partial<Prize>): Promise<Prize> {
    const res = await request<{ success: boolean; data: Prize }>(`/admin/prizes/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    })
    return res.data
  },

  async deletePrize(id: number): Promise<void> {
    await request(`/admin/prizes/${id}`, { method: 'DELETE' })
  },

  // Results
  async getResults(params?: {
    page?: number
    prize_id?: number
    status?: string
    from?: string
    to?: string
    code?: string
    delivery_status?: string
  }): Promise<{ data: SpinResult[]; meta: any }> {
    const query = new URLSearchParams()
    if (params?.page) query.set('page', String(params.page))
    if (params?.prize_id) query.set('prize_id', String(params.prize_id))
    if (params?.status) query.set('status', params.status)
    if (params?.from) query.set('from', params.from)
    if (params?.to) query.set('to', params.to)
    if (params?.code) query.set('code', params.code)
    if (params?.delivery_status) query.set('delivery_status', params.delivery_status)

    const res = await request<{ success: boolean; data: { data: SpinResult[]; meta: any } }>(
      `/admin/stats/results?${query}`
    )
    return res.data
  },

  // Update delivery status
  async updateDeliveryStatus(
    id: number,
    data: { delivery_status: 'pending' | 'delivered'; delivery_note?: string }
  ): Promise<SpinResult> {
    const res = await request<{ success: boolean; data: SpinResult }>(
      `/admin/results/${id}/delivery`,
      {
        method: 'PUT',
        body: JSON.stringify(data),
      }
    )
    return res.data
  },

  // Bulk update delivery status
  async bulkUpdateDeliveryStatus(data: {
    ids: number[]
    delivery_status: 'pending' | 'delivered'
    delivery_note?: string
  }): Promise<{ updated_count: number }> {
    const res = await request<{ success: boolean; data: { updated_count: number } }>(
      '/admin/results/bulk-delivery',
      {
        method: 'POST',
        body: JSON.stringify(data),
      }
    )
    return res.data
  },
}

export default adminApi
