<script setup>
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { computed, ref } from 'vue'

// รับข้อมูลจาก props ตามโครงสร้างใน HomeController
const props = defineProps({
  news: Array
})

const activeTab = ref('active')

const filteredNews = computed(() => {
  const allNews = props.news || []
  if (allNews.length === 0) return []

  const today = new Date()
  today.setHours(0, 0, 0, 0)

  if (activeTab.value === 'active') {
    // กิจกรรมปัจจุบัน: ไม่มีวันที่สิ้นสุด หรือ ยังไม่ถึงวันสิ้นสุด
    return allNews.filter(item => !item.event_date || new Date(item.event_date) >= today)
  } else {
    // กิจกรรมที่สิ้นสุดแล้ว
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
    <Head title="ข่าวกิจกรรม | MJU New Theory Agriculture" />

    <div class="news-page-wrapper">
      <div class="top-decoration"></div>

      <section class="container">
        <header class="page-header-box">
          <div class="nav-bar">
            <Link href="/" class="back-link">
              <span class="icon">🏠</span>
              <span class="text">กลับหน้าแรก</span>
            </Link>
          </div>
          <h1 class="page-main-title">EVENTS & <span class="green-highlight">ACTIVITIES</span></h1>

          <div class="tab-switcher">
            <button @click="activeTab = 'active'" :class="['tab-btn', { active: activeTab === 'active' }]">
              กิจกรรมปัจจุบัน ({{ props.news?.filter(i => !i.event_date || new Date(i.event_date) >= new Date().setHours(0,0,0,0)).length || 0 }})
            </button>
          </div>
        </header>

        <div class="news-grid" v-if="filteredNews.length > 0">
          <Link
            v-for="item in filteredNews"
            :key="item.id"
            :href="`/news/activity/detail/${item.id}`"
            class="premium-card"
            :class="{ 'is-expired': activeTab === 'expired' }"
          >
            <div class="card-image-box">
              <img :src="item.image || '/no-image.jpg'" alt="activity-image" loading="lazy" />
              <div class="image-overlay">
                <span class="view-text">ดูรายละเอียด</span>
              </div>
              <div class="expired-tag" v-if="activeTab === 'expired'">Finished</div>
            </div>

            <div class="card-content-box">
              <span class="date-label" v-if="item.event_date">
                📅 {{ new Date(item.event_date).toLocaleDateString('th-TH') }}
              </span>
              <h3 class="card-title">{{ truncate(item.title, 60) }}</h3>
              <p class="card-desc">{{ truncate(item.description, 95) }}</p>
              <div class="card-footer-link">อ่านเพิ่มเติม <span>→</span></div>
            </div>
          </Link>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">🍃</div>
          <h3>ไม่พบข้อมูลกิจกรรมในหมวดนี้</h3>
        </div>
      </section>
    </div>
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap');

.news-page-wrapper { font-family: 'Sarabun', sans-serif; background-color: #f8fafc; min-height: 100vh; position: relative; overflow: hidden; padding-bottom: 100px; margin-top: -28px; }
.top-decoration { position: absolute; top: 0; left: 0; right: 0; height: 400px; background: linear-gradient(180deg, #e1f2e9 0%, #f8fafc 100%); z-index: 0; }
.container { max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; padding: 40px 20px; }

.page-header-box { text-align: center; margin-bottom: 60px; }
.nav-bar { display: flex; justify-content: flex-start; margin-bottom: 20px; }
.back-link { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #065f46; font-weight: 700; padding: 10px 20px; border-radius: 50px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; }
.back-link:hover { transform: translateX(-5px); background: #065f46; color: #fff; }

.page-main-title { font-size: 48px; font-weight: 800; color: #064e3b; margin-bottom: 30px; }
.green-highlight { color: #10b981; }

.tab-switcher { display: inline-flex; background: #e2e8f0; padding: 6px; border-radius: 20px; }
.tab-btn { padding: 12px 25px; border: none; background: transparent; font-family: 'Sarabun'; font-weight: 700; color: #64748b; cursor: pointer; border-radius: 15px; transition: 0.3s; }
.tab-btn.active { background: #fff; color: #059669; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }

.news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 35px; }
.premium-card { background: #fff; border-radius: 30px; overflow: hidden; text-decoration: none; border: 1px solid #f1f5f9; box-shadow: 0 15px 35px rgba(0,0,0,0.03); transition: 0.4s; position: relative; display: flex; flex-direction: column; }
.premium-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px rgba(6, 78, 59, 0.1); }

.card-image-box { height: 240px; overflow: hidden; position: relative; }
.card-image-box img { width: 100%; height: 100%; object-fit: cover; transition: 1s; }
.premium-card:hover img { transform: scale(1.1); }
.image-overlay { position: absolute; inset: 0; background: rgba(6, 78, 59, 0.4); backdrop-filter: blur(3px); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.4s; }
.premium-card:hover .image-overlay { opacity: 1; }
.view-text { background: #fff; color: #064e3b; padding: 10px 20px; border-radius: 50px; font-weight: 800; }

.card-content-box { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
.date-label { color: #059669; font-weight: 700; font-size: 13px; margin-bottom: 10px; display: block; }
.card-title { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 12px; line-height: 1.4; }
.card-desc { color: #64748b; font-size: 14px; margin-bottom: 20px; line-height: 1.6; }
.card-footer-link { margin-top: auto; color: #059669; font-weight: 800; }

.is-expired { filter: grayscale(0.5); opacity: 0.8; }
.expired-tag { position: absolute; top: 15px; right: 15px; background: #ef4444; color: #fff; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 800; }

.empty-state { text-align: center; padding: 80px 0; grid-column: 1/-1; }
.empty-icon { font-size: 50px; margin-bottom: 15px; }

@media (max-width: 768px) { .page-main-title { font-size: 32px; } .news-grid { grid-template-columns: 1fr; } }
</style>
