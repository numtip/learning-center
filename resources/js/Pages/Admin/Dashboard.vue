<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Admin Dashboard" />

  <AdminLayout>
    <h1 class="text-2xl font-bold mb-4">🛠 แดชบอร์ดผู้ดูแลระบบ</h1>

    <ul class="space-y-2">
      <li>👥 จัดการผู้ใช้งาน</li>
      <li>📰 ข่าว / รูป / วิดีโอ</li>
      <li>📊 สถิติการใช้งาน</li>
      <li>📅 การจอง</li>
      <li>🚚 ตรวจสอบคำสั่งซื้อ</li>
    </ul>
  </AdminLayout>
</template>
