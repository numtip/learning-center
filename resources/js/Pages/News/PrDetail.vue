<script setup>
import { appUrl } from '@/utils/urls.js'
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { computed } from 'vue'

// ดึงข้อมูลผ่าน usePage().props โดยตรง เพื่อป้องกันปัญหาเรื่อง defineProps รับค่าไม่ตรง
const page = usePage()

// ดึงค่า news ออกมา และตรวจสอบว่ามีข้อมูลอยู่จริงไหม
const news = computed(() => page.props.news || null)

// ฟังก์ชันสำหรับจัดการวันที่ให้ปลอดภัย (ไม่พังถ้าวันที่มาเป็น null)
const formatDate = (dateString) => {
  if (!dateString) return null
  return new Date(dateString).toLocaleDateString('th-TH', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}
</script>

<template>
  <PublicLayout>
    <Head :title="news?.title || 'รายละเอียดข่าวประชาสัมพันธ์'" />

    <div class="pr-detail-page">
      <div class="grad-bg"></div>

      <div class="main-content" v-if="news && news.title">
        <div class="nav-bar">
          <Link :href="appUrl('/news/pr')" class="btn-back-glass">
            <span class="icon">←</span> ย้อนกลับไปหน้าข่าวสาร
          </Link>
          <div class="share-info">PR News MJU</div>
        </div>

        <article class="news-magazine-card">
          <header class="header-box">
            <div class="category-tag">ประกาศประชาสัมพันธ์</div>
            <h1 class="headline">{{ news.title }}</h1>
            <div class="date-info" v-if="news.event_date">
              📅 วันที่จัดงาน/ประกาศ: {{ formatDate(news.event_date) }}
            </div>
          </header>

          <div class="hero-image" v-if="news.image">
            <img :src="news.image" alt="news-image" />
            <div class="glow-shadow"></div>
          </div>

          <div class="body-text">
            <div class="rich-content" v-if="news.description" v-html="news.description.replace(/\n/g,'<br>')"></div>
            <div class="rich-content" v-else>ไม่มีรายละเอียดเนื้อหาข่าว</div>
          </div>

          <footer class="card-footer">
            <div class="line-divider"></div>
            <div class="outro-text">
              <p>ติดตามข่าวประชาสัมพันธ์และประกาศใหม่ๆ ได้ที่หน้าหลักของมหาวิทยาลัย</p>
            </div>
          </footer>
        </article>
      </div>

      <div v-else class="not-found-state">
        <div class="error-box">
          <div class="error-icon">🍃</div>
          <h2>ไม่พบข้อมูลข่าวสาร</h2>
          <p>ลิงก์ที่คุณเรียกใช้ไม่มีข้อมูลในระบบ หรือข้อมูลกำลังโหลด...</p>
          <Link :href="appUrl('/news/pr')" class="btn-home-primary">กลับสู่หน้ารวมข่าวสาร</Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Mitr:wght@500&family=Prompt:wght@400;700;800&display=swap');

.pr-detail-page {
  font-family: 'Prompt', sans-serif;
  background-color: #f1f5f9;
  min-height: 100vh;
  padding: 60px 20px 120px 20px;
  position: relative;
   margin-top: -28px;
}

.grad-bg {
  position: absolute; top: 0; left: 0; width: 100%; height: 450px;
  background: linear-gradient(180deg, #dcfce7 0%, #f1f5f9 100%);
  z-index: 0;
}

.main-content { max-width: 980px; margin: 0 auto; position: relative; z-index: 1; }

.nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
.btn-back-glass {
  text-decoration: none; color: #065f46; font-weight: 800; font-size: 15px;
  background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);
  padding: 12px 28px; border-radius: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: 0.3s;
}
.btn-back-glass:hover { background: #065f46; color: #fff; transform: translateX(-5px); }
.share-info { color: #059669; font-weight: 800; font-size: 14px; letter-spacing: 1px; }

.news-magazine-card {
  background: #fff; border-radius: 45px; padding: 70px;
  box-shadow: 0 40px 100px -15px rgba(0,0,0,0.08);
}

.header-box { text-align: center; margin-bottom: 45px; }
.category-tag {
  background: #d1fae5; color: #064e3b; padding: 7px 20px;
  border-radius: 50px; font-weight: 800; font-size: 14px; display: inline-block;
}
.headline {
  font-family: 'Mitr', sans-serif; font-size: clamp(28px, 4vw, 42px);
  color: #064e3b; line-height: 1.35; margin: 30px 0 20px 0;
}
.date-info { color: #64748b; font-weight: 600; font-size: 16px; }

.hero-image { position: relative; margin-bottom: 60px; }
.hero-image img {
  width: 100%; border-radius: 30px; display: block;
  box-shadow: 0 25px 50px rgba(0,0,0,0.1);
}
.glow-shadow {
  position: absolute; bottom: -20px; left: 10%; width: 80%; height: 50px;
  background: #10b981; filter: blur(45px); opacity: 0.15; z-index: -1;
}

.body-text { margin-bottom: 60px; }
.rich-content { font-size: 19px; line-height: 2; color: #334155; text-align: justify; }

.card-footer { text-align: center; }
.line-divider { height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent); margin-bottom: 35px; }
.outro-text p { color: #94a3b8; font-size: 15px; }

/* แก้ไข Error State */
.not-found-state { display: flex; justify-content: center; align-items: center; min-height: 60vh; position: relative; z-index: 10; }
.error-box { text-align: center; background: #fff; padding: 60px; border-radius: 40px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); }
.error-icon { font-size: 50px; margin-bottom: 10px; }
.btn-home-primary {
  display: inline-block; margin-top: 30px; background: #059669; color: #fff;
  padding: 14px 35px; border-radius: 15px; text-decoration: none; font-weight: 800;
}

@media (max-width: 768px) {
  .news-magazine-card { padding: 35px 25px; }
  .headline { font-size: 26px; }
  .rich-content { font-size: 17px; }
}
</style>
