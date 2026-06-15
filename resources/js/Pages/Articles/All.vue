<script setup>
import { appUrl } from '@/utils/urls.js'
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { computed } from 'vue'

const { articles } = usePage().props

const truncate = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<template>
  <PublicLayout>
    <Head title="บทความความรู้เกษตร | MJU New Theory Agriculture" />

    <div class="articles-page">
      <div class="top-accent"></div>

      <section class="wrapper">
        <header class="page-header">
          <div class="nav-top">
            <Link :href="appUrl('/')" class="btn-home-glass">
              <span class="icon">🏠</span> หน้าแรก
            </Link>
          </div>
          <h1 class="main-title">KNOWLEDGE & <span class="green-text">ARTICLES</span></h1>
          <p class="sub-title">คลังความรู้และนวัตกรรมเกษตรทฤษฎีใหม่เพื่อการพัฒนาที่ยั่งยืน</p>
          <div class="title-divider"></div>
        </header>

        <div class="articles-grid" v-if="articles && articles.length > 0">
          <Link
            v-for="item in articles"
            :key="item.id"
            :href="appUrl(`/articles/${item.id}`)"
            class="article-card-premium"
          >
            <div class="card-thumb">
              <img :src="item.image || '/no-image.jpg'" alt="article-image" loading="lazy" />
              <div class="thumb-overlay">
                <span class="read-label">อ่านบทความ</span>
              </div>
            </div>

            <div class="card-body">
              <span class="category-tag">AGRICULTURE</span>
              <h3 class="subject">{{ truncate(item.title, 60) }}</h3>
              <p class="summary">{{ truncate(item.description, 100) }}</p>

              <div class="card-footer">
                <span class="more-text">อ่านรายละเอียดเพิ่มเติม</span>
                <span class="arrow">→</span>
              </div>
            </div>
          </Link>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">🍃</div>
          <h3>กำลังเตรียมข้อมูลบทความ</h3>
          <p>ขออภัย ขณะนี้ยังไม่มีรายการบทความที่ระบบต้องการแสดง</p>
        </div>
      </section>
    </div>
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Mitr:wght@400;500;600&family=Prompt:wght@300;400;600;700&display=swap');

.articles-page {
  font-family: 'Prompt', sans-serif;
  background-color: #fcfdfc;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  padding-bottom: 120px;
   margin-top: -28px;
}

.top-accent {
  position: absolute; top: -100px; right: -50px;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.07) 0%, transparent 70%);
  filter: blur(80px); z-index: 0;
}

.wrapper { max-width: 1350px; margin: 0 auto; padding: 80px 24px; position: relative; z-index: 1; }

.page-header { text-align: center; margin-bottom: 80px; }
.btn-home-glass {
  display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
  color: #064e3b; font-weight: 700; background: #fff;
  padding: 10px 24px; border-radius: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: 0.3s; margin-bottom: 30px;
}
.btn-home-glass:hover { transform: translateX(-5px); background: #064e3b; color: #fff; }

.main-title { font-family: 'Mitr', sans-serif; font-size: 52px; font-weight: 600; color: #064e3b; letter-spacing: -1px; margin-bottom: 15px; }
.green-text { color: #10b981; }
.sub-title { color: #64748b; font-size: 18px; margin-bottom: 30px; }

.title-divider {
  width: 80px; height: 4px; background: #10b981; margin: 0 auto; border-radius: 10px;
}

.articles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 45px; }

.article-card-premium {
  background: #fff; border-radius: 35px; overflow: hidden; text-decoration: none;
  display: flex; flex-direction: column; transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid #f1f5f9; box-shadow: 0 20px 40px rgba(0,0,0,0.03);
}
.article-card-premium:hover { transform: translateY(-15px); box-shadow: 0 40px 80px rgba(6, 78, 59, 0.12); }

.card-thumb { height: 260px; position: relative; overflow: hidden; }
.card-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 1.5s ease; }
.article-card-premium:hover .card-thumb img { transform: scale(1.12); }

.thumb-overlay {
  position: absolute; inset: 0; background: rgba(6, 78, 59, 0.5);
  backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: 0.4s;
}
.article-card-premium:hover .thumb-overlay { opacity: 1; }
.read-label { background: #fff; color: #064e3b; padding: 12px 28px; border-radius: 50px; font-weight: 800; }

.card-body { padding: 35px; flex-grow: 1; display: flex; flex-direction: column; }
.category-tag { color: #10b981; font-weight: 800; font-size: 12px; letter-spacing: 2px; margin-bottom: 12px; }

.subject { font-family: 'Mitr', sans-serif; font-size: 24px; color: #1e293b; line-height: 1.4; margin-bottom: 15px; transition: 0.3s; }
.article-card-premium:hover .subject { color: #059669; }

.summary { color: #64748b; font-size: 15px; line-height: 1.7; margin-bottom: 25px; }
.card-footer { margin-top: auto; color: #059669; font-weight: 800; display: flex; align-items: center; gap: 10px; }
.arrow { transition: transform 0.3s; }
.article-card-premium:hover .arrow { transform: translateX(8px); }

.empty-state { text-align: center; padding: 100px 0; grid-column: 1/-1; }
.empty-icon { font-size: 60px; margin-bottom: 20px; }

@media (max-width: 768px) {
  .articles-grid { grid-template-columns: 1fr; }
  .main-title { font-size: 36px; }
  .wrapper { padding: 60px 20px; }
}
</style>
