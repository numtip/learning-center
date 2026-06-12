<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'

const { orgs } = usePage().props
</script>

<template>
  <PublicLayout>
    <Head title="โครงสร้างการบริหารงาน" />

    <div class="org-wrapper">
      <h1 class="title">
        โครงสร้างการบริหารงานสนับสนุนโครงการตามพระราชดำริ และพัฒนาเชิงพื้นที่
      </h1>

      <div class="center">
        <div class="box arrow">{{ orgs[0].description }}</div>
        <div class="box arrow">{{ orgs[2].description }}</div>
        <div class="box arrow">{{ orgs[4].description }}</div>
        <div class="box arrow">{{ orgs[5].description }}</div>
        <div class="box arrow">{{ orgs[6].description }}</div>
        <div class="box arrow">{{ orgs[7].description }}</div>
      </div>

      <div class="right">
        <div class="box small right-arrow">{{ orgs[1].description }}</div>
        <div class="box small right-arrow">{{ orgs[3].description }}</div>
      </div>

      <div class="branch"></div>

      <div class="bottom">
        <div class="column">
          <div class="box arrow">{{ orgs[8].description }}</div>
          <div class="box arrow">{{ orgs[9].description }}</div>

          <div class="box arrow small">{{ orgs[10].description }}</div>
          <div class="box small">{{ orgs[11].description }}</div>
        </div>

        <div class="column">
          <div class="box arrow">{{ orgs[13].description }}</div>
          <div class="box arrow">{{ orgs[12].description }}</div>
          <div class="box arrow small">{{ orgs[14].description }}</div>
          <div class="box small">{{ orgs[15].description }}</div>
        </div>
      </div>
    </div>

    <FooterFarm />
  </PublicLayout>
</template>

<style scoped>
.org-wrapper {
  padding: 70px 20px 80px;
  background: #eef6f1;
  text-align: center;
  position: relative;
}

.title {
  font-size: 34px;
  font-weight: 800;
  line-height: 1.4;
  text-align: center;
  color: #1f3d2b;
  margin-bottom: 50px;
  letter-spacing: 0.5px;
  position: relative;
}

.title::after {
  content: "";
  display: block;
  width: 120px;
  height: 4px;
  background: linear-gradient(90deg, #c9a23f, #6c8b5e);
  margin: 18px auto 0;
  border-radius: 999px;
}


.center {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
}

.right {
  position: absolute;
  right: 18%;
  top: 190px;
  display: grid;
  row-gap: 14px;
}

.branch {
  width: 35%;
  height: 3px;
  background-color: #6c8b5e;
  margin: 28px auto 18px;
  position: relative;
  border-radius: 2px;
}

.branch::before,
.branch::after {
  content: "";
  position: absolute;
  top: 0;
  width: 2px;
  height: 14px;
  background-color: #6c8b5e;
}

.branch::before { left: 0; }
.branch::after { right: 0; }

.bottom {
  display: flex;
  justify-content: center;
  gap: 120px;
  margin-top: 20px;
}

.column {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.box {
  background: linear-gradient(135deg, #7f8f64, #5f7c55);
  color: #ffffff;
  padding: 10px 22px;
  border-radius: 40px;
  min-width: 280px;
  max-width: 420px;
  line-height: 1.5;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
  border: 2px solid rgba(255,255,255,0.6);
  position: relative;
  text-align: center;
  transition: transform .2s ease, box-shadow .2s ease;
}

.box:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 26px rgba(0, 0, 0, 0.25);
}

.box.small {
  min-width: 240px;
  max-width: 360px;
  font-size: 13px;
}

.box.arrow::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: -10px;
  width: 2px;
  height: 10px;
  background-color: #6c8b5e;
  transform: translateX(-50%);
}

.box.arrow::before {
  content: "";
  position: absolute;
  left: 50%;
  bottom: -15px;
  transform: translateX(-50%);
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 6px solid #6c8b5e;
}

.right-arrow::after {
  content: "";
  position: absolute;
  left: -14px;
  top: 50%;
  width: 8px;
  height: 2px;
  background-color: #6c8b5e;
  transform: translateY(-50%);
}

.right-arrow::before {
  content: "";
  position: absolute;
  left: -22px;
  top: 50%;
  transform: translateY(-50%);
  border-top: 5px solid transparent;
  border-bottom: 5px solid transparent;
  border-right: 7px solid #6c8b5e;
}

@media (max-width: 992px) {
  .right {
    position: static;
    margin-top: 30px;
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .branch {
    width: 90%;
  }
}

@media (max-width: 768px) {
  .title {
    font-size: 20px;
  }

  .bottom {
    flex-direction: column;
    gap: 22px;
  }

  .column {
    align-items: center;
  }

  .right {
    flex-direction: column;
    align-items: center;
  }
}

@media (max-width: 480px) {
  .box {
    width: 100%;
    border-radius: 18px;
  }

  .box.arrow::after,
  .box.arrow::before,
  .right-arrow::after,
  .right-arrow::before {
    display: none;
  }
}
</style>
