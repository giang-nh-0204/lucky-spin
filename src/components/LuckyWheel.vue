<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Prize {
  id: number
  name: string
  price: number
  image: string
  color: string
}

const initialPrizes: Prize[] = [
  { id: 1, name: 'Cao băng bạo', price: 120, image: 'images/cao-bang-bao.jpg', color: '#FF6B6B' },
  { id: 2, name: 'Cao bí hựu', price: 200, image: 'images/cao-bi-huu.jpg', color: '#4ECDC4' },
  { id: 3, name: 'Cao di hồn', price: 100, image: 'images/cao-di-hon.jpg', color: '#45B7D1' },
  { id: 4, name: 'Cao huyết bạo', price: 750, image: 'images/cao-huyet-bao.jpg', color: '#F7DC6F' },
  { id: 5, name: 'Cao linh đông', price: 500, image: 'images/cao-linh-dong.jpg', color: '#BB8FCE' },
  { id: 6, name: 'Cao mãnh kích', price: 750, image: 'images/cao-manh-kich.jpg', color: '#85C1E2' },
  { id: 7, name: 'Cao ngưng thần', price: 450, image: 'images/cao-ngung-than.jpg', color: '#F8B739' },
  { id: 8, name: 'Cao cường thân', price: 450, image: 'images/cao-cuong-than.jpg', color: '#52C469' },
  { id: 9, name: 'Cao phản chấn', price: 300, image: 'images/cao-phan-chan.jpg', color: '#FF8C94' },
  { id: 10, name: 'Cao phản kich', price: 300, image: 'images/cao-phan-kich.jpg', color: '#A8E6CF' },
  { id: 11, name: 'Cao phụ thân', price: 150, image: 'images/cao-phu-than.jpg', color: '#FFD3B6' },
  { id: 12, name: 'Cao tá lực', price: 150, image: 'images/cao-ta-luc.jpg', color: '#FFAAA5' },
  { id: 13, name: 'Cao thế sát', price: 600, image: 'images/cao-the-sat.jpg', color: '#FF8B94' },
  { id: 14, name: 'Cao thị huyết', price: 100, image: 'images/cao-thi-huyet.jpg', color: '#A8D8EA' },
  { id: 15, name: 'Cao thuấn ảnh', price: 400, image: 'images/cao-thuan-anh.jpg', color: '#AA96DA' },
  { id: 16, name: 'Cao nội lực', price: 600, image: 'images/cao-noi-luc.jpg', color: '#FCBAD3' },
  { id: 17, name: 'Cao cộng sinh', price: 300, image: 'images/cao-cong-sinh.jpg', color: '#FFFFD2' },
  { id: 18, name: 'Cao nhục tường', price: 100, image: 'images/cao-nhuc-tuong.jpg', color: '#A0CED9' },
  { id: 19, name: 'Cao Huyết tế', price: 4500, image: 'images/cao-huyet-te.jpg', color: '#FFC09F' }
]

const prizes = ref<Prize[]>([...initialPrizes])

// Tính xác suất dựa trên giá (giá càng cao, xác suất càng thấp)
const calculateProbabilities = () => {
  // ===== DEMO MODE: Tỉ lệ đều nhau =====
  const equalProbability = 1 / prizes.value.length
  return prizes.value.map(() => equalProbability)

  // ===== PRODUCTION MODE: Tỉ lệ theo giá (comment out cho demo) =====
  // Uncomment đoạn code bên dưới để sử dụng tỉ lệ theo giá
  // Đảo ngược giá để tạo trọng số (giá thấp = trọng số cao)
  // const maxPrice = Math.max(...prizes.value.map(p => p.price))
  // const weights = prizes.value.map(p => maxPrice - p.price + 50) // +50 để đảm bảo mọi item đều có cơ hội
  // const totalWeight = weights.reduce((sum, w) => sum + w, 0)
  // return weights.map(w => w / totalWeight)
}

const probabilities = computed(() => calculateProbabilities())

// Shuffle prizes
const shufflePrizes = () => {
  if (isSpinning.value) return

  const shuffled = [...prizes.value]
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    const temp = shuffled[i]
    const other = shuffled[j]
    if (temp && other) {
      shuffled[i] = other
      shuffled[j] = temp
    }
  }
  prizes.value = shuffled
}

const isSpinning = ref(false)
const rotation = ref(0)
const currentPrize = ref<Prize | null>(null)
const showResult = ref(false)

