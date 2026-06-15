<script setup>
import { appUrl } from '@/utils/urls.js'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, Link, Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'

const slides = ref([])
const currentIndex = ref(0)
const progress = ref(0)
let timer = null

const page = usePage()

const allPromotions = computed(() => page.props.promotions || [])
const productPromotions = computed(() => {
  if (page.props.productPromotions && page.props.productPromotions.length > 0) return page.props.productPromotions;
  return allPromotions.value.filter(p => p.type === 'shop');
})
const centerPromotions = computed(() => {
  if (page.props.centerPromotions && page.props.centerPromotions.length > 0) return page.props.centerPromotions;
  return allPromotions.value.filter(p => p.type === 'center');
})

const prNews = computed(() => page.props.prNews || [])
const actNews = computed(() => page.props.actNews || [])
const articles = computed(() => page.props.articles || [])

const hempVideoComputed = computed(() => {
  let url = page.props.hempVideoUrl || 'https://www.youtube.com/embed/-u-k3cqvbec';
  const separator = url.includes('?') ? '&' : '?';
  return url + separator + 'modestbranding=1&rel=0';
});

const nextSlide = () => {
  if (slides.value.length === 0) return
  currentIndex.value = (currentIndex.value + 1) % slides.value.length
  startProgress()
}

const prevSlide = () => {
  if (slides.value.length === 0) return
  currentIndex.value = (currentIndex.value - 1 + slides.value.length) % slides.value.length
  startProgress()
}

const startProgress = () => {
  progress.value = 0
  clearInterval(timer)
  timer = setInterval(() => {
    progress.value += 1
    if (progress.value >= 100) {
      nextSlide()
    }
  }, 100)
}

const showTop = ref(false)

const prIndex = ref(0)
const actIndex = ref(0)
const artIndex = ref(0)
const centerPromoIndex = ref(0)
const productPromoIndex = ref(0)

let prTimer, actTimer, artTimer, centerPromoTimer, productPromoTimer

const startCarousel = (indexRef, listRef, speed = 4000) => {
  if (!listRef.value || listRef.value.length === 0) return null
  return setInterval(() => {
    const len = listRef.value.length
    if (len <= 1) return
    indexRef.value++
    if (indexRef.value === len) {
      setTimeout(() => {
        indexRef.value = 0
      }, 800)
    }
  }, speed)
}

const bookedDates = computed(() => {
  return page.props.bookedDates || {};
})

const currentDateCal = ref(new Date())
const currentMonthCal = ref(currentDateCal.value.getMonth())
const currentYearCal = ref(currentDateCal.value.getFullYear())
const monthNamesCal = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]

const changeMonthCal = (step) => {
  let newMonth = currentMonthCal.value + step
  if (newMonth > 11) { currentMonthCal.value = 0; currentYearCal.value++ }
  else if (newMonth < 0) { currentMonthCal.value = 11; currentYearCal.value-- }
  else { currentMonthCal.value = newMonth }
}

const calendarDaysDisplay = computed(() => {
  const daysInMonth = new Date(currentYearCal.value, currentMonthCal.value + 1, 0).getDate()
  const firstDay = new Date(currentYearCal.value, currentMonthCal.value, 1).getDay()
  let days = []

  for(let i=0; i < firstDay; i++) days.push(null)

  let today = new Date()
  today.setHours(0,0,0,0)

  for(let i=1; i <= daysInMonth; i++) {
    let d = new Date(currentYearCal.value, currentMonthCal.value, i)
    let dateStr = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
    let blockReason = bookedDates.value[dateStr] || null;
    let isFull = blockReason === 'full';
    let isHoliday = blockReason === 'holiday';
    let isPast = d < today
    days.push({ day: i, dateStr, isFull, isHoliday, isPast })
  }
  return days
})

onMounted(async () => {
  try {
    const slideRes = await fetch(appUrl('/slides'), {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    if (slideRes.ok) {
      const slideData = await slideRes.json()
      if (Array.isArray(slideData) && slideData.length > 0) {
        slides.value = slideData.map(item => item.image)
        startProgress()
      }
    }
  } catch (error) {
    console.warn("Failed to load slides smoothly.")
  }

  const reveals = document.querySelectorAll('.reveal')
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show')
        observer.unobserve(entry.target)
      }
    })
  }, { threshold: 0.15 })

  reveals.forEach(el => observer.observe(el))

  window.addEventListener('scroll', () => {
    showTop.value = window.scrollY > 400
  }, { passive: true })

  prTimer = startCarousel(prIndex, prNews)
  actTimer = startCarousel(actIndex, actNews)
  artTimer = startCarousel(artIndex, articles)
  centerPromoTimer = startCarousel(centerPromoIndex, centerPromotions)
  productPromoTimer = startCarousel(productPromoIndex, productPromotions)
})

