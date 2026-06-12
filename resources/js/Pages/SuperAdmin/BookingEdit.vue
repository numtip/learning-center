<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  booking: Object
})

const page = usePage()

const rolePrefix = page.props.auth.user.roles.some(r => r.name === 'superadmin')
  ? '/superadmin'
  : '/admin'

const formatText = (data) => {
  if (!data) return ''
  if (Array.isArray(data)) return data.join(', ')
  if (typeof data === 'object') return JSON.stringify(data)
  return String(data)
}

const form = useForm({
  name: props.booking.name,
  centers: formatText(props.booking.centers),
  activities: formatText(props.booking.activities),
  level: props.booking.level,
  people: props.booking.people,
  total_price: props.booking.total_price,
  status: props.booking.status,
  admin_note: props.booking.admin_note || '' // 👈 เพิ่มฟิลด์ admin_note
})

const submit = () => {
  form.put(`${rolePrefix}/booking/${props.booking.id}`, {
    onSuccess: () => {
      alert('✅ บันทึกการแก้ไขการจองสำเร็จเรียบร้อยแล้ว')
      router.visit(`${rolePrefix}/booking`)
    }
  })
}

const goBack = () => {
  router.visit(`${rolePrefix}/booking`)
}
</script>

<template>
  <AdminLayout>
    <Head title="แก้ไขข้อมูลการจอง" />

    <div class="admin-container">

      <div class="page-header">
        <div class="header-title">
          <h2>✏️ แก้ไขข้อมูลการจอง</h2>
          <p class="subtitle">ปรับปรุงรายละเอียดการจองของ: <strong>{{ booking.name }}</strong></p>
        </div>
      </div>

      <div class="form-card">
        <div class="form-grid">

          <div class="form-section-title full-width">
            <span>👤 ข้อมูลผู้จองและการเข้าชม</span>
            <hr class="section-divider">
          </div>

          <div class="input-group full-width">
            <label>ชื่อผู้จอง / ชื่อหน่วยงาน</label>
            <input v-model="form.name" type="text" class="modern-input" placeholder="ระบุชื่อผู้จอง...">
          </div>

          <div class="input-group">
            <label>ระดับชั้น / ประเภทหมู่คณะ</label>
            <div class="select-wrapper">
              <select v-model="form.level" class="modern-input">
                <option value="primary">อนุบาล - ประถม</option>
                <option value="secondary">มัธยม - ปริญญาตรี</option>
                <option value="public">ป.โท ป.เอก และประชาชน</option>
                <option value="smallgroup">หมู่คณะต่ำกว่า 10 คน</option>
              </select>
            </div>
          </div>

          <div class="input-group">
            <label>จำนวนผู้เข้าชม (คน)</label>
            <input v-model="form.people" type="number" min="1" class="modern-input" placeholder="0">
          </div>

          <div class="form-section-title full-width" style="margin-top: 20px;">
            <span>📍 สถานที่และกิจกรรม</span>
            <hr class="section-divider">
          </div>

          <div class="input-group full-width">
            <label>ศูนย์การเรียนรู้ที่ต้องการเข้าชม</label>
            <textarea v-model="form.centers" rows="2" class="modern-input" placeholder="ระบุศูนย์ที่ต้องการเข้าชม..."></textarea>
          </div>

          <div class="input-group full-width">
            <label>รายละเอียดกิจกรรมที่สนใจ</label>
            <textarea v-model="form.activities" rows="3" class="modern-input" placeholder="ระบุกิจกรรมเพิ่มเติม..."></textarea>
          </div>

          <div class="form-section-title full-width" style="margin-top: 20px;">
            <span>💰 ค่าใช้จ่าย และหมายเหตุแอดมิน</span>
            <hr class="section-divider">
          </div>

          <div class="input-group">
            <label>ราคารวมทั้งหมด (บาท)</label>
            <input v-model="form.total_price" type="number" min="0" class="modern-input" placeholder="0.00">
          </div>

          <div class="input-group">
            <label>สถานะการจอง</label>
            <div class="select-wrapper">
              <select v-model="form.status" class="modern-input">
                <option value="รอยืนยัน">รอยืนยัน</option>
                <option value="อนุมัติ">อนุมัติ</option>
                <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                <option value="ยกเลิก">ยกเลิก</option>
              </select>
            </div>
          </div>

          <div class="input-group full-width" style="margin-top: 10px;">
            <label class="text-orange-600 font-bold">📝 หมายเหตุจากผู้ดูแล (Admin Note)</label>
            <textarea v-model="form.admin_note" rows="2" class="modern-input" style="border-color: #fdba74; background-color: #fff7ed;" placeholder="ตัวอย่าง: เตรียมอุปกรณ์ปลูกข้าว, ลูกค้าขอเลื่อนเวลา... (ข้อความนี้จะเห็นเฉพาะแอดมิน)"></textarea>
          </div>

        </div>

        <div class="action-bar">
          <button type="button" @click="goBack" class="btn-cancel">
            ย้อนกลับ
          </button>
          <button @click="submit" :disabled="form.processing" class="btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4.207a1 1 0 0 0-.293-.707l-2.5-2.5A1 1 0 0 0 10.5 1H2zm10.5 1.5L14 4V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h9.5z"/>
              <path d="M4 11h8v2H4v-2zm0-7h6v3H4V4z"/>
            </svg>
            บันทึกการแก้ไข
          </button>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* ===== BASE & FONT ===== */
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container { max-width: 800px; margin: 0 auto; padding: 32px 20px; font-family: 'Prompt', sans-serif; color: #1e293b; }
.page-header { margin-bottom: 24px; }
.header-title h2 { font-size: 28px; font-weight: 700; color: #065f46; margin: 0 0 6px 0; }
.subtitle { font-size: 15px; color: #64748b; margin: 0; }
.form-card { background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); border: 1px solid #e2e8f0; }
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px 24px; }
.full-width { grid-column: 1 / -1; }
.form-section-title { display: flex; align-items: center; gap: 12px; font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
.section-divider { flex-grow: 1; height: 1px; background: #e2e8f0; border: none; margin: 0; }
.input-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.modern-input { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; font-family: inherit; font-size: 15px; color: #1e293b; transition: all 0.2s ease; resize: vertical; }
.modern-input:focus { outline: none; border-color: #10b981; background: #ffffff; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
.select-wrapper { position: relative; }
select.modern-input { appearance: none; cursor: pointer; }
.select-wrapper::after { content: '▼'; font-size: 12px; color: #64748b; position: absolute; right: 16px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.action-bar { display: flex; justify-content: flex-end; align-items: center; gap: 16px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
.btn-cancel { background: transparent; border: none; color: #64748b; font-weight: 600; font-size: 15px; font-family: inherit; cursor: pointer; padding: 10px 20px; transition: color 0.2s; }
.btn-cancel:hover { color: #0f172a; }
.btn-submit { display: inline-flex; align-items: center; gap: 8px; background: #10b981; color: #ffffff; border: none; padding: 12px 28px; border-radius: 10px; font-size: 16px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); }
.btn-submit:hover:not(:disabled) { background: #059669; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35); }
.btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }
@media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } .action-bar { flex-direction: column; width: 100%; } .action-bar button { width: 100%; justify-content: center; } }
</style>
