<script setup>
import { ref, computed } from 'vue'
import { usePage, router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()

// ดึงข้อมูลบุคลากรจาก props
const personnels = computed(() => page.props.personnels || [])

// จัดกลุ่มข้อมูลตามศูนย์การเรียนรู้
const grouped = computed(() => {
  const g = {}
  personnels.value.forEach(p => {
    if (!g[p.center]) g[p.center] = []
    g[p.center].push(p)
  })
  return g
})

// สถานะ Form
const showForm = ref(false)
const preview = ref(null)
const form = ref({
  id: null,
  name: '',
  position: '',
  center: '',
  image: null
})

// รายชื่อศูนย์ (ให้ตรงกับหน้าแสดงผลหลัก)
const centers = [
  'ศูนย์วัฒนธรรมเกษตรล้านนา',
  'ศูนย์การเรียนรู้แปลงสาธิตเกษตรทฤษฎีใหม่ตามแนวพระราชดำริ',
  'ศูนย์เกษตรทฤษฎีใหม่',
  'ศูนย์ฟาร์มสาธิต',
  'ศูนย์บ้านโป่ง',
  'ศูนย์การเรียนรู้กัญชง'
]

// ฟังก์ชันเปิด Modal สำหรับเพิ่ม
const openAdd = () => {
  form.value = { id: null, name: '', position: '', center: '', image: null }
  preview.value = null
  showForm.value = true
}

// ฟังก์ชันเปิด Modal สำหรับแก้ไข
const openEdit = (p) => {
  form.value = {
    id: p.id,
    name: p.name,
    position: p.position,
    center: p.center,
    image: null // เก็บเป็น null ไว้ถ้าไม่ได้เลือกใหม่
  }
  preview.value = p.image // แสดงรูปเดิม
  showForm.value = true
}

// จัดการการเปลี่ยนรูปภาพ
const onFileChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.value.image = file
  const reader = new FileReader()
  reader.onload = ev => preview.value = ev.target.result
  reader.readAsDataURL(file)
}

// บันทึกข้อมูล
const save = () => {
  if (!form.value.name || !form.value.position || !form.value.center) {
    alert('กรุณากรอกข้อมูล ชื่อ ตำแหน่ง และศูนย์ให้ครบถ้วน')
    return
  }

  const data = new FormData()
  data.append('name', form.value.name)
  data.append('position', form.value.position)
  data.append('center', form.value.center)

  // ถ้ามีการเลือกรูปใหม่ ให้ส่งไฟล์ไป
  if (form.value.image instanceof File) {
    data.append('image', form.value.image)
  }

  if (form.value.id) {
    // กรณี Update
    data.append('_method', 'PUT')
    router.post(`/superadmin/personnel/${form.value.id}`, data, {
      forceFormData: true,
      onSuccess: () => {
        showForm.value = false
      }
    })
  } else {
    // กรณี Create
    router.post('/superadmin/personnel', data, {
      onSuccess: () => {
        showForm.value = false
      }
    })
  }
}

