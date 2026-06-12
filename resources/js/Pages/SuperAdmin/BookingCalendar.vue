<template>
<AdminLayout>
<div class="calendar-admin-container">
<div class="page-header">
<h2>📅 ปฏิทินการจองกิจกรรม</h2>
<p>ตรวจสอบตารางการจอง และจัดการวันหยุด/คิวเต็ม</p>
</div>

<div class="calendar-card">
<div class="calendar-header">
<button class="nav-btn" @click="changeMonth(-1)">&#10094; เดือนก่อนหน้า</button>
<div class="month-year-title">{{ monthNames[currentMonth] }} {{ currentYear + 543 }}</div>
<button class="nav-btn" @click="changeMonth(1)">เดือนถัดไป &#10095;</button>
</div>

<div class="calendar-grid days-header">
<div v-for="day in ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์']" :key="day">{{ day }}</div>
</div>

<div class="calendar-grid">
<div v-for="(day, index) in calendarDays" :key="index" class="calendar-cell" :class="{ 'empty': !day, 'today': day && day.isToday }">

<template v-if="day">
<div class="cell-header">
<div class="date-number">{{ day.day }}</div>
<button class="add-btn" @click="openAddModal(day.dateStr)" title="เพิ่มข้อมูล">+</button>
</div>

<div class="booking-list" v-if="day.bookings.length > 0">
<div v-for="b in day.bookings" :key="b.id"
class="booking-badge"
:class="getBadgeClass(b.type)"
@click="viewDetails(b)">
<span class="dot"></span>
{{ b.name }}
<span v-if="b.type === 'booking'">({{ b.people }} คน)</span>
</div>
</div>
</template>

</div>
</div>
</div>

<div class="modal-overlay" v-if="selectedBooking" @click="selectedBooking = null">
<div class="modal-content" @click.stop>
<button class="close-btn" @click="selectedBooking = null">×</button>
<h3>{{ getModalIcon(selectedBooking.type) }} รายละเอียดข้อมูล</h3>

<div class="modal-body">
<p><strong>ประเภท:</strong>
<span class="badge" :class="getBadgeClass(selectedBooking.type)">
{{ getTypeName(selectedBooking.type) }}
</span>
</p>
<p><strong>หัวข้อ / ผู้ติดต่อ:</strong> {{ selectedBooking.name }} <span v-if="selectedBooking.org">({{ selectedBooking.org }})</span></p>
<p><strong>วันที่:</strong> {{ formatDateThai(selectedBooking.visit_date) }}</p>

<template v-if="selectedBooking.type === 'booking'">
<p><strong>โทรศัพท์:</strong> {{ selectedBooking.phone || '-' }}</p>
<p><strong>จำนวน:</strong> {{ selectedBooking.people }} คน</p>
<div class="status-box">
สถานะ: <span class="badge approved">{{ selectedBooking.status || 'อนุมัติ' }}</span>
</div>
</template>

<div class="action-buttons">
<button class="btn-edit" @click="openEditModal(selectedBooking)">✏️ แก้ไข</button>
<button class="btn-delete" @click="deleteBooking(selectedBooking.id)">🗑️ ลบ</button>
</div>
</div>
</div>
</div>

<div class="modal-overlay" v-if="showFormModal" @click="showFormModal = false">
<div class="modal-content form-modal" @click.stop>
<button class="close-btn" @click="showFormModal = false">×</button>
<h3>{{ isEditing ? '✏️ แก้ไขข้อมูล' : '➕ เพิ่มข้อมูลใหม่' }}</h3>

<form @submit.prevent="saveBooking" class="form-body">
<div class="form-group">
<label>ประเภทรายการ</label>
<select v-model="form.type" required>
<option value="booking">👥 การจองเข้าชม</option>
<option value="full">⚠️ คิวเต็ม</option>
<option value="holiday">🛑 วันหยุด</option>
</select>
</div>

<div class="form-group">
<label>วันที่</label>
<input type="date" v-model="form.visit_date" required>
</div>

<div class="form-group">
<label>{{ form.type === 'booking' ? 'ชื่อผู้ติดต่อ' : 'รายละเอียด (เช่น วันหยุดสงกรานต์)' }}</label>
<input type="text" v-model="form.name" required placeholder="กรอกชื่อหรือรายละเอียด">
</div>

