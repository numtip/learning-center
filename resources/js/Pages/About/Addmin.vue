<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import FooterFarm from '@/Components/FooterFarm.vue'
import { computed } from 'vue'

const page = usePage()
const sections = computed(() => page.props.sections || [])

// ฟังก์ชันเลือกไอคอนตามตำแหน่ง
const getPositionIcon = (position) => {
  const pos = position?.toLowerCase() || '';


  if (pos.includes('ผู้อำนวยการ') || pos.includes('หัวหน้า') || pos.includes('ประธาน')) {
    return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`;
  }

  if (pos.includes('วิชาการ') || pos.includes('วิจัย') || pos.includes('อาจารย์')) {
    return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`;
  }

  if (pos.includes('เกษตร') || pos.includes('ฟาร์ม') || pos.includes('สวน')) {
    return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L7 7H2v10h5l5 5V2z"/><path d="M11 22V2"/><path d="M15 10l4 4-4 4"/></svg>`;
  }

  return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`;
}
</script>

<template>
  <PublicLayout>
    <Head title="บุคลากรและเจ้าหน้าที่" />

    <section class="premium-staff-page">
      <div class="luxury-bg-glow"></div>

      <header class="main-header">
        <h1 class="page-title">⭐ บุคลากรและเจ้าหน้าที่ ⭐</h1>
        <div class="title-underline">
          <span class="diamond-spacer"></span>
        </div>
      </header>

      <div v-for="(section, idx) in sections" :key="idx" class="center-block">
        <div v-if="section.staff.length > 0">

          <div class="section-divider">
            <div class="line-fade"></div>
            <div class="pill-title">
              <svg class="pill-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              <h2>{{ section.title }}</h2>
            </div>
            <div class="line-fade"></div>
          </div>

          <div class="staff-grid">
            <div v-for="(staff, sIdx) in section.staff" :key="sIdx" class="card-wrap">
              <div class="executive-card">

                <div class="image-container">
                  <img v-if="staff.image" :src="staff.image" class="photo" />
                  <div v-else class="photo-null">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#C9A23F" stroke-width="0.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  </div>
                  <div class="light-sweep"></div>
                </div>

                <div class="data-box">
                  <h3 class="staff-name">{{ staff.name }}</h3>
                  <div class="accent-bar"></div>

                  <div class="position-tag">
                    <span class="tag-icon" v-html="getPositionIcon(staff.position)"></span>
                    <span class="position-text">{{ staff.position }}</span>
                  </div>
                </div>

                <div class="hover-border"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <FooterFarm />
  </PublicLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;600;700&display=swap');

.premium-staff-page {
  background: #ffffff;
  font-family: 'IBM Plex Sans Thai', sans-serif;
  padding: 150px 0 100px;
  position: relative;
  overflow: hidden;
  margin-top: -38px;
}

.luxury-bg-glow {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: radial-gradient(circle at 50% -10%, rgba(201, 162, 63, 0.08) 0%, transparent 50%);
  pointer-events: none;
}

.main-header { text-align: center; margin-bottom: 100px; }
.page-title { font-size: 3.5rem;
    font-weight: 800;
    color: #1b5e20;
     margin-top: -68px;

}
.title-underline { width: 140px; height: 1px; background: #c9a23f50; margin: 25px auto; position: relative; }
.diamond-spacer { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg); width: 8px; height: 8px; background: #c9a23f; }

.center-block { margin-bottom: 120px; padding: 0 40px; }
.section-divider { display: flex; align-items: center; justify-content: center; gap: 30px; margin-bottom: 60px; }
.line-fade { flex-grow: 1; height: 1px; background: linear-gradient(90deg, transparent, #c9a23f40, transparent); max-width: 250px; }
.pill-title { display: flex; align-items: center; gap: 15px; background: #fff; padding: 12px 40px; border-radius: 100px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: 1px solid rgba(46, 125, 50, 0.1); }
.pill-icon { color: #c9a23f; }
.pill-title h2 { font-size: 1.6rem; font-weight: 700; color: #1b5e20; }

.staff-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 310px)); gap: 40px; justify-content: center; max-width: 1500px; margin: 0 auto; }
.executive-card { background: #fff; border-radius: 28px; overflow: hidden; position: relative; box-shadow: 0 15px 45px rgba(0,0,0,0.03); transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1); border: 1px solid rgba(0,0,0,0.02); height: 100%; display: flex; flex-direction: column; }

.image-container { position: relative; width: 100%; aspect-ratio: 4 / 5.2; overflow: hidden; background: #f8f8f8; }
.photo { width: 100%; height: 100%; object-fit: cover; transition: 1.2s cubic-bezier(0.23, 1, 0.32, 1); }
.executive-card:hover .photo { transform: scale(1.1); }
.light-sweep { position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transform: skewX(-20deg); transition: 0.8s; }
.executive-card:hover .light-sweep { left: 150%; }

.data-box { padding: 35px 25px; text-align: center; flex-grow: 1; background: #fff; }
.staff-name { font-size: 1.35rem; font-weight: 700; color: #1b5e20; margin-bottom: 12px; }
.accent-bar { width: 35px; height: 3px; background: #c9a23f; margin: 0 auto 20px; border-radius: 10px; transition: 0.5s; }
.executive-card:hover .accent-bar { width: 100px; }

.position-tag { display: inline-flex; align-items: center; justify-content: center; gap: 10px; background: #f9fbf8; padding: 8px 18px; border-radius: 50px; border: 1px solid #2e7d3208; width: 100%; }
.tag-icon { color: #c9a23f; display: flex; align-items: center; }
.position-text { font-size: 0.95rem; color: #4b5563; font-weight: 500; }

.executive-card:hover { transform: translateY(-15px); box-shadow: 0 35px 70px rgba(27, 94, 32, 0.15); }
.hover-border { position: absolute; inset: 0; border: 2px solid transparent; border-radius: 28px; transition: 0.5s; pointer-events: none; }
.executive-card:hover .hover-border { border-color: #c9a23f40; }

@media (max-width: 768px) {
  .page-title { font-size: 2.4rem; }
  .pill-title h2 { font-size: 1.2rem; }
  .staff-grid { grid-template-columns: 1fr; }
}
</style>
