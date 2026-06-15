<script setup>
import { appUrl } from '@/utils/urls.js'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <div class="hero-section">
        <div class="hero-bg-glow"></div>
        <div class="hero-content">
          <div class="icon-pulse">
            <span class="main-icon">🗺️</span>
          </div>
          <div class="text-content">
            <h1 class="page-title">จัดการแผนที่ศูนย์การเรียนรู้</h1>
            <p class="page-subtitle">เลือกระบบแผนที่จำลองของแต่ละศูนย์ เพื่อจัดการจุดพิกัดและรายละเอียดเชิงพื้นที่</p>
          </div>
        </div>
      </div>

      <div class="map-grid">

        <Link :href="appUrl('/superadmin/map/3')" class="map-card" style="animation-delay: 0.1s;">
          <div class="card-bg-pattern"></div>
          <div class="card-body">
            <div class="icon-wrapper">
              <span class="emoji">🌿</span>
            </div>
            <div class="info-wrapper">
              <h3 class="center-name">ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา</h3>
              <p class="center-desc">จุดเรียนรู้ภูมิปัญญาท้องถิ่น</p>
            </div>
          </div>
          <div class="card-footer">
            <span class="action-text">เข้าสู่ระบบจัดการแผนที่</span>
            <div class="arrow-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </div>
          </div>
        </Link>

        <Link :href="appUrl('/superadmin/map/1')" class="map-card" style="animation-delay: 0.2s;">
          <div class="card-bg-pattern"></div>
          <div class="card-body">
            <div class="icon-wrapper">
              <span class="emoji">🌾</span>
            </div>
            <div class="info-wrapper">
              <h3 class="center-name">ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่</h3>
              <p class="center-desc">พื้นที่สาธิตแนวคิดเกษตรทฤษฎีใหม่</p>
            </div>
          </div>
          <div class="card-footer">
            <span class="action-text">เข้าสู่ระบบจัดการแผนที่</span>
            <div class="arrow-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </div>
          </div>
        </Link>

        <Link :href="appUrl('/superadmin/map/4')" class="map-card" style="animation-delay: 0.3s;">
          <div class="card-bg-pattern"></div>
          <div class="card-body">
            <div class="icon-wrapper">
              <span class="emoji">🏡</span>
            </div>
            <div class="info-wrapper">
              <h3 class="center-name">ศูนย์เรียนรู้พัฒนาบ้านโปง</h3>
              <p class="center-desc">แปลงสาธิตและศูนย์บริการชุมชน</p>
            </div>
          </div>
          <div class="card-footer">
            <span class="action-text">เข้าสู่ระบบจัดการแผนที่</span>
            <div class="arrow-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </div>
          </div>
        </Link>

        <Link :href="appUrl('/superadmin/map/5')" class="map-card" style="animation-delay: 0.4s;">
          <div class="card-bg-pattern"></div>
          <div class="card-body">
            <div class="icon-wrapper">
              <span class="emoji">🍃</span>
            </div>
            <div class="info-wrapper">
              <h3 class="center-name">ศูนย์กัญชง</h3>
              <p class="center-desc">ศูนย์การเรียนรู้และวิจัยพืชกัญชง</p>
            </div>
          </div>
          <div class="card-footer">
            <span class="action-text">เข้าสู่ระบบจัดการแผนที่</span>
            <div class="arrow-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </div>
          </div>
        </Link>

        <Link :href="appUrl('/superadmin/map/2')" class="map-card" style="animation-delay: 0.5s;">
          <div class="card-bg-pattern"></div>
          <div class="card-body">
            <div class="icon-wrapper">
              <span class="emoji">🐄</span>
            </div>
            <div class="info-wrapper">
              <h3 class="center-name">ฟาร์มสาธิตสำนักวิจัยฯ</h3>
              <p class="center-desc">โรงเรือนสัตว์และพื้นที่ทดลอง</p>
            </div>
          </div>
          <div class="card-footer">
            <span class="action-text">เข้าสู่ระบบจัดการแผนที่</span>
            <div class="arrow-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </div>
          </div>
        </Link>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;900&display=swap');