<template v-if="form.type === 'booking'">
<div class="form-row">
<div class="form-group">
<label>หน่วยงาน (ถ้ามี)</label>
<input type="text" v-model="form.org">
</div>
<div class="form-group">
<label>จำนวนคน</label>
<input type="number" v-model="form.people" min="1" required>
</div>
</div>
<div class="form-group">
<label>เบอร์โทรศัพท์</label>
<input type="text" v-model="form.phone">
</div>
</template>

<div class="form-actions">
<button type="button" class="btn-cancel" @click="showFormModal = false">ยกเลิก</button>
<button type="submit" class="btn-submit">บันทึกข้อมูล</button>
</div>
</form>
</div>
</div>

</div>
</AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()

const bookingsList = computed(() => page.props.bookings || [])

const currentDate = ref(new Date())
const currentMonth = ref(currentDate.value.getMonth())
const currentYear = ref(currentDate.value.getFullYear())
const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"]

const selectedBooking = ref(null)

// State สำหรับ Form
const showFormModal = ref(false)
const isEditing = ref(false)
const form = ref({
id: null,
type: 'full',
name: '',
visit_date: '',
people: 1,
org: '',
phone: '',
status: 'อนุมัติแล้ว'
})

// นำทางเดือน
const changeMonth = (step) => {
let newMonth = currentMonth.value + step
if (newMonth > 11) { currentMonth.value = 0; currentYear.value++ }
else if (newMonth < 0) { currentMonth.value = 11; currentYear.value-- }
else { currentMonth.value = newMonth }
}

