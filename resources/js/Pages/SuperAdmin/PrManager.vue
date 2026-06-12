<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({ news: Array })

const showModal = ref(false)
const isEdit = ref(false)
const isProcessing = ref(false)

const form = ref({
  id: null, title: '', description: '', event_date: '',
  image1: null, image2: null, image3: null, image4: null, image5: null
})

// ฟังก์ชันบีบอัดรูปภาพ
const compressImage = (file, maxWidth = 1200, quality = 0.8) => {
  return new Promise((resolve) => {
    if (!file || !file.type.startsWith('image/')) return resolve(file)
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (event) => {
      const img = new Image()
      img.src = event.target.result
      img.onload = () => {
        let { width, height } = img
        if (width > maxWidth) {
          height = Math.round((height * maxWidth) / width)
          width = maxWidth
        }
        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height
        const ctx = canvas.getContext('2d')
        ctx.drawImage(img, 0, 0, width, height)
        canvas.toBlob((blob) => {
          resolve(new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), { type: 'image/jpeg' }))
        }, 'image/jpeg', quality)
      }
    }
  })
}

const resetForm = () => {
  form.value = { id: null, title: '', description: '', event_date: '', image1: null, image2: null, image3: null, image4: null, image5: null }
}

const openAdd = () => { resetForm(); isEdit.value = false; showModal.value = true }
const openEdit = (n) => {
  resetForm()
  form.value.id = n.id
  form.value.title = n.title
  form.value.description = n.description
  form.value.event_date = n.event_date
  isEdit.value = true
  showModal.value = true
}

const submitForm = async () => {
  if (!form.value.title || !form.value.description) return alert('กรุณากรอกข้อมูลให้ครบถ้วน')
  isProcessing.value = true
  const fd = new FormData()

  for (const k of Object.keys(form.value)) {
    if (form.value[k] !== null && form.value[k] !== '') {
      fd.append(k, form.value[k])
    }
  }

  const url = isEdit.value ? `/superadmin/pr/${form.value.id}` : '/superadmin/pr'

  router.post(url, fd, {
    onSuccess: () => { resetForm(); showModal.value = false; isProcessing.value = false },
    onError: () => { isProcessing.value = false }
  })
}

const remove = (id) => {
  if (confirm('ลบข่าวประชาสัมพันธ์นี้ใช่หรือไม่?')) {
    router.delete(`/superadmin/pr/${id}`)
  }
}

