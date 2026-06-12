<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue' // 🔥 เพิ่มตัวนี้เข้ามาเพื่อแสดง Error

const user = usePage().props.auth.user

const form = useForm({
  photo: null,
})

const submit = () => {
  form.post(route('profile.avatar'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset('photo')
      alert('✅ อัปโหลดรูปโปรไฟล์สำเร็จ!'); // แจ้งเตือนเมื่อสำเร็จ
    },
    onError: (errors) => {
      console.error("เกิดข้อผิดพลาด:", errors); // แสดง error ใน console
    }
  })
}
</script>

<template>
  <div>
    <h3 class="text-lg font-semibold mb-3 text-gray-900">รูปโปรไฟล์</h3>

    <img
      :src="user.profile_photo_url || '/images/default-avatar.png'"
      class="w-24 h-24 rounded-full object-cover mb-3 border"
    />

    <form @submit.prevent="submit">
      <input type="file" accept="image/*" @change="e => form.photo = e.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />

      <InputError :message="form.errors.photo" class="mt-2" />

      <button
        type="submit"
        class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors disabled:opacity-50"
        :disabled="form.processing || !form.photo"
      >
        <span v-if="form.processing">กำลังอัปโหลด...</span>
        <span v-else>อัปโหลด</span>
      </button>
    </form>
  </div>
</template>
