<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({
  historyData: Array
})

const showAdd = ref(false)
const showEdit = ref(false)
const editId = ref(null)

const form = useForm({
  year: '',
  title: '',
  description: '',
  image: null
})

// เปิดฟอร์มเพิ่ม
const openAdd = () => {
  showEdit.value = false
  editId.value = null
  form.reset()
  showAdd.value = !showAdd.value
}

// เพิ่ม
const submit = () => {
  if (!form.year || !form.title) {
    alert('กรุณากรอก ปี พ.ศ. และ หัวข้อ ให้ครบถ้วน')
    return
  }

  form.post('/superadmin/history', {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      showAdd.value = false
      alert('✅ บันทึกข้อมูลประวัติใหม่สำเร็จ')
    }
  })
}

// เปิดแก้ไข
const openEdit = (item) => {
  showAdd.value = false
  showEdit.value = true
  editId.value = item.id

  form.year = item.year
  form.title = item.title
  form.description = item.description
  form.image = null
}

const update = () => {
  if (!form.year || !form.title) {
    alert('กรุณากรอก ปี พ.ศ. และ หัวข้อ ให้ครบถ้วน')
    return
  }

  form.post(`/superadmin/history/${editId.value}`, {
    forceFormData: true,
    onSuccess: () => {
      showEdit.value = false
      editId.value = null
      form.reset()
      alert('✅ แก้ไขข้อมูลประวัติสำเร็จ')
    }
  })
}

// ลบ
const del = (id) => {
  if (confirm('ยืนยันที่จะลบข้อมูลประวัตินี้ใช่หรือไม่?')) {
    router.delete(`/superadmin/history/${id}`)
  }
}

/* ===== PAGINATION (แบ่งหน้าละ 10 รายการ) ===== */
const currentPage = ref(1)
const itemsPerPage = 10

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(props.historyData.length / itemsPerPage))
})

