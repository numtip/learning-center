<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = ref({
  name: '', price: '', unit: '', stock: 0, category: '', description: '', image: null, size: '', weight: ''
})

const preview = ref(null)
const isProcessing = ref(false)
const isDragging = ref(false)

const compressImage = (file, maxWidth = 800, quality = 0.8) => {
  return new Promise((resolve) => {
    if (!file || !file.type.startsWith('image/')) return resolve(file)
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = (event) => {
      const img = new Image()
      img.src = event.target.result
      img.onload = () => {
        let { width, height } = img
        if (width > maxWidth) { height = Math.round((height * maxWidth) / width); width = maxWidth; }
        const canvas = document.createElement('canvas')
        canvas.width = width; canvas.height = height;
        const ctx = canvas.getContext('2d')
        ctx.drawImage(img, 0, 0, width, height)
        canvas.toBlob((blob) => {
          resolve(new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), { type: 'image/jpeg' }))
        }, 'image/jpeg', quality)
      }
    }
  })
}

const handleFile = async (file) => {
  if (!file) return
  isProcessing.value = true
  const reader = new FileReader()
  reader.onload = ev => preview.value = ev.target.result
  reader.readAsDataURL(file)
  try {
    const compressedFile = await compressImage(file)
    form.value.image = compressedFile
  } catch (error) { form.value.image = file }
  finally { isProcessing.value = false }
}

const onDrop = (e) => { isDragging.value = false; handleFile(e.dataTransfer.files[0]) }
const onFileChange = (e) => handleFile(e.target.files[0])

const submit = () => {
  if (!form.value.name || !form.value.price || !form.value.weight) {
    alert('⚠️ กรุณากรอก ชื่อสินค้า, ราคา และน้ำหนัก ให้ครบถ้วน')
    return
  }
  const data = new FormData()
  Object.keys(form.value).forEach(k => { if (form.value[k] !== null && form.value[k] !== '') data.append(k, form.value[k]) })

  router.post('/admin/products', data, {
    forceFormData: true,
    onSuccess: () => {
      alert('✅ เพิ่มสินค้าสำเร็จเรียบร้อยแล้ว')
      form.value = { name: '', price: '', unit: '', stock: 0, category: '', description: '', image: null, size: '', weight: '' }
      preview.value = null
    }
  })
}
</script>

