<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
    name: '',
    fullname: '',
    organization: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

/* =========================
    🔥 VALIDATION LOGIC
========================= */

const isValidFullname = computed(() => /^[A-Za-zก-๙.\-\s]{2,100}$/.test(form.fullname))
const isValidOrg = computed(() => form.organization && form.organization.length <= 255)
const isValidEmail = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email))
const isValidPhone = computed(() => /^[0-9]{9,10}$/.test(form.phone))
const isValidPassword = computed(() => /^(?=.*[a-z])(?=.*\d).{8,}$/.test(form.password))
const isPasswordMatch = computed(() => form.password === form.password_confirmation)

const submit = () => {
    if (!isValidFullname.value) { alert('❌ ชื่อ-นามสกุลไม่ถูกต้อง'); return; }
    if (!isValidOrg.value) { alert('❌ หน่วยงานต้องไม่เกิน 255 ตัว'); return; }
    if (!isValidEmail.value) { alert('❌ อีเมลไม่ถูกต้อง'); return; }
    if (!isValidPhone.value) { alert('❌ เบอร์โทรไม่ถูกต้อง (9-10 หลัก)'); return; }
    if (!isValidPassword.value) { alert('❌ รหัสผ่านต้องมี a-z 0-9 และอย่างน้อย 8 ตัว'); return; }
    if (!isPasswordMatch.value) { alert('❌ รหัสผ่านไม่ตรงกัน'); return; }

    const weakPasswords = ['123456','password','12345678','qwerty']
    if (weakPasswords.includes(form.password)) {
        alert('❌ ห้ามใช้รหัสผ่านง่ายเกินไป')
        return
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <PublicLayout>
        <Head title="สมัครสมาชิก" />

        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-[#f8fafc]">

            <div class="absolute top-0 -left-4 w-96 h-96 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-20 left-20 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>

            <div class="max-w-2xl w-full relative">
                <div class="bg-white/80 backdrop-blur-2xl p-8 sm:p-12 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white/40">

                    <div class="text-center mb-10">
                        <div class="mx-auto h-16 w-16 bg-gradient-to-tr from-green-600 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200 rotate-3 hover:rotate-0 transition-transform duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <h2 class="mt-6 text-3xl font-black text-gray-900 tracking-tight">สร้างบัญชีใหม่</h2>
                        <p class="mt-2 text-sm text-gray-500 font-medium">เริ่มต้นการใช้งานระบบพรีเมียมของคุณ</p>
                    </div>

                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="group">
                            <InputLabel value="ชื่อผู้ใช้ (Username)" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput v-model="form.name" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="username" required />
                            <InputError :message="form.errors.name" class="mt-1 ml-1" />
                        </div>

                        <div class="group">
                            <InputLabel value="ชื่อ - นามสกุล" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput v-model="form.fullname" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="ชื่อ นามสกุลจริง" required />
                            <InputError :message="form.errors.fullname" class="mt-1 ml-1" />
                            <InputError v-if="!isValidFullname && form.fullname && !form.errors.fullname" message="ชื่อไม่ถูกต้อง" class="mt-1 ml-1" />
                        </div>

                        <div class="md:col-span-2 group">
                            <InputLabel value="หน่วยงาน / โรงเรียน / มหาวิทยาลัย" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput v-model="form.organization" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="ระบุชื่อหน่วยงานของคุณ" required />
                            <InputError :message="form.errors.organization" class="mt-1 ml-1" />
                            <InputError v-if="!isValidOrg && form.organization && !form.errors.organization" message="ชื่อหน่วยงานยาวเกินไป" class="mt-1 ml-1" />
                        </div>

                        <div class="group">
                            <InputLabel value="อีเมล" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput type="email" v-model="form.email" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="example@email.com" required />
                            <InputError :message="form.errors.email" class="mt-1 ml-1" />
                            <InputError v-if="!isValidEmail && form.email && !form.errors.email" message="รูปแบบอีเมลไม่ถูกต้อง" class="mt-1 ml-1" />
                        </div>

                        <div class="group">
                            <InputLabel value="เบอร์โทรศัพท์" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput v-model="form.phone" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="08XXXXXXXX" required />
                            <InputError :message="form.errors.phone" class="mt-1 ml-1" />
                            <InputError v-if="!isValidPhone && form.phone && !form.errors.phone" message="ต้องเป็นตัวเลข 9-10 หลัก" class="mt-1 ml-1" />
                        </div>

                        <div class="group">
                            <InputLabel value="รหัสผ่าน" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput type="password" v-model="form.password" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="••••••••" required />
                            <InputError :message="form.errors.password" class="mt-1 ml-1" />
                            <InputError v-if="!isValidPassword && form.password && !form.errors.password" message="ต้องมี a-z และ 0-9 (8 ตัวขึ้นไป)" class="mt-1 ml-1 text-[10px]" />
                        </div>

                        <div class="group">
                            <InputLabel value="ยืนยันรหัสผ่าน" class="text-gray-700 group-focus-within:text-green-600 transition-colors ml-1" />
                            <TextInput type="password" v-model="form.password_confirmation" class="mt-1 block w-full border-gray-100 bg-white/50 focus:bg-white focus:ring-[6px] focus:ring-green-500/10 focus:border-green-500 rounded-2xl transition-all py-3" placeholder="••••••••" required />
                            <InputError v-if="!isPasswordMatch && form.password_confirmation" message="รหัสผ่านไม่ตรงกัน" class="mt-1 ml-1" />
                        </div>

                        <div class="md:col-span-2 flex flex-col items-center space-y-4 mt-4">
                            <PrimaryButton
                                class="w-full justify-center py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-2xl shadow-xl shadow-green-100 transform transition-all active:scale-[0.98] disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    กำลังลงทะเบียน...
                                </span>
                                <span v-else>ลงทะเบียนเข้าใช้งาน</span>
                            </PrimaryButton>

                            <Link :href="route('login')" class="text-sm font-semibold text-gray-500 hover:text-green-700 transition-colors">
                                มีบัญชีอยู่แล้ว? <span class="text-green-600">เข้าสู่ระบบที่นี่</span>
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
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

input:focus {
    outline: none !important;
}
</style>