// Drag states
const isDragging = ref(false)
const startAngle = ref(0)
const lastAngle = ref(0)
const dragVelocity = ref(0)
const lastTime = ref(0)

// Responsive wheel size
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1920)
const wheelSize = computed(() => {
  const w = windowWidth.value
  if (w < 640) return 280 // mobile
  if (w < 768) return 350 // sm
  if (w < 1024) return 450 // md
  if (w < 1280) return 550 // lg
  return 600 // xl
})

const wheelConfig = computed(() => {
  const size = wheelSize.value
  const padding = 16 // p-4 = 16px
  const innerSize = size - padding * 2
  const center = innerSize / 2
  const radius = center * 0.86 // ~86% of center for image placement
  const imageSize = size < 640 ? 32 : size < 768 ? 36 : size < 1024 ? 40 : 48 // responsive image size
  const fontSize = size < 640 ? 3.5 : size < 768 ? 4 : size < 1024 ? 4.5 : 5 // SVG text font size
  const legendaryFontSize = fontSize * 1.2
  return { size, center, radius, imageSize, fontSize, legendaryFontSize }
})

const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('resize', updateWindowWidth)
    updateWindowWidth()
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('resize', updateWindowWidth)
  }
})

const spinWheel = () => {
  if (isSpinning.value) return

  isSpinning.value = true
  showResult.value = false
  currentPrize.value = null

  // Chọn giải thưởng dựa trên xác suất
  const random = Math.random()
  let cumulativeProbability = 0
  let selectedPrizeIndex = 0
  const probs = probabilities.value

  for (let i = 0; i < probs.length; i++) {
    const prob = probs[i]
    if (prob === undefined) continue
    cumulativeProbability += prob
    if (random <= cumulativeProbability) {
      selectedPrizeIndex = i
      break
    }
  }

  const selectedPrize = prizes.value[selectedPrizeIndex]
  if (!selectedPrize) return
  currentPrize.value = selectedPrize

  // Tính góc quay
  const prizeAngle = 360 / prizes.value.length
  // Normalize rotation hiện tại về 0-360
  const currentRotation = ((rotation.value % 360) + 360) % 360
  // Thêm offset ngẫu nhiên trong khoảng -30% đến +30% góc của prize để trông tự nhiên hơn
  const randomOffset = (Math.random() - 0.5) * prizeAngle * 0.6
  // Góc mục tiêu để mũi tên chỉ vào prize (không phải giữa chính xác)
  const targetAngle = 360 - (selectedPrizeIndex * prizeAngle + prizeAngle / 2 + randomOffset)
  // Tính độ lệch cần quay
  let deltaAngle = targetAngle - currentRotation
  if (deltaAngle < 0) deltaAngle += 360
  // Quay ít nhất 7 vòng + góc đích
  const spinRotation = 360 * 7 + deltaAngle

  rotation.value += spinRotation

  // Hiển thị kết quả sau khi quay xong
  setTimeout(() => {
    isSpinning.value = false
    showResult.value = true
  }, 8000)
}

const closeResult = () => {
  showResult.value = false
}

const wheelStyle = computed(() => ({
  transform: `rotate(${rotation.value}deg)`,
  transition: isSpinning.value ? 'transform 8s cubic-bezier(0.17, 0.67, 0.02, 1)' : isDragging.value ? 'none' : 'transform 0.3s ease-out'
}))

const sortedPrizes = computed(() => {
  return [...prizes.value].sort((a, b) => b.price - a.price)
})

const getPrizeImageStyle = (index: number, total: number) => {
  const angle = 360 / total
  const midAngle = index * angle + angle / 2 - 90
  const midAngleRad = midAngle * (Math.PI / 180)

  // Use responsive wheel config
  const config = wheelConfig.value
  const { center, radius, imageSize } = config
  const halfImage = imageSize / 2

  const left = center + radius * Math.cos(midAngleRad) - halfImage
  const top = center + radius * Math.sin(midAngleRad) - halfImage

  // Ảnh xoay theo góc của segment (không cộng rotation vì container đã xoay rồi)
  const imageRotation = midAngle

  return {
    left: `${left}px`,
    top: `${top}px`,
    transform: `rotate(${imageRotation}deg)`
  }
}

