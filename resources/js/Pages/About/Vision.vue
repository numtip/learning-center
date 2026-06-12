<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import FooterFarm from '@/Components/FooterFarm.vue'

const page = usePage()
const visions = page.props.visions
</script>

<template>
  <PublicLayout>
    <Head title="ปรัชญา วิสัยทัศน์ พันธกิจ" />

    <section class="vision-section">
      <h1 class="vision-title-main">ปรัชญา วิสัยทัศน์ พันธกิจ</h1>

      <div class="vision-grid">
        <div
          v-for="(item, index) in visions"
          :key="item.id"
          class="vision-card"
          :class="`offset-${index}`"
        >
          <!-- IMAGE (หัวการ์ดมน) -->
          <div class="vision-img">
            <img :src="item.image" :alt="item.title" />
          </div>

          <!-- BODY (เหลี่ยม) -->
          <div class="vision-body">
            <span class="vision-tag">
            </span>

            <h2 class="vision-title">{{ item.title }}</h2>

            <p v-if="index !== 2" class="vision-text">
              {{ item.description }}
            </p>

            <ol v-else class="vision-list">
              <li
                v-for="(line, i) in item.description.split('\n')"
                :key="i"
              >
                {{ line }}
              </li>
            </ol>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>

  <FooterFarm />
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Mitr:wght@200;300;400;500;600;700&display=swap');

/* ================= THEME ================= */
:root {
  --green-dark: #1b5e20;
  --green-main: #2f6b4f;
  --green-soft: #4caf50;
  --gold: #c9a23f;
  --bg: #eef6f1;
  --text: #2e3d32;
}

/* ================= SECTION ================= */
.vision-section {
  background: radial-gradient(circle at top, #f0fdf4, #e5efe9);
  padding: 140px 8% 160px;
  min-height: 100vh;
  margin-top: -78px;
  font-family: 'Mitr', sans-serif;
}

/* ================= TITLE ================= */
.vision-title-main {
  text-align: center;
  font-size: 48px;
  font-weight: 600;
  margin-bottom: 110px;
  position: relative;
}

.vision-title-main::after {
  content: '';
  width: 80px;
  height: 4px;
  background: var(--gold);
  display: block;
  margin: 20px auto 0;
  border-radius: 999px;
}

/* ================= GRID ================= */
.vision-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  justify-items: center;
  margin-top: -70px;
}

/* ================= CARD ================= */
.vision-card {
  position: relative;
  max-width: 370px;
  max-height: 610px;
  background: #ffffff;
  border-radius: 180px 180px 24px 24px;
  overflow: visible;

  transform: translateY(0);
  transition:
    transform .45s ease,
    box-shadow .45s ease;

  box-shadow:
    0 10px 25px rgba(0,0,0,0.08),
    0 28px 60px rgba(0,0,0,0.12);
}

.vision-card:hover {
  transform: translateY(-12px);
  box-shadow:
    0 18px 40px rgba(0,0,0,0.18),
    0 45px 90px rgba(0,0,0,0.15);
}

/* เงาลอยใต้การ์ด (ไม่สั่น) */
.vision-card::after {
  content: "";
  position: absolute;
  left: 12%;
  right: 12%;
  bottom: -22px;
  height: 26px;
  background: rgba(0,0,0,0.22);
  filter: blur(24px);
  z-index: -1;
  transition: opacity .45s ease;
}

.vision-card:hover::after {
  opacity: .7;
}

/* ================= STAGGER (ใช้ transform ไม่ใช้ margin) ================= */
.offset-0 { transform: translateY(0); }
.offset-1 { transform: translateY(28px); }
.offset-2 { transform: translateY(12px); }

/* ================= IMAGE ================= */
.vision-img {
  display: flex;
  justify-content: center;
  overflow: hidden;
  border-radius: 180px 180px 0 0;
  margin-bottom: 4px;
}

.vision-img img {
  width: 75%;
  height: 260px;
  object-fit: cover;
  border-radius: 999px 999px 40px 40px;
}

/* ================= BODY ================= */
.vision-body {
  padding: 36px 34px 42px;
}

/* ================= TAG ================= */
.vision-tag {
  display: inline-block;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 3px;
  color: var(--gold);
  margin-bottom: 10px;
}

/* ================= TITLE ================= */
.vision-title {
  font-size: 26px;
  font-weight: 600;
  color: var(--green-dark);
  margin-bottom: 16px;
}

/* ================= TEXT ================= */
.vision-text {
  font-size: 17px;
  font-weight: 400;       /* <<< จาก 300 → 400 กำลังสวย */
  line-height: 1.9;       /* อ่านสบายขึ้น */
  color: #2f3f34;
}

/* ================= LIST ================= */
.vision-list {
  padding-left: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;              /* หายใจโปร่งขึ้นนิด */
}

.vision-list li {
  font-size: 17px;
  font-weight: 400;       /* <<< จาก 300 → 400 กำลังสวย */
  line-height: 1.9;       /* อ่านสบายขึ้น */
  color: #2f3f34;         /* เข้มขึ้นนิด ไม่ซีด */
}

/* ================= RESPONSIVE ================= */
@media (max-width: 1024px) {
  .vision-grid {
    grid-template-columns: 1fr;
    gap: 70px;
  }

  .offset-0,
  .offset-1,
  .offset-2 {
    transform: translateY(0);
  }

  .vision-title-main {
    font-size: 38px;
    margin-bottom: 80px;
  }
}

@media (max-width: 480px) {
  .vision-title {
    font-size: 22px;
  }

  .vision-text,
  .vision-list li {
    font-size: 16px;
  }
}

</style>