<template>
  <AdminLayout>
    <div class="high-contrast-layout">

      <div class="sticky-header slide-down">
        <div class="header-content">
          <div class="back-btn" @click="router.get('/admin/products')">❮ กลับ</div>
          <h2>✨ สร้างสินค้าใหม่</h2>
        </div>
        <button class="btn-glow" @click="submit" :disabled="isProcessing">
          <span v-if="!isProcessing">🚀 เผยแพร่สินค้า</span>
          <span v-else>⏳ กำลังเตรียมข้อมูล...</span>
        </button>
      </div>

      <div class="form-wrapper fade-in">
        <div class="main-column">
          <div class="opaque-card">
            <div class="card-header">
              <h3>📝 ข้อมูลพื้นฐาน</h3>
            </div>
            <div class="card-body">
              <div class="input-float">
                <input v-model="form.name" type="text" id="p_name" placeholder=" " required />
                <label for="p_name">ชื่อสินค้า <span class="req">*</span></label>
              </div>

              <div class="input-float select-arrow">
                <select v-model="form.category" id="p_cat" required>
                  <option value="" disabled hidden></option>
                  <option value="สินค้าของศูนย์วัฒนธรรมเกษตรล้านนา">ศูนย์วัฒนธรรมเกษตรล้านนา</option>
                  <option value="สินค้าของศูนย์เกษตรทฤษฎีใหม่">ศูนย์เกษตรทฤษฎีใหม่</option>
                  <option value="สินค้าของศูนย์ฟาร์มสาธิต">ศูนย์ฟาร์มสาธิต</option>
                  <option value="สินค้าของศูนย์บ้านโป่ง">ศูนย์บ้านโป่ง</option>
                  <option value="สินค้าของศูนย์กัญชง">ศูนย์การเรียนรู้กัญชง</option>
                </select>
                <label for="p_cat">หมวดหมู่ศูนย์ 📍</label>
              </div>

              <div class="input-float textarea-float">
                <textarea v-model="form.description" id="p_desc" rows="4" placeholder=" "></textarea>
                <label for="p_desc">รายละเอียดสินค้า สรรพคุณ จุดเด่น...</label>
              </div>
            </div>
          </div>

          <div class="opaque-card">
            <div class="card-header">
              <h3>💰 การขายและคลัง</h3>
            </div>
            <div class="card-body grid-2">
              <div class="input-float">
                <input v-model="form.price" type="number" id="p_price" placeholder=" " required />
                <label for="p_price">ราคาขาย (฿) <span class="req">*</span></label>
              </div>
              <div class="input-float">
                <input v-model="form.stock" type="number" id="p_stock" placeholder=" " />
                <label for="p_stock">จำนวนสต๊อก 📦</label>
              </div>
              <div class="input-float">
                <input v-model="form.weight" type="number" step="0.01" id="p_weight" placeholder=" " required />
                <label for="p_weight">น้ำหนัก (กิโลกรัม) <span class="req">*</span></label>
              </div>
              <div class="input-float">
                <input v-model="form.unit" type="text" id="p_unit" placeholder=" " />
                <label for="p_unit">หน่วยนับ (ชิ้น, กล่อง)</label>
              </div>
            </div>
          </div>
        </div>

        <div class="side-column">
          <div class="opaque-card sticky-card">
            <div class="card-header">
              <h3>📸 รูปภาพหน้าปก</h3>
            </div>
            <div class="card-body">
              <div
                class="dropzone"
                :class="{'drag-over': isDragging, 'has-img': preview}"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="onDrop"
              >
                <input type="file" id="img_upload" accept="image/*" @change="onFileChange" hidden :disabled="isProcessing">

                <div v-if="preview" class="preview-box">
                  <img :src="preview" alt="Preview">
                  <label for="img_upload" class="overlay-btn">เปลี่ยนรูปภาพ</label>
                </div>

                <label v-else for="img_upload" class="upload-prompt">
                  <div class="cloud-icon" :class="{'bounce': isDragging}">☁️</div>
                  <h4>ลากรูปภาพมาวางที่นี่</h4>
                  <p>หรือคลิกเพื่อเลือกไฟล์ (ระบบจะย่อขนาดให้อัตโนมัติ)</p>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

