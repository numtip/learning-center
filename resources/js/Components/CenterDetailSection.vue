<script setup>
defineProps({
  detail: Object
})
</script>

<template>
  <section class="content-modern">
    <h2 class="section-title">รายละเอียด</h2>

    <div class="content-row">
      <div class="text-box">
        <h2>ชื่อศูนย์การเรียนรู้</h2>
        <p>{{ detail.center_name }}<br>{{ detail.affiliation }}</p>
      </div>
      <div class="image-card">
        <img v-if="detail.image1" :src="detail.image1" class="activity-image" />
      </div>
    </div>

    <div class="content-row reverse">
      <div class="image-card">
        <img v-if="detail.image2" :src="detail.image2" class="activity-image" />
      </div>
      <div class="text-box">
        <h2>ความเป็นมา</h2>
        <p>{{ detail.history }}</p>
      </div>
    </div>

    <div class="content-row">
      <div class="text-box">
        <h2>วัตถุประสงค์</h2>
        <p>{{ detail.objective }}</p>
      </div>
      <div class="image-card">
        <img v-if="detail.image3" :src="detail.image3" class="activity-image" />
      </div>
    </div>
  </section>
</template>
