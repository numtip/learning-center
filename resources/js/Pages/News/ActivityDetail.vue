<script setup>
import { appUrl } from '@/utils/urls.js'
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

// รับข้อมูลจาก backend
const page = usePage()
const news = page.props.news || {}
</script>

<template>
  <PublicLayout>
    <Head :title="news.title || 'รายละเอียดข่าว'" />

    <div class="news-page-wrapper">
      <div class="top-decoration"></div>

      <div class="container" v-if="news.title">

        <div class="nav-bar">
          <Link :href="appUrl('/news/activity')" class="back-link">
            <span class="icon">←</span>
            <span class="text">ย้อนกลับ</span>
          </Link>
          <div class="category-badge">ข่าวสารและกิจกรรม</div>
        </div>

        <article class="news-card">
          <header class="news-header">
            <h1 class="news-title">{{ news.title }}</h1>
            <div class="news-meta">
              <div class="meta-item">
                <span class="dot"></span>
                อัปเดตล่าสุดเมื่อ {{ new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }) }}
              </div>
            </div>
          </header>

          <div class="image-wrapper" v-if="news.image">
            <div class="image-inner">
              <img :src="news.image" :alt="news.title" class="main-news-img" />
            </div>
            <div class="image-shadow"></div>
          </div>

          <div class="news-body" v-if="news.description">
            <div class="content-text" v-html="news.description.replace(/\n/g,'<br>')"></div>
          </div>

          <footer class="news-footer">
            <div class="divider"></div>
            <p>ขอบคุณที่ติดตามข่าวสารจากเรา</p>
          </footer>
        </article>
      </div>

      <div v-else class="error-container">
        <div class="error-card">
          <div class="error-icon">🍃</div>
          <h2>ไม่พบข้อมูลข่าวสาร</h2>
          <p>ขออภัย ข้อมูลที่คุณกำลังมองหาอาจถูกลบหรือย้ายไปที่อื่นแล้ว</p>
          <Link :href="appUrl('/news/activity')" class="error-btn">กลับหน้าหลัก</Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap');

.news-page-wrapper {
  font-family: 'Sarabun', sans-serif;
  background-color: #f8fafc;
  min-height: 100vh;
  padding: 40px 20px 100px 20px;
  position: relative;
  overflow: hidden;
   margin-top: -28px;
}

/* พื้นหลังสีเขียวจางๆ ด้านบนช่วยให้ดูมีมิติ */
.top-decoration {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 400px;
  background: linear-gradient(180deg, #e1f2e9 0%, #f8fafc 100%);
  z-index: 0;
}

.container {
  max-width: 900px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

/* Navigation Bar */
.nav-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.back-link {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  color: #065f46;
  font-weight: 700;
  transition: all 0.3s ease;
  padding: 8px 16px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(10px);
}

.back-link:hover {
  background: #065f46;
  color: #ffffff;
  transform: translateX(-5px);
}

.category-badge {
  background: #d1fae5;
  color: #065f46;
  padding: 6px 16px;
  border-radius: 99px;
  font-size: 14px;
  font-weight: 600;
  border: 1px solid #a7f3d0;
}

/* Main Card Style */
.news-card {
  background: #ffffff;
  border-radius: 32px;
  padding: 60px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.8);
}

/* Header & Title */
.news-header {
  margin-bottom: 40px;
  text-align: center;
}

.news-title {
  font-size: clamp(28px, 5vw, 42px);
  font-weight: 800;
  line-height: 1.3;
  color: #064e3b;
  margin-bottom: 20px;
  letter-spacing: -0.5px;
}

.news-meta {
  display: flex;
  justify-content: center;
  color: #6b7280;
  font-size: 15px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dot {
  width: 8px;
  height: 8px;
  background: #10b981;
  border-radius: 50%;
}

/* Image Section */
.image-wrapper {
  position: relative;
  margin-bottom: 50px;
}

.image-inner {
  position: relative;
  z-index: 2;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.main-news-img {
  width: 100%;
  display: block;
  transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

.image-wrapper:hover .main-news-img {
  transform: scale(1.03);
}

.image-shadow {
  position: absolute;
  bottom: -15px;
  left: 5%;
  width: 90%;
  height: 50px;
  background: #065f46;
  filter: blur(40px);
  opacity: 0.15;
  z-index: 1;
}

/* Content Body */
.news-body {
  margin-top: 40px;
}

.content-text {
  font-size: 18px;
  line-height: 2;
  color: #374151;
  text-align: justify;
}

/* Footer Decor */
.news-footer {
  margin-top: 60px;
  text-align: center;
}

.divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
  margin-bottom: 20px;
}

.news-footer p {
  color: #9ca3af;
  font-size: 14px;
}

/* Error State UI */
.error-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
}

.error-card {
  text-align: center;
  background: white;
  padding: 50px;
  border-radius: 30px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.error-icon {
  font-size: 60px;
  margin-bottom: 20px;
}

.error-btn {
  display: inline-block;
  margin-top: 25px;
  background: #059669;
  color: white;
  padding: 12px 30px;
  border-radius: 15px;
  text-decoration: none;
  font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
  .news-card {
    padding: 30px 20px;
  }
  .news-title {
    font-size: 26px;
  }
  .content-text {
    font-size: 16px;
    line-height: 1.8;
  }
}
</style>