.admin-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 24px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
  position: relative;
  z-index: 1;
}

.hero-section {
  position: relative;
  background: #ffffff;
  border-radius: 28px;
  padding: 48px;
  margin-bottom: 40px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  animation: fadeUp 0.8s ease-out forwards;
}

.hero-bg-glow {
  position: absolute;
  top: -100px;
  right: -100px;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
  z-index: -1;
  pointer-events: none;
}

.hero-content {
  display: flex;
  align-items: center;
  gap: 32px;
  position: relative;
  z-index: 2;
}

.icon-pulse {
  width: 88px;
  height: 88px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.5);
  position: relative;
}

.icon-pulse::before {
  content: '';
  position: absolute;
  inset: -8px;
  border-radius: 32px;
  border: 2px solid rgba(16, 185, 129, 0.3);
  animation: pulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}

.main-icon {
  font-size: 40px;
  filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
}

.text-content {
  flex: 1;
}

.page-title {
  font-size: 38px;
  font-weight: 900;
  color: #0f172a;
  margin: 0 0 12px 0;
  letter-spacing: -0.5px;
}

.page-subtitle {
  font-size: 18px;
  color: #64748b;
  font-weight: 500;
  margin: 0;
  line-height: 1.5;
  max-width: 700px;
}

.map-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 32px;
}

.map-card {
  position: relative;
  background: #ffffff;
  border-radius: 24px;
  border: 1px solid #e2e8f0;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  opacity: 0;
  transform: translateY(20px);
  animation: fadeUp 0.6s ease-out forwards;
}

.card-bg-pattern {
  position: absolute;
  top: 0;
  right: 0;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle at top right, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
  opacity: 0;
  transition: opacity 0.4s ease;
  pointer-events: none;
}

.map-card:hover {
  transform: translateY(-8px);
  border-color: #10b981;
  box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25);
}

.map-card:hover .card-bg-pattern {
  opacity: 1;
}

.card-body {
  padding: 32px;
  display: flex;
  align-items: center;
  gap: 24px;
  flex: 1;
  position: relative;
  z-index: 2;
}

.icon-wrapper {
  width: 72px;
  height: 72px;
  background: #f8fafc;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #f1f5f9;
  transition: all 0.4s ease;
  flex-shrink: 0;
}

.emoji {
  font-size: 36px;
  transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.map-card:hover .icon-wrapper {
  background: #ecfdf5;
  border-color: #a7f3d0;
  transform: scale(1.1) rotate(-5deg);
}

.map-card:hover .emoji {
  transform: scale(1.15);
}

.info-wrapper {
  flex: 1;
}

.center-name {
  font-size: 22px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
  transition: color 0.3s ease;
}

.center-desc {
  font-size: 15px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}

.map-card:hover .center-name {
  color: #059669;
}

.card-footer {
  padding: 20px 32px;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.4s ease;
  position: relative;
  z-index: 2;
}

.action-text {
  font-size: 15px;
  font-weight: 600;
  color: #475569;
  transition: color 0.3s ease;
}

.arrow-btn {
  width: 36px;
  height: 36px;
  background: #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.map-card:hover .card-footer {
  background: #10b981;
  border-top-color: #10b981;
}

.map-card:hover .action-text {
  color: #ffffff;
}

.map-card:hover .arrow-btn {
  color: #10b981;
  background: #ffffff;
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@keyframes fadeUp {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0% {
    transform: scale(0.95);
    opacity: 1;
  }
  100% {
    transform: scale(1.15);
    opacity: 0;
  }
}

@media (max-width: 768px) {
  .hero-content {
    flex-direction: column;
    text-align: center;
    gap: 20px;
  }
  .hero-section {
    padding: 32px 24px;
  }
  .page-title {
    font-size: 28px;
  }
  .map-grid {
    grid-template-columns: 1fr;
  }
  .card-body {
    padding: 24px;
    flex-direction: column;
    text-align: center;
  }
}
</style>
