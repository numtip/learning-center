<template>
  <PublicLayout>
    <Head title="ระบบจองศูนย์การเรียนรู้" />

    <div class="super-premium-wrapper">
      <div class="bg-orb orb-1"></div>
      <div class="bg-orb orb-2"></div>
      <div class="bg-orb orb-3"></div>

      <div class="container-inner relative z-10">

        <div class="hero-header text-center animate-fade-down">
          <h1 class="hero-title">ระบบจองคอร์สกิจกรรม<span class="text-gradient"></span></h1>
          <p class="hero-subtitle">เปิดประสบการณ์การเรียนรู้เชิงปฏิบัติการ ท่ามกลางธรรมชาติที่สมบูรณ์แบบ</p>
        </div>

        <div class="glass-master-card animate-fade-up delay-1">

          <div class="content-section mt-10">
            <div class="section-title-wrap">
              <div class="icon-box">📅</div>
              <h2>เลือกวันที่ต้องการเข้าชม</h2>
            </div>

            <div class="calendar-luxury-wrapper">
              <div class="cal-header">
                <button class="cal-nav-btn" @click.prevent="changeMonth(-1)">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <div class="cal-month-year">{{ fullMonthNames[currentMonth] }} <span>{{ currentYear + 543 }}</span></div>
                <button class="cal-nav-btn" @click.prevent="changeMonth(1)">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
              </div>

              <div class="cal-grid cal-days-row">
                <div v-for="day in ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส']" :key="day" :class="{'text-red-500': day==='อา'}">{{ day }}</div>
              </div>

              <div class="cal-grid cal-dates-row">
                <div v-for="(day, index) in calendarDays" :key="index"
                     class="cal-cell"
                     :class="{
                       'is-empty': !day,
                       'is-past': day && day.isPast,
                       'is-blocked': day && day.isFull,
                       'is-holiday': day && day.isHoliday,
                       'is-selected': day && day.dateStr === form.visit_date
                     }"
                     @click="day && selectDate(day)">

                  <span class="date-num" v-if="day">{{ day.day }}</span>
                  <div class="status-indicator" v-if="day && day.isFull">เต็ม</div>
                  <div class="status-indicator holiday" v-if="day && day.isHoliday">วันหยุด</div>
                  <div class="selected-ring" v-if="day && day.dateStr === form.visit_date"></div>
                </div>
              </div>

              <div class="cal-legend">
                <div class="l-item"><div class="dot d-avail"></div>ว่าง / เปิดรับจอง (รับได้ 200 คน/วัน)</div>
                <div class="l-item"><div class="dot d-full"></div>คิวเต็มแล้ว</div>
                <div class="l-item"><div class="dot d-holiday"></div>วันหยุด</div>
                <div class="l-item"><div class="dot d-sel"></div>วันที่เลือก</div>
              </div>
            </div>

            <div class="date-result-box" v-if="form.visit_date">
              <div class="check-circle">✓</div>
              <div class="res-text">
                <p>วันที่ท่านเลือกเข้าชม</p>
                <h3>{{ formatDateThai(form.visit_date, false) }}</h3>
              </div>
            </div>
          </div>

          <hr class="section-divider" />

          <div class="content-section">
            <div class="section-title-wrap">
              <div class="icon-box">👤</div>
              <h2>ข้อมูลผู้ติดต่อ</h2>
            </div>
            <div class="modern-form-grid">
              <div class="input-float-group">
                <input v-model="form.name" type="text" class="input-float" placeholder=" " required/>
                <label class="label-float">ชื่อ - นามสกุล ผู้ติดต่อ</label>
              </div>
              <div class="input-float-group">
                <input v-model="form.org" type="text" class="input-float" placeholder=" "/>
                <label class="label-float">หน่วยงาน / โรงเรียน (ถ้ามี)</label>
              </div>
              <div class="input-float-group">
                <input v-model="form.email" type="email" class="input-float" placeholder=" " required/>
                <label class="label-float">อีเมล (Email)</label>
              </div>
              <div class="input-float-group">
                <input v-model="form.phone" type="tel" class="input-float" placeholder=" " required maxlength="10"/>
                <label class="label-float">เบอร์โทรศัพท์ (08xxxxxxxx)</label>
              </div>
            </div>
          </div>

          <hr class="section-divider" />

          <div class="content-section">
            <div class="section-title-wrap">
              <div class="icon-box">🏫</div>
              <h2>เลือกศูนย์การเรียนรู้และกิจกรรม</h2>
            </div>

            <div class="center-selector-grid-compact">
              <label v-for="c in centers" :key="c" class="center-choice-card-compact">
                <input type="checkbox" v-model="form.centers" :value="c" class="hidden-check" @change="handleCenterChange(c)" />
                <div class="c-card-inner-compact">
                  <div class="c-card-bg"></div>
                  <div class="c-status-icon-compact">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                  </div>
                  <span class="c-name-compact">{{ c }}</span>
                </div>
              </label>
            </div>

            <div class="activities-wrapper" v-if="form.centers.length > 0">
              <div v-for="center in form.centers" :key="center" class="activity-module animate-pop-in">
                <div class="mod-header">
                  <span class="mod-dot"></span> กิจกรรมย่อยของ <strong>{{ center }}</strong>
                  <span class="text-red-500 text-xs ml-2">*เลือกอย่างน้อย 1 กิจกรรม</span>
                </div>
                <div class="mod-body" v-if="activitiesByCenter[center] && activitiesByCenter[center].length > 0">
                  <label v-for="act in activitiesByCenter[center]" :key="act" class="act-chip">
                    <input type="checkbox" :value="act" v-model="form.activities[center]" class="hidden-check"/>
                    <div class="chip-inner">
                      <span class="chip-icon">+</span>
                      <span class="chip-icon-active">✓</span>
                      {{ act }}
                    </div>
                  </label>
                </div>
                <div v-else class="text-sm text-gray-400 mt-2 ml-4">
                  - ยังไม่มีข้อมูลกิจกรรมสำหรับศูนย์นี้ -
                </div>
              </div>
            </div>
          </div>

          <div class="content-section mt-10">
            <div class="modern-form-grid dual">
              <div class="input-float-group">
                <select v-model="form.level" class="input-float select-float" required>
                  <option disabled value="" selected hidden></option>
                  <option value="primary">อนุบาล - ประถม</option>
                  <option value="secondary">มัธยม - ป.ตรี</option>
                  <option value="public">ป.โท ป.เอก และประชาชนทั่วไป</option>
                  <option value="smallgroup">หมู่คณะ (ต่ำกว่า 10 คน)</option>
                </select>
                <label class="label-float">เลือกระดับผู้เข้าร่วม</label>
              </div>
              <div class="input-float-group">
                <input type="number" v-model.number="form.people" min="1" max="200" class="input-float" placeholder=" " required/>
                <label class="label-float">จำนวนผู้เข้าร่วม (สูงสุด 200 คน)</label>
              </div>
            </div>
          </div>

          <div class="receipt-card">
            <div class="r-header">สรุปค่าใช้จ่ายเบื้องต้น (ประมาณการ)</div>
            <div class="r-body">
              <div class="r-row"><span>ค่าธรรมเนียมต่อคน</span> <span>{{ feePerPerson }} ฿</span></div>
              <div class="r-row"><span>จำนวนศูนย์ที่เลือก</span> <span>{{ form.centers.length }} แห่ง</span></div>
              <div class="r-row"><span>จำนวนผู้เข้าร่วม</span> <span>{{ form.people }} คน</span></div>
            </div>
            <div class="r-footer">
              <span>ยอดชำระสุทธิ</span>
              <span class="r-total">฿ {{ totalPrice.toLocaleString() }}</span>
            </div>
          </div>

