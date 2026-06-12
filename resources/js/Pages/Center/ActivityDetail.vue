<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { usePage, Head, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import FooterFarm from '@/Components/FooterFarm.vue'

const page = usePage()
const activity = computed(() => page.props.activity || {})

const images = computed(() => {
  if (!activity.value?.images) return []
  return activity.value.images.filter(Boolean)
})

const slider = ref(null)
let interval = null
const isScrolled = ref(false)

const scrollLeft = () => {
  slider.value.scrollBy({ left: -600, behavior: 'smooth' })
}

const scrollRight = () => {
  slider.value.scrollBy({ left: 600, behavior: 'smooth' })
}

const goToBooking = () => {
  router.visit('/service/booking')
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 300
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)

  interval = setInterval(() => {
    if (!slider.value) return

    slider.value.scrollBy({ left: 600, behavior: 'smooth' })

    if (
      slider.value.scrollLeft + slider.value.clientWidth >=
      slider.value.scrollWidth - 10
    ) {
      slider.value.scrollTo({ left: 0, behavior: 'smooth' })
    }
  }, 4500)
})

onUnmounted(() => {
  clearInterval(interval)
  window.removeEventListener('scroll', handleScroll)
})

const goBack = () => {
  window.history.back()
}
</script>

<template>
  <PublicLayout>
    <Head :title="activity.activity_name || 'Activity Detail'" />

    <div class="organic-detail-page">

      <div class="organic-bg-layer">
        <div class="floating-blob blob-1"></div>
        <div class="floating-blob blob-2"></div>
        <div class="floating-blob blob-3"></div>
        <div class="pattern-overlay"></div>
      </div>

     <div class="layout-container relative z-10 animate-fade-in-up">

  <div class="back-navigation mb-8">
    <button @click="goBack" class="back-btn-glass">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"/>
      </svg>
      <span>ย้อนกลับ</span>
    </button>
  </div>

        <div class="hero-header text-center">

          <h1 class="organic-title text-gradient">{{ activity.activity_name }}</h1>
          <p class="subtitle-text">สัมผัสประสบการณ์สุดพิเศษ เรียนรู้และลงมือทำจริงท่ามกลางธรรมชาติที่โอบล้อม</p>
        </div>

        <div class="gallery-section stagger-1" v-if="images.length">
          <button class="nav-btn left glass-btn" @click="scrollLeft">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          </button>

          <div class="gallery-track snap-x" ref="slider">
            <div v-for="(img, i) in images" :key="i" class="image-card snap-center">
              <img :src="img" class="gallery-img" />
              <div class="image-overlay"></div>
            </div>
          </div>

          <button class="nav-btn right glass-btn" @click="scrollRight">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>

        <div class="content-bento-grid stagger-2">

          <div class="feature-panel-glass premium-glass hover-float">
            <div class="panel-deco-circle pulse-slow"></div>
            <div class="relative z-10">
              <div class="panel-header">
                <div class="icon-box-nature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <h2>รายละเอียดกิจกรรม</h2>
              </div>
              <div class="panel-body prose-nature">
                <p>{{ activity.description || 'ยังไม่มีรายละเอียดสำหรับกิจกรรมนี้' }}</p>
              </div>
            </div>
          </div>

          <div class="info-section">
             <div class="section-label">
                <span class="label-line"></span>
                <h3>ข้อมูลเบื้องต้น</h3>
             </div>

            <div class="info-grid-2x2">
              <div class="floating-card 3d-hover">
                <div class="card-icon icon-loc">📍</div>
                <div class="card-content">
                  <span class="card-label">สถานที่</span>
                  <span class="card-value">{{ activity.location_name || '-' }}</span>
                </div>
              </div>

              <div class="floating-card 3d-hover">
                <div class="card-icon icon-time">🕒</div>
                <div class="card-content">
                  <span class="card-label">ช่วงเวลาแนะนำ</span>
                  <span class="card-value">{{ activity.recommended_time || '-' }}</span>
                </div>
              </div>

              <div class="floating-card 3d-hover">
                <div class="card-icon icon-dur">⏱</div>
                <div class="card-content">
                  <span class="card-label">ระยะเวลา</span>
                  <span class="card-value">{{ activity.duration || '-' }}</span>
                </div>
              </div>

              <div class="floating-card 3d-hover">
                <div class="card-icon icon-user">👥</div>
                <div class="card-content">
                  <span class="card-label">ผู้เข้าร่วม</span>
                  <span class="card-value">{{ activity.participants_count || '-' }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="nature-cta-banner stagger-3">
          <div class="cta-content">
            <div class="cta-text">
              <h3>พร้อมเริ่มต้นประสบการณ์แล้วหรือยัง?</h3>
              <p>จองคิวล่วงหน้า เพื่อรับบริการและการดูแลที่ดียิ่งขึ้น</p>
            </div>
            <div class="cta-action">
              <button class="btn-god-tier" @click="goToBooking">
                <span class="btn-text">🔥 จองเลยวันนี้</span>
                <span class="shimmer"></span>
              </button>
            </div>
          </div>
          <svg class="cta-leaf-deco pulse-slow" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="currentColor"><path d="M21.41 11.41l-8.83-8.83a1.99 1.99 0 0 0-2.83 0L.93 11.41a1.99 1.99 0 0 0 0 2.83l8.83 8.83a1.99 1.99 0 0 0 2.83 0l9.66-9.66a1.99 1.99 0 0 0-.84-2zM10 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path></svg>
        </div>

      </div>
    </div>

    <div :class="['floating-booking-bar', { 'is-visible': isScrolled }]">
      <div class="bar-container">
        <div class="bar-info">
          <span class="bar-title">{{ activity.activity_name || 'จองกิจกรรมนี้' }}</span>
          <span class="bar-subtitle">พร้อมเปิดประสบการณ์ใหม่ไปกับเรา</span>
        </div>
        <button class="btn-god-tier compact" @click="goToBooking">
          <span class="btn-text">จองเลย 🚀</span>
          <span class="shimmer"></span>
        </button>
      </div>
    </div>

    <FooterFarm />
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Mitr:wght@400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap');

:root {
  --c-emerald-primary: #10b981;
  --c-emerald-dark: #065f46;
  --c-text-dark: #0c2e25;
  --c-text-muted: #4b6b63;
}

* { font-family: 'Prompt', sans-serif; box-sizing: border-box; }

.organic-detail-page {
  background: linear-gradient(180deg, #f0fdf4 0%, #ecfdf5 50%, #f8fafc 100%);
  min-height: 100vh;
  color: var(--c-text-dark);
  position: relative;
  overflow: hidden;
  padding-top: 60px;
  padding-bottom: 120px; /* เพิ่มพื้นที่ให้ Floating Bar */
  margin-top: -50px;
}

/* --- 🌸 Background & Animations --- */
.organic-bg-layer { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
.floating-blob { position: absolute; border-radius: 50%; filter: blur(90px); opacity: 0.5; mix-blend-mode: multiply; animation: floatBlob 20s infinite alternate ease-in-out; }
.blob-1 { top: -10%; left: -10%; width: 600px; height: 600px; background: #6ee7b7; }
.blob-2 { top: 40%; right: -20%; width: 500px; height: 500px; background: #a7f3d0; animation-delay: -5s; }
.blob-3 { bottom: -10%; left: 20%; width: 700px; height: 700px; background: #bef264; animation-delay: -10s; }

@keyframes floatBlob { 0% { transform: translate(0, 0) scale(1) rotate(0deg); } 100% { transform: translate(15%, 15%) scale(1.15) rotate(15deg); } }
.pattern-overlay { position: absolute; inset: 0; background-image: radial-gradient(rgba(6, 95, 70, 0.08) 2px, transparent 2px); background-size: 30px 30px; opacity: 0.6; }

/* Entrance Animations */
.animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
.stagger-1 { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
.stagger-2 { animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0; }
.stagger-3 { animation: fadeInUp 0.8s ease-out 0.6s forwards; opacity: 0; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

.layout-container { max-width: 1350px; margin: 0 auto; padding: 0 24px; }

/* --- Header --- */
.hero-header { margin-bottom: 60px; }
.badge-nature { display: inline-flex; align-items: center; padding: 8px 24px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border-radius: 30px; border: 1px solid rgba(16, 185, 129, 0.3); color: var(--c-emerald-dark); font-weight: 700; font-size: 14px; letter-spacing: 1.5px; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15); margin-bottom: 24px; }
.animate-bounce-slow { animation: bounceSlow 3s infinite; }
@keyframes bounceSlow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

.text-gradient { background: linear-gradient(135deg, #065f46, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.organic-title { font-family: 'Mitr', sans-serif; font-size: clamp(40px, 5vw, 64px); font-weight: 700; margin: 0 0 16px 0; line-height: 1.2; text-shadow: 0 10px 30px rgba(16, 185, 129, 0.1); }
.subtitle-text { font-size: 20px; color: var(--c-text-muted); max-width: 750px; margin: 0 auto; font-weight: 300; }

/* --- Gallery --- */
.gallery-section { position: relative; margin-bottom: 80px; }
.gallery-track { display: flex; gap: 24px; overflow-x: auto; padding: 20px 10px; scroll-behavior: smooth; scrollbar-width: none; scroll-snap-type: x mandatory; }
.gallery-track::-webkit-scrollbar { display: none; }
.image-card { position: relative; flex-shrink: 0; border-radius: 30px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); border: 4px solid #ffffff; scroll-snap-align: center; }
.image-card:hover { transform: translateY(-15px) scale(1.03); box-shadow: 0 30px 60px rgba(16, 185, 129, 0.25); z-index: 10; }
.gallery-img { height: 450px; width: 700px; object-fit: cover; transition: transform 0.5s; }
.image-card:hover .gallery-img { transform: scale(1.05); }
.image-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.3), transparent 50%); opacity: 0; transition: opacity 0.3s; }
.image-card:hover .image-overlay { opacity: 1; }

.glass-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); border: 2px solid #fff; color: var(--c-emerald-primary); display: flex; justify-content: center; align-items: center; cursor: pointer; box-shadow: 0 15px 35px rgba(0,0,0,0.15); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 5; }
.glass-btn:hover { background: var(--c-emerald-primary); color: white; transform: translateY(-50%) scale(1.15); box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3); }
.nav-btn.left { left: -32px; } .nav-btn.right { right: -32px; }

/* --- Bento Grid --- */
.content-bento-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 40px; margin-bottom: 80px; align-items: start; }

.premium-glass { background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(240,253,244,0.85)); backdrop-filter: blur(24px); border-radius: 36px; padding: 48px; border: 1px solid rgba(255,255,255,0.8); box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15); overflow: hidden; transition: transform 0.4s; }
.hover-float:hover { transform: translateY(-5px); }
.panel-deco-circle { position: absolute; top: -20%; right: -10%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(52, 211, 153, 0.15), transparent 70%); border-radius: 50%; pointer-events: none; }
.pulse-slow { animation: pulse 4s infinite alternate; }
@keyframes pulse { 0% { opacity: 0.6; transform: scale(1); } 100% { opacity: 1; transform: scale(1.1); } }

