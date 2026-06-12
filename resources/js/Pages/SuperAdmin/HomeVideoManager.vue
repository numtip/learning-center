<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  videos: Array // ข้อมูลทั้ง 5 ศูนย์ที่ส่งมาจาก Controller
})

const form = ref({
  table_name: '',
  youtube_url: ''
})

const isSubmitting = ref(false)

// นับจำนวนวิดีโอที่มีอยู่จริง (ไม่นับศูนย์ที่ URL ว่าง)
const activeVideosCount = computed(() => {
  return props.videos.filter(v => v.video_url).length
})

// เมื่อเลือกศูนย์ ให้ดึง URL เดิมมาใส่ในฟอร์มอัตโนมัติ (ถ้ามี)
watch(() => form.value.table_name, (newTable) => {
  const center = props.videos.find(v => v.table_name === newTable)
  if (center && center.video_url) {
    form.value.youtube_url = center.video_url
  } else {
    form.value.youtube_url = ''
  }
})

const save = () => {
  if (!form.value.table_name || !form.value.youtube_url) {
    alert('กรุณาเลือกศูนย์และระบุลิงก์ YouTube ให้ครบถ้วน')
    return
  }

  isSubmitting.value = true

  // ส่งข้อมูลไปอัปเดต (พาร์ทนี้ต้องตรงกับ Route ที่ตั้งไว้)
  router.post('/superadmin/home/videos', form.value, {
    preserveScroll: true,
    onSuccess: () => {
      form.value = { table_name: '', youtube_url: '' }
      isSubmitting.value = false
    },
    onError: () => {
      isSubmitting.value = false
    }
  })
}

const remove = (tableName) => {
  if (confirm('ยืนยันที่จะลบวิดีโอของศูนย์นี้ใช่หรือไม่? (จะเป็นการนำวิดีโอออก)')) {
    router.delete(`/superadmin/home/videos/${tableName}`, {
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
          <h2>🎬 จัดการวิดีโอ 5 ศูนย์การเรียนรู้</h2>
          <p class="subtitle">ระบบจัดการวิดีโอแนะนำของแต่ละศูนย์ ซึ่งจะไปแสดงในหน้ารายละเอียดของศูนย์นั้นๆ</p>
        </div>
      </div>

      <div class="form-card">
        <h3 class="card-title">➕ เพิ่ม หรือ แก้ไขวิดีโอ</h3>

        <div class="form-grid">
          <div class="input-group">
            <label>เลือกศูนย์การเรียนรู้ <span class="required">*</span></label>
            <div class="select-wrapper">
              <select v-model="form.table_name" class="modern-input">
                <option value="">-- กรุณาเลือกศูนย์ --</option>
                <option v-for="v in videos" :key="v.table_name" :value="v.table_name">
                  {{ v.center_name }}
                </option>
              </select>
            </div>
          </div>

          <div class="input-group">
            <label>ลิงก์ YouTube <span class="required">*</span></label>
            <input
              v-model="form.youtube_url"
              class="modern-input"
              placeholder="เช่น https://www.youtube.com/watch?v=..."
            />
          </div>
        </div>

        <div class="action-bar">
          <button class="btn-submit" @click="save" :disabled="isSubmitting">
            💾 บันทึกวิดีโอ
          </button>
        </div>
      </div>

      <div class="gallery-section">
        <div class="gallery-header">
          <h3 class="gallery-title">วิดีโอที่แสดงผลอยู่ปัจจุบัน</h3>
          <span class="video-count">มีวิดีโอ {{ activeVideosCount }} จาก 5 ศูนย์</span>
        </div>

        <div v-if="activeVideosCount === 0" class="empty-state">
          ไม่พบข้อมูลวิดีโอในระบบ
        </div>

        <div v-else class="video-grid">
          <template v-for="v in videos" :key="v.table_name">
            <div v-if="v.youtube_id" class="video-card">
              <div class="iframe-wrapper">
                <iframe
                  :src="`https://www.youtube.com/embed/${v.youtube_id}`"
                  allowfullscreen
                  title="YouTube video player"
                ></iframe>
              </div>

              <div class="video-info">
                <h4 class="video-title">{{ v.center_name }}</h4>

                <div class="video-meta">
                  <span class="position-badge">อัปเดตแล้ว</span>
                  <button class="btn-delete" @click="remove(v.table_name)" title="ลบวิดีโอนี้">
                    🗑️ ลบวิดีโอ
                  </button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container { max-width: 1200px; margin: 0 auto; padding: 40px 24px; font-family: 'Prompt', sans-serif; color: #1e293b; }
.page-header { margin-bottom: 32px; }
.header-title h2 { font-size: 32px; font-weight: 700; color: #065f46; margin: 0 0 8px 0; }
.subtitle { font-size: 16px; color: #64748b; margin: 0; }
.form-card { background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 48px; }
.card-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 24px 0; display: flex; align-items: center; gap: 8px; }
.form-grid { display: grid; grid-template-columns: 1.5fr 2fr; gap: 20px; }
.input-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.required { color: #ef4444; }
.modern-input { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; font-family: inherit; font-size: 15px; color: #1e293b; transition: all 0.2s ease; }
.modern-input:focus { outline: none; border-color: #10b981; background: #ffffff; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
.select-wrapper { position: relative; }
select.modern-input { appearance: none; cursor: pointer; }
.select-wrapper::after { content: '▼'; font-size: 12px; color: #64748b; position: absolute; right: 16px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.action-bar { display: flex; justify-content: flex-end; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
.btn-submit { background: #10b981; color: #ffffff; border: none; padding: 12px 32px; border-radius: 10px; font-size: 16px; font-weight: 600; font-family: inherit; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
.btn-submit:hover:not(:disabled) { background: #059669; transform: translateY(-2px); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.gallery-section { background: #f8fafc; padding: 32px; border-radius: 20px; border: 1px solid #e2e8f0; }
.gallery-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
.gallery-title { font-size: 22px; font-weight: 700; color: #065f46; margin: 0; padding-left: 16px; border-left: 6px solid #10b981; }
.video-count { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 999px; font-size: 14px; font-weight: 600; }
.empty-state { background: #ffffff; border-radius: 12px; padding: 40px; text-align: center; color: #94a3b8; font-size: 16px; border: 1px solid #e2e8f0; }
.video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px; }
.video-card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; transition: all 0.3s ease; display: flex; flex-direction: column; }
.video-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
.iframe-wrapper { position: relative; width: 100%; padding-bottom: 56.25%; background: #000000; }
.iframe-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
.video-info { padding: 20px; display: flex; flex-direction: column; gap: 16px; flex-grow: 1; }
.video-title { font-size: 16px; font-weight: 600; color: #0f172a; margin: 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.video-meta { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
.position-badge { background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; }
.btn-delete { background: #fee2e2; color: #ef4444; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; font-family: inherit; }
.btn-delete:hover { background: #ef4444; color: #ffffff; }

@media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }
</style>