// สร้างตารางปฏิทิน
const calendarDays = computed(() => {
const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate()
const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay()
let days = []

for(let i=0; i < firstDay; i++) days.push(null)

let todayStr = new Date().toISOString().split('T')[0]

for(let i=1; i <= daysInMonth; i++) {
let d = new Date(currentYear.value, currentMonth.value, i)
let dateStr = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`

let dayBookings = bookingsList.value.filter(b => b.visit_date === dateStr)

days.push({
day: i, dateStr, isToday: dateStr === todayStr, bookings: dayBookings
})
}
return days
})

// เปิดดูรายละเอียด
const viewDetails = (booking) => { selectedBooking.value = booking }

// ---------------- Functions จัดการข้อมูล ---------------- //

const openAddModal = (dateStr) => {
isEditing.value = false
form.value = { id: null, type: 'full', name: '', visit_date: dateStr }
showFormModal.value = true
}

const openEditModal = (booking) => {
isEditing.value = true
form.value = { ...booking }
selectedBooking.value = null
showFormModal.value = true
}

const saveBooking = () => {
if (form.value.type === 'full' || form.value.type === 'holiday') {
router.post('/booking-calendar/toggle-block', {
date: form.value.visit_date,
type: form.value.type
}, {
preserveScroll: true,
onSuccess: () => { showFormModal.value = false }
})
} else {
alert('กรุณาใช้เมนูจัดการการจอง เพื่อแก้ไขข้อมูลผู้จองเข้าชม');
showFormModal.value = false;
}
}

const deleteBooking = (id) => {
if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?')) {
const booking = bookingsList.value.find(b => b.id === id);
if(!booking) return;

if (booking.type === 'full' || booking.type === 'holiday') {
router.post('/booking-calendar/toggle-block', {
date: booking.visit_date,
type: booking.type
}, {
preserveScroll: true,
onSuccess: () => { selectedBooking.value = null; }
})
} else if (booking.type === 'booking') {
router.delete(`/admin/booking/${id}`, {
preserveScroll: true,
onSuccess: () => { selectedBooking.value = null; }
})
}
}
}

// ---------------- Helpers สำหรับ UI ---------------- //
const formatDateThai = (dateStr) => {
if(!dateStr) return ''
const [y, m, d] = dateStr.split('-')
return `${parseInt(d)} ${monthNames[parseInt(m)-1]} ${parseInt(y) + 543}`
}

const getBadgeClass = (type) => {
if (type === 'holiday') return 'badge-holiday'
if (type === 'full') return 'badge-full'
return 'badge-booking'
}

const getTypeName = (type) => {
if (type === 'holiday') return 'วันหยุด'
if (type === 'full') return 'คิวเต็ม'
return 'การจองเข้าชม'
}

const getModalIcon = (type) => {
if (type === 'holiday') return '🛑'
if (type === 'full') return '⚠️'
return '🏷️'
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap');
.calendar-admin-container { padding: 20px; font-family: 'Sarabun', sans-serif; max-width: 1400px; margin: 0 auto; }
.page-header h2 { font-size: 32px; color: #064e3b; font-weight: 800; margin-bottom: 5px; }
.page-header p { color: #64748b; font-size: 16px; margin-bottom: 30px; }

.calendar-card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
.calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.month-year-title { font-size: 28px; font-weight: 900; color: #059669; }
.nav-btn { background: #f1f5f9; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; color: #0f172a; cursor: pointer; transition: 0.3s; }
.nav-btn:hover { background: #10b981; color: white; }

.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; }
.days-header div { text-align: center; font-weight: 800; color: #64748b; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px; }

.calendar-cell { min-height: 140px; background: #f8fafc; border-radius: 16px; padding: 10px; border: 2px solid transparent; display: flex; flex-direction: column; transition: 0.3s; position: relative; }
.calendar-cell:not(.empty):hover { border-color: #cbd5e1; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
.calendar-cell.today { border-color: #10b981; background: #ecfdf5; }

.cell-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
.date-number { font-size: 20px; font-weight: 800; color: #1e293b; }

/* ปุ่ม + สำหรับเพิ่มกิจกรรม */
.add-btn { background: #e2e8f0; border: none; color: #475569; width: 24px; height: 24px; border-radius: 50%; font-weight: bold; cursor: pointer; opacity: 0; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
.calendar-cell:hover .add-btn { opacity: 1; }
.add-btn:hover { background: #10b981; color: white; transform: scale(1.1); }

.booking-list { display: flex; flex-direction: column; gap: 6px; overflow-y: auto; max-height: 90px; }
.booking-list::-webkit-scrollbar { width: 4px; }
.booking-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

/* Styles สำหรับ Badge หลายประเภท */
.booking-badge { padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: white; }
.booking-badge:hover { transform: scale(1.02); }

.badge-booking { background: linear-gradient(90deg, #10b981, #34d399); box-shadow: 0 2px 5px rgba(16,185,129,0.3); }
.badge-full { background: linear-gradient(90deg, #f59e0b, #fbbf24); box-shadow: 0 2px 5px rgba(245,158,11,0.3); }
.badge-holiday { background: linear-gradient(90deg, #ef4444, #f87171); box-shadow: 0 2px 5px rgba(239,68,68,0.3); }

.dot { width: 6px; height: 6px; background: white; border-radius: 50%; flex-shrink: 0; }

/* Modal General */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); z-index: 100; display: flex; align-items: center; justify-content: center; }
.modal-content { background: white; width: 450px; padding: 30px; border-radius: 24px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: pop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: 90vh; overflow-y: auto; }
@keyframes pop { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

.close-btn { position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 20px; font-weight: bold; cursor: pointer; color: #64748b; }
.close-btn:hover { background: #ef4444; color: white; }
h3 { margin-top: 0; color: #064e3b; font-weight: 900; border-bottom: 2px dashed #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }

/* Detail Modal specific */
.modal-body p { margin: 10px 0; font-size: 15px; color: #334155; }
.badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700; color: white; }
.status-box { background: #f8fafc; padding: 15px; border-radius: 12px; text-align: center; font-weight: 800; font-size: 16px; margin-top: 20px; border: 1px solid #e2e8f0; }
.badge.approved { background: #dcfce7; color: #059669; padding: 5px 15px; border-radius: 20px; margin-left: 10px; }

.action-buttons { display: flex; gap: 10px; margin-top: 25px; }
.btn-edit, .btn-delete { flex: 1; padding: 10px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; }
.btn-edit { background: #f1f5f9; color: #334155; }
.btn-edit:hover { background: #e2e8f0; }
.btn-delete { background: #fee2e2; color: #ef4444; }
.btn-delete:hover { background: #fecaca; }

/* Form Styles */
.form-body { display: flex; flex-direction: column; gap: 15px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label { font-size: 14px; font-weight: 700; color: #475569; }
.form-group input, .form-group select { padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 10px; font-family: 'Sarabun'; font-size: 15px; outline: none; transition: 0.2s; }
.form-group input:focus, .form-group select:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

.form-actions { display: flex; gap: 10px; margin-top: 20px; }
.btn-cancel, .btn-submit { flex: 1; padding: 12px; border: none; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; transition: 0.2s; }
.btn-cancel { background: #f1f5f9; color: #64748b; }
.btn-cancel:hover { background: #e2e8f0; }
.btn-submit { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16,185,129,0.3); }
.btn-submit:hover { background: #059669; transform: translateY(-2px); }
</style>