// Calculate angle from mouse position relative to wheel center
const getAngleFromMouse = (event: MouseEvent, element: HTMLElement) => {
  const rect = element.getBoundingClientRect()
  const centerX = rect.left + rect.width / 2
  const centerY = rect.top + rect.height / 2
  const deltaX = event.clientX - centerX
  const deltaY = event.clientY - centerY
  return Math.atan2(deltaY, deltaX) * (180 / Math.PI)
}

// Drag handlers
const onMouseDown = (event: MouseEvent) => {
  if (isSpinning.value) return

  const wheelElement = event.currentTarget as HTMLElement
  isDragging.value = true
  startAngle.value = getAngleFromMouse(event, wheelElement)
  lastAngle.value = startAngle.value
  dragVelocity.value = 0
  lastTime.value = Date.now()
}

const onMouseMove = (event: MouseEvent) => {
  if (!isDragging.value || isSpinning.value) return

  const wheelElement = document.querySelector('.wheel-container') as HTMLElement
  if (!wheelElement) return

  const currentAngle = getAngleFromMouse(event, wheelElement)
  const angleDiff = currentAngle - lastAngle.value

  // Handle angle wrap around (-180 to 180)
  let normalizedDiff = angleDiff
  if (angleDiff > 180) normalizedDiff = angleDiff - 360
  if (angleDiff < -180) normalizedDiff = angleDiff + 360

  rotation.value += normalizedDiff

  // Calculate velocity
  const currentTime = Date.now()
  const timeDiff = currentTime - lastTime.value
  if (timeDiff > 0) {
    dragVelocity.value = normalizedDiff / timeDiff
  }

  lastAngle.value = currentAngle
  lastTime.value = currentTime
}

const onMouseUp = () => {
  if (!isDragging.value) return

  isDragging.value = false

  // If velocity is high enough, trigger spin
  const velocityThreshold = 0.5 // degrees per millisecond
  if (Math.abs(dragVelocity.value) > velocityThreshold) {
    spinWheel()
  }
}

