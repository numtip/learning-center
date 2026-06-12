<script setup>
import { useForm, usePage } from '@inertiajs/vue3'

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
      <input type="file" @change="e => form.photo = e.target.files[0]" />
      <button class="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
        อัปโหลด
      </button>
    </form>
  </div>
</template>
