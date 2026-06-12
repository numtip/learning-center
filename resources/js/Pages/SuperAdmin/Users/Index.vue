<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  users: Array,
  roles: Array
})

// === จัดการ Modal และ ฟอร์ม ===
const isModalOpen = ref(false)
const isEditing = ref(false)
const editingUserId = ref(null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: ''
})

// เปิดหน้าต่าง เพิ่มผู้ใช้
const openAddModal = () => {
  isEditing.value = false
  form.reset()
  form.clearErrors()
  if (props.roles.length > 0) form.role_id = props.roles[0].id // ตั้งค่าเริ่มต้น
  isModalOpen.value = true
}

// เปิดหน้าต่าง แก้ไขผู้ใช้
const openEditModal = (user) => {
  isEditing.value = true
  editingUserId.value = user.id
  form.reset()
  form.clearErrors()
  form.name = user.name
  form.email = user.email
  form.password = '' // เว้นว่างไว้ หากไม่ต้องการเปลี่ยนรหัสผ่าน
  form.password_confirmation = ''
  form.role_id = user.roles && user.roles.length > 0 ? user.roles[0].id : ''
  isModalOpen.value = true
}

// ปิดหน้าต่าง Modal
const closeModal = () => {
  isModalOpen.value = false
  form.reset()
}

