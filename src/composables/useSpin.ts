import { ref, computed, onMounted } from 'vue'
import api, {
  getSessionToken,
  clearSessionToken,
  ApiError,
  type Prize,
  type SessionInfo,
} from '@/services/api'

/**
 * Composable quản lý trạng thái vòng quay
 */
export function useSpin() {
  // State
  const prizes = ref<Prize[]>([])
  const session = ref<SessionInfo | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const hasSession = ref(false)

  // Computed
  const spinBalance = computed(() => session.value?.spin_balance ?? 0)
  const canSpin = computed(() => hasSession.value && spinBalance.value > 0)

  // Khởi tạo - load prizes và check session
  const initialize = async () => {
    isLoading.value = true
    error.value = null

    try {
      // Load prizes
      prizes.value = await api.getPrizes()

      // Check existing session
      const token = getSessionToken()
      if (token) {
        try {
          session.value = await api.getSession()
          hasSession.value = true
        } catch (e) {
          // Session hết hạn hoặc không hợp lệ
          clearSessionToken()
          hasSession.value = false
        }
      }
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Không thể tải dữ liệu'
    } finally {
      isLoading.value = false
    }
  }

  // Đổi mã code
  const redeemCode = async (code: string) => {
    isLoading.value = true
    error.value = null

    try {
      const result = await api.redeemCode(code)
      session.value = {
        spin_balance: result.spin_balance,
        total_spins: 0,
        expires_at: result.expires_at,
      }
      hasSession.value = true
      return { success: true }
    } catch (e) {
      const message = e instanceof ApiError ? e.message : 'Không thể đổi mã'
      error.value = message
      return { success: false, message }
    } finally {
      isLoading.value = false
    }
  }

  // Bắt đầu quay - trả về góc quay từ server
  const startSpin = async () => {
    error.value = null

    try {
      const result = await api.startSpin()

      // Cập nhật số lượt còn lại
      if (session.value) {
        session.value.spin_balance = result.remaining_spins
      }

      return {
        success: true,
        spinToken: result.spin_token,
        targetAngle: result.target_angle,
      }
    } catch (e) {
      const message = e instanceof ApiError ? e.message : 'Không thể quay'
      error.value = message

      // Nếu session hết hạn
      if (e instanceof ApiError && e.code === 'SESSION_EXPIRED') {
        clearSessionToken()
        hasSession.value = false
        session.value = null
      }

      return { success: false, message }
    }
  }

  // Claim kết quả
  const claimResult = async (spinToken: string) => {
    try {
      const result = await api.claimResult(spinToken)
      return { success: true, prize: result.prize }
    } catch (e) {
      const message = e instanceof ApiError ? e.message : 'Không thể nhận kết quả'
      return { success: false, message }
    }
  }

  // Đăng xuất / xóa session
  const logout = () => {
    clearSessionToken()
    hasSession.value = false
    session.value = null
  }

  // Auto initialize
  onMounted(initialize)

  return {
    // State
    prizes,
    session,
    isLoading,
    error,
    hasSession,

    // Computed
    spinBalance,
    canSpin,

    // Methods
    initialize,
    redeemCode,
    startSpin,
    claimResult,
    logout,
  }
}
