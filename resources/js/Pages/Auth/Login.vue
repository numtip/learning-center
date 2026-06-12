<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <PublicLayout>
    <Head title="เข้าสู่ระบบ" />

    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">

      <div class="absolute top-0 -left-4 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
      <div class="absolute top-0 -right-4 w-72 h-72 bg-emerald-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-8 left-20 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>

      <div class="max-w-md w-full space-y-8 relative">
        <div class="bg-white/80 backdrop-blur-xl p-10 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white/20">

          <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-green-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200 rotate-3 hover:rotate-0 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 tracking-tight">
              ยินดีต้อนรับกลับมา
            </h2>
            <p class="mt-2 text-sm text-gray-500">
              กรุณากรอกข้อมูลเพื่อเข้าใช้งานระบบ
            </p>
          </div>

          <div v-if="status" class="mt-4 p-3 bg-green-50 border border-green-200 text-sm text-green-600 text-center rounded-lg animate-pulse">
            {{ status }}
          </div>

          <form class="mt-8 space-y-6" @submit.prevent="submit">
            <div class="space-y-4">
              <div class="group">
                <InputLabel for="email" value="อีเมล" class="text-gray-700 group-focus-within:text-green-600 transition-colors" />
                <TextInput
                  id="email"
                  type="email"
                  class="mt-1 block w-full border-gray-200 focus:border-green-500 focus:ring-green-500 rounded-xl transition-all duration-300 shadow-sm group-hover:shadow-md"
                  v-model="form.email"
                  placeholder="name@example.com"
                  required
                  autofocus
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <div class="group">
                <InputLabel for="password" value="รหัสผ่าน" class="text-gray-700 group-focus-within:text-green-600 transition-colors" />
                <TextInput
                  id="password"
                  type="password"
                  class="mt-1 block w-full border-gray-200 focus:border-green-500 focus:ring-green-500 rounded-xl transition-all duration-300 shadow-sm group-hover:shadow-md"
                  v-model="form.password"
                  placeholder="••••••••"
                  required
                />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>
            </div>

            <div class="flex items-center justify-between">
              <label class="flex items-center cursor-pointer group">
                <Checkbox v-model:checked="form.remember" class="rounded text-green-600 focus:ring-green-500 border-gray-300" />
                <span class="ml-2 text-sm text-gray-600 group-hover:text-green-700 transition-colors">จดจำฉัน</span>
              </label>

              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-sm font-medium text-green-600 hover:text-green-500 transition-colors underline-offset-4 hover:underline"
              >
                ลืมรหัสผ่าน?
              </Link>
            </div>

            <div>
              <PrimaryButton
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform transition-all active:scale-[0.98] hover:shadow-green-200"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
              >
                <span v-if="form.processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    กำลังเข้าสู่ระบบ...
                </span>
                <span v-else>เข้าสู่ระบบทันที</span>
              </PrimaryButton>
            </div>
          </form>

          <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
              ยังไม่มีบัญชีใช่ไหม?
              <Link :href="route('register')" class="font-bold text-green-600 hover:text-green-500 transition-colors">
                สมัครสมาชิกฟรี
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<style scoped>
/* สำหรับ Animation พื้นหลังที่ขยับไปมา */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
