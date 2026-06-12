<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  centerId: Number,
  mapImage: String,
  markers: Array,
  activities: Array
})

const mapRef = ref(null)
const mapWrapper = ref(null)

const pins = ref([])
const previewPin = ref(null)
const selectedActivity = ref(null)
const tempPosition = ref(null)
const showSelect = ref(false)

watch(() => props.markers, (newMarkers) => {
  pins.value = newMarkers.map(p => ({
    ...p,
    show: false,
    edit: false,
    newActivityId: p.activity_id
  }))
}, { immediate: true })

const addPin = (e) => {
  const rect = mapRef.value.getBoundingClientRect()
  const x = ((e.clientX - rect.left) / rect.width) * 100
  const y = ((e.clientY - rect.top) / rect.height) * 100

  tempPosition.value = { x, y }
  previewPin.value = { x, y }
  showSelect.value = true

  nextTick(() => {
    const cx = e.clientX - window.innerWidth / 2
    const cy = e.clientY - window.innerHeight / 2
    mapWrapper.value.scrollBy({ left: cx, top: cy, behavior: 'smooth' })
  })
}

const savePin = () => {
  if (!selectedActivity.value) return alert('กรุณาเลือกกิจกรรมก่อนบันทึกหมุด')

  const act = props.activities.find(a => a.id === selectedActivity.value)

  router.post('/superadmin/map-marker', {
    center_id: props.centerId,
    activity_id: selectedActivity.value,
    title: act.activity_name,
    description: act.description,
    x_percent: tempPosition.value.x,
    y_percent: tempPosition.value.y
  }, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['markers'] })
      cancelPin()
    }
  })
}

const cancelPin = () => {
  previewPin.value = null
  selectedActivity.value = null
  tempPosition.value = null
  showSelect.value = false
}

const deletePin = (id) => {
  if (!confirm('ยืนยันที่จะลบหมุดนี้ใช่หรือไม่?')) return
  router.delete(`/superadmin/map-marker/${id}`, {
    data: { center_id: props.centerId },
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['markers'] })
  })
}

const goDetail = (activityId) => {
  router.visit(`/superadmin/map/${props.centerId}/activities/${activityId}`)
}

const updatePinActivity = (pin) => {
  router.put(`/superadmin/map-marker/${pin.id}`, {
    center_id: props.centerId,
    activity_id: pin.newActivityId
  }, {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['markers'] })
  })
}

const showAddActivity = ref(false)
const newActivity = ref({
  activity_name: '',
  description: '',
  location_name: '',
  instructor: '',
  duration: '',
  participants_count: '',
  image: null
})

const saveActivity = () => {
  if (!newActivity.value.activity_name || !newActivity.value.location_name) {
    alert('กรุณากรอกชื่อกิจกรรมและสถานที่ให้ครบถ้วน')
    return
  }

  const form = new FormData()
  Object.keys(newActivity.value).forEach(k => {
    if(newActivity.value[k] !== null && newActivity.value[k] !== '') {
      form.append(k, newActivity.value[k])
    }
  })
  form.append('center_id', props.centerId)

  router.post(`/superadmin/activity/${props.centerId}`, form, {
    preserveScroll: true,
    onSuccess: () => {
      showAddActivity.value = false
      Object.keys(newActivity.value).forEach(k => newActivity.value[k] = '')
      router.reload({ only: ['activities'] })
    }
  })
}

const onImageChange = (e) => {
  newActivity.value.image = e.target.files[0]
}