const onFile = async (e, key) => {
  const file = e.target.files[0]
  if (file) {
    isProcessing.value = true
    form.value[key] = await compressImage(file)
    isProcessing.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <div class="admin-wrapper">
      <div class="header-section">
        <div>
          <h2 class="page-title">📣 จัดการข่าวประชาสัมพันธ์</h2>
          <p class="page-subtitle">ประกาศ แจ้งเตือน หรือโปรโมทกิจกรรมต่างๆ ล่วงหน้า</p>
        </div>
        <button class="btn-primary" @click="openAdd">
          <span class="icon">➕</span> สร้างประกาศใหม่
        </button>
      </div>

      <div class="content-grid">
        <div v-for="n in news" :key="n.id" class="elegant-card">
          <div class="card-image-wrapper">
            <img :src="n.image1" alt="Cover" class="card-image">
            <div class="badge-overlay">ประกาศ (PR)</div>
          </div>
          <div class="card-content">
            <div class="meta-info" v-if="n.event_date">📅 วันที่จัดงาน: {{ n.event_date }}</div>
            <h4 class="card-title">{{ n.title }}</h4>
            <p class="card-desc">{{ n.description }}</p>
            <div class="card-actions">
              <button class="btn-edit" @click="openEdit(n)">✏️ แก้ไข</button>
              <button class="btn-delete" @click="remove(n.id)">🗑 ลบ</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal-container">
          <div class="modal-header">
            <h3>{{ isEdit ? '✏️ แก้ไขข่าวประกาศ' : '➕ สร้างข่าวประกาศใหม่' }}</h3>
            <button class="btn-close" @click="showModal = false">✕</button>
          </div>

          <div class="modal-body">
            <div class="form-group">
              <label>หัวข้อประกาศ <span class="text-red">*</span></label>
              <input v-model="form.title" class="modern-input" placeholder="ระบุหัวข้อที่ต้องการประกาศ" />
            </div>

            <div class="form-group">
              <label>วันที่จัดกิจกรรม (ถ้ามี)</label>
              <input type="date" v-model="form.event_date" class="modern-input" />
            </div>

            <div class="form-group">
              <label>รายละเอียดประกาศ <span class="text-red">*</span></label>
              <textarea v-model="form.description" class="modern-input textarea" placeholder="เนื้อหาสำคัญที่ต้องการแจ้งให้ทราบ..."></textarea>
            </div>

            <div class="upload-section">
              <div class="upload-cover">
                <label class="upload-label">🖼️ โปสเตอร์ / รูปหน้าปก (Cover) <small>ภาพหลักดึงดูดสายตา</small></label>
                <input type="file" class="modern-file" @change="e => onFile(e, 'image1')" accept="image/*" />
              </div>

              <div class="upload-gallery">
                <label class="upload-label">📸 รูปภาพบรรยากาศ / เพิ่มเติม (Gallery - สูงสุด 4 รูป)</label>
                <div class="gallery-grid">
                  <input type="file" class="modern-file small" @change="e => onFile(e, 'image2')" accept="image/*" title="รูปเพิ่มเติม 1" />
                  <input type="file" class="modern-file small" @change="e => onFile(e, 'image3')" accept="image/*" title="รูปเพิ่มเติม 2" />
                  <input type="file" class="modern-file small" @change="e => onFile(e, 'image4')" accept="image/*" title="รูปเพิ่มเติม 3" />
                  <input type="file" class="modern-file small" @change="e => onFile(e, 'image5')" accept="image/*" title="รูปเพิ่มเติม 4" />
                </div>
              </div>
              <p class="upload-note">* ไฟล์ภาพจะถูกปรับขนาดให้เหมาะสมกับการแสดงผลอัตโนมัติ</p>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn-cancel" @click="showModal = false" :disabled="isProcessing">ยกเลิก</button>
            <button class="btn-submit" @click="submitForm" :disabled="isProcessing">
              {{ isProcessing ? '⏳ กำลังย่อขนาดภาพ...' : '💾 ประกาศข่าว' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-wrapper { font-family: 'Prompt', sans-serif; max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; color: #1f2937; }
.header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; }
.page-title { font-size: 1.75rem; font-weight: 700; color: #b45309; margin: 0; } /* เปลี่ยนโทนสีให้เป็นสีส้มอิฐสำหรับ PR */
.page-subtitle { color: #6b7280; font-size: 0.9rem; margin-top: 0.25rem; }

.btn-primary { background: #f59e0b; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2); }
.btn-primary:hover { background: #d97706; transform: translateY(-2px); }
.btn-edit { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 500; cursor: pointer; flex: 1; transition: 0.2s; }
.btn-edit:hover { background: #dbeafe; }
.btn-delete { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 500; cursor: pointer; flex: 1; transition: 0.2s; }
.btn-delete:hover { background: #fee2e2; }

.content-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
.elegant-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #f3f4f6; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transition: 0.3s ease; display: flex; flex-direction: column; }
.elegant-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border-color: #fde68a; }
.card-image-wrapper { position: relative; width: 100%; padding-top: 60%; overflow: hidden; background: #f9fafb; }
.card-image { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
.badge-overlay { position: absolute; top: 1rem; left: 1rem; background: rgba(255, 255, 255, 0.9); color: #b45309; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(4px); }
.card-content { padding: 1.25rem; display: flex; flex-direction: column; flex: 1; }
.meta-info { font-size: 0.8rem; color: #d97706; font-weight: 600; margin-bottom: 0.5rem; background: #fef3c7; padding: 0.25rem 0.5rem; border-radius: 6px; display: inline-block; width: fit-content; }
.card-title { font-size: 1.1rem; font-weight: 600; color: #111827; margin: 0 0 0.5rem 0; line-height: 1.4; }
.card-desc { font-size: 0.875rem; color: #4b5563; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 1rem; flex: 1; }
.card-actions { display: flex; gap: 0.75rem; }

.modal-overlay { position: fixed; inset: 0; background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 50; padding: 1rem; }
.modal-container { background: white; width: 100%; max-width: 600px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; }
.modal-header { padding: 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 600; color: #111827; }
.btn-close { background: none; border: none; font-size: 1.25rem; color: #9ca3af; cursor: pointer; transition: 0.2s; }
.btn-close:hover { color: #111827; }
.modal-body { padding: 1.5rem; overflow-y: auto; }
.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9rem; color: #374151; }
.text-red { color: #ef4444; }
.modern-input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 10px; font-family: inherit; font-size: 0.95rem; transition: 0.2s; box-sizing: border-box; }
.modern-input:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
.textarea { min-height: 120px; resize: vertical; }

.upload-section { background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 12px; padding: 1.25rem; }
.upload-label { font-weight: 600; font-size: 0.9rem; color: #1f2937; display: block; margin-bottom: 0.5rem; }
.upload-label small { font-weight: normal; color: #6b7280; font-size: 0.8rem; }
.upload-cover { margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid #e5e7eb; }
.modern-file { width: 100%; font-size: 0.85rem; color: #4b5563; }
.modern-file::file-selector-button { background: #fff; border: 1px solid #d1d5db; padding: 0.5rem 1rem; border-radius: 8px; font-family: inherit; font-weight: 500; cursor: pointer; margin-right: 1rem; transition: 0.2s; }
.modern-file::file-selector-button:hover { background: #f3f4f6; }
.gallery-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.modern-file.small::file-selector-button { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
.upload-note { font-size: 0.75rem; color: #b45309; margin: 1rem 0 0 0; text-align: center; }

.modal-footer { padding: 1.25rem 1.5rem; border-top: 1px solid #f3f4f6; display: flex; justify-content: flex-end; gap: 1rem; background: #f9fafb; }
.btn-cancel { background: white; border: 1px solid #d1d5db; color: #374151; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 500; cursor: pointer; transition: 0.2s; }
.btn-cancel:hover { background: #f3f4f6; }
.btn-submit { background: #f59e0b; border: none; color: white; padding: 0.6rem 1.5rem; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-submit:hover:not(:disabled) { background: #d97706; }
.btn-submit:disabled, .btn-cancel:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
