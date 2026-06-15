<script setup>
import { appUrl } from '@/utils/urls.js'
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { computed, ref } from 'vue'

const { news } = usePage().props

// คัดกรองข่าว: แยกสถานะ Active กับ Expired โดยอิงจาก event_date ใน Controller
const activeTab = ref('active')

const filteredNews = computed(() => {
  const allNews = news || []
  if (allNews.length === 0) return []

  const today = new Date()
  today.setHours(0, 0, 0, 0)

  if (activeTab.value === 'active') {
    // ข่าวปัจจุบัน: ไม่มีวันที่จัดงาน หรือ วันที่จัดงานยังมาไม่ถึง
    return allNews.filter(item => !item.event_date || new Date(item.event_date) >= today)
  } else {
    // ข่าวที่งานสิ้นสุดไปแล้ว
    return allNews.filter(item => item.event_date && new Date(item.event_date) < today)
  }
})

const truncate = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<template>
  <PublicLayout>
    <Head title="ข่าวประชาสัมพันธ์ | Maejo University" />

    <div class="pr-page">
      <div class="top-blob"></div>

      <section class="wrapper">
        <header class="page-header">
          <div class="nav-top">
            <Link :href="appUrl('/')" class="btn-home-glass">
              <span class="icon">🏠</span> หน้าแรก
            </Link>
          </div>
          <h1 class="main-title">PUBLIC <span class="green-text">RELATIONS</span></h1>
          <p class="sub-title">ข้อมูลข่าวสารประชาสัมพันธ์และประกาศต่างๆ จากมหาวิทยาลัย</p>

          <div class="premium-tabs">
            <button
              @click="activeTab = 'active'"
              :class="['tab-btn', { active: activeTab === 'active' }]"
            >
              ข่าวสารล่าสุด ({{ news?.filter(i => !i.event_date || new Date(i.event_date) >= new Date().setHours(0,0,0,0)).length || 0 }})
            </button>
            <button
              @click="activeTab = 'expired'"
              :class="['tab-btn', { active: activeTab === 'expired' }]"
            >
              ข่าวประกาศย้อนหลัง ({{ news?.filter(i => i.event_date && new Date(i.event_date) < new Date().setHours(0,0,0,0)).length || 0 }})
            </button>
          </div>
        </header>

        <div class="news-grid" v-if="filteredNews.length > 0">
          <Link
            v-for="item in filteredNews"
            :key="item.id"
            :href="appUrl(`/news/pr/detail/${item.id}`)"
            class="pr-card-premium"
            :class="{ 'is-old': activeTab === 'expired' }"
          >
            <div class="status-badge red" v-if="activeTab === 'expired'">Finished</div>

            <div class="card-image">
              <img :src="item.image || '/no-image.jpg'" alt="pr-image" loading="lazy" />
              <div class="overlay-hover">
                <span class="btn-view">อ่านข่าวสาร</span>
              </div>
            </div>

            <div class="card-content">
              <div class="meta-row">
                <span class="type-badge">PR NEWS</span>
                <span class="date-text" v-if="item.event_date">
                  📅 {{ new Date(item.event_date).toLocaleDateString('th-TH') }}
                </span>
              </div>
              <h3 class="subject">{{ truncate(item.title, 65) }}</h3>
              <p class="summary">{{ truncate(item.description, 100) }}</p>

              <div class="card-footer">
                อ่านเพิ่มเติม <span>→</span>
              </div>
            </div>
          </Link>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">🍃</div>
          <h3>ไม่พบข้อมูลข่าวสารประชาสัมพันธ์</h3>
          <p>ในหมวดหมู่นี้ยังไม่มีข่าวสารที่คุณต้องการเรียกดูในขณะนี้</p>
        </div>
      </section>
    </div>
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Mitr:wght@400;500;600&family=Prompt:wght@300;400;600;700&display=swap');

.pr-page {
  font-family: 'Prompt', sans-serif;
  background-color: #f8fafc;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  padding-bottom: 120px;
margin-top: -28px;
}

.top-blob {
  position: absolute; top: -50px; right: -50px;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.06) 0%, transparent 70%);
  filter: blur(60px); z-index: 0;
}

.wrapper { max-width: 1350px; margin: 0 auto; padding: 80px 24px; position: relative; z-index: 1; }

.page-header { text-align: center; margin-bottom: 60px; }
.btn-home-glass {
  display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
  color: #064e3b; font-weight: 700; background: #fff;
  padding: 10px 24px; border-radius: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: 0.3s; margin-bottom: 30px;
}
.btn-home-glass:hover { transform: translateX(-5px); background: #064e3b; color: #fff; }

.main-title { font-family: 'Mitr', sans-serif; font-size: 52px; font-weight: 600; color: #064e3b; margin-bottom: 10px; }
.green-text { color: #10b981; }
.sub-title { color: #64748b; font-size: 18px; margin-bottom: 40px; }

.premium-tabs { display: inline-flex; background: #e2e8f0; padding: 6px; border-radius: 24px; }
.tab-btn {
  padding: 14px 28px; border: none; background: transparent;
  font-family: 'Prompt'; font-weight: 700; color: #64748b;
  cursor: pointer; border-radius: 18px; transition: 0.3s;
}
.tab-btn.active { background: #fff; color: #059669; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }

.news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 40px; }

.pr-card-premium {
  background: #fff; border-radius: 35px; overflow: hidden; text-decoration: none;
  display: flex; flex-direction: column; transition: 0.5s;
  border: 1px solid #f1f5f9; box-shadow: 0 20px 40px rgba(0,0,0,0.03);
  position: relative;
}
.pr-card-premium:hover { transform: translateY(-15px); box-shadow: 0 40px 80px rgba(6, 78, 59, 0.12); }

.card-image { height: 260px; position: relative; overflow: hidden; }
.card-image img { width: 100%; height: 100%; object-fit: cover; transition: 1.2s ease; }
.pr-card-premium:hover .card-image img { transform: scale(1.15); }

.overlay-hover {
  position: absolute; inset: 0; background: rgba(6, 78, 59, 0.55);
  backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: 0.4s;
}
.pr-card-premium:hover .overlay-hover { opacity: 1; }
.btn-view { background: #fff; color: #064e3b; padding: 12px 28px; border-radius: 50px; font-weight: 800; }

.card-content { padding: 30px; flex-grow: 1; display: flex; flex-direction: column; }
.meta-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 13px; font-weight: 800; }
.type-badge { color: #10b981; }
.date-text { color: #94a3b8; }

.subject { font-family: 'Mitr', sans-serif; font-size: 22px; color: #1e293b; line-height: 1.4; margin-bottom: 15px; }
.summary { color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 25px; }
.card-footer { margin-top: auto; color: #059669; font-weight: 800; display: flex; align-items: center; gap: 8px; }

.is-old { filter: grayscale(0.5); opacity: 0.85; }
.status-badge.red {
  position: absolute; top: 20px; right: 20px; z-index: 5;
  background: #ef4444; color: #fff; padding: 6px 18px; border-radius: 50px; font-size: 12px; font-weight: 800;
}

.empty-state { text-align: center; padding: 120px 0; grid-column: 1/-1; }
.empty-icon { font-size: 70px; margin-bottom: 25px; }

@media (max-width: 768px) {
  .news-grid { grid-template-columns: 1fr; }
  .main-title { font-size: 38px; }
}
</style>