const paginatedHistory = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return props.historyData.slice(start, end)
})

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <div class="page-header">
        <div class="header-title">
          <h2>📜 จัดการประวัติความเป็นมา</h2>
          <p class="subtitle">ระบบบันทึกและแก้ไขประวัติไทม์ไลน์ของศูนย์การเรียนรู้</p>
        </div>

        <button @click="openAdd" :class="['btn-add', showAdd ? 'btn-active' : '']">
          <svg v-if="!showAdd" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          <span v-if="!showAdd">เพิ่มข้อมูลใหม่</span>
          <span v-else>✖ ยกเลิกการเพิ่ม</span>
        </button>
      </div>

      <transition name="slide-fade">
        <div v-if="showAdd" class="form-card mb-24">
          <div class="form-section-title">
            <span>📝 ฟอร์มบันทึกประวัติใหม่</span>
            <hr class="section-divider">
          </div>

          <form @submit.prevent="submit" class="form-grid">

            <div class="input-group">
              <label>ปี พ.ศ. <span class="required">*</span></label>
              <input v-model="form.year" type="text" class="modern-input" placeholder="เช่น 2567">
            </div>

            <div class="input-group">
              <label>หัวข้อ / เหตุการณ์สำคัญ <span class="required">*</span></label>
              <input v-model="form.title" type="text" class="modern-input" placeholder="ใส่ชื่อเหตุการณ์...">
            </div>

            <div class="input-group full-width">
              <label>รายละเอียดเพิ่มเติม</label>
              <textarea v-model="form.description" rows="4" class="modern-input" placeholder="อธิบายเหตุการณ์โดยย่อ..."></textarea>
            </div>

            <div class="input-group full-width upload-section">
              <label>อัปโหลดรูปภาพประกอบ</label>
              <div class="upload-container">
                <input type="file" id="add-image" class="file-input" @change="e => form.image = e.target.files[0]" accept="image/*">
                <label for="add-image" class="upload-btn">
                  📸 เลือกรูปภาพ
                </label>
                <span class="file-name">{{ form.image ? form.image.name : 'ยังไม่ได้เลือกไฟล์...' }}</span>
              </div>
            </div>

            <div class="action-bar full-width">
              <button type="submit" class="btn-submit" :disabled="form.processing">
                💾 บันทึกข้อมูล
              </button>
            </div>

          </form>
        </div>
      </transition>

      <div class="card-table-wrapper">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th width="140">ปี พ.ศ.</th>
                <th>หัวข้อ / เหตุการณ์</th>
                <th class="text-center" width="180">รูปภาพ</th>
                <th class="text-center" width="180">จัดการ</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="paginatedHistory.length === 0">
                <td colspan="4" class="text-center empty-state">ไม่พบข้อมูลประวัติในระบบ</td>
              </tr>
              <tr v-for="item in paginatedHistory" :key="item.id" class="table-row">

                <td>
                  <span class="year-badge">{{ item.year }}</span>
                </td>

                <td>
                  <span class="history-title">{{ item.title }}</span>
                </td>

                <td class="text-center">
                  <div class="img-wrapper">
                    <img v-if="item.imageUrl" :src="item.imageUrl" class="preview-img" alt="History Image" />
                    <div v-else class="no-img">ไม่มีรูป</div>
                  </div>
                </td>

                <td class="text-center">
                  <div class="action-buttons">
                    <button type="button" @click="openEdit(item)" class="btn-action edit" title="แก้ไข">
                      ✏️
                    </button>
                    <button type="button" @click="del(item.id)" class="btn-action delete" title="ลบ">
                      🗑️
                    </button>
                  </div>
                </td>

              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-container" v-if="totalPages > 1">
          <div class="pagination-info">
            แสดงหน้า <strong>{{ currentPage }}</strong> จาก <strong>{{ totalPages }}</strong>
          </div>
          <div class="pagination-controls">
            <button @click="prevPage" :disabled="currentPage === 1" class="page-btn">
              « ก่อนหน้า
            </button>

            <button
              v-for="pageNum in totalPages"
              :key="pageNum"
              @click="currentPage = pageNum"
              :class="['page-num', { active: currentPage === pageNum }]"
            >
              {{ pageNum }}
            </button>

            <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">
              ถัดไป »
            </button>
          </div>
        </div>

      </div>

    </div>

    <div v-if="showEdit" class="modal-backdrop">
      <div class="modal-card">

        <div class="modal-header">
          <h3 class="modal-title">✏️ แก้ไขข้อมูลประวัติ</h3>
          <button @click="showEdit = false" class="close-btn">✖</button>
        </div>

        <form @submit.prevent="update" class="modal-body">
          <div class="input-group">
            <label>ปี พ.ศ.</label>
            <input v-model="form.year" type="text" class="modern-input">
          </div>

          <div class="input-group">
            <label>หัวข้อ</label>
            <input v-model="form.title" type="text" class="modern-input">
          </div>

          <div class="input-group">
            <label>รายละเอียด</label>
            <textarea v-model="form.description" class="modern-input" rows="5"></textarea>
          </div>

          <div class="input-group upload-section">
            <label>เปลี่ยนรูปภาพ (ถ้ามี)</label>
            <div class="upload-container">
              <input type="file" id="edit-image" class="file-input" @change="e => form.image = e.target.files[0]" accept="image/*">
              <label for="edit-image" class="upload-btn">
                📸 เลือกรูปภาพใหม่
              </label>
              <span class="file-name">{{ form.image ? form.image.name : 'ไม่ได้เลือกรูปใหม่' }}</span>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="showEdit = false" class="btn-cancel">
              ยกเลิก
            </button>
            <button type="submit" class="btn-submit" :disabled="form.processing">
              บันทึกการแก้ไข
            </button>
          </div>
        </form>

      </div>
    </div>

  </AdminLayout>
</template>

<style scoped>
/* ===== BASE & FONT ===== */
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container {
  max-width: 1400px; /* ขยายขนาดใหญ่ขึ้น จาก 1100px เป็น 1400px */
  margin: 0 auto;
  padding: 40px 24px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
}

.text-center { text-align: center; }
.mb-24 { margin-bottom: 32px; }
.full-width { grid-column: 1 / -1; }

