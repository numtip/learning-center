<script setup>
import { appUrl } from '@/utils/urls.js'
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const { article } = usePage().props
</script>

<template>
  <PublicLayout>
    <Head :title="article?.title || 'บทความความรู้'" />

    <div class="article-detail-page">
      <div class="top-decoration"></div>

      <div class="container" v-if="article">
        <div class="nav-bar">
          <Link :href="appUrl('/articles')" class="btn-back-organic">
            <span class="icon">←</span> ย้อนกลับหน้าบทความ
          </Link>
          <div class="reading-tag">Knowledge Sharing</div>
        </div>

        <article class="article-main-card">
          <header class="article-header">
            <div class="meta-badge">AGRICULTURAL KNOWLEDGE</div>
            <h1 class="article-headline">{{ article.title }}</h1>
            <div class="ornament"></div>
          </header>

          <div class="article-hero" v-if="article.image">
            <div class="hero-inner">
              <img :src="article.image" :alt="article.title" />
            </div>
            <div class="hero-shadow"></div>
          </div>

          <div class="article-content-body">
            <div class="rich-text" v-html="article.description?.replace(/\n/g,'<br>')"></div>
          </div>

          <footer class="article-footer">
            <div class="footer-divider"></div>
            <div class="closing-text">
              <p>หวังว่าบทความนี้จะเป็นประโยชน์ต่อการเรียนรู้และการพัฒนาการเกษตรของท่าน</p>
              <p class="brand">MJU New Theory Agriculture Learning Center</p>
            </div>
          </footer>
        </article>
      </div>

      <div v-else class="not-found-container">
        <div class="error-card">
          <div class="icon">🍃</div>
          <h2>ไม่พบข้อมูลบทความ</h2>
          <p>ขออภัย ข้อมูลที่คุณกำลังมองหาอาจไม่มีอยู่ในระบบ</p>
          <Link :href="appUrl('/articles')" class="btn-retry">กลับหน้าบทความ</Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&family=Mitr:wght@500&display=swap');

.article-detail-page {
  font-family: 'Sarabun', sans-serif;
  background-color: #f8fafc;
  min-height: 100vh;
  padding: 60px 20px 120px 20px;
  position: relative;
   margin-top: -28px;
}

.top-decoration {
  position: absolute; top: 0; left: 0; right: 0; height: 500px;
  background: linear-gradient(180deg, #e1f2e9 0%, #f8fafc 100%);
  z-index: 0;
}

.container { max-width: 950px; margin: 0 auto; position: relative; z-index: 1; }

.nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
.btn-back-organic {
  text-decoration: none; color: #065f46; font-weight: 700; font-size: 15px;
  background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);
  padding: 12px 28px; border-radius: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: 0.3s;
}
.btn-back-organic:hover { background: #065f46; color: #fff; transform: translateX(-5px); }
.reading-tag { color: #059669; font-weight: 800; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }

.article-main-card {
  background: #fff; border-radius: 45px; padding: 80px;
  box-shadow: 0 40px 100px -15px rgba(0,0,0,0.08);
}

.article-header { text-align: center; margin-bottom: 50px; }
.meta-badge {
  background: #d1fae5; color: #064e3b; padding: 6px 20px;
  border-radius: 50px; font-weight: 800; font-size: 13px; display: inline-block; letter-spacing: 1px;
}
.article-headline {
  font-family: 'Mitr', sans-serif; font-size: clamp(28px, 4vw, 44px);
  color: #064e3b; line-height: 1.4; margin: 25px 0 25px 0;
}
.ornament { width: 60px; height: 4px; background: #10b981; margin: 0 auto; border-radius: 10px; }

.article-hero { position: relative; margin-bottom: 60px; }
.hero-inner { border-radius: 30px; overflow: hidden; position: relative; z-index: 2; box-shadow: 0 25px 50px rgba(0,0,0,0.1); }
.hero-inner img { width: 100%; display: block; height: auto; max-height: 550px; object-fit: cover; }
.hero-shadow {
  position: absolute; bottom: -20px; left: 10%; width: 80%; height: 50px;
  background: #10b981; filter: blur(45px); opacity: 0.15; z-index: 1;
}

.article-content-body { margin-bottom: 80px; }
.rich-text { font-size: 19px; line-height: 2.1; color: #334155; text-align: justify; }

.article-footer { text-align: center; }
.footer-divider { height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent); margin-bottom: 40px; }
.closing-text p { color: #94a3b8; font-size: 16px; margin-bottom: 8px; }
.closing-text .brand { color: #059669; font-weight: 700; margin-top: 15px; }

.not-found-container { display: flex; justify-content: center; align-items: center; min-height: 60vh; }
.error-card { text-align: center; background: #fff; padding: 60px; border-radius: 40px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); }
.btn-retry { display: inline-block; margin-top: 30px; background: #059669; color: #fff; padding: 14px 40px; border-radius: 15px; text-decoration: none; font-weight: 800; }

@media (max-width: 768px) {
  .article-main-card { padding: 40px 25px; border-radius: 35px; }
  .article-headline { font-size: 26px; }
  .rich-text { font-size: 17px; }
  .hero-inner { border-radius: 20px; }
}
</style>