<button class="btn-submit-glow" @click.prevent="openConfirmModal" :disabled="isSubmitting || !form.visit_date || form.centers.length === 0 || !form.name || !form.phone">
            <div class="btn-bg"></div>
            <div class="btn-content">
              <span v-if="isSubmitting" class="flex-center">
                กำลังส่งคำขอ...
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              </span>
              <span v-else class="flex-center">
                ส่งคำขอจองกิจกรรม
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
              </span>
            </div>
          </button>

        </div>

        <div class="glass-master-card history-section mt-10" v-if="recentBookings.length > 0">
          <div class="section-title-wrap mb-6">
            <div class="icon-box">📜</div>
            <h2>รายการจองล่าสุดของคุณ</h2>
          </div>

          <div class="custom-table-wrapper">
            <table class="premium-table">
              <thead>
                <tr>
                  <th>วันที่เข้าชม</th>
                  <th>รายละเอียดกิจกรรมที่เลือก</th>
                  <th>จำนวน</th>
                  <th>ยอดชำระ</th>
                  <th>สถานะ / จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="b in recentBookings" :key="b.id">
                  <td>
                    <div class="date-block" v-if="b.visit_date">
                      <span class="d-day">{{ new Date(b.visit_date).getDate() }}</span>
                      <span class="d-mth">{{ shortMonthNames[new Date(b.visit_date).getMonth()] }}</span>
                    </div>
                    <span v-else class="waiting-date-badge">รอแอดมินระบุ</span>
                  </td>
                  <td>
                    <div class="font-bold text-gray-800 mb-1" v-if="b.centers">{{ Array.isArray(b.centers) ? b.centers.join(', ') : b.centers }}</div>
                    <div class="t-acts" v-if="b.activities && typeof b.activities === 'object'">
                      <template v-for="(acts, cName) in b.activities" :key="cName">
                        <span v-if="Array.isArray(acts) && acts.length > 0" class="t-tag">
                          {{ acts.join(', ') }}
                        </span>
                      </template>
                    </div>
                  </td>
                  <td class="font-bold text-gray-700">{{ b.people }} คน</td>
                  <td class="t-price">฿ {{ Number(b.total_price).toLocaleString() }}</td>
                  <td>
                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-start;">
                      <div class="status-badge-modern" :class="{
                        'is-appr': b.status === 'อนุมัติ' || b.status === 'เสร็จสิ้น',
                        'is-pend': b.status === 'รอยืนยัน',
                        'is-rej': b.status === 'ยกเลิก'
                      }">
                        <span class="s-dot"></span> {{ b.status }}
                      </div>
                      <button v-if="b.status === 'รอยืนยัน'" @click="cancelBooking(b.id)" class="text-xs text-red-500 hover:text-red-700 underline cursor-pointer bg-transparent border-none p-0">
                        ยกเลิกการจอง
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
    <FooterFarm />

    <div v-if="modal.showError" class="custom-modal-overlay">
      <div class="custom-modal-box error-box animate-pop-in">
        <div class="c-icon text-red-500">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <h3 class="text-xl font-bold mt-4 mb-2">เกิดข้อผิดพลาด</h3>
        <p class="text-gray-600 mb-6">{{ modal.message }}</p>
        <button @click="modal.showError = false" class="btn-modal-close bg-red-500 hover:bg-red-600">ตกลง</button>
      </div>
    </div>

    <div v-if="modal.showConfirm" class="custom-modal-overlay">
      <div class="custom-modal-box confirm-box animate-pop-in">
        <div class="c-icon text-blue-500">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <h3 class="text-xl font-bold mt-4 mb-2">ยืนยันการทำรายการ</h3>
        <p class="text-gray-600 mb-6">คุณต้องการยืนยันการส่งคำขอจองกิจกรรมนี้ใช่หรือไม่?</p>
        <div class="flex gap-4 w-full">
          <button @click="modal.showConfirm = false" class="flex-1 py-3 rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition">ยกเลิก</button>
          <button @click="executeSubmit" class="flex-1 py-3 rounded-xl bg-emerald-500 text-white font-bold hover:bg-emerald-600 transition">ยืนยันการจอง</button>
        </div>
      </div>
    </div>

  </PublicLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useForm, Head, usePage, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'

