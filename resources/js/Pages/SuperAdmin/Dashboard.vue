<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { onMounted, nextTick, computed, ref, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  centerIncome: {
    type: Array,
    default: () => []
  },
  bookingStats: {
    type: Object,
    default: () => ({
      labels: [],
      people: [],
      counts: [],
      totalIncome: 0
    })
  },
  todayVisitors: { // 🔥 รับค่าผู้เข้าชมวันนี้
    type: Number,
    default: 0
  }
})

// ===============================
// 📌 จัดกลุ่มและบวกยอดขายสินค้าศูนย์เดียวกัน
// ===============================
const fullCenterIncome = computed(() => {
  const incomeMap = {
    'ศูนย์วัฒนธรรมเกษตรล้านนา': 0,
    'ศูนย์เกษตรทฤษฎีใหม่': 0,
    'ศูนย์ฟาร์มสาธิต': 0,
    'ศูนย์บ้านโป่ง': 0,
    'ศูนย์การเรียนรู้กัญชง': 0
  }

  let otherIncome = 0

  props.centerIncome?.forEach(dbItem => {
    const name = dbItem.center_name || ''
    const income = Number(dbItem.total_income) || 0

    if (name.includes('วัฒนธรรมเกษตรล้านนา')) {
      incomeMap['ศูนย์วัฒนธรรมเกษตรล้านนา'] += income
    } else if (name.includes('ทฤษฎีใหม่')) {
      incomeMap['ศูนย์เกษตรทฤษฎีใหม่'] += income
    } else if (name.includes('ฟาร์มสาธิต')) {
      incomeMap['ศูนย์ฟาร์มสาธิต'] += income
    } else if (name.includes('บ้านโป่ง')) {
      incomeMap['ศูนย์บ้านโป่ง'] += income
    } else if (name.includes('กัญชง')) {
      incomeMap['ศูนย์การเรียนรู้กัญชง'] += income
    } else {
      otherIncome += income
    }
  })

  const mappedCenters = Object.keys(incomeMap).map(key => ({
    center_name: key,
    total_income: incomeMap[key]
  }))

  if (otherIncome > 0) {
    mappedCenters.push({
      center_name: 'สินค้าอื่นๆ (ไม่ระบุศูนย์)',
      total_income: otherIncome
    })
  }

  return mappedCenters.sort((a, b) => b.total_income - a.total_income)
})

const fullBookingStats = computed(() => {
  return {
    labels: props.bookingStats?.labels || [],
    people: props.bookingStats?.people || [],
    counts: props.bookingStats?.counts || []
  }
})

const totalBookingIncome = computed(() => {
  return props.bookingStats?.totalIncome ?? 0
})

const formatCurrency = (value) => {
  return Number(value || 0).toLocaleString('th-TH')
}

// ===============================
// Chart.js Configuration
// ===============================
let chartInstance = null
const chartCanvas = ref(null)

const barShadowPlugin = {
  id: 'barShadowPlugin',
  beforeDatasetsDraw(chart) {
    const ctx = chart.ctx
    ctx.save()
    ctx.shadowColor = 'rgba(0, 0, 0, 0.25)'
    ctx.shadowBlur = 15
    ctx.shadowOffsetX = 0
    ctx.shadowOffsetY = 8
  },
  afterDatasetsDraw(chart) {
    chart.ctx.restore()
  }
}