/* ===== HEADER ===== */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 28px;
}
.header-title h2 { font-size: 32px; font-weight: 700; color: #065f46; margin: 0 0 8px 0; }
.subtitle { font-size: 16px; color: #64748b; margin: 0; }

.btn-add {
  display: inline-flex; align-items: center; gap: 8px; background: #10b981; color: #ffffff;
  font-weight: 600; font-size: 16px; padding: 12px 24px; border-radius: 12px; border: none;
  cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
  font-family: inherit;
}
.btn-add:hover { background: #059669; transform: translateY(-2px); }
.btn-active { background: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); }
.btn-active:hover { background: #dc2626; }

/* ===== ADD FORM CARD ===== */
.form-card {
  background: #ffffff; border-radius: 16px; padding: 32px 40px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;
}
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px 28px; }

.form-section-title {
  display: flex; align-items: center; gap: 12px; font-size: 18px; font-weight: 600;
  color: #0f172a; margin-bottom: 24px;
}
.section-divider { flex-grow: 1; height: 1px; background: #e2e8f0; border: none; margin: 0; }

.input-group label { display: block; font-size: 15px; font-weight: 600; color: #475569; margin-bottom: 10px; }
.required { color: #ef4444; }

.modern-input {
  width: 100%; padding: 14px 18px; border-radius: 12px; border: 1px solid #cbd5e1;
  background: #f8fafc; font-family: inherit; font-size: 16px; color: #1e293b; transition: all 0.2s ease;
}
.modern-input:focus { outline: none; border-color: #10b981; background: #ffffff; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }

/* UPLOAD */
.upload-section { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px dashed #cbd5e1; }
.upload-container { display: flex; align-items: center; gap: 16px; }
.file-input { display: none; }
.upload-btn {
  display: inline-flex; align-items: center; gap: 8px; background: #ffffff; border: 1px solid #cbd5e1;
  color: #475569; padding: 10px 20px; border-radius: 8px; font-size: 15px; font-weight: 600;
  cursor: pointer; transition: all 0.2s;
}
.upload-btn:hover { border-color: #10b981; color: #10b981; background: #f0fdf4; }
.file-name { font-size: 15px; color: #64748b; }

.action-bar { display: flex; justify-content: flex-end; margin-top: 16px; }
.btn-submit { background: #10b981; color: #ffffff; border: none; padding: 14px 32px; border-radius: 12px; font-size: 16px; font-weight: 600; font-family: inherit; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
.btn-submit:hover:not(:disabled) { background: #059669; transform: translateY(-2px); }

/* ===== TABLE WRAPPER ===== */
.card-table-wrapper {
  background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
  border: 1px solid #e2e8f0; overflow: hidden;
}
.table-responsive { width: 100%; overflow-x: auto; }
.modern-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
.modern-table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.modern-table th { padding: 20px; font-size: 15px; font-weight: 600; color: #475569; text-align: left; }
.modern-table th.text-center { text-align: center; }
.modern-table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
.table-row:hover { background: #f0fdf4; }
.empty-state { padding: 40px !important; color: #94a3b8; font-size: 16px; }

/* Table Items */
.year-badge { background: #dcfce7; color: #166534; padding: 6px 16px; border-radius: 8px; font-weight: 600; font-size: 16px; }
.history-title { font-weight: 600; color: #0f172a; font-size: 16px; }

.img-wrapper { width: 120px; height: 75px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; margin: 0 auto; background: #f8fafc; }
.preview-img { width: 100%; height: 100%; object-fit: cover; }
.no-img { font-size: 13px; color: #94a3b8; display: flex; align-items: center; justify-content: center; height: 100%; }

/* Actions */
.action-buttons { display: flex; justify-content: center; gap: 12px; }
.btn-action { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: none; cursor: pointer; font-size: 16px; transition: all 0.2s; }
.btn-action.edit { background: #f1f5f9; color: #3b82f6; } .btn-action.edit:hover { background: #dbeafe; transform: translateY(-2px); }
.btn-action.delete { background: #f1f5f9; color: #ef4444; } .btn-action.delete:hover { background: #fee2e2; transform: translateY(-2px); }

/* ===== PAGINATION ===== */
.pagination-container { display: flex; justify-content: space-between; align-items: center; padding: 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
.pagination-info { font-size: 15px; color: #64748b; font-weight: 500; }
.pagination-controls { display: flex; gap: 8px; }
.pagination-controls button { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 10px 18px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: inherit;}
.pagination-controls button:hover:not(:disabled) { border-color: #10b981; color: #10b981; }
.pagination-controls button.active { background: #10b981; color: #ffffff; border-color: #10b981; }
.pagination-controls button:disabled { opacity: 0.5; cursor: not-allowed; background: #f1f5f9; }

/* ===== MODAL (EDIT) ===== */
.modal-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal-card { background: #ffffff; border-radius: 20px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: modalIn 0.3s ease-out; }
@keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 24px 28px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.modal-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
.close-btn { background: transparent; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; transition: color 0.2s; } .close-btn:hover { color: #ef4444; }

.modal-body { padding: 28px; display: flex; flex-direction: column; gap: 20px; }

.modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
.btn-cancel { background: #f1f5f9; color: #475569; padding: 12px 24px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; font-family: inherit; font-size: 16px; transition: 0.2s; } .btn-cancel:hover { background: #e2e8f0; color: #0f172a; }

/* Transitions */
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateY(-10px); opacity: 0; }

@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .upload-container { flex-direction: column; align-items: flex-start; } }
</style>