const goBack = () => {
  window.history.back()
}
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <div class="header-section">
<div class="header-section">
  <div class="title-area">
    <button @click="goBack" class="btn-back-outline">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
      </svg>
      ย้อนกลับ
    </button>

    <h2 class="page-title">🗺️ จัดการพื้นที่และพิกัด</h2>
    <p class="page-subtitle">คลิกบนพื้นที่แผนที่เพื่อเพิ่มหมุดกิจกรรมประจำศูนย์การเรียนรู้</p>
  </div>
  </div>
        <div class="action-area">
          <button class="btn-primary" @click="showAddActivity = true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            เพิ่มกิจกรรมใหม่
          </button>
        </div>
      </div>

      <div class="map-workspace">

        <transition name="slide-down">
          <div v-if="showSelect" class="floating-command-bar">
            <div class="command-content">
              <span class="command-label">📍 เลือกกิจกรรมสำหรับหมุดใหม่:</span>
              <div class="select-wrapper">
                <select v-model="selectedActivity" class="modern-select">
                  <option value="" disabled>-- คลิกเพื่อเลือกกิจกรรม --</option>
                  <option v-for="a in activities" :key="a.id" :value="a.id">
                    {{ a.activity_name }}
                  </option>
                </select>
              </div>
              <div class="command-actions">
                <button class="btn-cancel-sm" @click="cancelPin">ยกเลิก</button>
                <button class="btn-save-sm" @click="savePin">บันทึกหมุด</button>
              </div>
            </div>
          </div>
        </transition>

        <div class="map-container" ref="mapWrapper">
          <div class="map-canvas">
            <img :src="mapImage" ref="mapRef" class="map-image" @click="addPin" alt="Center Map" />

            <div
              v-if="previewPin"
              class="marker-pin preview-marker"
              :style="{ left: previewPin.x + '%', top: previewPin.y + '%' }"
            >
              <div class="pulse-ring"></div>
              <div class="core-dot">+</div>
            </div>

            <div
              v-for="(p, i) in pins"
              :key="p.id"
              class="marker-pin real-marker"
              :style="{ left: p.x_percent + '%', top: p.y_percent + '%' }"
              @click.stop="p.show = !p.show"
            >
              <div class="pulse-ring"></div>
              <div class="core-dot">{{ i + 1 }}</div>

              <transition name="fade-scale">
                <div v-if="p.show" class="info-window" @click.stop>
                  <div class="info-header">
                    <h4 class="info-title">{{ p.title }}</h4>
                    <button class="close-info" @click.stop="p.show = false">✖</button>
                  </div>
                  <p class="info-desc">{{ p.description || 'ไม่มีรายละเอียดเพิ่มเติม' }}</p>

                  <div v-if="!p.edit" class="info-actions">
                    <button class="badge-btn btn-view" @click="goDetail(p.activity_id)">ดูรายละเอียด</button>
                    <button class="badge-btn btn-edit" @click="p.edit = true">เปลี่ยนกิจกรรม</button>
                    <button class="badge-btn btn-delete" @click="deletePin(p.id)">ลบหมุด</button>
                  </div>

                  <div v-else class="edit-mode-area">
                    <label class="edit-label">เลือกกิจกรรมใหม่</label>
                    <div class="select-wrapper">
                      <select v-model="p.newActivityId" class="modern-select small">
                        <option v-for="a in activities" :key="a.id" :value="a.id">
                          {{ a.activity_name }}
                        </option>
                      </select>
                    </div>
                    <div class="edit-actions">
                      <button class="badge-btn btn-cancel" @click="p.edit = false">ยกเลิก</button>
                      <button class="badge-btn btn-save" @click="updatePinActivity(p)">บันทึก</button>
                    </div>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>

    </div>

    <transition name="modal-fade">
      <div v-if="showAddActivity" class="modal-backdrop">
        <div class="modal-card">
          <div class="modal-header">
            <h3 class="modal-title">➕ เพิ่มข้อมูลกิจกรรมใหม่</h3>
            <button @click="showAddActivity = false" class="close-btn">✖</button>
          </div>

          <div class="modal-body">
            <div class="form-grid">
              <div class="input-group full-width">
                <label>ชื่อกิจกรรม <span class="required">*</span></label>
                <input v-model="newActivity.activity_name" class="modern-input" placeholder="ระบุชื่อกิจกรรม">
              </div>

              <div class="input-group full-width">
                <label>รายละเอียดกิจกรรม</label>
                <textarea v-model="newActivity.description" class="modern-input" rows="3" placeholder="คำอธิบายกิจกรรมพอสังเขป..."></textarea>
              </div>

              <div class="input-group">
                <label>ชื่อสถานที่ <span class="required">*</span></label>
                <input v-model="newActivity.location_name" class="modern-input" placeholder="เช่น อาคาร A, แปลงสาธิตที่ 1">
              </div>

              <div class="input-group">
                <label>วิทยากร / ผู้ดูแล</label>
                <input v-model="newActivity.instructor" class="modern-input" placeholder="ระบุชื่อวิทยากร (ถ้ามี)">
              </div>

              <div class="input-group">
                <label>ระยะเวลา</label>
                <input v-model="newActivity.duration" class="modern-input" placeholder="เช่น 2 ชั่วโมง, ครึ่งวัน">
              </div>

              <div class="input-group">
                <label>จำนวนผู้เข้าร่วมสูงสุด (คน)</label>
                <input v-model="newActivity.participants_count" type="number" class="modern-input" placeholder="เช่น 50">
              </div>

              <div class="input-group full-width upload-section">
                <label>รูปภาพกิจกรรมปก</label>
                <div class="upload-container">
                  <input type="file" id="activity-img" class="file-input" @change="onImageChange" accept="image/*">
                  <label for="activity-img" class="upload-btn">📸 เลือกรูปภาพ</label>
                  <span class="file-name">{{ newActivity.image ? newActivity.image.name : 'ยังไม่ได้เลือกไฟล์...' }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <button @click="showAddActivity = false" class="btn-cancel-modal">ยกเลิก</button>
            <button @click="saveActivity" class="btn-save-modal">บันทึกกิจกรรม</button>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;900&display=swap');

.admin-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: #065f46;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.page-subtitle {
  font-size: 16px;
  color: #64748b;
  margin: 0;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #10b981;
  color: #ffffff;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.btn-primary:hover {
  background: #059669;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
}

