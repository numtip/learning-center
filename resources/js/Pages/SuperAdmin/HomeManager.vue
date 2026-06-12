<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <div class="hero-header">
        <div class="header-content">
          <span class="badge">Super Admin</span>
          <h1 class="page-title">จัดการหน้าเว็บไซต์หลัก</h1>
          <p class="page-subtitle">ศูนย์ควบคุมเนื้อหาหลักและการแสดงผลทั้งหมดของหน้าแรก</p>
        </div>
      </div>

      <div class="dashboard-grid">

        <div class="feature-card" style="animation-delay: 0.1s;">
          <div class="icon-wrapper">
            <span class="emoji">🖼️</span>
          </div>
          <h3 class="card-title">ภาพสไลด์หน้าแรก</h3>
          <p class="card-desc">จัดการและอัปเดต Banner Slide รูปภาพขนาดใหญ่ด้านบนสุดของเว็บไซต์</p>
          <Link href="/superadmin/home/slides" class="action-btn">
            <span>จัดการสไลด์</span>
            <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </Link>
        </div>

        <div class="feature-card" style="animation-delay: 0.2s;">
          <div class="icon-wrapper">
            <span class="emoji">🎬</span>
          </div>
          <h3 class="card-title">วิดีโอแนะนำศูนย์</h3>
          <p class="card-desc">ควบคุมและเปลี่ยนแปลงวิดีโอประชาสัมพันธ์ทั้ง 4 ช่องบนหน้าแรก</p>
          <Link href="/superadmin/home/videos" class="action-btn">
            <span>จัดการวิดีโอ</span>
            <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </Link>
        </div>

        <div class="feature-card" style="animation-delay: 0.3s;">
          <div class="icon-wrapper">
            <span class="emoji">📣</span>
          </div>
          <h3 class="card-title">ข่าวประชาสัมพันธ์</h3>
          <p class="card-desc">เพิ่ม แก้ไข หรือลบ ข่าวประกาศและข่าวสารประชาสัมพันธ์อย่างเป็นทางการ</p>
          <Link href="/superadmin/pr" class="action-btn">
            <span>จัดการข่าว PR</span>
            <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </Link>
        </div>

        <div class="feature-card" style="animation-delay: 0.4s;">
          <div class="icon-wrapper">
            <span class="emoji">🎉</span>
          </div>
          <h3 class="card-title">ข่าวกิจกรรม</h3>
          <p class="card-desc">จัดการข้อมูลและรูปภาพของข่าวกิจกรรมทั้งหมดภายในศูนย์การเรียนรู้</p>
          <Link href="/superadmin/activity-news" class="action-btn">
            <span>จัดการกิจกรรม</span>
            <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </Link>
        </div>

        <div class="feature-card" style="animation-delay: 0.5s;">
          <div class="icon-wrapper">
            <span class="emoji">📚</span>
          </div>
          <h3 class="card-title">บทความและสาระ</h3>
          <p class="card-desc">ระบบสร้างและเผยแพร่บทความ องค์ความรู้ เพื่อแสดงบนหน้าแรกของเว็บไซต์</p>
          <Link href="/superadmin/articles-manager" class="action-btn">
            <span>จัดการบทความ</span>
            <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </Link>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;900&display=swap');

.admin-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 24px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
}

.hero-header {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border-radius: 24px;
  padding: 48px;
  margin-bottom: 40px;
  border: 1px solid #bbf7d0;
  box-shadow: 0 10px 30px -10px rgba(22, 163, 74, 0.15);
  animation: fadeUp 0.6s ease-out forwards;
}

.header-content {
  max-width: 800px;
}

.badge {
  display: inline-block;
  background: #166534;
  color: #ffffff;
  padding: 6px 16px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
  text-transform: uppercase;
}

.page-title {
  font-size: 42px;
  font-weight: 900;
  color: #064e3b;
  margin: 0 0 12px 0;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 18px;
  color: #065f46;
  font-weight: 500;
  margin: 0;
  opacity: 0.9;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 28px;
}

.feature-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 36px 32px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
  opacity: 0;
  transform: translateY(20px);
  animation: fadeUp 0.6s ease-out forwards;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25);
  border-color: #10b981;
}

.icon-wrapper {
  width: 72px;
  height: 72px;
  background: #f0fdf4;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
  transition: all 0.4s ease;
  border: 1px solid #dcfce7;
}

.emoji {
  font-size: 32px;
  transition: transform 0.4s ease;
}

.feature-card:hover .icon-wrapper {
  background: #10b981;
  border-color: #10b981;
  transform: scale(1.05) rotate(-5deg);
}

.feature-card:hover .emoji {
  transform: scale(1.1);
}

.card-title {
  font-size: 22px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 12px 0;
}

.card-desc {
  font-size: 15px;
  color: #64748b;
  line-height: 1.6;
  margin: 0 0 32px 0;
  flex-grow: 1;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 24px;
  background: #f8fafc;
  border-radius: 16px;
  color: #0f172a;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.arrow-icon {
  color: #94a3b8;
  transition: all 0.3s ease;
}

.feature-card:hover .action-btn {
  background: #10b981;
  color: #ffffff;
  border-color: #10b981;
}

.feature-card:hover .arrow-icon {
  color: #ffffff;
  transform: translateX(6px);
}

@keyframes fadeUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .hero-header {
    padding: 32px 24px;
  }
  .page-title {
    font-size: 32px;
  }
  .feature-card {
    padding: 28px 24px;
  }
}
</style>
