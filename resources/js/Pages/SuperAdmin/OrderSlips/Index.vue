<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  orders: Object
})

const preview = ref(null)
const statusFilter = ref(new URLSearchParams(window.location.search).get('status') || '')

const fileInput = ref(null)
const targetOrderId = ref(null)
const isUploading = ref(false)

const fetchOrders = () => {
  router.get('/superadmin/order-slips', { status: statusFilter.value }, { preserveState: true })
}

const goToPage = (page) => {
  router.get('/superadmin/order-slips', { page, status: statusFilter.value }, { preserveState: true })
}

const approvePayment = async (id) => {
  try {
    await axios.post(`/admin/orders/${id}/approve`)
    router.reload({ only: ['orders'] })
  } catch (error) {
    alert('เกิดข้อผิดพลาดในการอนุมัติ')
  }
}

const triggerUpload = (id) => {
  targetOrderId.value = id
  fileInput.value.click()
}

// ระบบจัดการขนาดรูปภาพอัตโนมัติก่อนอัปโหลด
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
        if (width > maxWidth) {
          height = Math.round((height * maxWidth) / width)
          width = maxWidth
        }
        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height
        const ctx = canvas.getContext('2d')
        ctx.drawImage(img, 0, 0, width, height)
        canvas.toBlob((blob) => {
          resolve(new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), { type: 'image/jpeg' }))
        }, 'image/jpeg', quality)
      }
    }
  })
}

const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  isUploading.value = true
  try {
    const compressedFile = await compressImage(file)
    const fd = new FormData()
    fd.append('slip_image', compressedFile)

    // ยิงไปที่ Route /superadmin/orders/{id}/update-slip ซึ่งตรงกับที่ตั้งไว้ใน web.php แน่นอน
    router.post(`/superadmin/orders/${targetOrderId.value}/update-slip`, fd, {
      preserveScroll: true,
      onSuccess: () => {
        event.target.value = ''
        isUploading.value = false
        alert('อัปเดตสลิปเรียบร้อยแล้ว สถานะกลับไปเป็นรอตรวจสอบ')
      },
      onError: (errors) => {
        event.target.value = ''
        isUploading.value = false
        console.error(errors)
        alert('เกิดข้อผิดพลาด: ไม่สามารถอัปโหลดไฟล์ได้')
      }
    })
  } catch (err) {
    console.error(err)
    isUploading.value = false
    alert('รูปแบบไฟล์รูปภาพไม่ถูกต้อง')
  }
}

// จัดการ URL ของรูปภาพ
const getSlipUrl = (img) => {
  if (!img || img === 'undefined' || img === 'null') return null
  return img.startsWith('http') || img.startsWith('data:image') ? img : `/storage/${img}`
}

// แสดงรูปสำรองเมื่อภาพโหลดไม่ขึ้น
const onImageError = (event) => {
  event.target.src = 'https://placehold.co/400x600/f1f5f9/94a3b8?text=Slip+Not+Found'
}

const openPreview = (img) => {
  const url = getSlipUrl(img)
  if(url) preview.value = url
}

const statusText = (status) => {
  if (status === 'pending') return 'สร้างคำสั่งซื้อ'
  if (status === 'waiting_payment') return 'รอโอนเงิน'
  if (status === 'verifying') return 'รอตรวจสอบสลิป'
  if (status === 'paid') return 'ชำระเงินแล้ว'
  if (status === 'shipped') return 'จัดส่งแล้ว'
  if (status === 'cancelled') return 'ยกเลิก'
  return status
}

const statusClass = (status) => {
  if (status === 'paid') return 'badge paid'
  if (status === 'waiting_payment') return 'badge waiting'
  if (status === 'verifying') return 'badge verifying'
  if (status === 'shipped') return 'badge shipped'
  if (status === 'cancelled') return 'badge cancelled'
  return 'badge'
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('th-TH', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  }) + ' น.'
}
</script>

