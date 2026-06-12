<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    fullname: user.fullname,
    organization: user.organization,
    email: user.email,
    phone: user.phone,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">ข้อมูลส่วนตัว</h2>
            <p class="mt-1 text-sm text-gray-600">
                อัปเดตข้อมูลบัญชี อีเมล และข้อมูลการติดต่อของคุณ
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="ชื่อผู้ใช้ (Username)" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="fullname" value="ชื่อ - นามสกุล" />
                <TextInput
                    id="fullname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.fullname"
                    required
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.fullname" />
            </div>

            <div>
                <InputLabel for="organization" value="หน่วยงาน / โรงเรียน / มหาวิทยาลัย" />
                <TextInput
                    id="organization"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.organization"
                    required
                />
                <InputError class="mt-2" :message="form.errors.organization" />
            </div>

            <div>
                <InputLabel for="email" value="อีเมล" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="phone" value="เบอร์โทรศัพท์" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    อีเมลของคุณยังไม่ได้รับการยืนยัน
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        คลิกที่นี่เพื่อส่งอีเมลยืนยันอีกครั้ง
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    ส่งลิงก์ยืนยันใหม่ไปยังอีเมลของคุณแล้ว
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">บันทึก</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">บันทึกข้อมูลเรียบร้อยแล้ว</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
