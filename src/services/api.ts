/**
 * API Service cho Lucky Spin
 * Kết nối với Laravel backend
 */

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// Lưu session token
const SESSION_KEY = 'spin_session_token'

export const getSessionToken = (): string | null => {
  return localStorage.getItem(SESSION_KEY)
}

export const setSessionToken = (token: string): void => {
  localStorage.setItem(SESSION_KEY, token)
}

export const clearSessionToken = (): void => {
  localStorage.removeItem(SESSION_KEY)
}

// HTTP Client
async function request<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<T> {
  const token = getSessionToken()

  const headers: HeadersInit = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    ...(token ? { 'X-Session-Token': token } : {}),
    ...options.headers,
  }

  const response = await fetch(`${API_BASE}${endpoint}`, {
    ...options,
    headers,
  })

  const data = await response.json()

  if (!response.ok) {
    throw new ApiError(data.message || 'Có lỗi xảy ra', response.status, data.code)
  }

  return data
}

// Custom Error
export class ApiError extends Error {
  constructor(
    message: string,
    public statusCode: number,
    public code?: string
  ) {
    super(message)
    this.name = 'ApiError'
  }
}

// Types
export interface Prize {
  id: number
  name: string
  price: number
  image: string
  image_url: string // URL đầy đủ từ server
  color: string
}

export interface SessionInfo {
  spin_balance: number
  total_spins: number
  expires_at: string
}

export interface SpinStartResult {
  spin_token: string
  target_angle: number
  remaining_spins: number
}

export interface SpinClaimResult {
  prize: Prize
}

export interface RedeemCodeResult {
  session_token: string
  spin_balance: number
  expires_at: string
}

// API Functions
export const api = {
  /**
   * Lấy danh sách giải thưởng
   */
  async getPrizes(): Promise<Prize[]> {
    const res = await request<{ success: boolean; data: Prize[] }>('/prizes')
    return res.data
  },

  /**
   * Đổi mã code lấy lượt quay
   */
  async redeemCode(code: string): Promise<RedeemCodeResult> {
    const res = await request<{ success: boolean; data: RedeemCodeResult }>(
      '/redeem-code',
      {
        method: 'POST',
        body: JSON.stringify({ code }),
      }
    )

    // Lưu session token
    setSessionToken(res.data.session_token)

    return res.data
  },

  /**
   * Lấy thông tin session hiện tại
   */
  async getSession(): Promise<SessionInfo> {
    const res = await request<{ success: boolean; data: SessionInfo }>('/session')
    return res.data
  },

  /**
   * Bắt đầu quay - Server trả về góc quay
   * @param currentRotation - Góc quay hiện tại của wheel (normalized 0-360)
   */
  async startSpin(currentRotation: number = 0): Promise<SpinStartResult> {
    const res = await request<{ success: boolean; data: SpinStartResult }>(
      '/spin/start',
      {
        method: 'POST',
        body: JSON.stringify({ current_rotation: currentRotation }),
      }
    )
    return res.data
  },

  /**
   * Claim kết quả sau khi animation xong
   */
  async claimResult(spinToken: string): Promise<SpinClaimResult> {
    const res = await request<{ success: boolean; data: SpinClaimResult }>(
      `/spin/claim/${spinToken}`,
      { method: 'POST' }
    )
    return res.data
  },

  /**
   * Lấy lịch sử quay
   */
  async getHistory(): Promise<Array<{ prize: Prize; claimed_at: string }>> {
    const res = await request<{
      success: boolean
      data: Array<{ prize: Prize; claimed_at: string }>
    }>('/spin/history')
    return res.data
  },
}

export default api