// Add global mouse move and up listeners
if (typeof window !== 'undefined') {
  window.addEventListener('mousemove', onMouseMove)
  window.addEventListener('mouseup', onMouseUp)
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 py-4 sm:py-8 md:py-12 px-4">
    <div class="container">
      <!-- Header -->
      <div class="text-center mb-4 sm:mb-6 md:mb-8">
        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2 drop-shadow-lg">
          Vòng Quay May Mắn
        </h1>
        <p class="text-sm sm:text-base md:text-lg lg:text-xl text-purple-200 mb-3 sm:mb-4">
          Quay để nhận Bí Kíp quý giá!
        </p>
        <button
          @click="shufflePrizes"
          :disabled="isSpinning"
          class="px-4 py-2 sm:px-6 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 text-white font-bold text-xs sm:text-sm shadow-xl transform transition-all duration-200 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-white"
        >
          🎲 TRỘN
        </button>
      </div>

      <!-- Wheel Container -->
      <div class="relative flex justify-center items-center mb-4 sm:mb-6 md:mb-8">
        <!-- Wheel -->
        <div
          class="relative w-[280px] h-[280px] sm:w-[350px] sm:h-[350px] md:w-[450px] md:h-[450px] lg:w-[550px] lg:h-[550px] xl:w-[600px] xl:h-[600px] wheel-container cursor-grab active:cursor-grabbing select-none"
          @mousedown="onMouseDown"
        >
          <!-- Pointer -->
          <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-2 sm:-translate-y-3 md:-translate-y-4 z-20">
            <div class="w-0 h-0 border-l-[12px] sm:border-l-[16px] md:border-l-[20px] border-l-transparent border-r-[12px] sm:border-r-[16px] md:border-r-[20px] border-r-transparent border-t-[24px] sm:border-t-[32px] md:border-t-[40px] border-t-red-500 drop-shadow-lg"></div>
          </div>

          <!-- Wheel Circle -->
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 p-4 shadow-2xl">
            <div class="relative w-full h-full rounded-full overflow-visible" :style="wheelStyle">
              <!-- Prize Segments -->
              <svg class="w-full h-full" viewBox="0 0 200 200">
                <defs>
                  <linearGradient id="legendaryGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#FBBF24;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#F97316;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#DC2626;stop-opacity:1" />
                  </linearGradient>
                  <clipPath
                    v-for="(prize, index) in prizes"
                    :key="`clip-${prize.id}`"
                    :id="`clip-${prize.id}`"
                  >
                    <circle cx="10" cy="10" r="10" />
                  </clipPath>
                  <filter id="textShadow" x="-50%" y="-50%" width="200%" height="200%">
                    <feDropShadow dx="0" dy="0.5" stdDeviation="0.5" flood-color="rgba(0,0,0,0.6)" />
                  </filter>
                  <filter id="legendaryTextGlow" x="-100%" y="-100%" width="300%" height="300%">
                    <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,0.9)" />
                  </filter>
                  <filter id="legendaryGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur in="SourceAlpha" stdDeviation="2" />
                    <feOffset dx="0" dy="0" result="offsetblur" />
                    <feFlood flood-color="#FFD700" flood-opacity="0.8" />
                    <feComposite in2="offsetblur" operator="in" />
                    <feMerge>
                      <feMergeNode />
                      <feMergeNode in="SourceGraphic" />
                    </feMerge>
                  </filter>
                </defs>
                <g v-for="(prize, index) in prizes" :key="prize.id">
                  <path
                    :d="describeSector(index, prizes.length)"
                    :fill="prize.price === 4500 ? 'url(#legendaryGradient)' : prize.color"
                    :stroke="prize.price === 4500 ? '#FFD700' : '#fff'"
                    :stroke-width="prize.price === 4500 ? '1.5' : '0.5'"
                    :filter="prize.price === 4500 ? 'url(#legendaryGlow)' : ''"
                  />
                  <!-- Image icon -->
                  <image
                    :href="prize.image"
                    :x="getImageX(index, prizes.length)"
                    :y="getImageY(index, prizes.length)"
                    :width="wheelConfig.fontSize * 2.4"
                    :height="wheelConfig.fontSize * 2.4"
                    :transform="getTextTransform(index, prizes.length)"
                    :clip-path="`url(#clip-${prize.id})`"
                  />
                  <!-- Text -->
                  <text
                    :x="getTextX(index, prizes.length)"
                    :y="getTextY(index, prizes.length)"
                    :transform="getTextTransform(index, prizes.length)"
                    :font-size="prize.price === 4500 ? wheelConfig.legendaryFontSize : wheelConfig.fontSize"
                    font-weight="bold"
                    :fill="prize.price === 4500 ? '#FFFEF0' : 'white'"
                    text-anchor="middle"
                    dominant-baseline="middle"
                    :filter="prize.price === 4500 ? 'url(#legendaryTextGlow)' : 'url(#textShadow)'"
                    :class="prize.price === 4500 ? 'legendary-text' : ''"
                  >
                    {{ prize.name }}
                  </text>
                </g>
              </svg>

              <!-- Prize Images Overlay -->
              <div class="absolute inset-0 pointer-events-none">
                <div
                  v-for="(prize, index) in prizes"
                  :key="`overlay-${prize.id}`"
                  class="absolute"
                  :style="getPrizeImageStyle(index, prizes.length)"
                >
                  <div
                    :class="[
                      'relative',
                      prize.price === 4500 ? 'legendary-icon' : ''
                    ]"
                  >
                    <img
                      :src="prize.image"
                      :alt="prize.name"
                      class="object-cover rounded-lg"
                      :style="{
                        width: `${wheelConfig.imageSize}px`,
                        height: `${wheelConfig.imageSize}px`,
                        boxShadow: prize.price === 4500
                          ? '0 0 15px rgba(255, 215, 0, 0.8), 0 0 30px rgba(255, 215, 0, 0.5), 0 2px 8px rgba(0, 0, 0, 0.3)'
                          : '0 2px 8px rgba(0, 0, 0, 0.3), 0 0 2px rgba(0, 0, 0, 0.2)'
                      }"
                    />
                    <div
                      v-if="prize.price === 4500"
                      class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2"
                      :style="{ fontSize: `${wheelConfig.imageSize * 0.5}px` }"
                    >
                      👑
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Center Button -->
          <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
            <button
              @click="spinWheel"
              :disabled="isSpinning"
              class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-red-500 to-pink-600 text-white font-bold text-xs sm:text-sm md:text-base lg:text-lg shadow-2xl transform transition-all duration-200 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed border-2 sm:border-3 md:border-4 border-white"
            >
              {{ isSpinning ? 'QUAY!' : 'QUAY' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Prize List -->
      <div class="bg-white/10 backdrop-blur-md rounded-xl sm:rounded-2xl p-3 sm:p-4 md:p-6 shadow-xl">
        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-2 text-center">Danh Sách Phần Thưởng</h2>
        <p class="text-center text-purple-200 text-[10px] sm:text-xs mb-3 sm:mb-4 opacity-70">
          * Giá vàng chỉ mang tính chất tham khảo
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3">
          <div
            v-for="prize in sortedPrizes"
            :key="prize.id"
            :class="[
              'rounded-md sm:rounded-lg p-2 sm:p-3 text-center transition-all duration-300',
              prize.price === 4500
                ? 'legendary-prize bg-gradient-to-br from-yellow-400 via-orange-500 to-red-600 shadow-2xl transform hover:scale-105'
                : 'bg-white/20 backdrop-blur-sm hover:bg-white/30'
            ]"
          >
            <div
              :class="[
                'w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 mx-auto mb-1 sm:mb-2 relative',
                prize.price === 4500 ? 'legendary-icon' : ''
              ]"
            >
              <img
                :src="prize.image"
                :alt="prize.name"
                class="w-full h-full object-cover rounded-md sm:rounded-lg shadow-md"
                :class="prize.price === 4500 ? 'border border-yellow-300 sm:border-2' : ''"
              />
              <div
                v-if="prize.price === 4500"
                class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 text-base sm:text-xl md:text-2xl"
              >
                👑
              </div>
            </div>
            <p :class="prize.price === 4500 ? 'text-white font-black text-[10px] sm:text-xs md:text-sm mb-0.5 sm:mb-1 drop-shadow-lg' : 'text-white font-semibold text-[10px] sm:text-xs md:text-sm mb-0.5 sm:mb-1'">
              {{ prize.name }}
            </p>
            <p :class="prize.price === 4500 ? 'text-white text-[9px] sm:text-[10px] md:text-xs font-black drop-shadow-lg' : 'text-yellow-300 text-[9px] sm:text-[10px] md:text-xs font-bold'">
              {{ prize.price }} vàng
            </p>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center mt-6 sm:mt-8 md:mt-12 pt-4 sm:pt-6 md:pt-8 border-t border-white/10">
        <p class="text-purple-200 text-xs sm:text-sm md:text-base px-4">
          Developed by <span class="text-white font-semibold">Mr. Giang</span> - <a href="mailto:nhgiang.dev@gmail.com" class="text-yellow-300 hover:text-yellow-200 transition-colors font-semibold break-all sm:break-normal">nhgiang.dev@gmail.com</a>
        </p>
      </div>
    </div>

    <!-- Result Modal -->
    <div
      v-if="showResult && currentPrize"
      class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4"
      @click="closeResult"
    >
      <div
        class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 max-w-sm sm:max-w-md w-full shadow-2xl transform animate-bounce-in"
        @click.stop
      >
        <div class="text-center">
          <div class="text-4xl sm:text-5xl md:text-6xl mb-3 sm:mb-4">🎉</div>
          <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2">Chúc Mừng!</h2>
          <p class="text-base sm:text-lg md:text-xl text-purple-100 mb-3 sm:mb-4">Bạn đã trúng:</p>

          <div class="bg-white/20 backdrop-blur-md rounded-xl sm:rounded-2xl p-4 sm:p-5 md:p-6 mb-4 sm:mb-5 md:mb-6">
            <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 mx-auto mb-3 sm:mb-4 relative">
              <img
                :src="currentPrize.image"
                :alt="currentPrize.name"
                class="w-full h-full object-cover rounded-lg sm:rounded-xl shadow-2xl"
                :class="currentPrize.price === 4500 ? 'border-2 sm:border-3 md:border-4 border-yellow-300' : ''"
              />
              <div
                v-if="currentPrize.price === 4500"
                class="absolute -top-2 -right-2 sm:-top-3 sm:-right-3 text-3xl sm:text-4xl md:text-5xl animate-bounce"
              >
                👑
              </div>
            </div>
            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-1 sm:mb-2">{{ currentPrize.name }}</h3>
            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-yellow-300">{{ currentPrize.price }} vàng</p>
          </div>

          <p class="text-purple-100 text-xs sm:text-sm mb-3 sm:mb-4 opacity-80 px-2">
            * Hiện tại đang thử nghiệm nên kết quả này sẽ không được lưu lại
          </p>

          <button
            @click="closeResult"
            class="bg-white text-purple-600 font-bold py-2 px-6 sm:py-2.5 sm:px-7 md:py-3 md:px-8 text-sm sm:text-base rounded-full hover:bg-purple-100 transition-colors shadow-lg"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
