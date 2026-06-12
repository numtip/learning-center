<script setup>
import { Head, Link } from '@inertiajs/vue3'
// เปลี่ยนชื่อ AdminLayout ตามไฟล์ Layout จริงของคุณ
import AdminLayout from '@/Layouts/AdminLayout.vue'

// ข้อมูลของการ์ดทั้ง 6 ใบ
const hubItems = [
  {
    title: 'ประวัติสำนักวิจัยฯ ส่วนกลาง',
    subtitle: 'จัดการประวัติความเป็นมาและวิสัยทัศน์ขององค์กรหลัก',
    icon: '📜',
    link: '/superadmin/history',
    bgColor: '#f3f4f6'
  },
  {
    title: 'ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา',
    subtitle: 'จุดเรียนรู้ภูมิปัญญาท้องถิ่น',
    icon: '🌿',
    link: '/superadmin/centers/lanna/edit',
    bgColor: '#f0fdf4'
  },
  {
    title: 'ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่',
    subtitle: 'พื้นที่สาธิตแนวคิดเกษตรทฤษฎีใหม่',
    icon: '🌾',
    link: '/superadmin/centers/royal/edit',
    bgColor: '#fefce8'
  },
  {
    title: 'ศูนย์เรียนรู้พัฒนาบ้านโปง',
    subtitle: 'แปลงสาธิตและศูนย์บริการชุมชน',
    icon: '🏡',
    link: '/superadmin/centers/banpong/edit',
    bgColor: '#fff7ed'
  },
  {
    title: 'ศูนย์กัญชง',
    subtitle: 'ศูนย์การเรียนรู้และวิจัยพืชกัญชง',
    icon: '🍃',
    link: '/superadmin/centers/hemp/edit',
    bgColor: '#f0fdfa'
  },
  {
    title: 'ฟาร์มสาธิตสำนักวิจัยฯ',
    subtitle: 'โรงเรือนสัตว์และพื้นที่ทดลอง',
    icon: '🐄',
    link: '/superadmin/centers/farm/edit',
    bgColor: '#f8fafc'
  }
]
</script>

<template>
  <AdminLayout>
    <Head title="จัดการข้อมูลศูนย์การเรียนรู้และประวัติ" />

    <div class="hub-container">

      <div class="hub-header">
        <div class="header-icon-box">
          <span class="header-icon">🏛️</span>
        </div>
        <div class="header-text">
          <h1>จัดการข้อมูลและประวัติศูนย์การเรียนรู้</h1>
          <p>เลือกระบบจัดการประวัติส่วนกลาง หรือแก้ไขรายละเอียดเนื้อหาของแต่ละศูนย์การเรียนรู้</p>
        </div>
      </div>

      <div class="hub-grid">
        <div v-for="(item, index) in hubItems" :key="index" class="hub-card">

          <div class="card-content">
            <div class="card-icon-box" :style="{ backgroundColor: item.bgColor }">
              <span class="card-icon">{{ item.icon }}</span>
            </div>
            <div class="card-info">
              <h3>{{ item.title }}</h3>
              <p>{{ item.subtitle }}</p>
            </div>
          </div>

          <Link :href="item.link" class="card-footer">
            <span class="footer-text">เข้าสู่ระบบจัดการเนื้อหา</span>
            <div class="arrow-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
              </svg>
            </div>
          </Link>

        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

.hub-container {
  font-family: 'Kanit', sans-serif;
  max-width: 1100px;
  margin: 0 auto;
  padding: 20px;
}

/* Header Banner */
.hub-header {
  background: linear-gradient(135deg, #e8fbf3 0%, #f4fdf8 100%);
  border-radius: 20px;
  padding: 30px 40px;
  display: flex;
  align-items: center;
  gap: 25px;
  margin-bottom: 40px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
  border: 1px solid #e0f2e9;
}

.header-icon-box {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}

.header-icon {
  font-size: 32px;
}

.header-text h1 {
  font-size: 26px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 8px 0;
}

.header-text p {
  font-size: 15px;
  color: #64748b;
  margin: 0;
}

/* Cards Grid */
.hub-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(48%, 1fr));
  gap: 25px;
}

/* Individual Card */
.hub-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
  overflow: hidden;
}

.hub-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

.card-content {
  padding: 25px;
  display: flex;
  gap: 20px;
  flex: 1;
}

.card-icon-box {
  width: 55px;
  height: 55px;
  border-radius: 14px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-shrink: 0;
}

.card-icon {
  font-size: 26px;
}

.card-info h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 8px 0;
}

.card-info p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}

/* Card Footer (Link Area) */
.card-footer {
  background: #f8fafc;
  padding: 16px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  text-decoration: none;
  border-top: 1px solid #e2e8f0;
  transition: background 0.3s;
}

.hub-card:hover .card-footer {
  background: #f1f5f9;
}

.footer-text {
  font-size: 14px;
  font-weight: 600;
  color: #475569;
}

.arrow-btn {
  width: 30px;
  height: 30px;
  background: #ffffff;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #94a3b8;
  border: 1px solid #e2e8f0;
  transition: all 0.3s;
}

.hub-card:hover .arrow-btn {
  background: #10b981;
  color: #ffffff;
  border-color: #10b981;
  transform: translateX(3px);
}

@media (max-width: 768px) {
  .hub-grid {
    grid-template-columns: 1fr;
  }
  .hub-header {
    flex-direction: column;
    text-align: center;
  }
}
</style>