// ลบข้อมูล
const remove = (id) => {
  if (!confirm('ยืนยันการลบข้อมูลบุคลากรท่านนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้')) return
  router.delete(`/superadmin/personnel/${id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <AdminLayout>
    <Head title="จัดการบุคลากร" />

    <div class="admin-container">
      <div class="page-header">
        <div class="header-title">
          <h2>👤 จัดการบุคลากรและเจ้าหน้าที่</h2>
          <p class="subtitle">เพิ่ม แก้ไข หรือลบรายชื่อบุคลากรประจำศูนย์การเรียนรู้</p>
        </div>

        <button @click="openAdd" class="btn-add">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          เพิ่มบุคลากรใหม่
        </button>
      </div>

      <div v-if="personnels.length === 0" class="empty-state">
        <div class="empty-icon">📁</div>
        <p>ยังไม่มีข้อมูลในระบบ กดปุ่มด้านบนเพื่อเพิ่มข้อมูล</p>
      </div>

      <div v-for="(list, centerName) in grouped" :key="centerName" class="center-section">
        <div class="center-header">
          <h3 class="center-title">{{ centerName }}</h3>
          <span class="personnel-count">{{ list.length }} ท่าน</span>
        </div>

        <div class="personnel-grid">
          <div v-for="p in list" :key="p.id" class="personnel-card">
            <div class="card-image-wrapper">
              <img v-if="p.image" :src="p.image" alt="Personnel" />
              <div v-else class="no-img">ไม่มีรูปภาพ</div>
            </div>

            <div class="card-info">
              <h4 class="personnel-name">{{ p.name }}</h4>
              <p class="personnel-position">{{ p.position }}</p>
            </div>

            <div class="card-actions">
              <button class="btn-action edit" @click="openEdit(p)">✏️ แก้ไข</button>
              <button class="btn-action delete" @click="remove(p.id)">🗑️ ลบ</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>

  <Teleport to="body">
    <transition name="modal-fade">
      <div v-if="showForm" class="modal-backdrop" @click.self="showForm = false">
        <div class="modal-card">
          <div class="modal-header">
            <h3 class="modal-title">{{ form.id ? 'แก้ไขข้อมูลบุคลากร' : 'เพิ่มบุคลากรใหม่' }}</h3>
            <button @click="showForm = false" class="close-btn">✕</button>
          </div>

          <div class="modal-body">
            <div class="input-group">
              <label>ชื่อ-นามสกุล</label>
              <input v-model="form.name" type="text" class="modern-input" placeholder="ตัวอย่าง: นายสมชาย ใจดี">
            </div>

            <div class="input-group">
              <label>ตำแหน่ง</label>
              <input v-model="form.position" type="text" class="modern-input" placeholder="ตัวอย่าง: ผู้อำนวยการศูนย์">
            </div>

            <div class="input-group">
              <label>ศูนย์การเรียนรู้ที่สังกัด</label>
              <select v-model="form.center" class="modern-input">
                <option value="" disabled>เลือกศูนย์การเรียนรู้</option>
                <option v-for="c in centers" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>

            <div class="input-group upload-section">
              <label>รูปภาพโปรไฟล์</label>
              <div class="upload-controls">
                <input type="file" id="p-img" class="file-hidden" @change="onFileChange" accept="image/*">
                <label for="p-img" class="custom-upload-btn">📸 เลือกรูปภาพ</label>
                <span class="file-status">{{ form.image ? 'เลือกไฟล์แล้ว' : 'ยังไม่ได้เลือกไฟล์' }}</span>
              </div>

              <div v-if="preview" class="preview-box">
                <img :src="preview" class="image-preview-el">
                <p class="preview-text">ตัวอย่างการแสดงผล</p>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <button @click="showForm = false" class="btn-cancel">ยกเลิก</button>
            <button @click="save" class="btn-submit">
              {{ form.id ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 24px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
}

.header-title h2 {
  font-size: 28px;
  font-weight: 700;
  color: #065f46;
  margin: 0;
}

.subtitle { color: #64748b; margin-top: 4px; }

.btn-add {
  background: #10b981;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.btn-add:hover { background: #059669; transform: translateY(-2px); }

.center-section {
  background: #ffffff;
  border-radius: 20px;
  padding: 30px;
  margin-bottom: 40px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}

.center-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
  border-bottom: 2px solid #f1f5f9;
  padding-bottom: 15px;
}

.center-title { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0; }

.personnel-count {
  background: #dcfce7;
  color: #166534;
  padding: 4px 12px;
  border-radius: 99px;
  font-size: 13px;
  font-weight: 600;
}

.personnel-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 25px;
}

.personnel-card {
  border: 1px solid #f1f5f9;
  border-radius: 16px;
  overflow: hidden;
  transition: 0.3s;
}

.personnel-card:hover { border-color: #10b981; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

.card-image-wrapper { height: 260px; background: #f8fafc; overflow: hidden; }
.card-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
.no-img { height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; }

.card-info { padding: 15px; text-align: center; }
.personnel-name { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.personnel-position { font-size: 13px; color: #64748b; }

.card-actions { display: flex; gap: 8px; padding: 12px; background: #f8fafc; }
.btn-action { flex: 1; border: none; padding: 8px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; font-family: inherit; }
.btn-action.edit { background: #eef2ff; color: #4f46e5; }
.btn-action.delete { background: #fff1f2; color: #e11d48; }
.btn-action.edit:hover { background: #e0e7ff; }
.btn-action.delete:hover { background: #ffe4e6; }
</style>

<style>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999; /* ดันให้อยู่บนสุดเสมอ */
  padding: 20px;
  box-sizing: border-box;
  font-family: 'Prompt', sans-serif; /* คงฟอนต์ไว้ */
}

.modal-card {
  background: white;
  width: 100%;
  max-width: 550px;
  border-radius: 24px;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
  display: flex;
  flex-direction: column;
  max-height: 90vh;
  overflow: hidden;
  margin: auto;
}

.modal-header {
  padding: 24px;
  background: #f8fafc;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
  flex-shrink: 0;
}

.modal-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }

.close-btn { background: transparent; border: none; font-size: 18px; cursor: pointer; color: #64748b; }

.modal-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  overflow-y: auto;
}

.input-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #475569; font-size: 14px; }

.modern-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  font-family: inherit;
  font-size: 15px;
  transition: 0.2s;
  box-sizing: border-box;
}

.modern-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

.upload-section { background: #f1f5f9; padding: 20px; border-radius: 16px; }

.file-hidden { display: none; }

.custom-upload-btn {
  background: white;
  border: 1px solid #cbd5e1;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  display: inline-block;
  font-weight: 600;
  font-size: 14px;
}

.preview-box { margin-top: 15px; text-align: center; }
.image-preview-el { width: 120px; height: 150px; object-fit: cover; border-radius: 12px; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.preview-text { font-size: 12px; color: #64748b; margin-top: 5px; }

.modal-actions {
  padding: 20px 24px;
  background: #f8fafc;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  border-top: 1px solid #e2e8f0;
  flex-shrink: 0;
}

.btn-cancel { background: #e2e8f0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; }
.btn-submit { background: #10b981; color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>