// Helper functions for SVG path calculation
export default {
  methods: {
    describeSector(index: number, total: number): string {
      const angle = 360 / total
      const startAngle = index * angle - 90
      const endAngle = startAngle + angle

      const x1 = 100 + 100 * Math.cos((startAngle * Math.PI) / 180)
      const y1 = 100 + 100 * Math.sin((startAngle * Math.PI) / 180)
      const x2 = 100 + 100 * Math.cos((endAngle * Math.PI) / 180)
      const y2 = 100 + 100 * Math.sin((endAngle * Math.PI) / 180)

      const largeArc = angle > 180 ? 1 : 0

      return `M 100 100 L ${x1} ${y1} A 100 100 0 ${largeArc} 1 ${x2} ${y2} Z`
    },

    getImageX(index: number, total: number): number {
      const angle = 360 / total
      const midAngle = index * angle + angle / 2 - 90
      // Đặt ảnh cách tâm 50 units (gần tâm hơn text)
      return 100 + 50 * Math.cos((midAngle * Math.PI) / 180) - 6
    },

    getImageY(index: number, total: number): number {
      const angle = 360 / total
      const midAngle = index * angle + angle / 2 - 90
      return 100 + 50 * Math.sin((midAngle * Math.PI) / 180) - 6
    },

    getTextX(index: number, total: number): number {
      const angle = 360 / total
      const midAngle = index * angle + angle / 2 - 90
      return 100 + 55 * Math.cos((midAngle * Math.PI) / 180)
    },

    getTextY(index: number, total: number): number {
      const angle = 360 / total
      const midAngle = index * angle + angle / 2 - 90
      return 100 + 55 * Math.sin((midAngle * Math.PI) / 180)
    },

    getTextTransform(index: number, total: number): string {
      const angle = 360 / total
      const midAngle = index * angle + angle / 2 - 90
      const x = this.getTextX(index, total)
      const y = this.getTextY(index, total)

      // Text thẳng hàng với tâm (radial)
      return `rotate(${midAngle}, ${x}, ${y})`
    }
  }
}
</script>

