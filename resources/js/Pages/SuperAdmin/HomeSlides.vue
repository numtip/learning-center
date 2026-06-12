<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ slides: Array })

const form = ref({
  title: '',
  image: null
})

const preview = ref(null)
const isSubmitting = ref(false)

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  form.value.image = file
  const reader = new FileReader()
  reader.onload = ev => preview.value = ev.target.result
  reader.readAsDataURL(file)
}

const submit = () => {
  if (!form.value.title || !form.value.image) {
    alert('กรุณากรอกหัวข้อภาพและเลือกรูปภาพให้ครบถ้วน')
    return
  }

  isSubmitting.value = true
  const data = new FormData()
  data.append('title', form.value.title)
  data.append('image', form.value.image)

  router.post('/superadmin/home/slides', data, {
    preserveScroll: true,
    onSuccess: () => {
      form.value.title = ''
      form.value.image = null
      preview.value = null
      isSubmitting.value = false
    },
    onError: () => {
      isSubmitting.value = false
    }
  })
}

const remove = (id) => {
  if (confirm('ยืนยันที่จะลบภาพสไลด์นี้ใช่หรือไม่?')) {
    router.delete(`/superadmin/home/slides/${id}`, {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <div class="page-header">
        <div class="header-title">
          <h2>🖼️ จัดการภาพสไลด์หน้าแรก</h2>
          <p class="subtitle">ระบบเพิ่มและแก้ไข Banner Slide ด้านบนของเว็บไซต์</p>
        </div>
      </div>

      <div class="form-card">
        <h3 class="card-title">➕ เพิ่มสไลด์ใหม่</h3>

        <div class="form-grid">
          <div class="input-group">
            <label>หัวข้อภาพ / คำอธิบาย <span class="required">*</span></label>
            <input v-model="form.title" class="modern-input" placeholder="ระบุหัวข้อสไลด์..." />
          </div>

          <div class="input-group upload-section">
            <label>รูปภาพสไลด์ <span class="required">*</span></label>
            <div class="upload-container">
              <input type="file" id="slide-upload" class="file-input" @change="onFileChange" accept="image/*">
              <label for="slide-upload" class="upload-btn">
                📸 เลือกรูปภาพ
              </label>
              <span class="file-name">{{ form.image ? form.image.name : 'ยังไม่ได้เลือกไฟล์...' }}</span>
            </div>
          </div>
        </div>

        <div v-if="preview" class="preview-container">
          <p class="preview-label">ตัวอย่างรูปภาพ:</p>
          <img :src="preview" class="preview-img" />
        </div>

        <div class="action-bar">
          <button class="btn-submit" @click="submit" :disabled="isSubmitting">
            💾 บันทึกสไลด์
          </button>
        </div>
      </div>

      <div class="gallery-section">
        <div class="gallery-header">
          <h3 class="gallery-title">ภาพสไลด์ปัจจุบัน</h3>
          <span class="slide-count">ทั้งหมด {{ slides.length }} ภาพ</span>
        </div>

        <div v-if="slides.length === 0" class="empty-state">
          ไม่พบข้อมูลภาพสไลด์ในระบบ
        </div>

        <div v-else class="slide-grid">
          <div v-for="s in slides" :key="s.id" class="slide-card">
            <div class="img-wrapper">
              <img :src="s.image" class="slide-img" />
            </div>

            <div class="slide-info">
              <span class="slide-title-text">{{ s.title }}</span>
              <button class="btn-delete" @click="remove(s.id)" title="ลบภาพนี้">
                🗑️ ลบ
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 24px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
}

.page-header {
  margin-bottom: 32px;
}

.header-title h2 {
  font-size: 32px;
  font-weight: 700;
  color: #065f46;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: #64748b;
  margin: 0;
}

.form-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
  border: 1px solid #e2e8f0;
  margin-bottom: 48px;
}

.card-title {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 24px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.input-group label {
  display: block;
  font-size: 15px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 8px;
}

.required {
  color: #ef4444;
}

.modern-input {
  width: 100%;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  font-family: inherit;
  font-size: 15px;
  color: #1e293b;
  transition: all 0.2s ease;
}

.modern-input:focus {
  outline: none;
  border-color: #10b981;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}

.upload-container {
  display: flex;
  align-items: center;
  gap: 16px;
}

.file-input {
  display: none;
}

.upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.upload-btn:hover {
  border-color: #10b981;
  color: #10b981;
  background: #f0fdf4;
}

.file-name {
  font-size: 14px;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 200px;
}

.preview-container {
  margin-top: 24px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px dashed #cbd5e1;
}

.preview-label {
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  margin: 0 0 12px 0;
}

.preview-img {
  max-width: 100%;
  height: auto;
  max-height: 300px;
  border-radius: 8px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  object-fit: cover;
}

.action-bar {
  display: flex;
  justify-content: flex-end;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
}

.btn-submit {
  background: #10b981;
  color: #ffffff;
  border: none;
  padding: 12px 32px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: 0.2s;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
}

.btn-submit:hover:not(:disabled) {
  background: #059669;
  transform: translateY(-2px);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.gallery-section {
  background: #f8fafc;
  padding: 32px;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
}

.gallery-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.gallery-title {
  font-size: 22px;
  font-weight: 700;
  color: #065f46;
  margin: 0;
  padding-left: 16px;
  border-left: 6px solid #10b981;
}

.slide-count {
  background: #dcfce7;
  color: #166534;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 600;
}

.empty-state {
  background: #ffffff;
  border-radius: 12px;
  padding: 40px;
  text-align: center;
  color: #94a3b8;
  font-size: 16px;
  border: 1px solid #e2e8f0;
}

.slide-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
}

.slide-card {
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.slide-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.img-wrapper {
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.slide-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.slide-card:hover .slide-img {
  transform: scale(1.05);
}

.slide-info {
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-grow: 1;
}

.slide-title-text {
  font-size: 16px;
  font-weight: 600;
  color: #0f172a;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btn-delete {
  background: #fee2e2;
  color: #ef4444;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
  font-family: inherit;
}

.btn-delete:hover {
  background: #ef4444;
  color: #ffffff;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
