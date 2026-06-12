<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdateAvatarForm from './Partials/UpdateAvatarForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import { Head, usePage } from '@inertiajs/vue3'

defineProps({
  mustVerifyEmail: Boolean,
  status: String,
})

const user = usePage().props.auth.user
</script>

<template>
  <Head title="โปรไฟล์ของฉัน" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto py-10 px-6 space-y-6">

      <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-6">
        <img
          :src="user.profile_photo_url || '/images/default-avatar.png'"
          class="w-28 h-28 rounded-full object-cover border-4 border-green-500"
        />
        <div>
          <h1 class="text-2xl font-bold text-green-700">{{ user.fullname || user.name }}</h1>
          <p class="text-gray-500">{{ user.email }}</p>
          <p v-if="user.organization" class="text-sm text-gray-400 mt-1">{{ user.organization }}</p>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow p-6">
        <UpdateAvatarForm />
      </div>

      <div class="bg-white rounded-2xl shadow p-6">
        <UpdateProfileInformationForm
          :must-verify-email="mustVerifyEmail"
          :status="status"
        />
      </div>

      <div class="bg-white rounded-2xl shadow p-6">
        <UpdatePasswordForm />
      </div>

      <div class="bg-red-50 rounded-2xl shadow p-6">
        <DeleteUserForm />
      </div>

    </div>
  </AuthenticatedLayout>
</template>