.map-workspace {
  position: relative;
  background: #ffffff;
  border-radius: 24px;
  padding: 16px;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.floating-command-bar {
  position: absolute;
  top: 32px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border: 1px solid #10b981;
  padding: 12px 20px;
  border-radius: 16px;
  box-shadow: 0 15px 35px -5px rgba(16, 185, 129, 0.3);
  z-index: 50;
}

.command-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.command-label {
  font-weight: 700;
  color: #065f46;
  font-size: 15px;
  white-space: nowrap;
}

.command-actions {
  display: flex;
  gap: 8px;
}

.btn-cancel-sm {
  background: #f1f5f9;
  color: #64748b;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: 0.2s;
}

.btn-cancel-sm:hover { background: #e2e8f0; color: #0f172a; }

.btn-save-sm {
  background: #10b981;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: 0.2s;
  white-space: nowrap;
}

.btn-save-sm:hover { background: #059669; }

.map-container {
  width: 100%;
  max-height: 75vh;
  overflow: auto;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
}

.map-canvas {
  position: relative;
  display: inline-block;
  min-width: 100%;
}

.map-image {
  display: block;
  width: 100%;
  height: auto;
  border-radius: 12px;
  cursor: crosshair;
}

.marker-pin {
  position: absolute;
  transform: translate(-50%, -100%);
  z-index: 10;
}

.core-dot {
  position: relative;
  width: 36px;
  height: 36px;
  background: #ef4444;
  border: 3px solid #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-weight: 800;
  font-size: 14px;
  box-shadow: 0 4px 10px rgba(239, 68, 68, 0.4);
  cursor: pointer;
  z-index: 2;
  transition: transform 0.2s ease;
}

.real-marker:hover .core-dot {
  transform: scale(1.15);
  background: #dc2626;
}

.pulse-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  height: 100%;
  background: rgba(239, 68, 68, 0.5);
  border-radius: 50%;
  z-index: 1;
  animation: radarPulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
  pointer-events: none;
}

.preview-marker .core-dot {
  background: #f87171;
  box-shadow: 0 0 15px rgba(248, 113, 113, 0.8);
}
.preview-marker .pulse-ring {
  animation: none;
  background: rgba(248, 113, 113, 0.3);
  transform: translate(-50%, -50%) scale(1.5);
}

@keyframes radarPulse {
  0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
  100% { transform: translate(-50%, -50%) scale(2.5); opacity: 0; }
}

.info-window {
  position: absolute;
  bottom: calc(100% + 12px);
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  width: 320px;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
  border: 1px solid #e2e8f0;
  z-index: 30;
  cursor: default;
}

.info-window::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  border-width: 8px 8px 0;
  border-style: solid;
  border-color: rgba(255, 255, 255, 0.95) transparent transparent transparent;
  display: block;
  width: 0;
}

.info-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 8px;
}