// บันทึกข้อมูล (เพิ่ม หรือ แก้ไข)
const submitForm = () => {
  if (isEditing.value) {
    // ส่ง Request ไปอัปเดตข้อมูล
    form.put(`/superadmin/users/${editingUserId.value}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    // ส่ง Request ไปสร้างผู้ใช้ใหม่
    form.post('/superadmin/users', {
      onSuccess: () => closeModal(),
    })
  }
}

// === ฟังก์ชันเดิม ===
const changeRole = (userId, roleId) => {
  router.post(`/superadmin/users/${userId}/role`, {
    role_id: roleId
  })
}

const deleteUser = (id) => {
  if (confirm('ยืนยันการลบผู้ใช้งาน?')) {
    router.delete(`/superadmin/users/${id}`)
  }
}
</script>

<template>
  <AdminLayout>
    <div class="header-actions">
      <h2 class="glass-title">👤 จัดการผู้ใช้งาน</h2>
      <button class="glass-btn primary add-btn" @click="openAddModal">
        ➕ เพิ่มผู้ใช้งาน
      </button>
    </div>

    <div class="glass-card">
      <table class="glass-table">
        <thead>
          <tr>
            <th>#</th>
            <th>ชื่อ</th>
            <th>Email</th>
            <th>Role</th>
            <th class="text-center">จัดการ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(u, i) in users" :key="u.id">
            <td>{{ i + 1 }}</td>
            <td class="name-cell">{{ u.name }}</td>
            <td class="email-cell">{{ u.email }}</td>
            <td>
              <select
                class="glass-select"
                :value="u.roles[0]?.id"
                @change="changeRole(u.id, $event.target.value)"
              >
                <option v-for="r in roles" :key="r.id" :value="r.id">
                  {{ r.name }}
                </option>
              </select>
            </td>
            <td class="text-center action-buttons">
              <button class="glass-btn warning" @click="openEditModal(u)">
                ✏️ แก้ไข
              </button>
              <button class="glass-btn danger" @click="deleteUser(u.id)">
                🗑 ลบ
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content glass-card">
        <h3 class="modal-title">{{ isEditing ? '✏️ แก้ไขผู้ใช้งาน' : '➕ เพิ่มผู้ใช้งาน' }}</h3>

        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label>ชื่อ-นามสกุล</label>
            <input v-model="form.name" type="text" class="glass-input" required />
            <span v-if="form.errors.name" class="error-text">{{ form.errors.name }}</span>
          </div>

          <div class="form-group">
            <label>อีเมล</label>
            <input v-model="form.email" type="email" class="glass-input" required />
            <span v-if="form.errors.email" class="error-text">{{ form.errors.email }}</span>
          </div>

          <div class="form-group">
            <label>รหัสผ่าน {{ isEditing ? '(เว้นว่างไว้หากไม่เปลี่ยน)' : '' }}</label>
            <input v-model="form.password" type="password" class="glass-input" :required="!isEditing" />
            <span v-if="form.errors.password" class="error-text">{{ form.errors.password }}</span>
          </div>

          <div class="form-group">
            <label>ยืนยันรหัสผ่าน</label>
            <input v-model="form.password_confirmation" type="password" class="glass-input" :required="!isEditing && form.password !== ''" />
          </div>

          <div class="form-group">
            <label>สิทธิ์การใช้งาน (Role)</label>
            <select v-model="form.role_id" class="glass-select" required>
              <option disabled value="">-- เลือกสิทธิ์ --</option>
              <option v-for="r in roles" :key="r.id" :value="r.id">
                {{ r.name }}
              </option>
            </select>
            <span v-if="form.errors.role_id" class="error-text">{{ form.errors.role_id }}</span>
          </div>

          <div class="modal-actions">
            <button type="button" class="glass-btn secondary" @click="closeModal">❌ ยกเลิก</button>
            <button type="submit" class="glass-btn primary" :disabled="form.processing">
              💾 บันทึก
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.glass-title {
  font-size: 26px;
  font-weight: 900;
  color: #064e3b;
  margin-bottom: 0; /* Adjusted for flexbox */
}

/* Card Glass */
.glass-card {
  background: rgba(240, 253, 244, 0.75);
  backdrop-filter: blur(14px);
  border-radius: 18px;
  padding: 22px;
  border: 1px solid rgba(120, 220, 160, 0.4);
  box-shadow: 0 12px 30px rgba(0, 120, 80, 0.2);
}

/* Table */
.glass-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 14px;
  overflow: hidden;
  color: #022c22;
}

.glass-table thead {
  background: linear-gradient(90deg, #dcfce7, #bbf7d0);
}

.glass-table th {
  padding: 14px;
  font-weight: 900;
  font-size: 14px;
  text-transform: uppercase;
  color: #064e3b;
}

.glass-table td {
  padding: 12px 14px;
  border-bottom: 1px solid rgba(6, 78, 59, 0.15);
  font-size: 15px;
  font-weight: 700;
  color: #033b2a;
}

.glass-table tbody tr:hover {
  background: rgba(187, 247, 208, 0.5);
}

/* Name highlight */
.name-cell {
  font-weight: 900;
  font-size: 16px;
  color: #022c22;
}

.email-cell {
  color: #064e3b;
  font-weight: 700;
}

/* Select & Inputs */
.glass-select, .glass-input {
  width: 100%;
  padding: 8px 12px;
  border-radius: 10px;
  border: 1px solid rgba(120, 220, 160, 0.6);
  background: rgba(255,255,255,0.9);
  font-weight: 800;
  color: #064e3b;
  box-sizing: border-box;
}

.glass-input:focus, .glass-select:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

/* Buttons */
.action-buttons {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.glass-btn {
  padding: 6px 16px;
  border-radius: 14px;
  border: none;
  font-weight: 900;
  font-size: 13px;
  cursor: pointer;
  transition: 0.25s;
}

.glass-btn.primary {
  background: linear-gradient(135deg, #a7f3d0, #10b981);
  color: #064e3b;
}

.glass-btn.danger {
  background: linear-gradient(135deg, #fecaca, #ef4444);
  color: #7f1d1d;
}

.glass-btn.warning {
  background: linear-gradient(135deg, #fef08a, #eab308);
  color: #713f12;
}

.glass-btn.secondary {
  background: linear-gradient(135deg, #e5e7eb, #9ca3af);
  color: #1f2937;
}

.glass-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.glass-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.add-btn {
  padding: 10px 20px;
  font-size: 15px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  width: 100%;
  max-width: 450px;
  background: rgba(240, 253, 244, 0.95);
  padding: 24px;
}

.modal-title {
  color: #064e3b;
  font-size: 20px;
  font-weight: 900;
  margin-top: 0;
  margin-bottom: 20px;
  text-align: center;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-weight: 800;
  color: #064e3b;
  margin-bottom: 6px;
  font-size: 14px;
}

.error-text {
  color: #ef4444;
  font-size: 12px;
  font-weight: 700;
  margin-top: 4px;
  display: block;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.modal-actions .glass-btn {
  padding: 10px 20px;
  font-size: 14px;
}
</style>
