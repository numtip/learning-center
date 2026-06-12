<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const visions = ref(page.props.visions || [])

const form = ref({
  id: null,
  title: '',
  description: '',
  image: null
})

const preview = ref(null)
const showForm = ref(false)

const openAdd = () => {
  form.value = { id: null, title: '', description: '', image: null }
  preview.value = null
  showForm.value = true
}

const openEdit = (v) => {
  form.value = { ...v, image: null }
  preview.value = v.image
  showForm.value = true
}

const onFileChange = (e) => {
  const file = e.target.files[0]
  form.value.image = file
  const reader = new FileReader()
  reader.onload = ev => preview.value = ev.target.result
  reader.readAsDataURL(file)
}

const save = () => {
  const data = new FormData()
  Object.keys(form.value).forEach(k => data.append(k, form.value[k]))

  if (form.value.id) {
    router.post(`/superadmin/vision/${form.value.id}`, data, {
      onSuccess: (page) => {
        visions.value = page.props.visions
        showForm.value = false
      }
    })
  } else {
    router.post('/superadmin/vision', data, {
      onSuccess: (page) => {
        visions.value = page.props.visions
        showForm.value = false
      }
    })
  }
}

const remove = (id) => {
  if (!confirm('ลบข้อมูลนี้หรือไม่?')) return
  router.delete(`/superadmin/vision/${id}`, {
    onSuccess: (page) => visions.value = page.props.visions
  })
}
</script>

<template>
  <AdminLayout>
    <h2 class="title">📘 จัดการ ปรัชญา / วิสัยทัศน์ / พันธกิจ</h2>
    <button class="btn-add" @click="openAdd">➕ เพิ่มรายการ</button>

    <div class="grid">
      <div v-for="v in visions" :key="v.id" class="card glass">
        <img v-if="v.image" :src="v.image" />
        <h3>{{ v.title }}</h3>
        <p style="white-space: pre-line">{{ v.description }}</p>

        <div class="actions">
          <button class="edit" @click="openEdit(v)">✏️ แก้ไข</button>
          <button class="delete" @click="remove(v.id)">🗑 ลบ</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showForm" class="modal">
      <div class="box glass">
        <h3>{{ form.id ? 'แก้ไข' : 'เพิ่ม' }} ข้อมูล</h3>

        <input v-model="form.title" placeholder="หัวข้อ" />
        <textarea v-model="form.description" rows="6" placeholder="รายละเอียด"></textarea>

        <input type="file" @change="onFileChange" />
        <img v-if="preview" :src="preview" class="preview"/>

        <div class="btns">
          <button class="save" @click="save">💾 บันทึก</button>
          <button class="cancel" @click="showForm=false">ยกเลิก</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.title {
  font-size: 28px;
  font-weight: 900;
  color: #064e3b;
  margin-bottom: 12px;
}

.btn-add {
  background: linear-gradient(135deg,#22c55e,#4ade80);
  border: none;
  padding: 10px 18px;
  border-radius: 12px;
  font-weight: 800;
  margin-bottom: 20px;
  cursor: pointer;
  color: #064e3b;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill,minmax(260px,1fr));
  gap: 24px;
}

.glass {
  background: rgba(236,253,245,0.7);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(34,197,94,0.3);
  box-shadow: 0 10px 25px rgba(16,185,129,0.25);
  border-radius: 18px;
}

.card {
  padding: 18px;
  text-align: center;
}

.card img {
  width: 100%;
  height: 180px;
  object-fit: contain;
  margin-bottom: 10px;
}

.actions button {
  border: none;
  border-radius: 8px;
  padding: 6px 12px;
  margin: 0 4px;
  font-weight: 700;
  cursor: pointer;
}

.edit { background: #dbeafe; color: #1d4ed8; }
.delete { background: #fee2e2; color: #b91c1c; }

.modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  display: flex;
  align-items: center;
  justify-content: center;
}

.box {
  width: 420px;
  padding: 24px;
}

.box input, .box textarea {
  width: 100%;
  margin-bottom: 10px;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #86efac;
}

.preview {
  width: 120px;
  margin-top: 10px;
  border-radius: 10px;
}

.btns {
  text-align: right;
}

.save {
  background: #22c55e;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  font-weight: 800;
}

.cancel {
  background: #fecaca;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  margin-left: 6px;
}
</style>