.info-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  line-height: 1.4;
  flex: 1;
  padding-right: 12px;
}

.close-info {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 14px;
  cursor: pointer;
  padding: 0;
}
.close-info:hover { color: #ef4444; }

.info-desc {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.info-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.badge-btn {
  border: none;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}

.btn-view { background: #dbeafe; color: #1e40af; }
.btn-view:hover { background: #bfdbfe; }
.btn-edit { background: #fef3c7; color: #b45309; }
.btn-edit:hover { background: #fde68a; }
.btn-delete { background: #fee2e2; color: #b91c1c; }
.btn-delete:hover { background: #fecaca; }

.edit-mode-area {
  background: #f8fafc;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.edit-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 8px;
}

.edit-actions {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  justify-content: flex-end;
}

.btn-cancel { background: #e2e8f0; color: #475569; }
.btn-save { background: #10b981; color: #ffffff; }

.select-wrapper { position: relative; width: 100%; }
.modern-select {
  width: 100%; padding: 10px 16px; border-radius: 10px;
  border: 1px solid #cbd5e1; background: #ffffff; font-family: inherit;
  font-size: 14px; color: #1e293b; appearance: none; cursor: pointer;
}
.modern-select:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15); }
.modern-select.small { padding: 8px 12px; font-size: 13px; }
.select-wrapper::after {
  content: '▼'; font-size: 10px; color: #64748b; position: absolute;
  right: 16px; top: 50%; transform: translateY(-50%); pointer-events: none;
}

.modal-backdrop {
  position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 9999;
}
.modal-card {
  background: #ffffff; border-radius: 20px; width: 100%; max-width: 650px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;
}
.modal-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 24px 28px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.modal-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
.close-btn { background: transparent; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; }
.close-btn:hover { color: #ef4444; }

.modal-body { padding: 28px; max-height: 70vh; overflow-y: auto; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.full-width { grid-column: 1 / -1; }
.input-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.required { color: #ef4444; }
.modern-input {
  width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1;
  background: #f8fafc; font-family: inherit; font-size: 14px; color: #1e293b; transition: all 0.2s;
}
.modern-input:focus { background: #ffffff; border-color: #10b981; outline: none; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }

.upload-section { background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px dashed #cbd5e1; }
.upload-container { display: flex; align-items: center; gap: 16px; }
.file-input { display: none; }
.upload-btn {
  display: inline-flex; align-items: center; gap: 8px; background: #ffffff; border: 1px solid #cbd5e1;
  padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;
}
.upload-btn:hover { border-color: #10b981; color: #10b981; }
.file-name { font-size: 13px; color: #64748b; }

.modal-actions {
  display: flex; justify-content: flex-end; gap: 12px; padding: 20px 28px; border-top: 1px solid #e2e8f0; background: #f8fafc;
}
.btn-cancel-modal { background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s; font-family: inherit; }
.btn-cancel-modal:hover { background: #e2e8f0; color: #0f172a; }
.btn-save-modal { background: #10b981; color: #ffffff; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s; font-family: inherit; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
.btn-save-modal:hover { background: #059669; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translate(-50%, -20px); }
.fade-scale-enter-active, .fade-scale-leave-active { transition: all 0.2s ease; transform-origin: bottom center; }
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: translateX(-50%) scale(0.9); }
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }


.btn-back-outline {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  color: #64748b;
  border: 1.5px solid #e2e8f0;
  padding: 6px 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: 12px;
  font-family: inherit;
}

.btn-back-outline:hover {
  background: #f8fafc;
  color: #1e293b;
  border-color: #cbd5e1;
  transform: translateX(-3px);
}

.title-area {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

@media (max-width: 768px) {
  .header-section { flex-direction: column; align-items: flex-start; gap: 16px; }
  .form-grid { grid-template-columns: 1fr; }
  .command-content { flex-direction: column; align-items: stretch; }
}
</style>
