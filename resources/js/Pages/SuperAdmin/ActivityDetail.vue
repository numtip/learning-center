<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3' 

const props = defineProps({
  centerKey: String,
  activity: Object
})

const activeImage = ref(props.activity?.image1)

const setMainImage = (img) => {
  if (img) activeImage.value = img
}

const goBack = () => {
  window.history.back()
}
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <div class="glass-card">

        <div class="detail-header">
          <h2 class="activity-title">{{ activity.activity_name }}</h2>
          <div class="quick-badges">
            <div class="badge price">💰 {{ activity.price || 'ไม่ระบุราคา' }}</div>
            <div class="badge duration">⏱️ {{ activity.duration }}</div>
            <div class="badge capacity">👥 {{ activity.participants_count }}</div>
          </div>
        </div>

        <div class="content-grid">

          <div class="visual-section">
            <div class="main-image-container">
              <img v-if="activeImage" :src="activeImage" class="main-img" />
              <div v-else class="no-image-placeholder">ไม่มีรูปภาพประกอบ</div>
            </div>

            <div class="thumbnail-row">
              <template v-for="i in 5" :key="i">
                <div
                  v-if="activity['image' + i]"
                  class="thumb-item"
                  :class="{ active: activeImage === activity['image' + i] }"
                  @click="setMainImage(activity['image' + i])"
                >
                  <img :src="activity['image' + i]" />
                </div>
              </template>
            </div>
          </div>

          <div class="info-section">
            <div class="info-group">
              <label>📝 รายละเอียดกิจกรรม</label>
              <div class="description-text">{{ activity.description || 'ไม่มีรายละเอียด' }}</div>
            </div>

            <div class="info-sub-grid">
              <div class="info-box">
                <label>📍 สถานที่</label>
                <p>{{ activity.location_name }}</p>
                <a v-if="activity.location_link" :href="activity.location_link" target="_blank" class="map-btn">
                  📍 ดูแผนที่ Google Maps
                </a>
              </div>

              <div class="info-box">
                <label>🎤 วิทยากร / ผู้ดูแล</label>
                <p>{{ activity.instructor || '-' }}</p>
              </div>

              <div class="info-box">
                <label>📅 ช่วงเวลาที่แนะนำ</label>
                <p>{{ activity.recommended_time || '-' }}</p>
              </div>

              <div class="info-box">
                <label>⏱️ ระยะเวลาดำเนินการ</label>
                <p>{{ activity.duration }}</p>
              </div>
            </div>
          </div>

        </div>

        <div class="detail-footer">
<a
  href="#"
  @click.prevent="goBack"
  class="btn-back"
>
  ย้อนกลับ
</a>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800;900&display=swap');

.admin-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
  font-family: 'Prompt', sans-serif;
}

.glass-card {
  background: #ffffff;
  border-radius: 30px;
  padding: 40px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.05);
  border: 1px solid #e2e8f0;
}

.detail-header {
  text-align: center;
  margin-bottom: 40px;
}

.activity-title {
  font-size: 32px;
  font-weight: 800;
  color: #064e3b;
  margin-bottom: 20px;
}

.quick-badges {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}

.badge {
  padding: 10px 20px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 15px;
}

.badge.price { background: #ecfdf5; color: #059669; border: 1px solid #bbf7d0; }
.badge.duration { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.badge.capacity { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
}

@media (max-width: 968px) {
  .content-grid { grid-template-columns: 1fr; }
}

.main-image-container {
  width: 100%;
  height: 400px;
  background: #f8fafc;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  margin-bottom: 15px;
}

.main-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.3s;
}

.thumbnail-row {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 5px;
}

.thumb-item {
  width: 80px;
  height: 60px;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: 0.2s;
  flex-shrink: 0;
}

.thumb-item img { width: 100%; height: 100%; object-fit: cover; }
.thumb-item.active { border-color: #10b981; transform: scale(1.05); }

.info-group {
  margin-bottom: 30px;
}

.info-group label {
  display: block;
  font-size: 18px;
  font-weight: 800;
  color: #065f46;
  margin-bottom: 12px;
}

.description-text {
  color: #475569;
  line-height: 1.8;
  font-size: 16px;
  white-space: pre-line;
}

.info-sub-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.info-box {
  background: #f8fafc;
  padding: 20px;
  border-radius: 18px;
  border: 1px solid #f1f5f9;
}

.info-box label {
  display: block;
  font-size: 13px;
  font-weight: 800;
  color: #64748b;
  margin-bottom: 8px;
  text-transform: uppercase;
}

.info-box p {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
}

.map-btn {
  display: inline-block;
  margin-top: 10px;
  color: #2563eb;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
}

.map-btn:hover { text-decoration: underline; }

.detail-footer {
  margin-top: 40px;
  border-top: 1px solid #f1f5f9;
  padding-top: 30px;
  text-align: center;
}

.btn-back {
  display: inline-block;
  background: #f1f5f9;
  color: #475569;
  border: none; /* เพิ่มส่วนนี้ */
  padding: 12px 40px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.2s;
  text-decoration: none;
  font-family: 'Prompt', sans-serif;
}

.btn-back:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.no-image-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #94a3b8;
}
</style>