const page = usePage()
const bookings = page.props.bookings || []
const centers = page.props.centers || []
const activitiesByCenter = page.props.activitiesByCenter || {}
const userData = page.props.userData || {}
const blockedDates = page.props.bookedDates || {}

const form = useForm({
  name: userData.fullname ?? userData.name ?? '',
  org: userData.organization ?? '',
  email: userData.email ?? '',
  phone: userData.phone ?? '',
  visit_date: '',
  centers: [],
  activities: {},
  level: '',
  people: 10,
  package: 'nofood'
})

// === UI State ===
const isSubmitting = ref(false)
const modal = reactive({
  showError: false,
  showConfirm: false,
  message: ''
})

const showErrorMsg = (msg) => {
  modal.message = msg
  modal.showError = true
}

const handleCenterChange = (center) => {
  if (!form.activities[center]) form.activities[center] = []
  if (!form.centers.includes(center)) {
    delete form.activities[center] // clear activities if center is unselected
  }
}

const rateTable = { nofood: { primary: 20, secondary: 30, public: 50, smallgroup: 100 } }
const feePerPerson = computed(() => form.level ? (rateTable[form.package][form.level] || 0) : 0)
const totalPrice = computed(() => feePerPerson.value * (Number(form.people) || 0) * (form.centers?.length || 0))