onBeforeUnmount(() => {
  clearInterval(timer)
  clearInterval(prTimer)
  clearInterval(actTimer)
  clearInterval(artTimer)
  clearInterval(centerPromoTimer)
  clearInterval(productPromoTimer)
})

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <Head>
    <title>หน้าแรก - ศูนย์การเรียนรู้มหาวิทยาลัยแม่โจ้</title>
    <meta name="description" content="ศูนย์การเรียนรู้และฟาร์มสาธิตสำนักวิจัยและส่งเสริมวิชาการการเกษตร มหาวิทยาลัยแม่โจ้ จองคิวเข้าชมและเลือกซื้อผลิตภัณฑ์การเกษตร">
  </Head>

  <PublicLayout>
    <section class="hero-cinematic" v-if="slides.length > 0">
      <div
        v-for="(img, i) in slides"
        :key="i"
        class="hero-layer"
        :class="{ active: i === currentIndex }"
      >
        <img :src="img" alt="ภาพสไลด์แนะนำศูนย์การเรียนรู้" class="hero-img-zoom" :loading="i === 0 ? 'eager' : 'lazy'" :fetchpriority="i === 0 ? 'high' : 'auto'" decoding="async" />
        <div class="hero-overlay-gradient"></div>
      </div>

      <div class="hero-controls">
        <button class="nav-btn prev" @click="prevSlide" aria-label="เลื่อนสไลด์ก่อนหน้า">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
        </button>
        <div class="hero-indicators">
          <div
            v-for="(s, i) in slides"
            :key="i"
            class="indicator-dot"
            :class="{ active: i === currentIndex }"
            @click="currentIndex = i"
            role="button"
            :aria-label="'ไปยังสไลด์ที่ ' + (i + 1)"
          >
            <div class="indicator-progress" :style="{ width: i === currentIndex ? progress + '%' : '0%' }"></div>
          </div>
        </div>
        <button class="nav-btn next" @click="nextSlide" aria-label="เลื่อนสไลด์ถัดไป">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
        </button>
      </div>
    </section>

    <div class="page-body-bg">
      <section class="chapter-showcase reveal">
        <div class="section-heading center">
          <span class="golden-subtitle">ศูนย์การเรียนรู้ของเรา</span>
          <h2 class="emerald-title">วิดีโอแนะนำศูนย์การเรียนรู้</h2>
        </div>

        <div class="chapter-container">
          <div class="chapter-row left">
            <div class="chapter-visual">
              <div class="blob-bg blob-shape-1"></div>
              <div class="chapter-frame frame-shape-1">
                <iframe src="https://www.youtube.com/embed/3O4fD4i5ky8?modestbranding=1&rel=0" allowfullscreen loading="lazy" title="วิดีโอแนะนำศูนย์การเรียนรู้เกษตรทฤษฎีใหม่" aria-label="วิดีโอแนะนำศูนย์การเรียนรู้เกษตรทฤษฎีใหม่"></iframe>
              </div>
            </div>
            <div class="chapter-content">
              <div class="bg-number">01</div>
              <div class="text-card">
                <h3>ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่</h3>
                <p>
                  สังกัด สำนักวิจัยและส่งเสริมวิชาการการเกษตร<br>
                  จัดตั้งขึ้นเพื่อเป็นแหล่งเรียนรู้และถ่ายทอดองค์ความรู้ด้านการจัดการทรัพยากรการเกษตร ตามแนวทาง “เกษตรทฤษฎีใหม่”
                </p>
                <Link :href="appUrl('/center/royal')" class="btn-explore" aria-label="ดูรายละเอียดศูนย์การเรียนรู้เกษตรทฤษฎีใหม่"><span>ดูรายละเอียด</span></Link>
              </div>
            </div>
          </div>

          <div class="chapter-row right">
            <div class="chapter-content">
              <div class="bg-number">02</div>
              <div class="text-card">
                <h3>ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา</h3>
                <p>
                  สังกัด สำนักวิจัยและส่งเสริมวิชาการการเกษตร<br>
                  ก่อตั้งเมื่อวันที่ 21 มิถุนายน พ.ศ. 2555 เพื่ออนุรักษ์และสืบสานวัฒนธรรมการเกษตรพื้นบ้านล้านนา
                </p>
                <Link :href="appUrl('/center/lanna')" class="btn-explore" aria-label="ดูรายละเอียดศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา"><span>ดูรายละเอียด</span></Link>
              </div>
            </div>
            <div class="chapter-visual">
              <div class="blob-bg blob-shape-2"></div>
              <div class="chapter-frame frame-shape-2">
                <iframe src="https://www.youtube.com/embed/gkQf3NXl-mI?modestbranding=1&rel=0" allowfullscreen loading="lazy" title="วิดีโอแนะนำศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา" aria-label="วิดีโอแนะนำศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา"></iframe>
              </div>
            </div>
          </div>

          <div class="chapter-row left">
            <div class="chapter-visual">
              <div class="blob-bg blob-shape-3"></div>
              <div class="chapter-frame frame-shape-3">
                <iframe src="https://www.youtube.com/embed/7sVE17IZ13Y?modestbranding=1&rel=0" allowfullscreen loading="lazy" title="วิดีโอแนะนำฟาร์มสาธิตสำนักวิจัยฯ" aria-label="วิดีโอแนะนำฟาร์มสาธิตสำนักวิจัยฯ"></iframe>
              </div>
            </div>
            <div class="chapter-content">
              <div class="bg-number">03</div>
              <div class="text-card">
                <h3>ฟาร์มสาธิตสำนักวิจัยฯ</h3>
                <p>
                  สังกัด สำนักวิจัยและส่งเสริมวิชาการการเกษตร<br>
                  จัดตั้งขึ้นเพื่อเป็นแหล่งเรียนรู้และสาธิตด้านการเกษตรแบบครบวงจร สนับสนุนภารกิจด้านการเรียนการสอน
                </p>
                <Link :href="appUrl('/center/farm')" class="btn-explore" aria-label="ดูรายละเอียดฟาร์มสาธิตสำนักวิจัยฯ"><span>ดูรายละเอียด</span></Link>
              </div>
            </div>
          </div>

          <div class="chapter-row right">
            <div class="chapter-content">
              <div class="bg-number">04</div>
              <div class="text-card">
                <h3>โครงการพัฒนาบ้านโปง</h3>
                <p>
                  จังหวัดเชียงใหม่<br>
                  เกิดขึ้นจากพระราชดำริของพระบาทสมเด็จพระบรมชนกาธิเบศร มหาภูมิพลอดุลยเดชมหาราช บรมนาถบพิตร (รัชกาลที่ 9)
                </p>
                <Link :href="appUrl('/center/banpong')" class="btn-explore" aria-label="ดูรายละเอียดโครงการพัฒนาบ้านโปง"><span>ดูรายละเอียด</span></Link>
              </div>
            </div>
            <div class="chapter-visual">
              <div class="blob-bg blob-shape-4"></div>
              <div class="chapter-frame frame-shape-4">
                <iframe src="https://www.youtube.com/embed/vVtT1CL5J5Q?modestbranding=1&rel=0" allowfullscreen loading="lazy" title="วิดีโอแนะนำโครงการพัฒนาบ้านโปง" aria-label="วิดีโอแนะนำโครงการพัฒนาบ้านโปง"></iframe>
              </div>
            </div>
          </div>

           <div class="chapter-row left">
            <div class="chapter-visual">
              <div class="blob-bg blob-shape-1"></div>
              <div class="chapter-frame frame-shape-1">
                <iframe :src="hempVideoComputed" allowfullscreen loading="lazy" title="วิดีโอแนะนำศูนย์วิจัยและพัฒนากัญชง" aria-label="วิดีโอแนะนำศูนย์วิจัยและพัฒนากัญชง"></iframe>
              </div>
            </div>
            <div class="chapter-content">
              <div class="bg-number">05</div>
              <div class="text-card">
                <h3>ศูนย์วิจัยและพัฒนากัญชง</h3>
                <p>
                  ศูนย์กลางความเป็นเลิศด้านการวิจัยและพัฒนากัญชงครบวงจร ตั้งแต่การปลูก การแปรรูป จนถึงการพัฒนาผลิตภัณฑ์ เพื่อประโยชน์ทางการแพทย์และอุตสาหกรรม
                </p>
                <Link :href="appUrl('/center/hemp')" class="btn-explore" aria-label="ดูรายละเอียดศูนย์วิจัยและพัฒนากัญชง">
                  <span>ดูรายละเอียด</span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="calendar-showcase reveal mt-120 mb-80">
        <div class="section-heading center">
          <span class="golden-subtitle">ปฏิทินตรวจสอบคิว</span>
          <h2 class="emerald-title">เช็คคิวว่างเพื่อเข้าชม</h2>
          <p style="color: #64748b; margin-top: 10px; font-size: 16px;">ตรวจสอบตารางคิวว่างก่อนทำการจองกิจกรรม (อัปเดตสถานะแบบเรียลไทม์)</p>
        </div>

        <div class="calendar-luxury-wrapper max-w-4xl mx-auto">
          <div class="cal-header">
            <button class="cal-nav-btn-home" @click="changeMonthCal(-1)" aria-label="เดือนก่อนหน้า">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <div class="cal-month-year" aria-live="polite">{{ monthNamesCal[currentMonthCal] }} <span>{{ currentYearCal + 543 }}</span></div>
            <button class="cal-nav-btn-home" @click="changeMonthCal(1)" aria-label="เดือนถัดไป">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
          </div>

          <div class="cal-grid cal-days-row" aria-hidden="true">
            <div v-for="day in ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส']" :key="day" :style="day==='อา' ? 'color: #ef4444;' : ''">{{ day }}</div>
          </div>

          <div class="cal-grid cal-dates-row" role="grid">
            <div v-for="(day, index) in calendarDaysDisplay" :key="index"
                 class="cal-cell-home"
                 :class="{
                   'is-empty': !day,
                   'is-past': day && day.isPast,
                   'is-blocked': day && day.isFull,
                   'is-holiday': day && day.isHoliday
                 }"
                 role="gridcell">
              <span class="date-num" v-if="day">{{ day.day }}</span>
              <div class="status-indicator" v-if="day && day.isFull">เต็ม</div>
              <div class="status-indicator holiday" v-if="day && day.isHoliday">วันหยุด</div>
            </div>
          </div>

          <div class="cal-legend" aria-hidden="true">
            <div class="l-item"><div class="dot d-avail"></div>ว่าง / เปิดรับจอง</div>
            <div class="l-item"><div class="dot d-full"></div>คิวเต็มแล้ว</div>
            <div class="l-item"><div class="dot d-holiday"></div>วันหยุด</div>
          </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
          <Link :href="appUrl('/service/booking')" class="btn-explore-large" aria-label="ไปยังหน้าจองคอร์สกิจกรรม">
            <span>📅 ทำการจองคอร์สกิจกรรมทันที</span>
          </Link>
        </div>
      </section>

      <section class="premium-slider-section reveal" v-if="prNews.length">
        <div class="section-heading text-left pl-20 pt-80">
          <span class="golden-subtitle">ประกาศข่าวสาร</span>
          <h2 class="emerald-title">ข่าวประชาสัมพันธ์</h2>
        </div>
        <div class="slider-viewport pb-40">
          <div class="slider-track" :class="{ reset: prIndex === 0 }" :style="{ transform: `translateX(-${prIndex * 380}px)` }">
            <Link v-for="item in prNews" :key="'pr-'+item.id" :href="appUrl(`/news/pr/detail/${item.id}`)" class="magazine-card news" :aria-label="'อ่านข่าว: ' + item.title">
              <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพข่าวประชาสัมพันธ์'" loading="lazy" decoding="async" /></div>
              <div class="mag-content">
                <span class="mag-date">{{ new Date(item.created_at || Date.now()).toLocaleDateString('th-TH') }}</span>
                <h4>{{ item.title }}</h4>
                <div class="mag-readmore">อ่านเนื้อหา <span>→</span></div>
              </div>
            </Link>
            <Link v-for="item in prNews.slice(0,3)" :key="'pr-clone-'+item.id" :href="appUrl(`/news/pr/detail/${item.id}`)" class="magazine-card news" aria-hidden="true" tabindex="-1">
              <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพข่าวประชาสัมพันธ์'" loading="lazy" decoding="async" /></div>
              <div class="mag-content">
                <span class="mag-date">{{ new Date(item.created_at || Date.now()).toLocaleDateString('th-TH') }}</span>
                <h4>{{ item.title }}</h4>
                <div class="mag-readmore">อ่านเนื้อหา <span>→</span></div>
              </div>
            </Link>
          </div>
        </div>
      </section>

      <section class="premium-slider-section">
        <div class="promo-wrap reveal" v-if="centerPromotions.length">
          <div class="section-heading text-left pl-20">
            <span class="golden-subtitle green-text">ข้อเสนอสุดพิเศษ</span>
            <h2 class="emerald-title">แพ็กเกจศูนย์การเรียนรู้</h2>
          </div>
          <div class="slider-viewport">
            <div class="slider-track" :class="{ reset: centerPromoIndex === 0 }" :style="{ transform: `translateX(-${centerPromoIndex * 380}px)` }">
              <Link v-for="item in centerPromotions" :key="'center-'+item.id" :href="appUrl('/service/booking')" class="magazine-card promo" :aria-label="'ดูแพ็กเกจ: ' + item.title">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพแพ็กเกจ'" loading="lazy" decoding="async" /></div>
                <div class="mag-overlay green-gradient">
                  <span class="mag-badge">แพ็กเกจเรียนรู้</span>
                  <h4>{{ item.title }}</h4>
                  <p>{{ item.description }}</p>
                </div>
              </Link>
              <Link v-for="item in centerPromotions.slice(0,3)" :key="'center-clone-'+item.id" :href="appUrl('/service/booking')" class="magazine-card promo" aria-hidden="true" tabindex="-1">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพแพ็กเกจ'" loading="lazy" decoding="async" /></div>
                <div class="mag-overlay green-gradient">
                  <span class="mag-badge">แพ็กเกจเรียนรู้</span>
                  <h4>{{ item.title }}</h4>
                  <p>{{ item.description }}</p>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <div class="promo-wrap reveal mt-120" v-if="productPromotions.length">
          <div class="section-heading text-left pl-20">
            <span class="golden-subtitle gold-text">ผลิตภัณฑ์จากฟาร์ม</span>
            <h2 class="emerald-title">โปรโมชันสินค้า</h2>
          </div>
          <div class="slider-viewport">
            <div class="slider-track" :class="{ reset: productPromoIndex === 0 }" :style="{ transform: `translateX(-${productPromoIndex * 380}px)` }">
              <Link v-for="item in productPromotions" :key="'product-'+item.id" :href="appUrl('/service/products')" class="magazine-card promo" :aria-label="'ดูสินค้า: ' + item.title">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพสินค้า'" loading="lazy" decoding="async" /></div>
                <div class="mag-overlay gold-gradient">
                  <span class="mag-badge gold">สินค้าแนะนำ</span>
                  <h4>{{ item.title }}</h4>
                  <p>{{ item.description }}</p>
                </div>
              </Link>
              <Link v-for="item in productPromotions.slice(0,3)" :key="'product-clone-'+item.id" :href="appUrl('/service/products')" class="magazine-card promo" aria-hidden="true" tabindex="-1">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพสินค้า'" loading="lazy" decoding="async" /></div>
                <div class="mag-overlay gold-gradient">
                  <span class="mag-badge gold">สินค้าแนะนำ</span>
                  <h4>{{ item.title }}</h4>
                  <p>{{ item.description }}</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </section>

      <div class="news-container-bg">
        <section class="premium-slider-section reveal" v-if="actNews.length">
          <div class="section-heading text-left pl-20">
            <span class="golden-subtitle">กิจกรรมและงานอีเวนต์</span>
            <h2 class="emerald-title">ข่าวกิจกรรม</h2>
          </div>
          <div class="slider-viewport pb-40">
            <div class="slider-track" :class="{ reset: actIndex === 0 }" :style="{ transform: `translateX(-${actIndex * 380}px)` }">
              <Link v-for="item in actNews" :key="'act-'+item.id" :href="appUrl(item.type === 'pr' ? `/news/pr/detail/${item.id}` : `/news/activity/detail/${item.id}`)" class="magazine-card news" :aria-label="'อ่านกิจกรรม: ' + item.title">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพข่าวกิจกรรม'" loading="lazy" decoding="async" /></div>
                <div class="mag-content">
                  <span class="mag-date">{{ new Date(item.created_at || Date.now()).toLocaleDateString('th-TH') }}</span>
                  <h4>{{ item.title }}</h4>
                  <div class="mag-readmore">อ่านเนื้อหา <span>→</span></div>
                </div>
              </Link>
              <Link v-for="item in actNews.slice(0,3)" :key="'act-clone-'+item.id" :href="appUrl(item.type === 'pr' ? `/news/pr/detail/${item.id}` : `/news/activity/detail/${item.id}`)" class="magazine-card news" aria-hidden="true" tabindex="-1">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพข่าวกิจกรรม'" loading="lazy" decoding="async" /></div>
                <div class="mag-content">
                  <span class="mag-date">{{ new Date(item.created_at || Date.now()).toLocaleDateString('th-TH') }}</span>
                  <h4>{{ item.title }}</h4>
                  <div class="mag-readmore">อ่านเนื้อหา <span>→</span></div>
                </div>
              </Link>
            </div>
          </div>
        </section>

        <section class="premium-slider-section reveal" v-if="articles.length">
          <div class="section-heading text-left pl-20">
            <span class="golden-subtitle">คลังความรู้</span>
            <h2 class="emerald-title">บทความและสาระน่ารู้</h2>
          </div>
          <div class="slider-viewport pb-80">
            <div class="slider-track" :class="{ reset: artIndex === 0 }" :style="{ transform: `translateX(-${artIndex * 380}px)` }">
              <Link v-for="item in articles" :key="'art-'+item.id" :href="appUrl(`/articles/${item.id}`)" class="magazine-card news" :aria-label="'อ่านบทความ: ' + item.title">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพบทความ'" loading="lazy" decoding="async" /></div>
                <div class="mag-content">
                  <span class="mag-date">บทความความรู้</span>
                  <h4>{{ item.title }}</h4>
                  <div class="mag-readmore">อ่านเนื้อหา <span>→</span></div>
                </div>
              </Link>
              <Link v-for="item in articles.slice(0,3)" :key="'art-clone-'+item.id" :href="appUrl(`/articles/${item.id}`)" class="magazine-card news" aria-hidden="true" tabindex="-1">
                <div class="mag-img"><img :src="item.image || '/no-image.jpg'" :alt="item.title || 'ภาพบทความ'" loading="lazy" decoding="async" /></div>
                <div class="mag-content">
                  <span class="mag-date">บทความความรู้</span>
                  <h4>{{ item.title }}</h4>
                  <div class="mag-readmore">อ่านเนื้อหา <span>→</span></div>
                </div>
              </Link>
            </div>
          </div>
        </section>
      </div>

    </div>

    <button class="fab-luxury" :class="{ show: showTop }" @click="scrollToTop" aria-label="กลับสู่ด้านบนของหน้า">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
    </button>
    <FooterFarm />
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Mitr:wght@300;400;500;600&family=Prompt:wght@300;400;500;600&display=swap');

