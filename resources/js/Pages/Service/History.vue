<template>
  <PublicLayout>
    <Head title="ประวัติการจองทั้งหมด" />

    <div class="history-page-wrapper">
      <div class="container-inner">

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="page-title">ประวัติการจองทั้งหมด</h1>
                <p class="page-subtitle">รายการแจ้งความประสงค์เข้าชมศูนย์การเรียนรู้ของคุณ</p>
            </div>
            <Link href="/service/booking" class="btn-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                กลับไปหน้าจอง
            </Link>
        </div>

        <div class="glass-card">
          <div v-if="bookings.length === 0" class="text-center py-10 text-gray-500">
             ไม่มีประวัติการทำรายการ
          </div>

          <div v-else class="custom-table-wrapper">
            <table class="premium-table">
              <thead>
                <tr>
                  <th>วันที่เข้าชม</th>
                  <th>รายละเอียดศูนย์</th>
                  <th>ผู้ติดต่อ</th>
                  <th>จำนวน</th>
                  <th>ยอดชำระ</th>
                  <th>สถานะ</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="b in paginatedBookings" :key="b.id">
                  <td>
                    <div class="date-block" v-if="b.visit_date">
                      <span class="d-day">{{ new Date(b.visit_date).getDate() }}</span>
                      <span class="d-mth">{{ monthNames[new Date(b.visit_date).getMonth()].substring(0,3) }}</span>
                      <span class="d-yr">{{ new Date(b.visit_date).getFullYear() + 543 }}</span>
                    </div>
                    <span v-else class="waiting-date-badge">รอแอดมินระบุ</span>
                  </td>
                  <td>
                    <div class="t-centers">{{ Array.isArray(b.centers) ? b.centers.join(', ') : b.centers }}</div>
                    <div class="t-acts" v-if="b.activities">
                      <span v-for="(acts, cName) in b.activities" :key="cName" class="t-tag">
                        {{ cName }}: {{ Array.isArray(acts) ? acts.length : 0 }} กิจกรรม
                      </span>
                    </div>
                  </td>
                  <td>
                      <div class="text-sm font-bold text-gray-800">{{ b.name }}</div>
                      <div class="text-xs text-gray-500">{{ b.phone }}</div>
                  </td>
                  <td class="font-bold text-gray-700">{{ b.people }} คน</td>
                  <td class="t-price">฿ {{ Number(b.total_price).toLocaleString() }}</td>
                  <td>
                    <div class="status-badge-modern" :class="{
                      'is-appr': b.status === 'อนุมัติ',
                      'is-pend': b.status === 'รอยืนยัน',
                      'is-rej': b.status === 'ยกเลิก'
                    }">
                      <span class="s-dot"></span> {{ b.status }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="pagination-wrapper" v-if="totalPages > 1">
            <button class="page-btn" @click.prevent="prevPage" :disabled="currentPage === 1">« ก่อนหน้า</button>
            <div class="page-numbers hidden md:flex">
              <button
                v-for="p in totalPages"
                :key="p"
                @click.prevent="goToPage(p)"
                class="page-num"
                :class="{ active: currentPage === p }"
              >
                {{ p }}
              </button>
            </div>
            <span class="md:hidden text-gray-500 font-bold">หน้า {{ currentPage }} / {{ totalPages }}</span>
            <button class="page-btn" @click.prevent="nextPage" :disabled="currentPage === totalPages">ถัดไป »</button>
          </div>

        </div>

      </div>
    </div>
    <FooterFarm />
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, usePage, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'

const page = usePage()
const bookings = page.props.bookings || []

const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]

// =========================
// 🔥 Pagination Logic (10 รายการต่อหน้า)
// =========================
const currentPage = ref(1)
const itemsPerPage = 10

const totalPages = computed(() => Math.max(1, Math.ceil(bookings.length / itemsPerPage)))

