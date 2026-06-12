<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

// นำเข้าไฟล์ Layout เมนูด้านซ้าย (แก้ Path นี้ให้ตรงกับชื่อไฟล์ Layout จริงของคุณ)
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  detail: Object,
  historyData: Array,
  centerType: String,
})

// --- Form สำหรับข้อมูลหลัก ---
const detailForm = useForm({
  center_name: props.detail?.center_name || '',
  affiliation: props.detail?.affiliation || '',
  history: props.detail?.history || '',
  objective: props.detail?.objective || '',
  image1: null,
  image2: null,
  image3: null,
})

const submitDetail = () => {
  // ส่งไป route แบบมี superadmin. นำหน้า
  detailForm.post(route('superadmin.centers.update', props.centerType), {
    preserveScroll: true,
    onSuccess: () => alert('อัปเดตข้อมูลหลักสำเร็จ'),
  })
}

// --- Form สำหรับ History (Timeline) ---
const showHistoryModal = ref(false)
const isEditingHistory = ref(false)

const historyForm = useForm({
  id: null,
  year: '',
  title: '',
  description: '',
  image: null,
})

const openAddHistory = () => {
  isEditingHistory.value = false
  historyForm.reset()
  showHistoryModal.value = true
}

const openEditHistory = (item) => {
  isEditingHistory.value = true
  historyForm.id = item.id
  historyForm.year = item.year
  historyForm.title = item.title
  historyForm.description = item.description
  historyForm.image = null
  showHistoryModal.value = true
}

const submitHistory = () => {
  // ส่งไป route แบบมี superadmin. นำหน้า
  const url = isEditingHistory.value
    ? route('superadmin.history.update', { type: props.centerType, id: historyForm.id })
    : route('superadmin.history.store', props.centerType)

  historyForm.post(url, {
    preserveScroll: true,
    onSuccess: () => {
      showHistoryModal.value = false
      historyForm.reset()
      alert('บันทึกข้อมูลประวัติสำเร็จ')
    },
  })
}

const deleteHistory = (id) => {
  if (confirm('ยืนยันการลบประวัตินี้?')) {
    // ส่งไป route แบบมี superadmin. นำหน้า
    router.delete(route('superadmin.history.destroy', { type: props.centerType, id: id }), {
      preserveScroll: true,
    })
  }
}
</script>