<template>
  <AdminLayout>
    <div class="admin-container">

      <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" style="display: none;" />

      <div class="page-header">
        <div class="header-title">
          <h2>🧾 ตรวจสอบสลิปการโอนเงิน</h2>
          <p class="subtitle">ตรวจสอบ อนุมัติ หรืออัปโหลดสลิปแก้ไขแทนลูกค้า</p>
        </div>
        <div class="filter-section">
          <select v-model="statusFilter" @change="fetchOrders" class="status-filter">
            <option value="">ทั้งหมด</option>
            <option value="waiting_payment">รอลูกค้าอัปโหลดสลิป</option>
            <option value="verifying">รอตรวจสอบสลิป</option>
            <option value="paid">ชำระเงินแล้ว</option>
            <option value="shipped">จัดส่งแล้ว</option>
            <option value="cancelled">ยกเลิก</option>
          </select>
        </div>
      </div>

      <div class="card-table-wrapper">
        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th width="150">เลขออเดอร์</th>
                <th class="text-center" width="160">สถานะ</th>
                <th>วันที่ทำรายการ</th>
                <th class="text-center" width="250">หลักฐานการโอนเงิน</th>
                <th class="text-center" width="120">จัดการ</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="orders.data.length === 0">
                <td colspan="5" class="text-center empty-state">ไม่มีข้อมูลสลิปในระบบ</td>
              </tr>
              <tr v-for="o in orders.data" :key="o.id" class="table-row">
                <td>
                  <span class="order-no">#{{ o.order_no }}</span>
                </td>
                <td class="text-center">
                  <span :class="statusClass(o.status)">
                    {{ statusText(o.status) }}
                  </span>
                </td>
                <td>
                  <span class="date-text">{{ formatDate(o.created_at) }}</span>
                </td>
                <td class="text-center">
                  <div class="slip-wrapper">
                    <div v-if="getSlipUrl(o.slip_image)" class="slip-content">
                      <img
                        :src="getSlipUrl(o.slip_image)"
                        @error="onImageError"
                        class="thumb-img"
                        @click="openPreview(o.slip_image)"
                        alt="สลิป"
                      />
                      <button class="btn-change-slip" @click="triggerUpload(o.id)" :disabled="isUploading">
                        <span v-if="isUploading && targetOrderId === o.id">⏳ กำลังอัปโหลด...</span>
                        <span v-else>🔄 อัปโหลดแทนที่</span>
                      </button>
                    </div>

                    <div v-else class="slip-content empty">
                      <span class="no-slip">ยังไม่มีรูปสลิป</span>
                      <button class="btn-upload-slip" @click="triggerUpload(o.id)" :disabled="isUploading">
                        <span v-if="isUploading && targetOrderId === o.id">⏳ กำลังอัปโหลด...</span>
                        <span v-else>⬆️ แนบสลิปให้ลูกค้า</span>
                      </button>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <button v-if="o.status === 'verifying'" @click="approvePayment(o.id)" class="approve-btn">
                    ✔️ อนุมัติ
                  </button>
                  <span v-else class="text-gray-400">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-container" v-if="orders.last_page > 1">
          <div class="pagination-info">
            แสดงหน้า <strong>{{ orders.current_page }}</strong> จาก <strong>{{ orders.last_page }}</strong>
          </div>
          <div class="pagination-controls">
            <button @click="goToPage(orders.current_page - 1)" :disabled="orders.current_page === 1" class="page-btn">
              « ก่อนหน้า
            </button>
            <button
              v-for="page in orders.last_page"
              :key="page"
              @click="goToPage(page)"
              :class="['page-num', { active: orders.current_page === page }]"
            >
              {{ page }}
            </button>
            <button @click="goToPage(orders.current_page + 1)" :disabled="orders.current_page === orders.last_page" class="page-btn">
              ถัดไป »
            </button>
          </div>
        </div>
      </div>

      <transition name="modal-fade">
        <div v-if="preview" class="modal-backdrop" @click.self="preview = null">
          <div class="modal-box">
            <button class="close-icon" @click="preview = null">✕</button>
            <img :src="preview" @error="onImageError" class="preview-large" alt="Preview Slip" />
          </div>
        </div>
      </transition>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container { max-width: 1400px; margin: 0 auto; padding: 40px 24px; font-family: 'Prompt', sans-serif; color: #1e293b; }
.text-center { text-align: center; }

.page-header { margin-bottom: 28px; display: flex; justify-content: space-between; align-items: flex-end; }
.header-title h2 { font-size: 32px; font-weight: 700; color: #065f46; margin: 0 0 8px 0; }
.subtitle { font-size: 16px; color: #64748b; margin: 0; }

.filter-section .status-filter { padding: 10px 16px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 15px; outline: none; background: white; }

.card-table-wrapper { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
.table-responsive { width: 100%; overflow-x: auto; }
.modern-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
.modern-table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.modern-table th { padding: 20px; font-size: 15px; font-weight: 600; color: #475569; text-align: left; }
.modern-table th.text-center { text-align: center; }
.modern-table td { padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
.table-row:hover { background: #f0fdf4; }
.empty-state { padding: 60px !important; color: #94a3b8; font-size: 16px; }

.order-no { font-weight: 700; color: #0f172a; font-size: 16px; background: #f1f5f9; padding: 6px 14px; border-radius: 8px; }
.date-text { color: #475569; font-size: 15px; }

.badge { padding: 6px 16px; border-radius: 999px; font-size: 14px; font-weight: 600; display: inline-block; }
.badge.paid { background: #dbeafe; color: #1e40af; }
.badge.waiting { background: #fef9c3; color: #854d0e; }
.badge.verifying { background: #fef9c3; color: #854d0e; }
.badge.shipped { background: #dcfce7; color: #166534; }
.badge.cancelled { background: #fecaca; color: #991b1b; }

.slip-wrapper { display: flex; justify-content: center; }
.slip-content { display: flex; flex-direction: column; align-items: center; gap: 10px; }

.thumb-img {
  width: 90px;
  height: 90px;
  object-fit: cover;
  object-position: center;
  border-radius: 10px;
  border: 2px solid #e2e8f0;
  cursor: zoom-in;
  transition: all 0.2s ease;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  background: #f8fafc;
}
.thumb-img:hover { transform: scale(1.05); border-color: #10b981; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2); }
.no-slip { color: #94a3b8; font-size: 13px; padding: 6px; }

.btn-change-slip, .btn-upload-slip { background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; font-family: inherit; }
.btn-change-slip:hover:not(:disabled), .btn-upload-slip:hover:not(:disabled) { background: #e2e8f0; color: #0f172a; }
.btn-change-slip:disabled, .btn-upload-slip:disabled { opacity: 0.6; cursor: not-allowed; }

.approve-btn { background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
.approve-btn:hover { background: #059669; transform: translateY(-2px); }
.text-gray-400 { color: #cbd5e1; font-weight: bold; }

.pagination-container { display: flex; justify-content: space-between; align-items: center; padding: 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
.pagination-info { font-size: 15px; color: #64748b; font-weight: 500; }
.pagination-controls { display: flex; gap: 8px; }
.pagination-controls button { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 10px 18px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: inherit; }
.pagination-controls button:hover:not(:disabled) { border-color: #10b981; color: #10b981; }
.pagination-controls button.active { background: #10b981; color: #ffffff; border-color: #10b981; }
.pagination-controls button:disabled { opacity: 0.5; cursor: not-allowed; background: #f1f5f9; }

.modal-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; }
.modal-box { position: relative; max-width: 90vw; max-height: 90vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }

.preview-large {
  max-width: 100%;
  max-height: 85vh;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  object-fit: contain;
  background: #ffffff;
}

.close-icon { position: absolute; top: -15px; right: -15px; background: #ef4444; color: #ffffff; border: 2px solid white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: bold; font-size: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); transition: all 0.2s; z-index: 10; }
.close-icon:hover { background: #dc2626; transform: scale(1.1); }
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.2s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>
