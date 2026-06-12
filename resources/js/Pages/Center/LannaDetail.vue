<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'

const page = usePage()
const detail = page.props.detail || {}

// เรียงลำดับ พ.ศ. จากน้อยไปมาก
const historyData = ref([...(page.props.historyData || [])].sort((a, b) => parseInt(a.year) - parseInt(b.year)))

const activeIndex = ref(0)
let interval = null
const loaded = ref(false)
const scrollY = ref(0)

const handleScroll = () => {
  scrollY.value = window.scrollY
}

const formattedObjective = computed(() => {
  if (!detail.objective) return []
  return detail.objective.split(/\d+\.\s*/).filter(text => text.trim() !== '')
})

const next = () => {
  activeIndex.value = (activeIndex.value + 1) % historyData.value.length
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  if (historyData.value.length > 0) {
    interval = setInterval(next, 5000)
  }
  setTimeout(() => {
    loaded.value = true
  }, 100)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  clearInterval(interval)
})
</script>

<template>
  <PublicLayout>
    <Head title="ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา" />
    <div class="minimal-gallery" :class="{ 'is-ready': loaded }">
      <section class="hero-split">
        <div class="hero-text-side">
          <div class="badge-new animate-up">มรดกแห่งภูมิปัญญา</div>
          <h1 class="hero-title animate-up-delay-1">
            ศูนย์เรียนรู้<br/>
            <span class="serif-italic">วัฒนธรรมเกษตรล้านนา</span>
          </h1>
          <p class="hero-para animate-up-delay-2">
            สัมผัสอัตลักษณ์แห่งล้านนาที่ผสานวิถีเกษตรดั้งเดิม รังสรรค์ผ่านสถาปัตยกรรมและจิตวิญญาณแห่งล้านนาอันทรงคุณค่า
          </p>
          <div class="hero-action animate-up-delay-3">
            <div class="line-btn">
              <span class="line"></span>
              สำรวจวัฒนธรรมเกษตรล้านนา
            </div>
          </div>
        </div>
        <div class="hero-image-side">
          <div class="main-frame" :style="{ transform: `translateY(${scrollY * -0.05}px)` }">
            <img :src="detail.image1" class="img-zoom" />
          </div>
          <div class="sub-frame" :style="{ transform: `translateY(${scrollY * 0.08}px)` }">
            <img :src="detail.image2" />
          </div>
          <div class="circle-deco"></div>
        </div>
      </section>

      <section class="chrono-v2">
        <div class="container-v2">
          <div class="section-intro">
            <h2 class="light-title">เส้นทางแห่งภูมิปัญญา</h2>
            <p class="sub-intro">บันทึกเรื่องราวและความเป็นมาของศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา</p>
          </div>
          <div class="timeline-box-v2">
            <div class="timeline-nav-v2">
              <div v-for="(item, i) in historyData" :key="i" class="nav-year-btn" :class="{ 'active': activeIndex === i }" @click="activeIndex = i">
                <span class="y-dot"></span>
                <span class="y-text">พ.ศ. {{ item.year }}</span>
              </div>
            </div>
            <div class="timeline-display-v2">
              <Transition name="fade-slide" mode="out-in">
                <div :key="activeIndex" class="mega-display-card">
                  <div class="display-image">
                    <img :src="historyData[activeIndex]?.imageUrl" v-if="historyData[activeIndex]?.imageUrl" />
                    <div class="image-inner-shadow"></div>
                  </div>
                  <div class="display-info">
                    <div class="big-year-watermark">{{ historyData[activeIndex]?.year }}</div>
                    <span class="history-label">ตำนานและความทรงจำ</span>
                    <h3>{{ historyData[activeIndex]?.title }}</h3>
                    <p class="wrap-text">{{ historyData[activeIndex]?.description }}</p>
                  </div>
                </div>
              </Transition>
            </div>
          </div>
        </div>
      </section>

      <section class="narrative-v3">
        <div class="narrative-container">
          <div class="quote-side">
             <div class="large-quote">“</div>
             <p class="quote-text">รากฐานล้านนา คือหัวใจของการเรียนรู้ที่ยั่งยืน</p>
             <span class="quote-author">— ศูนย์เรียนรู้ล้านนา</span>
          </div>
          <div class="content-side">
            <h2 class="narrative-h2">ความเป็นมา</h2>
            <p class="narrative-p wrap-text">{{ detail.history }}</p>
          </div>
        </div>
      </section>

      <section class="obj-v3">
        <div class="obj-grid-v3">
          <div class="obj-header-cell">
            <h2>วัตถุประสงค์</h2>
            <div class="dot-deco"></div>
          </div>
          <div v-for="(item, index) in formattedObjective" :key="index" class="obj-item-v3">
             <div class="obj-top">
               <span class="obj-index">๐{{ index + 1 }}</span>
               <div class="obj-icon-small">✦</div>
             </div>
             <p class="wrap-text">{{ item }}</p>
          </div>
        </div>
      </section>

      <section class="gallery-v3">
        <div class="gallery-header">
           <span class="gallery-label">ภาพสะท้อนวัฒนธรรมเกษตร</span>
           <div class="divider-long"></div>
           <span class="gallery-year">GALLERY</span>
        </div>
        <div class="gallery-wrapper-v3">
          <div class="gallery-item-v3" v-for="(img, i) in [detail.image1, detail.image2, detail.image3, detail.image1]" :key="i" :class="'item-'+i">
            <img :src="img" />
            <div class="img-overlay"><span>อัตลักษณ์ล้านนา</span></div>
          </div>
        </div>
      </section>
    </div>
    <FooterFarm />
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Anuphan:wght@200;300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap');
.wrap-text { word-wrap: break-word; overflow-wrap: break-word; white-space: normal; display: block; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-up { animation: fadeInUp 0.8s ease forwards; }
.animate-up-delay-1 { animation: fadeInUp 0.8s ease 0.2s forwards; opacity: 0; }
.animate-up-delay-2 { animation: fadeInUp 0.8s ease 0.4s forwards; opacity: 0; }
.animate-up-delay-3 { animation: fadeInUp 0.8s ease 0.6s forwards; opacity: 0; }
.minimal-gallery { background: #ffffff; color: #1a1a1a; font-family: 'Anuphan', sans-serif; overflow-x: hidden;   margin-top: -60px; }
.hero-split { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; align-items: center; padding: 0 8%; gap: 60px; background: radial-gradient(circle at 0% 0%, #fdfdfd 0%, #f4f7f4 100%); }
.badge-new { font-size: 0.9rem; letter-spacing: 4px; color: #c9a23f; margin-bottom: 20px; font-weight: 700; display: inline-block; border-bottom: 2px solid #c9a23f; padding-bottom: 5px; }
.hero-title { font-size: clamp(3rem, 6vw, 5rem); line-height: 1.2; font-weight: 400; margin-bottom: 30px; color: #1a3a3a; }
.serif-italic { font-family: 'Playfair Display', serif; font-style: italic; color: #c9a23f; background: linear-gradient(90deg, #c9a23f, #e2c074); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero-para { font-size: 1.2rem; line-height: 1.8; color: #555; max-width: 480px; margin-bottom: 40px; }
.line-btn { display: flex; align-items: center; gap: 15px; font-weight: 700; letter-spacing: 1px; cursor: pointer; color: #1a3a3a; transition: 0.3s; }
.line { width: 50px; height: 2px; background: #c9a23f; transition: 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.line-btn:hover { color: #c9a23f; }
.line-btn:hover .line { width: 80px; }
.hero-image-side { position: relative; height: 650px; display: flex; align-items: center; justify-content: center;}
.main-frame { width: 75%; height: 90%; border-radius: 400px 400px 20px 20px; overflow: hidden; position: relative; z-index: 2; box-shadow: 0 50px 100px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.3); }
.img-zoom { width: 100%; height: 100%; object-fit: cover; transition: 1.5s scale ease; }
.main-frame:hover .img-zoom { scale: 1.1; }
.sub-frame { position: absolute; bottom: 20px; right: -20px; width: 55%; height: 35%; border-radius: 15px; overflow: hidden; border: 8px solid #fff; z-index: 3; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
.sub-frame img { width: 100%; height: 100%; object-fit: cover; }
.circle-deco { position: absolute; top: 10%; left: 10%; width: 200px; height: 200px; border: 1px dashed #c9a23f; border-radius: 50%; z-index: 1; animation: rotate 20s linear infinite; }
@keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.chrono-v2 { padding: 120px 0; background: #fdfdfd; }
.container-v2 { max-width: 1400px; margin: 0 auto; padding: 0 40px; }
.section-intro { text-align: center; margin-bottom: 80px; }
.light-title { font-family: 'Playfair Display', serif; font-size: 4rem; color: #1a3a3a; margin-bottom: 10px; }
.sub-intro { color: #888; font-size: 1.2rem; }
.timeline-box-v2 { display: grid; grid-template-columns: 220px 1fr; gap: 0; background: #fff; border-radius: 40px; overflow: hidden; box-shadow: 0 40px 80px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; }
.timeline-nav-v2 { display: flex; flex-direction: column; background: #fcfcfc; border-right: 1px solid #eee; padding: 30px 0; }
.nav-year-btn { padding: 25px 40px; cursor: pointer; display: flex; align-items: center; gap: 20px; opacity: 0.4; transition: 0.4s; position: relative; }
.nav-year-btn.active { opacity: 1; background: #fff; color: #c9a23f; }
.nav-year-btn.active::after { content: ''; position: absolute; right: -1px; top: 20%; height: 60%; width: 3px; background: #c9a23f; }
.y-dot { width: 10px; height: 10px; background: #c9a23f; border-radius: 50%; transition: 0.3s; }
.nav-year-btn.active .y-dot { transform: scale(1.5); box-shadow: 0 0 10px rgba(201, 162, 63, 0.5); }
.y-text { font-weight: 700; font-size: 1.3rem; }
.timeline-display-v2 { padding: 60px; }
.mega-display-card { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 60px; align-items: center; }
.display-image { height: 480px; border-radius: 25px; overflow: hidden; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
.display-image img { width: 100%; height: 100%; object-fit: cover; }
.display-info { position: relative; }
.big-year-watermark { position: absolute; top: -60px; left: -20px; font-size: 10rem; font-weight: 900; color: rgba(201, 162, 63, 0.05); z-index: 0; font-family: 'Playfair Display'; }
.history-label { color: #c9a23f; font-weight: 700; letter-spacing: 2px; font-size: 0.8rem; }
.display-info h3 { font-size: 2.5rem; margin: 20px 0; color: #1a3a3a; position: relative; z-index: 1; }
.display-info p { line-height: 2; color: #666; font-size: 1.1rem; position: relative; z-index: 1; }
.narrative-v3 { padding: 140px 8%; background: #fff; }
.narrative-container { display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 100px; align-items: center; }
.large-quote { font-family: 'Playfair Display'; font-size: 10rem; color: #e9ece9; line-height: 0.1; margin-bottom: 40px; }
.quote-text { font-size: 2.4rem; font-weight: 300; line-height: 1.4; margin-bottom: 25px; color: #1a3a3a; }
.quote-author { letter-spacing: 4px; font-size: 0.9rem; color: #c9a23f; font-weight: 700; }
.narrative-h2 { font-family: 'Playfair Display'; font-size: 3.5rem; margin-bottom: 35px; color: #1a3a3a; }
.narrative-p { font-size: 1.2rem; line-height: 2.2; color: #555; text-align: justify; }
.obj-v3 { padding: 100px 8%; background: #f9fbf9; }
.obj-grid-v3 { display: grid; grid-template-columns: repeat(3, 1fr); background: #fff; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
.obj-header-cell { padding: 60px; background: #1a3a3a; color: #fff; grid-row: span 1; }
.obj-header-cell h2 { font-size: 2.5rem; font-weight: 400; line-height: 1.3; margin-bottom: 20px; }
.dot-deco { width: 40px; height: 3px; background: #c9a23f; margin: 25px 0; }
.obj-item-v3 { padding: 50px; border: 1px solid #f0f0f0; transition: 0.4s; position: relative; overflow: hidden; }
.obj-item-v3:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
.obj-item-v3::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: #c9a23f; transform: scaleX(0); transition: 0.4s; transform-origin: left; }
.obj-item-v3:hover::before { transform: scaleX(1); }
.obj-top { display: flex; justify-content: space-between; margin-bottom: 30px; }
.obj-index { font-weight: 800; color: #c9a23f; font-size: 1.5rem; font-family: 'Playfair Display'; }
.obj-icon-small { color: #eee; font-size: 1.2rem; }
.obj-item-v3 p { font-size: 1.15rem; line-height: 1.8; color: #444; font-weight: 400; }
.gallery-v3 { padding: 120px 8%; background: #fff; }
.gallery-header { display: flex; align-items: center; gap: 30px; margin-bottom: 50px; }
.gallery-label { font-weight: 700; letter-spacing: 3px; color: #1a3a3a; }
.gallery-year { font-family: 'Playfair Display'; font-style: italic; color: #c9a23f; font-size: 1.2rem; }
.divider-long { flex: 1; height: 1px; background: #eee; }
.gallery-wrapper-v3 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; height: 550px; }
.gallery-item-v3 { overflow: hidden; border-radius: 20px; position: relative; cursor: pointer; }
.gallery-item-v3 img { width: 100%; height: 100%; object-fit: cover; transition: 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
.img-overlay { position: absolute; inset: 0; background: rgba(26, 58, 58, 0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.4s; backdrop-filter: blur(5px); }
.img-overlay span { color: #fff; border: 1px solid #fff; padding: 10px 20px; border-radius: 50px; transform: translateY(20px); transition: 0.4s; }
.gallery-item-v3:hover img { transform: scale(1.1); }
.gallery-item-v3:hover .img-overlay { opacity: 1; }
.gallery-item-v3:hover .img-overlay span { transform: translateY(0); }
.item-0 { grid-column: span 2; }
.item-1 { grid-row: span 2; }
@media (max-width: 1024px) {
  .hero-split { grid-template-columns: 1fr; padding-top: 120px; text-align: center; }
  .hero-para { margin: 0 auto 40px; }
  .hero-action { display: flex; justify-content: center; }
  .hero-image-side { height: 500px; margin-top: 50px; }
  .narrative-container { grid-template-columns: 1fr; gap: 50px; }
  .obj-grid-v3 { grid-template-columns: 1fr; }
  .mega-display-card { grid-template-columns: 1fr; }
  .timeline-box-v2 { grid-template-columns: 1fr; }
  .timeline-nav-v2 { flex-direction: row; border-right: none; border-bottom: 1px solid #eee; overflow-x: auto; padding: 10px; }
  .nav-year-btn { padding: 15px 25px; white-space: nowrap; }
  .nav-year-btn.active::after { width: 100%; height: 2px; top: auto; bottom: 0; left: 0; }
  .gallery-wrapper-v3 { grid-template-columns: 1fr 1fr; height: auto; }
  .item-0, .item-1 { grid-column: span 2; height: 300px; }
}
.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
.fade-slide-enter-from { opacity: 0; transform: translateY(40px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-40px); }
</style>