<style scoped>
@keyframes bounce-in {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-bounce-in {
  animation: bounce-in 0.5s ease-out;
}

@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(251, 191, 36, 0.5), 0 0 40px rgba(251, 191, 36, 0.3);
  }
  50% {
    box-shadow: 0 0 30px rgba(251, 191, 36, 0.8), 0 0 60px rgba(251, 191, 36, 0.5);
  }
}

@keyframes rotate-icon {
  0% {
    transform: rotate(0deg) scale(1);
  }
  25% {
    transform: rotate(10deg) scale(1.1);
  }
  50% {
    transform: rotate(-10deg) scale(1.2);
  }
  75% {
    transform: rotate(10deg) scale(1.1);
  }
  100% {
    transform: rotate(0deg) scale(1);
  }
}

.legendary-prize {
  animation: pulse-glow 2s ease-in-out infinite;
  position: relative;
  overflow: hidden;
}

.legendary-prize::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    45deg,
    transparent,
    rgba(255, 255, 255, 0.3),
    transparent
  );
  transform: rotate(45deg);
  animation: shine 1.5s ease-in-out infinite;
}

@keyframes shine {
  0% {
    transform: translateX(-100%) translateY(-100%) rotate(45deg);
  }
  100% {
    transform: translateX(100%) translateY(100%) rotate(45deg);
  }
}

.legendary-icon {
  animation: rotate-icon 3s ease-in-out infinite;
}

@keyframes golden-pulse {
  0%, 100% {
    filter: brightness(1) drop-shadow(0 0 5px rgba(255, 215, 0, 0.5));
  }
  50% {
    filter: brightness(1.3) drop-shadow(0 0 15px rgba(255, 215, 0, 0.9));
  }
}

.legendary-image-glow {
  animation: golden-pulse 2s ease-in-out infinite;
}

@keyframes text-glow-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.85;
  }
}

.legendary-text {
  animation: text-glow-pulse 1.2s ease-in-out infinite;
}
</style>
