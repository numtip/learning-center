<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const page = usePage()
const historyData = ref(page.props.historyData || [])

// ตั้งค่าเริ่มต้นให้การ์ดกลางเป็นตัวเลือกแรก หรือตัวกลางของ Array
const activeIndex = ref(Math.floor(historyData.value.length / 2))

const currentYear = computed(() => {
  return historyData.value[activeIndex.value]?.year || ''
})

const setActive = (index) => {
  activeIndex.value = index
}

const prev = () => {
  if (activeIndex.value > 0) activeIndex.value--
}

const next = () => {
  if (activeIndex.value < historyData.value.length - 1) activeIndex.value++
}

// --- ระบบปัดหน้าจอ (Touch Swipe) ---
const touchStartX = ref(0)
const touchEndX = ref(0)

const handleTouchStart = (e) => {
  touchStartX.value = e.changedTouches[0].screenX
}

const handleTouchEnd = (e) => {
  touchEndX.value = e.changedTouches[0].screenX
  const swipeDistance = touchStartX.value - touchEndX.value
  if (swipeDistance > 50) next()
  else if (swipeDistance < -50) prev()
}
</script>

<template>
  <PublicLayout>
    <Head title="ประวัติความเป็นมา" />

    <section class="history-premium-section">
      <div class="bg-glow-spot"></div>
      <div class="floating-year-bg">{{ currentYear }}</div>
      <div class="blob-1"></div>
      <div class="blob-2"></div>

      <div class="header-content">
        <h1 class="page-title">⭐ประวัติความเป็นมา⭐</h1>
        <div class="title-divider">
          <div class="dot"></div>
        </div>
        <p class="subtitle">เส้นทางแห่งเกียรติยศ และความภาคภูมิใจที่ส่งต่อจากรุ่นสู่รุ่น</p>
      </div>

      <div
        class="carousel-container"
        @touchstart="handleTouchStart"
        @touchend="handleTouchEnd"
      >
        <div class="progress-container">
          <div
            class="progress-bar"
            :style="{ width: historyData.length > 0 ? ((activeIndex + 1) / historyData.length) * 100 + '%' : '0%' }"
          ></div>
        </div>

        <button class="nav-arrow left" @click="prev" :disabled="activeIndex === 0">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <button class="nav-arrow right" @click="next" :disabled="activeIndex === historyData.length - 1">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>

        <div class="card-track">
          <div
            v-for="(item, index) in historyData"
            :key="item.id || index"
            class="history-card"
            :class="{ active: index === activeIndex }"
            :style="{
              '--offset': index - activeIndex,
              '--abs-offset': Math.abs(index - activeIndex),
              visibility: Math.abs(index - activeIndex) > 3 ? 'hidden' : 'visible',
            }"
            @click="setActive(index)"
          >
            <div class="card-inner">
              <div class="card-image-box">
                <img v-if="item.imageUrl" :src="item.imageUrl" :alt="item.title" />
                <div v-else class="img-placeholder"></div>
                <div class="year-float-badge">{{ item.year }}</div>
              </div>

              <div class="card-body">
                <h3>{{ item.title }}</h3>
                <div class="card-divider"></div>
                <p>{{ item.description }}</p>
              </div>
            </div>
            <div class="card-reflection"></div>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>

  <FooterFarm />
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;600;700&family=Playfair+Display:wght@900&display=swap');

.history-premium-section {
  margin-top: -80px;
  min-height: 100vh;
  background: #fdfdfb;
  padding: 150px 0 120px;
  overflow: hidden;
  text-align: center;
  font-family: "IBM Plex Sans Thai", sans-serif;
  position: relative;
}

/* --- Background Decor --- */
.bg-glow-spot {
  position: absolute;
  top: 20%;
  left: 50%;
  width: 60%;
  height: 50%;
  background: radial-gradient(circle, rgba(201, 162, 63, 0.08) 0%, transparent 70%);
  transform: translateX(-50%);
  z-index: 0;
}

.floating-year-bg {
  position: absolute;
  top: 55%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-family: 'Playfair Display', serif;
  font-size: 35vw;
  font-weight: 900;
  color: rgba(47, 93, 63, 0.04);
  z-index: 0;
  pointer-events: none;
  transition: all 1s cubic-bezier(0.23, 1, 0.32, 1);
  white-space: nowrap;
}

.blob-1, .blob-2 {
  position: absolute;
  width: 600px;
  height: 600px;
  border-radius: 50%;
  filter: blur(80px);
  z-index: 0;
  animation: float-blob 20s infinite alternate;
}
.blob-1 {
  top: -10%;
  right: -10%;
  background: radial-gradient(circle, rgba(201, 162, 63, 0.12) 0%, transparent 70%);
}
.blob-2 {
  bottom: -10%;
  left: -10%;
  background: radial-gradient(circle, rgba(47, 93, 63, 0.1) 0%, transparent 70%);
  animation-delay: -5s;
}