.panel-header { display: flex; align-items: center; gap: 20px; margin-bottom: 32px; }
.icon-box-nature { width: 60px; height: 60px; background: linear-gradient(135deg, #34d399, #059669); border-radius: 20px; display: flex; justify-content: center; align-items: center; color: white; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); transform: rotate(-5deg); transition: transform 0.3s; }
.premium-glass:hover .icon-box-nature { transform: rotate(0deg) scale(1.1); }
.panel-header h2 { font-family: 'Mitr', sans-serif; font-size: 30px; color: var(--c-emerald-dark); margin: 0; font-weight: 700; }
.prose-nature p { font-size: 18px; line-height: 1.8; color: var(--c-text-muted); white-space: pre-line; }

/* Right Panel Info */
.info-section { display: flex; flex-direction: column; gap: 24px; }
.section-label { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.label-line { width: 5px; height: 28px; background: linear-gradient(to bottom, #f59e0b, #fbbf24); border-radius: 5px; }
.section-label h3 { font-family: 'Mitr', sans-serif; font-size: 24px; color: var(--c-text-dark); margin: 0; font-weight: 700; }

.info-grid-2x2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.floating-card { background: rgba(255,255,255,0.95); padding: 28px 24px; border-radius: 28px; border: 1px solid rgba(209, 250, 229, 0.8); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 16px; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: default; }
.3d-hover:hover { transform: translateY(-10px) rotateX(5deg) rotateY(-5deg); box-shadow: 15px 25px 40px -10px rgba(16, 185, 129, 0.2); border-color: #6ee7b7; }
.card-icon { width: 56px; height: 56px; border-radius: 20px; display: flex; justify-content: center; align-items: center; font-size: 26px; transition: all 0.3s; }
.3d-hover:hover .card-icon { transform: scale(1.15) rotate(10deg); border-radius: 50%; }
.icon-loc { background: #ecfdf5; color: #10b981; }
.icon-time { background: #fff7ed; color: #f59e0b; }
.icon-dur { background: #f0f9ff; color: #0ea5e9; }
.icon-user { background: #f5f3ff; color: #8b5cf6; }
.card-label { font-size: 14px; color: var(--c-text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.card-value { font-size: 18px; color: var(--c-text-dark); font-weight: 700; line-height: 1.4; }

/* --- Bottom CTA --- */
.nature-cta-banner { position: relative; background: linear-gradient(135deg, #064e3b, #047857, #065f46); border-radius: 40px; padding: 60px; display: flex; justify-content: space-between; align-items: center; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(4, 120, 87, 0.6); }
.cta-content { position: relative; z-index: 2; display: flex; width: 100%; justify-content: space-between; align-items: center; gap: 40px; }
.cta-text h3 { font-family: 'Mitr', sans-serif; font-size: 36px; color: #ffffff; margin: 0 0 12px 0; font-weight: 600; text-shadow: 0 4px 10px rgba(0,0,0,0.2); }
.cta-text p { font-size: 20px; color: #a7f3d0; margin: 0; font-weight: 300; }
.cta-leaf-deco { position: absolute; bottom: -50px; right: -20px; color: rgba(255,255,255,0.06); transform: rotate(-15deg); pointer-events: none; }

/* --- 🌟 God-Tier Button --- */
.btn-god-tier {
  position: relative;
  background: linear-gradient(135deg, #f59e0b, #ea580c, #f59e0b);
  background-size: 200% auto;
  color: #fff;
  border: none;
  padding: 20px 48px;
  border-radius: 100px;
  font-size: 22px;
  font-weight: 700;
  cursor: pointer;
  overflow: hidden;
  box-shadow: 0 15px 35px rgba(234, 88, 12, 0.4), inset 0 2px 0 rgba(255,255,255,0.3);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  display: flex;
  align-items: center;
  gap: 12px;
  animation: gradientShift 3s linear infinite;
}
.btn-god-tier:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 25px 45px rgba(234, 88, 12, 0.5), inset 0 2px 0 rgba(255,255,255,0.5); }
.btn-text { position: relative; z-index: 2; letter-spacing: 0.5px; }

/* Shimmer Effect */
.shimmer { position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.6), transparent); transform: skewX(-25deg); animation: shimmerFlow 3s infinite; z-index: 1; }
@keyframes shimmerFlow { 0% { left: -100%; } 20% { left: 200%; } 100% { left: 200%; } }
@keyframes gradientShift { 0% { background-position: 0% center; } 100% { background-position: 200% center; } }

/* --- 🚀 Floating Booking Bar --- */
.floating-booking-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(20px);
  border-top: 1px solid rgba(255,255,255,0.5);
  box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.08);
  padding: 20px 0;
  z-index: 50;
  transform: translateY(120%);
  transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.floating-booking-bar.is-visible { transform: translateY(0); }
.bar-container { max-width: 1350px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; align-items: center; }
.bar-info { display: flex; flex-direction: column; }
.bar-title { font-family: 'Mitr', sans-serif; font-size: 22px; font-weight: 700; color: var(--c-text-dark); }
.bar-subtitle { font-size: 15px; color: var(--c-emerald-primary); font-weight: 500; }
.btn-god-tier.compact { padding: 14px 36px; font-size: 18px; }

.back-navigation {
  display: flex;
  justify-content: flex-start;
  margin-top: 20px;
}

.back-btn-glass {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 16px;
  color: var(--c-emerald-dark);
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.back-btn-glass:hover {
  background: white;
  transform: translateX(-5px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
  color: var(--c-emerald-primary);
}

.back-btn-glass svg {
  transition: transform 0.3s;
}

.back-btn-glass:hover svg {
  transform: scale(1.1);
}

/* ปรับระยะขอบสำหรับมือถือ */
@media (max-width: 768px) {
  .back-navigation {
    margin-bottom: 20px;
  }
}
/* Responsive */
@media (max-width: 1024px) {
  .content-bento-grid { grid-template-columns: 1fr; gap: 40px; }
  .gallery-img { width: 550px; height: 380px; }
  .bar-subtitle { display: none; }
}

@media (max-width: 768px) {
  .nature-cta-banner { padding: 40px 30px; flex-direction: column; text-align: center; border-radius: 32px; }
  .cta-content { flex-direction: column; gap: 24px; }
  .nav-btn { display: none; }
  .gallery-img { width: 340px; height: 280px; }
  .info-grid-2x2 { grid-template-columns: 1fr; }
  .organic-title { font-size: 36px; }
  .premium-glass { padding: 32px 24px; }
  .bar-title { font-size: 18px; }
  .btn-god-tier { width: 100%; justify-content: center; }
  .bar-container { gap: 16px; flex-direction: column; }
  .floating-booking-bar { padding: 16px 0; }
  .btn-god-tier.compact { width: 100%; }
}
</style>