// =========================
// 🔥 Calendar Logic
// =========================
const currentDate = ref(new Date())
const currentMonth = ref(currentDate.value.getMonth())
const currentYear = ref(currentDate.value.getFullYear())

const fullMonthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]
const shortMonthNames = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."]

const changeMonth = (step) => {
  let newMonth = currentMonth.value + step
  if (newMonth > 11) { currentMonth.value = 0; currentYear.value++ }
  else if (newMonth < 0) { currentMonth.value = 11; currentYear.value-- }
  else { currentMonth.value = newMonth }
}

const calendarDays = computed(() => {
  const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate()
  const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay()
  let days = []

  for(let i=0; i < firstDay; i++) days.push(null)

  let today = new Date()
  today.setHours(0,0,0,0)

  for(let i=1; i <= daysInMonth; i++) {
    let d = new Date(currentYear.value, currentMonth.value, i)
    let dateStr = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`

    let blockReason = blockedDates[dateStr] || null;
    let isFull = blockReason === 'full';
    let isHoliday = blockReason === 'holiday';
    let isPast = d < today;

    days.push({
        day: i,
        dateStr,
        isFull,
        isHoliday,
        isPast,
        disabled: isFull || isHoliday || isPast
    })
  }
  return days
})

const selectDate = (day) => {
    if (day.disabled) {
        if (day.isFull || day.isHoliday) {
            showErrorMsg('วันที่คุณเลือกไม่สามารถจองได้ เนื่องจากเป็นวันหยุดหรือมีการจองเต็มแล้ว กรุณาเลือกวันอื่น');
        } else if (day.isPast) {
            showErrorMsg('ไม่สามารถจองวันย้อนหลังได้');
        }
        return;
    }
    form.visit_date = day.dateStr;
}

const formatDateThai = (dateStr, useShortMonth = false) => {
  if(!dateStr) return ''
  const d = new Date(dateStr)
  const monthArr = useShortMonth ? shortMonthNames : fullMonthNames
  return `${d.getDate()} ${monthArr[d.getMonth()]} ${d.getFullYear() + 543}`
}

// =========================
// 🔥 Submit & Validation Logic
// =========================
const openConfirmModal = () => {
  // 1. Validate Date
  if(!form.visit_date) return showErrorMsg('กรุณาเลือกวันที่เข้าชมในปฏิทิน')

  // 2. Validate Center & Activity
  if(form.centers.length === 0) return showErrorMsg('กรุณาเลือกศูนย์การเรียนรู้อย่างน้อย 1 แห่ง')
  for(let c of form.centers) {
    if(!form.activities[c] || form.activities[c].length === 0) {
      return showErrorMsg(`กรุณาเลือกกิจกรรมอย่างน้อย 1 อย่างสำหรับ "${c}"`);
    }
  }

  // 3. Validate People
  if(form.people < 1 || form.people > 200) return showErrorMsg('จำนวนคนต้องอยู่ระหว่าง 1 ถึง 200 คน')

  // 4. Validate Phone
  if(!/^[0-9]{9,10}$/.test(form.phone)) return showErrorMsg('กรุณากรอกเบอร์โทรศัพท์เป็นตัวเลข 9-10 หลักให้ถูกต้อง')

  modal.showConfirm = true;
}

const executeSubmit = () => {
  modal.showConfirm = false;
  isSubmitting.value = true;

  // 🔥 จุดที่เพิ่มเข้ามา: แปลงชื่อข้อมูลให้ตรงกับ Database ก่อนส่ง
  form.transform((data) => ({
    ...data,
    food_package: data.package,
    price_per_person: feePerPerson.value,
    total_price: totalPrice.value,
    type: 'booking'
  })).post('/service/booking', {
    preserveScroll: true,
    onSuccess: () => {
      isSubmitting.value = false;
      alert('ส่งคำขอจองสำเร็จ! กรุณารอการติดต่อกลับเพื่อยืนยันคิว');
      form.reset('centers', 'activities', 'level', 'people', 'visit_date');
      window.location.reload();
    },
    onError: (errors) => {
      isSubmitting.value = false;
      console.error("Backend Errors:", errors);

      // ดึง Error จาก Laravel มาแสดงตรงๆ
      const errorMessages = Object.values(errors);
      if (errorMessages.length > 0) {
        showErrorMsg(errorMessages[0]);
      } else {
        showErrorMsg('เกิดข้อผิดพลาด 500 (กรุณากด F12 ไปที่แท็บ Network เพื่อดู Error จาก Server)');
      }
    }
  })
}

// =========================
// 🔥 Cancel Booking (สำหรับ User)
// =========================
const cancelBooking = (id) => {
  if(confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้?')) {
    router.delete(`/service/booking/${id}`, {
      preserveScroll: true,
      onSuccess: () => {
        alert('ยกเลิกการจองสำเร็จแล้ว');
      }
    })
  }
}

const recentBookings = computed(() => bookings.slice(0, 5))
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=Prompt:wght@500;700;800&display=swap');

/* ================= Base Layout ================= */
.super-premium-wrapper { position: relative; min-height: 100vh; background: #f1f5f9; font-family: 'Sarabun', sans-serif; overflow: hidden; padding: 60px 20px; margin-top: -28px; }

/* 🌀 Animated Background Orbs */
.bg-orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.5; z-index: 1; animation: floatOrb 20s infinite alternate ease-in-out; pointer-events: none; }
.orb-1 { width: 500px; height: 500px; background: #34d399; top: -100px; left: -100px; animation-delay: 0s; }
.orb-2 { width: 400px; height: 400px; background: #38bdf8; bottom: 20%; right: -50px; animation-delay: -5s; }
.orb-3 { width: 300px; height: 300px; background: #818cf8; top: 40%; left: 30%; animation-delay: -10s; opacity: 0.3; }
@keyframes floatOrb { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(100px, 50px) scale(1.2); } }

.container-inner { max-width: 1000px; margin: 0 auto; }

/* ================= Animations ================= */
.animate-fade-down { animation: fadeDown 0.6s ease-out forwards; }
.animate-fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
.delay-1 { animation-delay: 0.1s; }
.animate-pop-in { animation: popIn 0.3s ease-out forwards; }
@keyframes fadeDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes popIn { 0% { opacity: 0; transform: scale(0.9) translateY(10px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }

/* ================= Custom Modals ================= */
.custom-modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px; }
.custom-modal-box { background: white; width: 100%; max-width: 400px; border-radius: 24px; padding: 32px; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
.c-icon { width: 64px; height: 64px; margin-bottom: 8px; }
.btn-modal-close { width: 100%; padding: 12px; border-radius: 12px; color: white; font-weight: bold; font-family: 'Prompt'; font-size: 16px; border: none; cursor: pointer; transition: 0.2s; }

/* ================= Hero Header ================= */
.hero-header { margin-bottom: 50px; }
.hero-title { font-family: 'Prompt', sans-serif; font-size: 48px; font-weight: 800; color: #0f172a; line-height: 1.2; margin: 0 0 16px 0; }
.text-gradient { background: linear-gradient(135deg, #059669 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero-subtitle { font-size: 18px; color: #475569; font-weight: 500; }

/* ================= Master Glass Card ================= */
.glass-master-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.8); border-radius: 32px; padding: 40px; box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.05); }
.content-section { margin-bottom: 40px; }
.section-divider { border: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.3), transparent); margin: 40px 0; }

.section-title-wrap { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
.icon-box { width: 48px; height: 48px; background: linear-gradient(135deg, #ffffff, #f1f5f9); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 8px 16px rgba(16,185,129,0.1); border: 1px solid rgba(255,255,255,0.9); }
.section-title-wrap h2 { font-family: 'Prompt', sans-serif; font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }

/* ================= 📅 Calendar iOS Style ================= */
.calendar-luxury-wrapper { background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.8); border-radius: 24px; padding: 24px; }
.cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.cal-nav-btn { width: 44px; height: 44px; border-radius: 14px; border: none; background: #ffffff; color: #334155; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s, transform 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.cal-nav-btn:hover { background: #10b981; color: white; transform: scale(1.05); }
.cal-month-year { font-family: 'Prompt'; font-size: 20px; font-weight: 700; color: #0f172a; }
.cal-month-year span { color: #10b981; }

.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; }
.cal-days-row div { font-size: 13px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; }
.cal-cell { position: relative; height: 60px; background: #ffffff; border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #334155; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); z-index: 1; border: 1px solid transparent; }
.cal-cell:not(.is-empty):not(.is-past):not(.is-blocked):not(.is-holiday):hover { transform: translateY(-3px); box-shadow: 0 6px 12px rgba(16,185,129,0.15); color: #10b981; border-color: #10b981; z-index: 2; }
.cal-cell.is-empty { background: transparent; box-shadow: none; cursor: default; }
.cal-cell.is-past { background: transparent; color: #cbd5e1; cursor: not-allowed; box-shadow: none; }

.cal-cell.is-blocked { background: #fef2f2; color: #ef4444; opacity: 0.8; cursor: not-allowed; border-color: #fecaca; }
.cal-cell.is-holiday { background: #fffbeb; color: #d97706; opacity: 0.8; cursor: not-allowed; border-color: #fde68a; }
.cal-cell.is-selected { color: white; font-weight: 800; z-index: 3; border: none; }

.selected-ring { position: absolute; inset: 0; background: linear-gradient(135deg, #10b981, #059669); border-radius: 14px; z-index: -1; box-shadow: 0 4px 10px rgba(16,185,129,0.3); }

.status-indicator { font-size: 10px; background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; margin-top: 2px; line-height: 1; font-weight: 800; }
.status-indicator.holiday { background: #f59e0b; }

.cal-legend { display: flex; flex-wrap: wrap; justify-content: center; gap: 16px; margin-top: 24px; }
.l-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #64748b; }
.dot { width: 12px; height: 12px; border-radius: 50%; }
.d-avail { background: #ffffff; border: 2px solid #cbd5e1; }
.d-full { background: #ef4444; }
.d-holiday { background: #f59e0b; }
.d-sel { background: #10b981; }

.date-result-box { margin-top: 24px; display: flex; align-items: center; gap: 16px; background: linear-gradient(90deg, #ecfdf5, rgba(255,255,255,0.5)); padding: 20px; border-radius: 20px; border-left: 6px solid #10b981; }
.check-circle { width: 40px; height: 40px; background: #10b981; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 4px 10px rgba(16,185,129,0.2); font-weight: bold; }
.res-text p { margin: 0; font-size: 13px; color: #64748b; font-weight: 600; }
.res-text h3 { margin: 0; font-size: 20px; font-weight: 800; color: #064e3b; }

/* ================= 📝 Floating Label Forms ================= */
.modern-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.modern-form-grid.dual { margin-top: 10px; }
.input-float-group { position: relative; }
.input-float { width: 100%; padding: 18px 20px 10px 20px; border: 1.5px solid #cbd5e1; border-radius: 16px; background: rgba(255,255,255,0.8); font-family: 'Sarabun'; font-size: 16px; color: #1e293b; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
.input-float:focus { border-color: #10b981; background: #ffffff; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
.label-float { position: absolute; left: 20px; top: 16px; color: #94a3b8; font-size: 15px; pointer-events: none; transition: 0.2s ease all; font-weight: 500; }
.input-float:focus ~ .label-float, .input-float:not(:placeholder-shown) ~ .label-float { top: 6px; font-size: 11px; color: #10b981; font-weight: 700; }
.select-float { appearance: none; }

/* ================= 🏫 Center Cards ================= */
.center-selector-grid-compact { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px; }
.center-choice-card-compact { cursor: pointer; display: block; }
.hidden-check { display: none; }
.c-card-inner-compact { position: relative; padding: 14px 16px; background: rgba(255,255,255,0.9); border: 1.5px solid #e2e8f0; border-radius: 14px; display: flex; align-items: center; gap: 12px; overflow: hidden; transition: border-color 0.2s, transform 0.2s; }
.c-card-bg { position: absolute; inset: 0; background: linear-gradient(135deg, #ecfdf5, #d1fae5); opacity: 0; transition: opacity 0.2s; z-index: 0; }
.c-status-icon-compact { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #cbd5e1; display: flex; align-items: center; justify-content: center; color: transparent; z-index: 1; transition: background 0.2s; }
.c-name-compact { font-size: 14.5px; font-weight: 700; color: #334155; z-index: 1; line-height: 1.3; }

.center-choice-card-compact:hover .c-card-inner-compact { border-color: #10b981; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
.hidden-check:checked + .c-card-inner-compact { border-color: #10b981; box-shadow: 0 4px 12px rgba(16,185,129,0.1); transform: translateY(-2px); }
.hidden-check:checked + .c-card-inner-compact .c-card-bg { opacity: 1; }
.hidden-check:checked + .c-card-inner-compact .c-status-icon-compact { background: #10b981; border-color: #10b981; color: white; }
.hidden-check:checked + .c-card-inner-compact .c-status-icon-compact::after { content: "✓"; font-size: 12px; font-weight: bold; }
.hidden-check:checked + .c-card-inner-compact .c-name-compact { color: #064e3b; }

/* 🎯 Activity Tags */
.activities-wrapper { display: flex; flex-direction: column; gap: 12px; }
.activity-module { background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 14px; padding: 16px; }
.mod-header { font-size: 14px; color: #64748b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.mod-dot { width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; }
.mod-header strong { color: #0f172a; font-weight: 800; font-size: 15px; }
.mod-body { display: flex; flex-wrap: wrap; gap: 8px; }

.act-chip { cursor: pointer; }
.chip-inner { display: flex; align-items: center; gap: 6px; padding: 6px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50px; font-size: 13.5px; font-weight: 600; color: #475569; transition: border-color 0.2s; }
.chip-icon { font-size: 14px; font-weight: 400; color: #94a3b8; }
.chip-icon-active { display: none; color: white; font-weight: bold; }
.act-chip:hover .chip-inner { border-color: #10b981; }
.hidden-check:checked + .chip-inner { background: linear-gradient(135deg, #059669, #10b981); border-color: #059669; color: white; }
.hidden-check:checked + .chip-inner .chip-icon { display: none; }
.hidden-check:checked + .chip-inner .chip-icon-active { display: inline; }

/* ================= 💰 Receipt Card ================= */
.receipt-card { background: #0f172a; border-radius: 20px; padding: 28px; color: white; margin-top: 30px; box-shadow: 0 15px 30px rgba(15,23,42,0.2); position: relative; overflow: hidden; }
.r-header { position: relative; z-index: 1; font-size: 15px; color: #34d399; font-weight: 800; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
.r-body { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 20px; }
.r-row { display: flex; justify-content: space-between; font-size: 15px; color: #cbd5e1; }
.r-row span:last-child { font-weight: 800; color: white; font-size: 16px; }
.r-footer { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; }
.r-footer span:first-child { font-size: 16px; font-weight: 700; }
.r-total { font-family: 'Prompt'; font-size: 32px; font-weight: 900; background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* ================= 🚀 Submit Button ================= */
.btn-submit-glow {
  position: relative;
  width: 100%;
  margin-top: 32px;
  padding: 18px 24px;
  border: none;
  border-radius: 16px;
  background: transparent;
  cursor: pointer;
  outline: none;
  overflow: hidden; /* สำหรับทำเอฟเฟกต์แสงสะท้อน */
}

.btn-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #10b981, #047857);
  border-radius: 16px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1;
  box-shadow: 0 8px 16px -4px rgba(16, 185, 129, 0.4);
}

/* เอฟเฟกต์แสงสะท้อนวิ่งผ่านปุ่ม */
.btn-bg::before {
  content: '';
  position: absolute;
  top: 0; left: -100%; width: 50%; height: 100%;
  background: linear-gradient(to right, transparent, rgba(255,255,255,0.25), transparent);
  transform: skewX(-20deg);
  transition: all 0.5s ease;
}

.btn-submit-glow:hover:not(:disabled) .btn-bg {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px -4px rgba(16, 185, 129, 0.6);
}

.btn-submit-glow:hover:not(:disabled) .btn-bg::before {
  left: 200%; /* แสงวิ่งไปทางขวาตอนชี้เมาส์ */
}

.btn-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-family: 'Prompt', sans-serif;
  font-size: 18px;
  font-weight: 800;
  letter-spacing: 0.5px;
}

/* ล็อกให้ข้อความกับไอคอนอยู่บรรทัดเดียวกันเสมอ */
.flex-center {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  white-space: nowrap; /* ห้ามตัดขึ้นบรรทัดใหม่ */
}

.btn-submit-glow:disabled .btn-bg {
  background: #cbd5e1;
  box-shadow: none;
  transform: none;
}

.btn-submit-glow:disabled .btn-content {
  color: #94a3b8;
}

.btn-submit-glow:disabled {
  cursor: not-allowed;
}

/* ================= 📜 History Table ================= */
.history-section { padding: 30px; }
.custom-table-wrapper { overflow-x: auto; }
.premium-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
.premium-table th { padding: 0 16px 10px 16px; text-align: left; color: #64748b; font-weight: 800; font-size: 13px; text-transform: uppercase; border-bottom: 2px dashed #e2e8f0; }
.premium-table td { background: white; padding: 16px; color: #1e293b; font-weight: 600; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
.premium-table td:first-child { border-left: 1px solid #f1f5f9; border-radius: 12px 0 0 12px; }
.premium-table td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 12px 12px 0; }
.premium-table tr { transition: transform 0.2s; }
.premium-table tr:hover td { background: #f8fafc; }

.date-block { display: inline-flex; flex-direction: column; align-items: center; justify-content: center; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; width: 55px; height: 65px; }
.d-day { font-family: 'Prompt'; font-size: 22px; font-weight: 900; color: #059669; line-height: 1; }
.d-mth { font-size: 12px; font-weight: 800; color: #10b981; }
.waiting-date-badge { background: #f1f5f9; color: #64748b; padding: 6px 12px; border-radius: 8px; font-size: 12px; }

.t-acts { display: flex; flex-direction: column; gap: 4px; align-items: flex-start; }
.t-tag { font-size: 12px; background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-weight: 600; border: 1px solid #e2e8f0; line-height: 1.4; display: inline-block;}
.t-price { font-family: 'Prompt'; font-size: 16px; font-weight: 900; color: #064e3b; }

.status-badge-modern { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 800; }
.s-dot { width: 8px; height: 8px; border-radius: 50%; }
.is-appr { background: #dcfce7; color: #059669; } .is-appr .s-dot { background: #059669; }
.is-pend { background: #fef3c7; color: #d97706; } .is-pend .s-dot { background: #d97706; }
.is-rej { background: #fee2e2; color: #dc2626; } .is-rej .s-dot { background: #dc2626; }

/* ================= Responsive ================= */
@media(max-width: 768px) {
  .super-premium-wrapper { padding: 20px 10px; }
  .hero-title { font-size: 32px; }
  .glass-master-card { padding: 20px; border-radius: 20px; }
  .center-selector-grid-compact { grid-template-columns: 1fr; }
  .modern-form-grid, .modern-form-grid.dual { grid-template-columns: 1fr; }
  .cal-cell { height: 50px; font-size: 15px; }
  .history-section { padding: 20px; }
  .premium-table { display: block; overflow-x: auto; white-space: nowrap; }
}
</style>