:root {
  --color-emerald-900: #064e3b;
  --color-emerald-700: #047857;
  --color-emerald-500: #10b981;
  --color-gold: #d4af37;
  --color-gold-dark: #9a7300;
  --color-bg-base: #fdfbf7;
  --color-text-dark: #1e293b;
  --color-text-muted: #475569;
  --shadow-elegant: 0 30px 60px -15px rgba(6, 78, 59, 0.15);
  --shadow-hover: 0 40px 80px -15px rgba(6, 78, 59, 0.25);
}

* { font-family: 'Prompt', sans-serif; box-sizing: border-box; }

.page-body-bg {
  background-color: var(--color-bg-base);
  background-image: radial-gradient(rgba(16, 185, 129, 0.03) 1px, transparent 1px);
  background-size: 40px 40px;
  overflow: hidden;
  color: var(--color-text-dark);
}

.hero-cinematic {
  position: relative; width: 100%; height: 95vh; min-height: 700px;
  background: #000; display: flex; align-items: center; justify-content: center;
}

.hero-layer {
  position: absolute; inset: 0; opacity: 0;
  transition: opacity 1.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.hero-layer.active { opacity: 1; z-index: 1; }

.hero-img-zoom {
  width: 100%; height: 100%; object-fit: cover; object-position: center;
  transform: scale(1.15); transition: transform 12s ease-out;
}
.hero-layer.active .hero-img-zoom { transform: scale(1); }

.hero-overlay-gradient {
  position: absolute; inset: 0; margin-top: -8px; width: 100%; height: 100vh;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0) 40%, rgba(0, 0, 0, 0.6) 80%, rgba(0, 0, 0, 0.9) 100%); pointer-events: none;
}
.hero-controls {
  position: absolute; bottom: 50px; width: 100%; display: flex;
  justify-content: center; align-items: center; gap: 40px; z-index: 10;
}