@keyframes float-blob {
  0% { transform: translate(0, 0) scale(1); }
  100% { transform: translate(80px, 40px) scale(1.15); }
}

/* --- Header --- */
.top-tag {
  display: block;
  letter-spacing: 4px;
  color: #c9a23f;
  font-weight: 700;
  font-size: 0.85rem;
  margin-bottom: 12px;
  text-transform: uppercase;
}

.page-title {
  font-size: 4rem;
  font-weight: 800;
  background: linear-gradient(135deg, #2f5d3f 0%, #1a3a27 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 0.5rem;

}
.pill-icon { color: #c9a23f; }
.title-divider {
  width: 140px;
  height: 2px;
  background: rgba(201, 162, 63, 0.3);
  margin: 2rem auto;
  position: relative;
}
.title-divider .dot {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 12px;
  height: 12px;
  background: #c9a23f;
  border-radius: 50%;
  border: 3px solid #fdfdfb;
}

.subtitle {
  color: #6b7280;
  font-size: 1.25rem;
  max-width: 600px;
  margin: 0 auto 4rem;
}

/* --- Carousel --- */
.carousel-container {
  position: relative;
  max-width: 1400px;
  margin: 0 auto;
  z-index: 10;
}

.progress-container {
  position: absolute;
  bottom: -70px;
  width: 280px;
  height: 4px;
  background: rgba(47, 93, 63, 0.1);
  border-radius: 10px;
  left: 50%;
  transform: translateX(-50%);
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  background: #c9a23f;
  transition: width 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.card-track {
  position: relative;
  height: 650px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  perspective: 2000px;
}

/* --- History Card --- */
.history-card {
  --card-w: 380px;
  --gap: 400px;
  position: absolute;
  width: var(--card-w);
  height: 560px;
  transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);

  transform:
    translateX(calc(var(--offset) * var(--gap)))
    translateZ(calc(var(--abs-offset) * -180px))
    rotateY(calc(var(--offset) * -20deg))
    scale(calc(1 - var(--abs-offset) * 0.15));

  opacity: calc(1 - var(--abs-offset) * 0.35);
  z-index: calc(100 - var(--abs-offset));
}

.card-inner {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(15px);
  border-radius: 40px;
  padding: 16px;
  height: 100%;
  box-shadow: 0 20px 50px rgba(0,0,0,0.06);
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(255, 255, 255, 0.8);
  position: relative;
  z-index: 2;
  transition: all 0.4s ease;
}

.history-card.active .card-inner {
  box-shadow: 0 40px 80px rgba(47, 93, 63, 0.12);
  border: 1px solid rgba(201, 162, 63, 0.2);
}

.card-image-box {
  width: 100%;
  height: 340px;
  border-radius: 32px;
  overflow: hidden;
  position: relative;
}

.card-image-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1.2s ease;
}

.history-card.active:hover img {
  transform: scale(1.1);
}

.year-float-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  background: white;
  padding: 8px 22px;
  border-radius: 50px;
  font-weight: 800;
  font-size: 1.1rem;
  color: #2f5d3f;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  transform: translateY(0);
  transition: all 0.4s ease;
}

.history-card.active .year-float-badge {
  background: #2f5d3f;
  color: #fff;
}

/* --- Card Content --- */
.card-body {
  padding: 24px 15px;
  text-align: left;
}

.card-body h3 {
  font-size: 1.6rem;
  color: #2f5d3f;
  margin-bottom: 10px;
  font-weight: 700;
}

.card-divider {
  width: 40px;
  height: 3px;
  background: #c9a23f;
  margin-bottom: 15px;
  transition: width 0.6s ease;
  border-radius: 10px;
}

.history-card.active .card-divider {
  width: 100px;
}

.card-body p {
  font-size: 1.05rem;
  color: #4b5563;
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* --- Nav Arrows --- */
.nav-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: white;
  border: 1px solid rgba(47, 93, 63, 0.1);
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  z-index: 100;
  color: #2f5d3f;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.nav-arrow:hover:not(:disabled) {
  background: #2f5d3f;
  color: white;
  transform: translateY(-50%) scale(1.15);
  box-shadow: 0 15px 35px rgba(47, 93, 63, 0.25);
}

.nav-arrow:disabled {
  opacity: 0.3;
  cursor: not-allowed;
  transform: translateY(-50%) scale(0.9);
}

.nav-arrow.left { left: 50px; }
.nav-arrow.right { right: 50px; }

/* --- Mobile --- */
@media (max-width: 768px) {
  .page-title { font-size: 2.8rem; }
  .subtitle { font-size: 1.1rem; padding: 0 20px; }
  .nav-arrow { display: none; }
  .history-card {
    --card-w: 300px;
    --gap: 320px;
    height: 500px;
  }
  .card-image-box { height: 260px; }
  .floating-year-bg { font-size: 55vw; top: 60%; }
  .card-track { height: 550px; }
  .history-premium-section { padding-top: 120px; }
}
</style>