<template>
  <AdminLayout>
    <div class="admin-container">
      <Head title="จัดการข้อมูลศูนย์การเรียนรู้" />

      <div class="header-box">
        <h1>จัดการข้อมูล: {{ detail?.center_name || 'ศูนย์การเรียนรู้' }}</h1>
      </div>

      <div class="card">
        <h2>ข้อมูลทั่วไป (Detail)</h2>
        <form @submit.prevent="submitDetail">
          <div class="form-group">
            <label>ชื่อศูนย์</label>
            <input type="text" v-model="detailForm.center_name" class="input-field" required>
          </div>

          <div class="form-group">
            <label>สังกัด (Affiliation)</label>
            <input type="text" v-model="detailForm.affiliation" class="input-field">
          </div>

          <div class="form-group">
            <label>ความเป็นมา (History)</label>
            <textarea v-model="detailForm.history" class="input-field textarea" rows="4"></textarea>
          </div>

          <div class="form-group">
            <label>วัตถุประสงค์ (ขึ้นบรรทัดใหม่ด้วย 1. 2. 3.)</label>
            <textarea v-model="detailForm.objective" class="input-field textarea" rows="4"></textarea>
          </div>

          <div class="image-grid">
            <div class="form-group">
              <label>รูปภาพ 1 (เปลี่ยนรูป)</label>
              <input type="file" @input="detailForm.image1 = $event.target.files[0]" accept="image/*" class="input-field">
              <img v-if="detail?.image1" :src="detail.image1" class="preview-img" />
            </div>
            <div class="form-group">
              <label>รูปภาพ 2 (เปลี่ยนรูป)</label>
              <input type="file" @input="detailForm.image2 = $event.target.files[0]" accept="image/*" class="input-field">
              <img v-if="detail?.image2" :src="detail.image2" class="preview-img" />
            </div>
            <div class="form-group">
              <label>รูปภาพ 3 (เปลี่ยนรูป)</label>
              <input type="file" @input="detailForm.image3 = $event.target.files[0]" accept="image/*" class="input-field">
              <img v-if="detail?.image3" :src="detail.image3" class="preview-img" />
            </div>
          </div>

          <button type="submit" class="btn-primary" :disabled="detailForm.processing">บันทึกข้อมูลหลัก</button>
        </form>
      </div>

      <div class="card mt-4">
        <div class="flex-between">
          <h2>เส้นทางแห่งการเติบโต (Timeline)</h2>
          <button @click="openAddHistory" class="btn-success">+ เพิ่มประวัติใหม่</button>
        </div>

        <table class="admin-table mt-4">
          <thead>
            <tr>
              <th>ปี พ.ศ.</th>
              <th>หัวข้อ</th>
              <th>รายละเอียด</th>
              <th>รูปภาพ</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in historyData" :key="item.id">
              <td>{{ item.year }}</td>
              <td>{{ item.title }}</td>
              <td class="wrap-text">{{ item.description }}</td>
              <td>
                <img v-if="item.imageUrl" :src="item.imageUrl" class="thumb-img" />
              </td>
              <td>
                <button @click="openEditHistory(item)" class="btn-warning">แก้ไข</button>
                <button @click="deleteHistory(item.id)" class="btn-danger">ลบ</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="showHistoryModal" class="modal-overlay">
        <div class="modal-content">
          <h3>{{ isEditingHistory ? 'แก้ไขประวัติ' : 'เพิ่มประวัติใหม่' }}</h3>
          <form @submit.prevent="submitHistory">
            <div class="form-group">
              <label>ปี พ.ศ.</label>
              <input type="number" v-model="historyForm.year" class="input-field" required>
            </div>
            <div class="form-group">
              <label>หัวข้อ (Title)</label>
              <input type="text" v-model="historyForm.title" class="input-field" required>
            </div>
            <div class="form-group">
              <label>รายละเอียด</label>
              <textarea v-model="historyForm.description" class="input-field textarea" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label>รูปภาพประกอบ (เลือกใหม่ถ้าต้องการเปลี่ยน)</label>
              <input type="file" @input="historyForm.image = $event.target.files[0]" accept="image/*" class="input-field">
            </div>

            <div class="modal-actions">
              <button type="button" @click="showHistoryModal = false" class="btn-secondary">ยกเลิก</button>
              <button type="submit" class="btn-primary" :disabled="historyForm.processing">บันทึก</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* CSS เบื้องต้นสำหรับหน้า Admin เน้นสะอาดและใช้งานง่าย */
.admin-container { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: sans-serif; }
.header-box { margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
.card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
.mt-4 { margin-top: 20px; }
.flex-between { display: flex; justify-content: space-between; align-items: center; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
.input-field { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
.textarea { resize: vertical; }
.image-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.preview-img { width: 100%; height: 150px; object-fit: cover; margin-top: 10px; border-radius: 5px; border: 1px solid #ddd; }
.thumb-img { width: 80px; height: 50px; object-fit: cover; border-radius: 4px; }
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th, .admin-table td { border: 1px solid #eee; padding: 12px; text-align: left; }
.admin-table th { background: #f9f9f9; }
.wrap-text { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Buttons */
button { cursor: pointer; border: none; padding: 10px 15px; border-radius: 5px; font-weight: bold; color: #fff; }
.btn-primary { background: #1a3a3a; }
.btn-success { background: #2e7d32; }
.btn-warning { background: #f57c00; margin-right: 5px; }
.btn-danger { background: #d32f2f; }
.btn-secondary { background: #757575; color: white; margin-right: 10px; }
button:disabled { opacity: 0.5; cursor: not-allowed; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content { background: #fff; padding: 30px; border-radius: 8px; width: 500px; max-width: 90%; }
.modal-actions { display: flex; justify-content: flex-end; margin-top: 20px; }
</style>