const paginatedBookings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return bookings.slice(start, start + itemsPerPage)
})

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
      currentPage.value++
      window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
      currentPage.value--
      window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToPage = (p) => {
  currentPage.value = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=Prompt:wght@500;700;800&display=swap');

.history-page-wrapper { min-height: 80vh; background: #f8fafc; font-family: 'Sarabun', sans-serif; padding: 60px 20px; }
.container-inner { max-width: 1100px; margin: 0 auto; }

.page-title { font-family: 'Prompt'; font-size: 32px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; }
.page-subtitle { font-size: 16px; color: #64748b; margin: 0; }

.btn-back { display: inline-flex; align-items: center; gap: 8px; font-family: 'Prompt'; font-weight: 600; color: #475569; background: white; padding: 10px 20px; border-radius: 50px; border: 1px solid #cbd5e1; transition: 0.2s; }
.btn-back:hover { border-color: #10b981; color: #10b981; transform: translateX(-4px); }

.glass-card { background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.02); }

/* Table Styles */
.custom-table-wrapper { overflow-x: auto; }
.premium-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; min-width: 800px; }
.premium-table th { padding: 0 16px 10px 16px; text-align: left; color: #64748b; font-weight: 800; font-size: 13px; text-transform: uppercase; border-bottom: 2px dashed #e2e8f0; }
.premium-table td { background: white; padding: 16px; color: #1e293b; font-weight: 600; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
.premium-table td:first-child { border-left: 1px solid #f1f5f9; border-radius: 12px 0 0 12px; }
.premium-table td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 12px 12px 0; }
.premium-table tr { transition: transform 0.2s; }
.premium-table tr:hover td { background: #f8fafc; }

.date-block { display: inline-flex; flex-direction: column; align-items: center; justify-content: center; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; width: 65px; height: 75px; }
.d-day { font-family: 'Prompt'; font-size: 24px; font-weight: 900; color: #059669; line-height: 1; }
.d-mth { font-size: 11px; font-weight: 800; color: #10b981; }
.d-yr { font-size: 10px; font-weight: 600; color: #047857; margin-top: 2px; }
.waiting-date-badge { background: #f1f5f9; color: #64748b; padding: 6px 12px; border-radius: 8px; font-size: 12px; }

.t-centers { font-weight: 800; color: #0f172a; margin-bottom: 6px; font-size: 14px; }
.t-acts { display: flex; flex-wrap: wrap; gap: 4px; }
.t-tag { font-size: 11px; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-weight: 700; border: 1px solid #e2e8f0; }
.t-price { font-family: 'Prompt'; font-size: 16px; font-weight: 900; color: #064e3b; }

.status-badge-modern { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 800; }
.s-dot { width: 8px; height: 8px; border-radius: 50%; }
.is-appr { background: #dcfce7; color: #059669; } .is-appr .s-dot { background: #059669; }
.is-pend { background: #fef3c7; color: #d97706; } .is-pend .s-dot { background: #d97706; }
.is-rej { background: #fee2e2; color: #dc2626; } .is-rej .s-dot { background: #dc2626; }

/* Pagination Styles */
.pagination-wrapper { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
.page-btn { background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 10px; color: #475569; font-weight: bold; font-size: 14px; cursor: pointer; transition: 0.2s; font-family: 'Prompt'; }
.page-btn:not(:disabled):hover { border-color: #10b981; color: #10b981; transform: translateY(-2px); }
.page-btn:disabled { opacity: 0.5; cursor: not-allowed; background: #f8fafc; }
.page-numbers { display: flex; gap: 8px; }
.page-num { background: #ffffff; border: 1px solid #cbd5e1; width: 40px; height: 40px; border-radius: 10px; color: #475569; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: 'Prompt'; }
.page-num:hover { border-color: #10b981; color: #10b981; transform: translateY(-2px); }
.page-num.active { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); transform: scale(1.05); }

@media(max-width: 768px) {
  .history-page-wrapper { padding: 30px 10px; }
  .page-title { font-size: 24px; }
  .btn-back { display: none; }
  .glass-card { padding: 15px; }
}
</style>