/* Animations */
@keyframes slideDown { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
@keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

/* Base & High Contrast Layout */
.high-contrast-layout { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: 'Prompt', sans-serif; color: #1e293b; background-color: #f1f5f9; border-radius: 20px; }
.slide-down { animation: slideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; }

/* Sticky Header - Defined & Opaque */
.sticky-header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 18px 35px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.08); margin-bottom: 35px; border: 1px solid #e2e8f0; position: sticky; top: 20px; z-index: 100; }
.header-content { display: flex; align-items: center; gap: 20px; }
.back-btn { background: #f1f5f9; padding: 10px 18px; border-radius: 12px; font-size: 15px; font-weight: 700; color: #475569; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.04); }
.back-btn:hover { background: #e2e8f0; color: #0f172a; transform: translateX(-4px); }
.sticky-header h2 { margin: 0; font-size: 26px; font-weight: 800; background: linear-gradient(135deg, #047857, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* Vibrant Glow Button */
.btn-glow { background: linear-gradient(45deg, #22c55e, #16a34a); border: none; color: white; padding: 14px 32px; border-radius: 16px; font-size: 17px; font-weight: 700; cursor: pointer; transition: all 0.3s; box-shadow: 0 12px 24px rgba(22, 163, 74, 0.45); border: 1px solid rgba(255,255,255,0.3); font-family: inherit; }
.btn-glow:hover:not(:disabled) { transform: translateY(-4px); box-shadow: 0 18px 30px rgba(22, 163, 74, 0.55); }
.btn-glow:disabled { opacity: 0.7; cursor: not-allowed; background: #94a3b8; box-shadow: none; }

/* Form Layout & Opaque Cards */
.form-wrapper { display: flex; gap: 30px; align-items: flex-start; }
.main-column { flex: 2; display: flex; flex-direction: column; gap: 30px; }
.side-column { flex: 1; position: sticky; top: 120px; }
.opaque-card { background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 15px 40px rgba(0,0,0,0.07); overflow: hidden; transition: 0.3s; }
.opaque-card:hover { box-shadow: 0 20px 50px rgba(0,0,0,0.1); transform: translateY(-3px); }
.card-header { padding: 22px 28px; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
.card-header h3 { margin: 0; font-size: 19px; color: #111827; font-weight: 800; }
.card-body { padding: 28px; display: flex; flex-direction: column; gap: 22px; }
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }

/* Defined Floating Inputs */
.input-float { position: relative; width: 100%; }
.input-float input, .input-float select, .input-float textarea { width: 100%; background: #f8fafc; border: 2px solid #e2e8f0; padding: 22px 18px 10px; border-radius: 16px; font-family: inherit; font-size: 16px; color: #1f2937; transition: all 0.3s; box-sizing: border-box; outline: none; box-shadow: inset 0 3px 6px rgba(0,0,0,0.03); }
.input-float textarea { resize: vertical; min-height: 140px; }
.input-float label { position: absolute; left: 18px; top: 16px; color: #6b7280; font-size: 16px; pointer-events: none; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-weight: 500; }
.input-float input:focus, .input-float select:focus, .input-float textarea:focus { background: #ffffff; border-color: #22c55e; box-shadow: 0 10px 20px rgba(34,197,94,0.08); }
.input-float input:focus ~ label, .input-float input:not(:placeholder-shown) ~ label,
.input-float select:focus ~ label, .input-float select:valid ~ label,
.input-float textarea:focus ~ label, .input-float textarea:not(:placeholder-shown) ~ label { top: 7px; font-size: 12px; color: #15803d; font-weight: 700; }
.req { color: #dc2626; }

/* Interactive & Solid Dropzone */
.dropzone { border: 3px dashed #cbd5e1; border-radius: 22px; min-height: 280px; display: flex; align-items: center; justify-content: center; text-align: center; background: #f8fafc; transition: all 0.3s; position: relative; overflow: hidden; }
.dropzone:hover, .drag-over { border-color: #2563eb; background: #eff6ff; }
.has-img { border-style: solid; border-color: transparent; background: transparent; padding: 0; }
.upload-prompt { cursor: pointer; width: 100%; height: 100%; padding: 50px 25px; display: flex; flex-direction: column; align-items: center; }
.cloud-icon { font-size: 56px; margin-bottom: 12px; transition: 0.3s; filter: grayscale(1); opacity: 0.6; }
.bounce { animation: bounce 1s infinite; filter: grayscale(0); opacity: 1; }
.upload-prompt h4 { margin: 0 0 6px; color: #2563eb; font-size: 17px; font-weight: 700; }
.upload-prompt p { margin: 0; color: #64748b; font-size: 14px; }
.preview-box { width: 100%; height: 100%; position: relative; border-radius: 22px; overflow: hidden; box-shadow: 0 12px 24px rgba(0,0,0,0.12); }
.preview-box img { width: 100%; height: 100%; object-fit: cover; display: block; }
.overlay-btn { position: absolute; inset: 0; background: rgba(0,0,0,0.6); color: white; display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 700; opacity: 0; transition: 0.3s; cursor: pointer; backdrop-filter: blur(6px); }
.preview-box:hover .overlay-btn { opacity: 1; }

@media (max-width: 950px) { .form-wrapper { flex-direction: column; } .side-column { width: 100%; position: static; } .grid-2 { grid-template-columns: 1fr; } }
@media (max-width: 650px) { .sticky-header { flex-direction: column; gap: 18px; align-items: stretch; padding: 20px; } .sticky-header h2 { font-size: 22px; } .btn-glow { width: 100%; font-size: 16px; } .opaque-card { border-radius: 20px; } }
</style>