const renderChart = () => {
  if (!chartCanvas.value) return

  const stats = fullBookingStats.value
  if (!stats.labels.length) return

  if (chartInstance) {
    chartInstance.destroy()
  }

  const ctx = chartCanvas.value.getContext('2d')

  const gradient1 = ctx.createLinearGradient(0, 0, 0, 400)
  gradient1.addColorStop(0, '#a855f7')
  gradient1.addColorStop(1, '#ec4899')

  const gradient2 = ctx.createLinearGradient(0, 0, 0, 400)
  gradient2.addColorStop(0, '#06b6d4')
  gradient2.addColorStop(1, '#3b82f6')

  chartInstance = new Chart(chartCanvas.value, {
    type: 'bar',
    plugins: [barShadowPlugin],
    data: {
      labels: stats.labels,
      datasets: [
        {
          label: 'จำนวนผู้เข้าชม (คน)',
          data: stats.people,
          backgroundColor: gradient1,
          borderRadius: 100,
          barThickness: 20,
          borderSkipped: false,
          hoverBackgroundColor: '#d946ef'
        },
        {
          label: 'ความถี่การจอง (ครั้ง)',
          data: stats.counts,
          backgroundColor: gradient2,
          borderRadius: 100,
          barThickness: 20,
          borderSkipped: false,
          hoverBackgroundColor: '#0ea5e9'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: { padding: { top: 20 } },
      plugins: {
        legend: { position: 'top', align: 'end', labels: { font: { family: "'Prompt', sans-serif", size: 14, weight: '600' }, usePointStyle: true, pointStyle: 'circle', padding: 24, color: '#334155' } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.85)', backdropFilter: 'blur(10px)', titleFont: { family: "'Prompt', sans-serif", size: 15, weight: 'bold' }, bodyFont: { family: "'Prompt', sans-serif", size: 14 }, padding: 16, cornerRadius: 16, boxPadding: 8, borderColor: 'rgba(255,255,255,0.15)', borderWidth: 1, usePointStyle: true }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { family: "'Prompt', sans-serif", weight: '600' }, color: '#64748b', padding: 10 }, border: { display: false } },
        y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.8)', borderDash: [8, 8], drawBorder: false }, ticks: { font: { family: "'Prompt', sans-serif", weight: '500' }, color: '#94a3b8', padding: 15 }, border: { display: false } }
      },
      animation: { y: { duration: 1500, easing: 'easeOutQuart' } },
      interaction: { mode: 'index', intersect: false, }
    }
  })
}

onMounted(async () => {
  await nextTick()
  renderChart()
})

watch(() => props.bookingStats, () => {
  renderChart()
}, { deep: true })
</script>

<template>
  <AdminLayout>
    <div class="ultra-premium-dashboard">
      <div class="bg-pattern"></div>

      <div class="dashboard-header animate-slide-down">
        <div>
          <h1 class="page-title">
            <span class="gradient-text">สรุปภาพรวม</span> การดำเนินการ
          </h1>
          <p class="page-subtitle">แสดงสถิติการจองและรายได้ล่าสุดแบบเรียลไทม์ (นับเฉพาะคิวที่เสร็จสิ้น)</p>
        </div>
        <div class="live-badge">
          <span class="pulse-ring"></span>
          <span class="pulse-dot"></span>
          Live System
        </div>
      </div>

      <div class="revenue-banner-container animate-fade-in stagger-1">
        <div class="revenue-banner">
          <div class="banner-aurora"></div>

          <div class="banner-content">
            <div class="banner-left">
              <div class="icon-glass">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <div class="banner-text">
                <p class="banner-label">ยอดรายได้รวม (กิจกรรมที่เสร็จสิ้นแล้ว)</p>
                <div class="amount-wrapper">
                  <span class="currency">฿</span>
                  <h2 class="banner-value">{{ formatCurrency(totalBookingIncome) }}</h2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="chart-section animate-fade-in stagger-2">
        <div class="premium-card chart-card">
          <div class="card-header">
            <div class="header-title-group">
              <div class="icon-box gradient-blue-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
              </div>
              <div>
                <h3 class="card-title">สถิติการจองกิจกรรมแยกตามศูนย์</h3>
                <p class="card-subtitle">เปรียบเทียบจำนวนผู้เข้าชมและความถี่ในการจอง (นับเฉพาะกิจกรรมที่เสร็จสิ้น)</p>
              </div>
            </div>
          </div>
          <div class="canvas-wrapper">
            <canvas ref="chartCanvas"></canvas>
          </div>
        </div>
      </div>

      <div class="leaderboard-section animate-fade-in stagger-3">
        <div class="section-title-wrapper">
          <div class="icon-box gradient-orange-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
          </div>
          <h3 class="section-title">จัดอันดับยอดขายสินค้าสูงสุด</h3>
        </div>

        <div class="ranking-grid">
          <div
            v-for="(center, index) in fullCenterIncome"
            :key="index"
            class="rank-card"
            :class="`rank-style-${index + 1}`"
          >
            <div class="shimmer" v-if="index < 3"></div>

            <div class="card-inner">
              <div class="rank-header">
                <div class="medal-badge">
                  <span v-if="index === 0" class="trophy">🏆</span>
                  <span v-else-if="index === 1" class="trophy">🥈</span>
                  <span v-else-if="index === 2" class="trophy">🥉</span>
                  <span v-else class="normal-rank">#{{ index + 1 }}</span>
                </div>
              </div>

              <div class="center-info">
                <h4 class="center-name" :title="center.center_name">{{ center.center_name }}</h4>
              </div>

              <div class="rank-footer">
                <div class="sales-label">ยอดขายสะสม</div>
                <div class="sales-amount">
                  <span class="currency-small">฿</span>
                  <span class="number-val">{{ formatCurrency(center.total_income) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap');

.ultra-premium-dashboard { padding: 40px 32px 80px; font-family: 'Prompt', sans-serif; background-color: #f0f4f8; min-height: 100vh; position: relative; overflow: hidden; }
.bg-pattern { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: linear-gradient(rgba(148, 163, 184, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(148, 163, 184, 0.05) 1px, transparent 1px); background-size: 30px 30px; z-index: 0; pointer-events: none; }
.animate-slide-down { opacity: 0; animation: slideDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; position: relative; z-index: 2; }
.animate-fade-in { opacity: 0; transform: translateY(30px); animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; position: relative; z-index: 2; }
.stagger-1 { animation-delay: 0.1s; } .stagger-2 { animation-delay: 0.2s; } .stagger-3 { animation-delay: 0.3s; }

@keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
@keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
@keyframes shiftAurora { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
@keyframes shimmerAnim { 0% { left: -150%; } 100% { left: 150%; } }

.dashboard-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
.page-title { font-size: 36px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; letter-spacing: -1px; }
.gradient-text { background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.page-subtitle { font-size: 16px; color: #64748b; font-weight: 500; margin: 0; }
.live-badge { display: flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 10px 20px; border-radius: 100px; font-size: 14px; font-weight: 700; color: #059669; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,1); position: relative; }
.pulse-dot { width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; position: relative; z-index: 2; }
.pulse-ring { position: absolute; left: 20px; width: 8px; height: 8px; border-radius: 50%; animation: pulse 2s infinite; z-index: 1; }

.revenue-banner-container { margin-bottom: 40px; }
.revenue-banner { background-color: #0f172a; border-radius: 36px; padding: 48px 56px; position: relative; overflow: hidden; box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.4); display: flex; align-items: center; }
.banner-aurora { position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%; background: radial-gradient(circle at 50% 50%, rgba(56, 189, 248, 0.2) 0%, rgba(139, 92, 246, 0.2) 30%, transparent 60%); background-size: 200% 200%; animation: shiftAurora 15s ease infinite; pointer-events: none; z-index: 1; filter: blur(40px); }
.banner-content { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 30px; }

/* ฝั่งซ้าย (รายได้รวม) */
.banner-left { display: flex; align-items: center; gap: 32px; }
.icon-glass { background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.02) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.15); width: 80px; height: 80px; border-radius: 24px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: inset 0 2px 4px rgba(255,255,255,0.1), 0 10px 20px rgba(0,0,0,0.2); }
.icon-glass svg { width: 40px; height: 40px; }
.banner-text { color: white; }
.banner-label { font-size: 15px; font-weight: 600; color: #94a3b8; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px; }
.amount-wrapper { display: flex; align-items: baseline; gap: 10px; }
.currency { font-size: 32px; font-weight: 400; color: #38bdf8; }
.banner-value { font-size: 64px; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -2px; background: linear-gradient(to bottom, #ffffff 0%, #cbd5e1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 4px 15px rgba(0,0,0,0.3)); }

/* 🌟 ฝั่งขวา (Widget วันนี้) */
.banner-right { display: flex; justify-content: flex-end; }
.today-visitor-widget { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 24px; padding: 20px 32px; display: flex; align-items: center; gap: 20px; color: white; box-shadow: inset 0 2px 5px rgba(255,255,255,0.05), 0 10px 20px rgba(0,0,0,0.1); }
.tv-icon { font-size: 36px; background: rgba(16, 185, 129, 0.2); color: #34d399; width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; border-radius: 18px; border: 1px solid rgba(16, 185, 129, 0.3); }
.tv-info { display: flex; flex-direction: column; }
.tv-label { font-size: 14px; color: #cbd5e1; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.tv-value-wrap { display: flex; align-items: baseline; gap: 8px; }
.tv-value { font-size: 42px; font-weight: 800; margin: 0; color: #34d399; line-height: 1; }
.tv-value-wrap small { font-size: 18px; font-weight: 600; color: #94a3b8; }

.chart-section { margin-bottom: 48px; }
.premium-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border-radius: 36px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05); border: 1px solid rgba(255,255,255,1); }
.card-header { margin-bottom: 30px; }
.header-title-group { display: flex; align-items: center; gap: 20px; }
.icon-box { width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.15); }
.icon-box svg { width: 28px; height: 28px; }
.gradient-blue-box { background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%); shadow-color: #3b82f6; }
.gradient-orange-box { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); shadow-color: #ea580c; }
.card-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; }
.card-subtitle { font-size: 15px; color: #64748b; margin: 0; }
.canvas-wrapper { height: 400px; width: 100%; position: relative; }

.section-title-wrapper { display: flex; align-items: center; gap: 16px; margin-bottom: 30px; }
.section-title { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0; }
.ranking-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; }
.rank-card { position: relative; border-radius: 28px; transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow: hidden; background: white; }
.rank-card:hover { transform: translateY(-12px) scale(1.02); z-index: 10; box-shadow: 0 30px 60px -15px rgba(0,0,0,0.1); }
.shimmer { position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, transparent, rgba(255,255,255,0.6), transparent); transform: skewX(-20deg); animation: shimmerAnim 3s infinite; z-index: 5; pointer-events: none; }
.card-inner { background: #ffffff; border-radius: 28px; padding: 32px 24px; height: 100%; display: flex; flex-direction: column; position: relative; z-index: 2; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); border: 2px solid transparent; }
.rank-style-1 { padding: 4px; background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%); } .rank-style-1 .card-inner { background: linear-gradient(180deg, #fffbeb 0%, #ffffff 50%); }
.rank-style-2 { padding: 4px; background: linear-gradient(135deg, #e2e8f0 0%, #94a3b8 100%); } .rank-style-2 .card-inner { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 50%); }
.rank-style-3 { padding: 4px; background: linear-gradient(135deg, #fed7aa 0%, #d97706 100%); } .rank-style-3 .card-inner { background: linear-gradient(180deg, #fff7ed 0%, #ffffff 50%); }
.rank-style-4, .rank-style-5 { padding: 0; } .rank-style-4 .card-inner, .rank-style-5 .card-inner { border: 2px solid #f1f5f9; }
.rank-header { margin-bottom: 24px; text-align: center; }
.medal-badge { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.06); margin: 0 auto; }
.trophy { font-size: 32px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); }
.normal-rank { font-size: 20px; font-weight: 800; color: #94a3b8; }
.center-info { flex-grow: 1; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
.center-name { font-size: 17px; font-weight: 700; color: #1e293b; text-align: center; margin: 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.rank-footer { text-align: center; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); }
.sales-label { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
.sales-amount { display: flex; align-items: baseline; justify-content: center; gap: 4px; }
.currency-small { font-size: 18px; font-weight: 600; }
.number-val { font-size: 32px; font-weight: 800; letter-spacing: -1px; }

.rank-style-1 .number-val, .rank-style-1 .currency-small { color: #d97706; }
.rank-style-2 .number-val, .rank-style-2 .currency-small { color: #475569; }
.rank-style-3 .number-val, .rank-style-3 .currency-small { color: #b45309; }
.rank-style-4 .number-val, .rank-style-4 .currency-small, .rank-style-5 .number-val, .rank-style-5 .currency-small { color: #0f172a; }

@media (max-width: 1024px) {
  .banner-content { flex-direction: column; align-items: stretch; }
  .banner-right { justify-content: flex-start; }
  .revenue-banner { padding: 32px; }
  .banner-value { font-size: 48px; }
}
@media (max-width: 640px) { .ranking-grid { grid-template-columns: 1fr; } }
</style>