.nav-btn {
  width: 54px; height: 54px; border-radius: 50%;
  background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2); color: white;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.4s ease;
}
.nav-btn:hover { background: var(--color-gold); border-color: var(--color-gold); transform: scale(1.1); color: #000; }

.hero-indicators { display: flex; gap: 12px; align-items: center; }
.indicator-dot { width: 50px; height: 3px; background: rgba(255,255,255,0.3); cursor: pointer; position: relative; overflow: hidden; }
.indicator-progress { position: absolute; top: 0; left: 0; height: 100%; background: var(--color-gold); transition: width 0.1s linear; }

.section-heading { margin-bottom: 60px; position: relative; }
.section-heading.center { text-align: center; padding: 0 24px; }
.section-heading.text-left { text-align: left; }

.pl-20 { padding-left: max(24px, calc((100vw - 1200px) / 2)); }
.pt-80 { padding-top: 80px; }

.golden-subtitle {
  font-family: 'Cinzel', serif; font-size: 14px; font-weight: 700;
  color: var(--color-gold-dark); letter-spacing: 4px; display: flex; align-items: center;
  text-transform: uppercase; margin-bottom: 12px;
}
.section-heading.center .golden-subtitle { justify-content: center; }
.section-heading.center .golden-subtitle::before,
.section-heading.center .golden-subtitle::after {
  content: ""; display: inline-block; width: 40px; height: 2px;
  background-color: var(--color-gold-dark); margin: 0 16px; opacity: 0.6;
}
.section-heading.text-left .golden-subtitle::before {
  content: ""; display: inline-block; width: 40px; height: 2px;
  background-color: var(--color-gold-dark); margin-right: 16px; opacity: 0.6;
}
.golden-subtitle.green-text { color: var(--color-emerald-700); }
.golden-subtitle.green-text::before { background-color: var(--color-emerald-700); }

.emerald-title {
  font-family: 'Mitr', sans-serif; font-size: 44px; font-weight: 500; margin: 0; line-height: 1.2;
  background: linear-gradient(to right, #064e3b, #047857); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  text-shadow: 0px 10px 20px rgba(6, 78, 59, 0.12);
}

.chapter-showcase { padding: 80px 0 20px 0; max-width: 1000px; margin: 0 auto; }
.chapter-container { display: flex; flex-direction: column; gap: 120px; padding: 0 20px; }

.chapter-row { display: flex; align-items: center; position: relative; }
.chapter-row.left { flex-direction: row; }
.chapter-row.right { flex-direction: row; justify-content: flex-end; }

.chapter-visual { flex: 0 0 55%; position: relative; z-index: 2; }
.blob-bg { position: absolute; inset: -20px; background: linear-gradient(135deg, rgba(27, 122, 90, 0.1), rgba(212, 175, 55, 0.1)); z-index: -1; transition: all 0.5s ease; }

.chapter-frame { position: relative; background: #ffffff; padding: 8px; box-shadow: 0 20px 40px rgba(15, 76, 58, 0.12); overflow: hidden; transition: transform 0.4s ease; }
.chapter-frame:hover { transform: translateY(-5px); }
.chapter-frame iframe { width: 100%; aspect-ratio: 16/9; border: none; display: block; border-radius: inherit; }

.frame-shape-1 { border-radius: 40px 120px 40px 120px; }
.blob-shape-1 { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
.frame-shape-2 { border-radius: 120px 40px 120px 40px; }
.blob-shape-2 { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
.frame-shape-3 { border-radius: 40px 120px 120px 40px; }
.blob-shape-3 { border-radius: 50% 50% 20% 80% / 25% 80% 20% 75%; }
.frame-shape-4 { border-radius: 120px 40px 40px 120px; }
.blob-shape-4 { border-radius: 40% 60% 70% 30% / 40% 50% 50% 60%; }

.chapter-content { flex: 0 0 50%; position: relative; z-index: 3; }
.chapter-row.left .chapter-content { margin-left: -5%; }
.chapter-row.right .chapter-content { margin-right: -5%; z-index: 3; }

.bg-number { font-family: 'Cinzel', serif; font-size: 180px; font-weight: 900; color: rgba(15, 76, 58, 0.04); position: absolute; top: -60px; line-height: 1; z-index: -1; user-select: none; }
.chapter-row.left .bg-number { right: 20px; }
.chapter-row.right .bg-number { left: 20px; }

.text-card {
  background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(10px); padding: 40px; border-radius: 24px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.06); border: 1px solid rgba(255,255,255,0.7);
}
.text-card h3 { font-family: 'Mitr', sans-serif; font-size: 26px; color: var(--color-emerald-900); margin: 0 0 16px 0; font-weight: 500; line-height: 1.4; }
.text-card p { font-size: 15px; color: var(--color-text-dark); font-weight: 400; line-height: 1.8; margin-bottom: 24px; }

.btn-explore {
  display: inline-flex; align-items: center; padding: 10px 28px; background: #064e3b; color: #fff;
  border-radius: 30px; text-decoration: none; font-weight: 500; font-size: 14px;
  transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(6, 78, 59, 0.25);
}
.btn-explore:hover { background: #047857; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(6, 78, 59, 0.35); }

.btn-explore-large {
  display: inline-flex; align-items: center; padding: 16px 40px; background: linear-gradient(135deg, #10b981, #059669); color: #fff;
  border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 18px;
  transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3); font-family: 'Prompt';
}
.btn-explore-large:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 15px 40px rgba(16, 185, 129, 0.5); }

.calendar-showcase { max-width: 900px; margin: 0 auto; padding: 20px; }
.calendar-luxury-wrapper { background: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,1); border-radius: 24px; padding: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }

.cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.cal-nav-btn-home { width: 44px; height: 44px; border-radius: 14px; border: 1px solid #e2e8f0; background: #ffffff; color: #334155; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
.cal-nav-btn-home:hover { background: #10b981; color: white; border-color: #10b981; transform: scale(1.05); }
.cal-month-year { font-family: 'Prompt'; font-size: 22px; font-weight: 800; color: #0f172a; }
.cal-month-year span { color: #10b981; }

.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center; }
.cal-days-row div { font-size: 14px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 12px; }
.cal-cell-home { position: relative; height: 65px; background: #ffffff; border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; color: #334155; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid transparent; }

.cal-cell-home.is-empty { background: transparent; box-shadow: none; }
.cal-cell-home.is-past { background: transparent; color: #cbd5e1; }

.cal-cell-home.is-blocked { background: #fef2f2; color: #ef4444; border-color: #fecaca; opacity: 0.8; }
.cal-cell-home.is-holiday { background: #fffbeb; color: #d97706; border-color: #fde68a; opacity: 0.8; }

.cal-cell-home:not(.is-empty):not(.is-past):not(.is-blocked):not(.is-holiday) { border-color: #e2e8f0; }
.cal-cell-home:not(.is-empty):not(.is-past):not(.is-blocked):not(.is-holiday):hover { border-color: #10b981; transform: translateY(-3px); box-shadow: 0 8px 15px rgba(16,185,129,0.15); color: #10b981; }

.status-indicator { font-size: 11px; background: #ef4444; color: white; padding: 2px 8px; border-radius: 4px; margin-top: 4px; line-height: 1; font-weight: 800; letter-spacing: 0.5px; }
.status-indicator.holiday { background: #f59e0b; }

.cal-legend { display: flex; justify-content: center; gap: 24px; margin-top: 30px; }
.l-item { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #64748b; }
.dot { width: 12px; height: 12px; border-radius: 50%; }
.d-avail { background: #ffffff; border: 2px solid #cbd5e1; }
.d-full { background: #ef4444; }
.d-holiday { background: #f59e0b; }

.premium-slider-section { padding: 40px 0; }
.slider-viewport { overflow: hidden; max-width: 1400px; margin: 0 auto; padding: 20px 0 20px max(24px, calc((100vw - 1200px) / 2)); }
.slider-track { display: flex; gap: 30px; will-change: transform; transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1); }
.slider-track.reset { transition: none !important; }

.mt-120 { margin-top: 80px; }
.mb-80 { margin-bottom: 80px; }
.pb-40 { padding-bottom: 40px; }
.pb-80 { padding-bottom: 80px; }

.magazine-card {
  min-width: 350px; max-width: 350px; border-radius: 20px; overflow: hidden;
  text-decoration: none; color: inherit; position: relative; display: block;
  box-shadow: 0 15px 35px rgba(0,0,0,0.06); transition: all 0.5s ease;
  background: white; border: 1px solid rgba(0,0,0,0.03);
}
.magazine-card:hover { transform: translateY(-12px); box-shadow: var(--shadow-hover); }

.mag-img { width: 100%; overflow: hidden; position: relative; }
.mag-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
.magazine-card:hover .mag-img img { transform: scale(1.08); }

.magazine-card.promo { height: 480px; }
.magazine-card.promo .mag-img { height: 100%; }

.mag-overlay { position: absolute; inset: 0; padding: 32px 28px; display: flex; flex-direction: column; justify-content: flex-end; color: white; z-index: 2; }
.green-gradient { background: linear-gradient(to top, rgba(6,78,59,0.95) 0%, rgba(6,78,59,0.3) 60%, transparent 100%); }
.gold-gradient { background: linear-gradient(to top, rgba(120,53,15,0.95) 0%, rgba(120,53,15,0.3) 60%, transparent 100%); }

.mag-badge { display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 16px; background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3); width: fit-content; }

.mag-overlay h4 { font-family: 'Mitr', sans-serif; font-size: 26px; font-weight: 500; margin: 0 0 12px 0; line-height: 1.3; }
.mag-overlay p { font-size: 15px; color: rgba(255,255,255,0.85); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.news-container-bg { background: #ffffff; border-top-left-radius: 80px; border-top-right-radius: 80px; padding-top: 40px; box-shadow: 0 -20px 60px rgba(0,0,0,0.02); }

.magazine-card.news { background: transparent; box-shadow: none; border: none; }
.magazine-card.news:hover { transform: translateY(-8px); box-shadow: none; }
.magazine-card.news .mag-img { height: 260px; border-radius: 20px; margin-bottom: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.08); }

.mag-content { padding: 0 8px; }
.mag-date { font-family: 'Cinzel', serif; font-size: 13px; font-weight: 700; color: var(--color-gold-dark); margin-bottom: 12px; display: block; letter-spacing: 1px; }
.mag-content h4 { font-family: 'Mitr', sans-serif; font-size: 22px; font-weight: 500; color: var(--color-emerald-900); margin: 0 0 20px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: color 0.3s; }
.magazine-card.news:hover h4 { color: var(--color-emerald-500); }

.mag-readmore { font-size: 15px; font-weight: 600; color: var(--color-text-dark); display: flex; align-items: center; gap: 8px; transition: gap 0.3s ease; }
.mag-readmore span { color: var(--color-emerald-500); transition: transform 0.3s; }
.magazine-card.news:hover .mag-readmore { gap: 12px; }

.fab-luxury {
  position: fixed; right: 32px; bottom: 32px; width: 56px; height: 56px;
  border-radius: 50%; background: var(--color-emerald-900); color: var(--color-gold);
  border: 2px solid rgba(212, 175, 55, 0.3); display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 15px 30px rgba(6, 78, 59, 0.4);
  opacity: 0; visibility: hidden; transform: translateY(20px);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 99;
}
.fab-luxury.show { opacity: 1; visibility: visible; transform: translateY(0); }
.fab-luxury:hover { background: var(--color-emerald-700); border-color: var(--color-gold); transform: translateY(-6px) scale(1.05); }

.reveal { opacity: 0; transform: translateY(50px); transition: all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
.reveal.show { opacity: 1; transform: translateY(0); }

@media (max-width: 1024px) {
  .chapter-block, .chapter-block.reverse { flex-direction: column; }
  .chapter-video, .chapter-glass-card { flex: auto; width: 100%; }
  .chapter-container { gap: 100px; }
  .emerald-title { font-size: 38px; }
}

@media (max-width: 900px) {
  .chapter-row.left, .chapter-row.right { flex-direction: column; }
  .chapter-visual, .chapter-content { flex: 0 0 100%; width: 100%; }
  .chapter-row.left .chapter-content, .chapter-row.right .chapter-content { margin: -30px 0 0 0; padding: 0 10px; }
  .bg-number { font-size: 120px; top: -40px; right: 10px !important; left: auto !important; }
  .text-card { padding: 30px 20px; }
}

@media (max-width: 768px) {
  .hero-cinematic { height: 75vh; min-height: 550px; }
  .emerald-title { font-size: 30px; }
  .nav-btn { width: 44px; height: 44px; }
  .magazine-card { min-width: 300px; max-width: 300px; }
  .cal-cell-home { height: 55px; font-size: 16px; }
}

@media (max-width: 480px) {
  .emerald-title { font-size: 26px; }
  .magazine-card { min-width: 280px; max-width: 280px; }
  .slider-track { gap: 20px; }
  .hero-controls { bottom: 30px; gap: 20px; }
  .pl-20 { padding-left: 20px; }
  .cal-cell-home { height: 45px; font-size: 14px; }
  .status-indicator { font-size: 9px; padding: 2px 4px; }
  .btn-explore-large { font-size: 14px; padding: 12px 24px; }
}
</style>
