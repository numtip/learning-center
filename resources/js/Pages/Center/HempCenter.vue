<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import FooterFarm from '@/Components/FooterFarm.vue'

const page = usePage()
const activities = page.props.activities ?? []
const detail = page.props.detail ?? {}
const fees = page.props.fees ?? []
const pins = ref(page.props.pins ?? [])

const centerName = ref(detail.center_name ?? '')
const centerDescription = ref(detail.center_description ?? '')

const activePin = ref(null)

const openPin = (pin) => {
  activePin.value = pin
}

const closePin = () => {
  activePin.value = null
}

const goToDetail = (pin) => {
  router.visit(`/map/${pin.center_id}/activities/${pin.activity_id}`)
}

const goToActivity = (act) => {
  const centerId = act.center_id || detail.center_id || detail.id || 1;
  const activityId = act.id || act.activity_id;
  router.visit(`/map/${centerId}/activities/${activityId}`)
}
</script>

<template>
  <PublicLayout>
    <Head title="ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่" />

    <div class="premium-eco-page" @click="closePin">

      <div class="ambient-background">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="mesh-overlay"></div>
      </div>
<section class="hero-header">
        <div class="floating-sparkle fs-1">✨</div>
        <div class="floating-sparkle fs-2">🌟</div>
        <div class="floating-sparkle fs-3">✨</div>
        <div class="floating-sparkle fs-4">⭐</div>

        <div class="hero-content">
          <div class="badge-wrapper">
          </div>
          <h1 class="center-title text-shimmer">{{ centerName }}</h1>
        </div>
      </section>

      <section class="map-showcase">
        <div class="map-container">
          <div class="map-aura-glow"></div>

          <div class="map-glass-frame">
            <img v-if="detail.map_image" :src="detail.map_image" class="map-image" alt="Map" />
            <div v-else class="no-map">
              <div class="icon-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
              </div>
              <p>อยู่ระหว่างการจัดทำแผนที่</p>
            </div>

            <div
              v-for="(pin, index) in pins"
              :key="pin.id"
              class="map-pin-wrapper"
              :style="{ left: pin.x_percent + '%', top: pin.y_percent + '%' }"
              @click.stop="openPin(pin)"
            >
              <div class="radar-ring radar-1"></div>
              <div class="radar-ring radar-2"></div>

              <div class="pin-marker" :class="{ 'is-active': activePin === pin }">
                <div class="pin-core">{{ index + 1 }}</div>
              </div>

              <transition name="glass-pop">
                <div v-if="activePin === pin" class="pin-popup-premium" @click.stop>
                  <div class="popup-glare"></div>
                  <div class="popup-header">
                    <span class="station-tag">
                      <span class="dot-indicator"></span> ฐานที่ {{ index + 1 }}
                    </span>
                    <button class="btn-close-blur" @click="closePin">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                  </div>
                  <div class="popup-body">
                    <h3 class="popup-title">{{ pin.title }}</h3>
                    <p class="popup-desc">{{ pin.description }}</p>
                  </div>

                  <button class="btn-hyper-glow" @click="goToDetail(pin)">
                    <span class="btn-text">รายละเอียดเพิ่มเติม</span>
                    <span class="btn-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                  </button>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </section>

      <section class="content-section">
        <div class="grid-layout">

          <div class="grid-panel panel-large">
            <div class="section-heading">
              <div class="heading-badge text-amber">ACTIVITIES & TIMETABLE</div>
              <h2 class="main-title">ตารางกิจกรรมการเรียนรู้</h2>
            </div>

            <div class="premium-data-list">
              <div v-for="(act, i) in activities" :key="act.id" class="activity-super-card group">
                <div class="card-glow"></div>

                <div class="act-card-inner">
                  <div class="col-num">
                    <div class="number-hexagon">
                      <span>{{ i + 1 }}</span>
                    </div>
                  </div>

                  <div class="col-main">
                    <h4 class="act-name">{{ act.activity_name }}</h4>
                    <p class="act-subtext" v-if="act.description">รายละเอียด: {{ act.description }}</p>

                    <div class="act-info-grid">
                      <div class="info-pill" v-if="act.location_name">
                        <span class="pill-icon">📍</span>
                        <span class="pill-text">สถานที่: {{ act.location_name }}</span>
                      </div>
                      <div class="info-pill" v-if="act.recommended_time">
                        <span class="pill-icon">🕒</span>
                        <span class="pill-text">ช่วงเวลา: {{ act.recommended_time }}</span>
                      </div>
                      <div class="info-pill" v-if="act.duration">
                        <span class="pill-icon">⏱</span>
                        <span class="pill-text">ระยะเวลา: {{ act.duration }}</span>
                      </div>
                      <div class="info-pill" v-if="act.participants_count">
                        <span class="pill-icon">👥</span>
                        <span class="pill-text">ผู้เข้าร่วม: {{ act.participants_count }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="col-action">
                    <button class="btn-detail-link" @click="goToActivity(act)">
                      <span>ดูรายละเอียด</span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="!activities || activities.length === 0" class="empty-state-premium">
                <div class="empty-icon">📁</div>
                <p>ยังไม่มีข้อมูลกิจกรรมในขณะนี้</p>
              </div>
            </div>
          </div>

          <div class="grid-panel panel-small">
            <div class="section-heading">
              <div class="heading-badge text-emerald">SHOWCASE</div>
              <h2 class="main-title">วิดีโอแนะนำ</h2>
            </div>
            <div class="video-monitor-frame">
              <div class="monitor-glow"></div>
              <div class="video-wrapper">
                <iframe
                  v-if="detail.video_url"
                  :src="detail.video_url.replace('youtu.be/', 'www.youtube.com/embed/').replace('watch?v=', 'embed/')"
                  frameborder="0"
                  allowfullscreen
                ></iframe>
                <div v-else class="empty-video">
                  <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>
                  <p>ไม่มีวิดีโอแนะนำ</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

      <section class="fees-section">
        <div class="container-center">
          <div class="section-heading text-center">
            <div class="heading-badge text-amber mx-auto">TICKETS & FEES</div>
            <h2 class="main-title text-gradient-gold">อัตราค่าธรรมเนียมการเข้าชม</h2>
            <div class="heading-divider"></div>
            <p class="fee-subtitle">* อัตราค่าบริการนี้เป็นราคา ต่อคน / ต่อวัน / ต่อศูนย์การเรียนรู้</p>
          </div>

          <div class="premium-data-list max-w-1000 mx-auto">
            <template v-for="fee in fees" :key="fee.id">
              <div class="fee-row-card">
                <div class="fee-icon-box">
                  <div class="icon-bg-glow"></div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M7 15h0M2 9.5h20"></path></svg>
                </div>
                <div class="fee-content">
                  <h4 class="fee-name">{{ fee.title }}</h4>
                  <ul class="fee-detail-list">
                    <li v-if="fee.detail1">
                      <span class="detail-text">{{ fee.detail1 }}</span>
                      <span class="price-badge" v-if="fee.price1">{{ fee.price1 }} ฿</span>
                    </li>
                    <li v-if="fee.detail2">
                      <span class="detail-text">{{ fee.detail2 }}</span>
                      <span class="price-badge" v-if="fee.price2">{{ fee.price2 }} ฿</span>
                    </li>
                    <li v-if="fee.detail3">
                      <span class="detail-text">{{ fee.detail3 }}</span>
                      <span class="price-badge" v-if="fee.price3">{{ fee.price3 }} ฿</span>
                    </li>
                    <li v-if="fee.detail4">
                      <span class="detail-text">{{ fee.detail4 }}</span>
                      <span class="price-badge" v-if="fee.price4">{{ fee.price4 }} ฿</span>
                    </li>
                  </ul>
                </div>
              </div>
            </template>
            <div v-if="!fees || fees.length === 0" class="empty-state-premium" style="margin-top: 20px;">
              <p>ยังไม่มีข้อมูลอัตราค่าธรรมเนียม</p>
            </div>

            <div class="fee-reference">
              <div class="ref-content">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>อ้างอิงตามประกาศมหาวิทยาลัยแม่โจ้ เรื่อง อัตราค่าธรรมเนียมการเข้าชมศูนย์การเรียนรู้</span>
              </div>
                <a href="https://drive.google.com/drive/folders/1L8L_7HwLcfr3mcnuzQeSUVud3lIUpqkU?usp=drive_link" target="_blank" class="ref-link">
                <span>อ่านประกาศฉบับเต็ม</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
              </a>
            </div>

          </div>
        </div>
      </section>
    </div>

    <FooterFarm />
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Mitr:wght@400;500;600&family=Prompt:wght@300;400;500;600;700&display=swap');

:root {
  --color-primary: #059669;
  --color-secondary: #0ea5e9;
  --color-accent: #f59e0b;
  --color-neon-pin: #ff0055;
  --bg-base: #f0fdf4;
  --glass-bg: rgba(255, 255, 255, 0.75);
  --glass-border: rgba(255, 255, 255, 0.6);
  --text-main: #0f172a;
  --text-muted: #475569;
}

* { font-family: 'Prompt', sans-serif; box-sizing: border-box; }

.premium-eco-page { background-color: var(--bg-base); min-height: 100vh; color: var(--text-main); margin-top: -25px; position: relative; overflow: hidden; }

.ambient-background { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
.orb { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.5; animation: floatOrb 15s infinite alternate cubic-bezier(0.4, 0, 0.2, 1); }
.orb-1 { top: -10%; left: -10%; width: 60vw; height: 60vw; background: #6ee7b7; mix-blend-mode: multiply; }
.orb-2 { top: 30%; right: -20%; width: 50vw; height: 50vw; background: #fde047; mix-blend-mode: multiply; animation-delay: -5s; }
.orb-3 { bottom: -20%; left: 10%; width: 70vw; height: 70vw; background: #7dd3fc; mix-blend-mode: multiply; animation-delay: -10s; }
.mesh-overlay { position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 40px 40px; opacity: 0.3; }

@keyframes floatOrb { 0% { transform: translate(0, 0) scale(1) rotate(0deg); } 100% { transform: translate(5vw, 5vh) scale(1.1) rotate(10deg); } }

.hero-header { position: relative; z-index: 10; padding: 60px 24px 60px; display: flex; justify-content: center; text-align: center; }
.hero-content { max-width: 900px; display: flex; flex-direction: column; align-items: center; }
.badge-wrapper { margin-bottom: 28px; }
.badge-glowing { display: inline-flex; align-items: center; gap: 8px; padding: 8px 24px; border-radius: 30px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); color: #b45309; font-weight: 700; font-size: 14px; letter-spacing: 2px; box-shadow: 0 4px 20px rgba(245, 158, 11, 0.2); border: 1px solid rgba(253, 230, 138, 0.8); }
.sparkle { animation: pulseSparkle 2s infinite; }
@keyframes pulseSparkle { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.8); } }
.center-title { font-family: 'Mitr', sans-serif; font-size: clamp(38px, 6vw, 64px); font-weight: 600; margin: 0 0 24px 0; line-height: 1.2; background: linear-gradient(135deg, #065f46 0%, #0284c7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0px 15px 30px rgba(6, 95, 70, 0.15); }
.center-description { font-size: 18px; color: var(--text-muted); line-height: 1.8; font-weight: 400; max-width: 750px; margin: 0 auto; text-wrap: balance; }

.map-showcase { padding: 0 24px 80px; position: relative; z-index: 10; display: flex; justify-content: center; }
.map-container { width: 100%; max-width: 1350px; position: relative; }

.map-aura-glow {
  position: absolute; inset: -5px; z-index: -1; border-radius: 46px;
  background: linear-gradient(135deg, #059669, #0ea5e9, #f59e0b, #059669);
  background-size: 300% 300%; filter: blur(15px); opacity: 0.8;
  animation: auraGlow 6s ease infinite;
}
@keyframes auraGlow { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

.map-glass-frame {
  position: relative; border-radius: 40px; background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(20px); padding: 18px;
  box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1), inset 0 0 0 2px rgba(255,255,255,0.9);
}
.map-image { width: 100%; border-radius: 28px; display: block; object-fit: cover; filter: contrast(1.05) saturate(1.1); }
.no-map { padding: 150px 20px; text-align: center; background: rgba(226, 232, 240, 0.5); border-radius: 28px; color: var(--text-muted); font-size: 20px; display: flex; flex-direction: column; align-items: center; gap: 16px; }

.map-pin-wrapper { position: absolute; width: 0; height: 0; z-index: 20; }
.radar-ring { position: absolute; top: -20px; left: -20px; width: 40px; height: 40px; background: var(--color-neon-pin); border-radius: 50%; opacity: 0; pointer-events: none; }
.radar-1 { animation: radar 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite; }
.radar-2 { animation: radar 2s cubic-bezier(0.215, 0.61, 0.355, 1) 1s infinite; }
@keyframes radar { 0% { transform: scale(0.5); opacity: 0.8; } 100% { transform: scale(3.5); opacity: 0; } }

.pin-marker {
  position: absolute; top: -45px; left: -20px; width: 40px; height: 40px;
  background: linear-gradient(135deg, #ff0077, #d9004c); border-radius: 50% 50% 50% 0; transform: rotate(-45deg);
  display: flex; justify-content: center; align-items: center; cursor: pointer;
  box-shadow: 0 10px 25px rgba(255,0,85,0.6), inset 0 0 10px rgba(255,255,255,0.5);
  border: 3px solid #ffffff; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.pin-marker.is-active, .pin-marker:hover {
  transform: rotate(-45deg) scale(1.3) translateY(-5px); z-index: 30;
  box-shadow: 0 15px 35px rgba(255, 0, 85, 0.8), inset 0 0 15px rgba(255,255,255,0.8); border-color: #ffe4e6;
}
.pin-core { transform: rotate(45deg); color: #fff; font-weight: 800; font-size: 16px; font-family: 'Mitr', sans-serif; }

.pin-popup-premium {
  position: absolute; bottom: 75px; left: -150px; width: 320px;
  background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(24px) saturate(150%);
  border: 1px solid rgba(255, 255, 255, 1); border-radius: 28px; padding: 24px; box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
  z-index: 50; cursor: default; overflow: hidden;
}
.pin-popup-premium::after { content: ''; position: absolute; bottom: -14px; left: 50%; transform: translateX(-50%); border-width: 14px 14px 0; border-style: solid; border-color: rgba(255, 255, 255, 0.9) transparent transparent transparent; }
.popup-glare { position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent); transform: skewX(-20deg); animation: glareRun 3s infinite; pointer-events: none; }
@keyframes glareRun { 0% { left: -100%; } 20%, 100% { left: 200%; } }
.popup-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.station-tag { display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 0, 85, 0.1); color: #e11d48; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 20px; border: 1px solid rgba(255, 0, 85, 0.2); }
.dot-indicator { width: 8px; height: 8px; background: #e11d48; border-radius: 50%; animation: pulseOpacity 1.5s infinite; }
@keyframes pulseOpacity { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
.btn-close-blur { background: rgba(15, 23, 42, 0.05); border: none; color: #475569; width: 32px; height: 32px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: all 0.3s; padding: 0; }
.btn-close-blur:hover { background: #fee2e2; color: #e11d48; transform: rotate(90deg) scale(1.1); }
.popup-title { font-family: 'Mitr', sans-serif; font-size: 22px; color: var(--text-main); margin: 0 0 10px 0; font-weight: 600; line-height: 1.3; }
.popup-desc { font-size: 14.5px; color: var(--text-muted); line-height: 1.6; margin: 0 0 24px 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

.btn-hyper-glow { position: relative; width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--color-primary), #0284c7); color: #102e05; border: none; padding: 14px; border-radius: 16px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3); overflow: hidden; }
.btn-hyper-glow:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 30px rgba(5, 150, 105, 0.4); }

.glass-pop-enter-active { animation: glass-bounce-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
.glass-pop-leave-active { animation: glass-bounce-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) reverse; }
@keyframes glass-bounce-in { 0% { opacity: 0; transform: scale(0.8) translateY(30px) rotateX(-20deg); } 100% { opacity: 1; transform: scale(1) translateY(0) rotateX(0deg); } }

.content-section { padding: 40px 24px 80px; position: relative; z-index: 10; }
.grid-layout { display: grid; grid-template-columns: 1.35fr 0.65fr; gap: 40px; max-width: 1400px; margin: 0 auto; align-items: start; }

.section-heading { margin-bottom: 30px; }
.section-heading.text-center { text-align: center; display: flex; flex-direction: column; align-items: center; }
.heading-badge { display: inline-block; font-size: 12px; font-weight: 800; letter-spacing: 3px; margin-bottom: 12px; text-transform: uppercase; padding: 4px 12px; border-radius: 8px; background: rgba(255,255,255,0.5); backdrop-filter: blur(4px); }
.text-amber { color: #d97706; border: 1px solid rgba(217, 119, 6, 0.2); }
.text-emerald { color: var(--color-primary); border: 1px solid rgba(5, 150, 105, 0.2); }
.mx-auto { margin-left: auto; margin-right: auto; }
.main-title { font-family: 'Mitr', sans-serif; font-size: 32px; color: var(--text-main); font-weight: 600; margin: 0; line-height: 1.3; }
.text-gradient-gold { background: linear-gradient(135deg, #b45309, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.heading-divider { width: 60px; height: 5px; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 5px; margin-top: 20px; }

.premium-data-list { display: flex; flex-direction: column; gap: 20px; }
.activity-super-card {
  position: relative; background: var(--glass-bg); backdrop-filter: blur(12px);
  border-radius: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  border: 1px solid var(--glass-border); transition: all 0.4s; overflow: hidden;
}
.card-glow { position: absolute; inset: 0; background: radial-gradient(circle at -20% 50%, rgba(5,150,105,0.1), transparent 70%); opacity: 0; transition: opacity 0.4s; pointer-events: none; }
.activity-super-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -10px rgba(5, 150, 105, 0.15); background: #ffffff; }
.activity-super-card:hover .card-glow { opacity: 1; }

.act-card-inner { display: flex; align-items: stretch; padding: 24px; gap: 20px; flex-wrap: wrap; }

.col-num { display: flex; align-items: flex-start; justify-content: center; }
.number-hexagon {
  width: 56px; height: 56px; background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  color: var(--color-primary); border-radius: 16px; display: flex; justify-content: center; align-items: center;
  font-family: 'Mitr', sans-serif; font-size: 24px; font-weight: 700; transform: rotate(45deg);
  box-shadow: 0 8px 16px rgba(5, 150, 105, 0.15); border: 2px solid #fff; transition: all 0.3s;
}
.number-hexagon span { transform: rotate(-45deg); }
.activity-super-card:hover .number-hexagon { background: linear-gradient(135deg, var(--color-primary), #0284c7); color: #fff; transform: rotate(45deg) scale(1.1); }

.col-main { flex: 1; min-width: 250px; }
.act-name { margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: var(--text-main); font-family: 'Mitr', sans-serif;}
.act-subtext { font-size: 14px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5; }

.act-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px; }
.info-pill {
  display: flex; align-items: flex-start; gap: 10px; background: #f8fafc;
  padding: 10px 14px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 13.5px; font-weight: 500;
  transition: all 0.2s;
}
.info-pill:hover { background: #f1f5f9; border-color: #cbd5e1; }
.pill-icon { font-size: 16px; margin-top: 2px; }
.pill-text { color: var(--text-muted); word-break: break-word; line-height: 1.5; white-space: normal; }

.col-action { display: flex; align-items: center; justify-content: center; min-width: 140px; }
.btn-detail-link {
  display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
  background: transparent; color: var(--color-primary); border: 2px solid var(--color-primary);
  padding: 12px 16px; border-radius: 16px; font-size: 15px; font-weight: 600;
  cursor: pointer; transition: all 0.3s;
}
.btn-detail-link:hover {
  background: var(--color-primary); color: #fff;
  box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2); transform: translateX(5px);
}

.empty-state-premium { padding: 60px 40px; text-align: center; color: var(--text-muted); background: var(--glass-bg); border-radius: 24px; border: 1px dashed rgba(148, 163, 184, 0.5); display: flex; flex-direction: column; align-items: center; }
.empty-icon { font-size: 48px; opacity: 0.5; margin-bottom: 16px; filter: grayscale(1); }

.video-monitor-frame { position: relative; background: #ffffff; padding: 16px; border-radius: 36px; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15); border: 1px solid rgba(255,255,255,0.8); transform: perspective(1000px) rotateY(-2deg); transition: transform 0.5s; }
.video-monitor-frame:hover { transform: perspective(1000px) rotateY(0deg) translateY(-5px); }
.monitor-glow { position: absolute; inset: -20px; background: linear-gradient(135deg, rgba(14, 165, 233, 0.3), rgba(5, 150, 105, 0.3)); filter: blur(30px); z-index: -1; opacity: 0.5; border-radius: 40px; }
.video-wrapper { position: relative; width: 100%; padding-bottom: 56.25%; border-radius: 24px; background: #020617; overflow: hidden; box-shadow: inset 0 0 20px rgba(0,0,0,0.5); }
.video-wrapper iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: none; }
.empty-video { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: rgba(255,255,255,0.4); }

.fees-section { padding: 40px 24px 120px; position: relative; z-index: 10; }
.container-center { max-width: 1250px; margin: 0 auto; }
.max-w-1000 { max-width: 1000px; }

.fee-subtitle { margin-top: 16px; font-size: 16px; color: var(--text-muted); font-weight: 500; }

.fee-row-card { display: flex; align-items: stretch; background: var(--glass-bg); backdrop-filter: blur(16px); border-radius: 28px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.05); border: 1px solid var(--glass-border); transition: all 0.3s; margin-bottom: 20px; }
.fee-row-card:hover { box-shadow: 0 25px 50px -12px rgba(245, 158, 11, 0.15); transform: translateY(-4px); }
.fee-icon-box { position: relative; background: linear-gradient(135deg, #f59e0b, #ea580c); padding: 40px; display: flex; align-items: center; justify-content: center; color: #fff; min-width: 120px; overflow: hidden; }
.icon-bg-glow { position: absolute; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%); top: -50%; left: -50%; animation: rotateGlow 10s linear infinite; }
@keyframes rotateGlow { 100% { transform: rotate(360deg); } }

.fee-content { padding: 30px; flex: 1; display: flex; flex-direction: column; justify-content: center; background: rgba(255,255,255,0.4); }
.fee-name { margin: 0 0 20px 0; font-family: 'Mitr', sans-serif; font-size: 24px; color: var(--text-main); font-weight: 600; }
.fee-detail-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.fee-detail-list li { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 14px 20px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; transition: all 0.2s; }
.fee-detail-list li:hover { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #fde68a; }
.detail-text { font-size: 16px; color: var(--text-muted); font-weight: 400; flex: 1; padding-right: 16px; }
.price-badge { background: linear-gradient(135deg, #fffbeb, #fef3c7); color: #b45309; padding: 8px 18px; border-radius: 20px; font-weight: 700; font-size: 16px; white-space: nowrap; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15); border: 1px solid #fde68a; }

.fee-reference { margin-top: 24px; display: flex; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 16px 24px; border-radius: 20px; border: 1px solid var(--glass-border); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); flex-wrap: wrap; gap: 16px; }
.ref-content { display: flex; align-items: center; gap: 12px; color: var(--text-muted); font-size: 14.5px; font-weight: 500; }
.ref-link { display: inline-flex; align-items: center; gap: 8px; color: var(--color-primary); font-size: 14.5px; font-weight: 600; text-decoration: none; padding: 10px 20px; background: rgba(5, 150, 105, 0.1); border-radius: 14px; transition: all 0.3s; }
.ref-link:hover { background: var(--color-primary); color: #fff; transform: translateX(5px); box-shadow: 0 8px 15px rgba(5, 150, 105, 0.2); }
.shine-effect {
  position: relative;
  overflow: hidden;
}
.shine-effect::before {
  content: '';
  position: absolute;
  top: 0; left: -100%; width: 50%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  transform: skewX(-20deg);
  animation: shineRun 3s infinite;
}
@keyframes shineRun {
  0% { left: -100%; }
  20%, 100% { left: 200%; }
}

.text-shimmer {
  background: linear-gradient(135deg, #065f46 0%, #0ea5e9 25%, #059669 50%, #0284c7 75%, #065f46 100%);
  background-size: 300% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: gradientMove 6s linear infinite;
}
@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.floating-sparkle {
  position: absolute;
  opacity: 0;
  pointer-events: none;
  z-index: 0;
}
.fs-1 { top: 15%; left: 20%; font-size: 24px; animation: floatSparkle 4s infinite 0.5s; }
.fs-2 { top: 30%; right: 22%; font-size: 18px; animation: floatSparkle 5s infinite 1.2s; }
.fs-3 { bottom: 25%; left: 28%; font-size: 28px; animation: floatSparkle 4.5s infinite 2s; }
.fs-4 { top: 10%; right: 35%; font-size: 20px; animation: floatSparkle 6s infinite 0.8s; }

@keyframes floatSparkle {
  0%, 100% { transform: translateY(0) scale(0.5) rotate(0deg); opacity: 0; }
  50% { transform: translateY(-20px) scale(1.2) rotate(15deg); opacity: 0.9; filter: drop-shadow(0 0 10px #fde047); }
}
@media (max-width: 1024px) {
  .grid-layout { grid-template-columns: 1fr; gap: 60px; }
  .video-monitor-frame { transform: none; }
  .video-monitor-frame:hover { transform: translateY(-5px); }
  .col-action { min-width: auto; width: 100%; margin-top: 10px; }
}

@media (max-width: 768px) {
  .center-title { font-size: 36px; }
  .pin-popup-premium { left: 50%; transform: translateX(-50%); bottom: 65px; width: 300px; }
  .pin-popup-premium::after { left: 50%; }

  .act-card-inner { flex-direction: column; align-items: flex-start; gap: 16px; }
  .col-num { margin-bottom: 0; }
  .act-info-grid { grid-template-columns: 1fr; }

  .fee-row-card { flex-direction: column; }
  .fee-icon-box { padding: 24px; flex-direction: row; gap: 16px; justify-content: flex-start; min-height: auto; }
  .fee-content { padding: 24px; }
  .fee-detail-list li { flex-direction: column; align-items: flex-start; gap: 12px; }
  .detail-text { padding-right: 0; }
  .price-badge { align-self: flex-start; }

  .fee-reference { flex-direction: column; align-items: flex-start; padding: 20px; }
  .ref-link { width: 100%; justify-content: center; }
}

@media (max-width: 480px) {
  .hero-header { padding: 100px 16px 50px; }
  .map-showcase { padding: 0 16px 50px; }
  .map-glass-frame { padding: 10px; border-radius: 24px; }
  .number-hexagon { width: 44px; height: 44px; font-size: 18px; }
  .pin-popup-premium { width: 280px; padding: 20px; }
  .popup-title { font-size: 20px; }
}
</style>
