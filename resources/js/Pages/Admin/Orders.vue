<template>
  <AdminLayout>
    <div class="admin-container">
      <div class="page-header">
        <h2>📦 จัดการคำสั่งซื้อ</h2>
        <p class="subtitle">ระบบจัดการออเดอร์และตรวจสอบสถานะการจัดส่ง</p>
      </div>

      <div class="filter-section">
        <select v-model="statusFilter" @change="fetchOrders" class="status-filter">
          <option value="">ทั้งหมด</option>
          <option value="verifying">รอตรวจสอบสลิป</option>
          <option value="paid">ชำระเงินแล้ว</option>
          <option value="shipped">จัดส่งแล้ว</option>
          <option value="cancelled">ยกเลิก</option>
        </select>
      </div>

      <div v-for="order in orders.data" :key="order.id" class="order-card">
        <div class="order-header">
          <div class="order-id">
            <span class="label">รหัสออเดอร์</span>
            <strong>#{{ order.order_no }}</strong>
          </div>
          <div class="order-date-badge">
            🗓️ {{ formatDate(order.created_at) }}
          </div>
        </div>

        <div class="order-body">
          <div class="customer-info-box">
            <div class="info-row">
              <span class="icon">👤</span>
              <span class="text"><b>ชื่อลูกค้า:</b> {{ order.customer_name || '-' }}</span>
            </div>
            <div class="info-row">
              <span class="icon">📞</span>
              <span class="text"><b>เบอร์โทรศัพท์:</b> {{ order.customer_phone || '-' }}</span>
            </div>
            <div class="info-row">
              <span class="icon">📍</span>
              <span class="text"><b>ที่อยู่จัดส่ง:</b> {{ order.shipping_address || 'รับที่มหาวิทยาลัยแม่โจ้' }}</span>
            </div>
          </div>

          <h4 class="section-title">🛒 รายการสินค้า</h4>

          <div class="product-list">
            <div v-for="(items, center) in groupByCenter(order.items)" :key="center" class="center-group">
              <div class="center-header">
                📍 สินค้าของ{{ center }}
              </div>
              <div class="center-items-box">
                <div v-for="item in items" :key="item.id" class="product-line-item">
                  <div class="prod-left">
                    <div class="prod-name">{{ item.product_name }}</div>
                    <div class="prod-meta">
                      จำนวน: {{ item.qty }} | ราคา: {{ Number(item.price).toLocaleString() }} ฿
                    </div>
                  </div>
                  <div class="prod-right">
                    {{ Number(item.price * item.qty).toLocaleString() }} ฿
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="full-price-summary">
            <div class="summary-row">
              <span>💵 ค่าสินค้า:</span>
              <span>{{ Number(order.total - order.shipping_fee).toLocaleString() }} ฿</span>
            </div>
            <div class="summary-row">
              <span>🚛 ค่าจัดส่ง:</span>
              <span>{{ Number(order.shipping_fee).toLocaleString() }} ฿</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row grand-total">
              <span>💰 รวมทั้งหมด:</span>
              <span>{{ Number(order.total).toLocaleString() }} ฿</span>
            </div>
          </div>

          <div class="details-row">
            <div class="slip-box">
              <div class="slip-title">📸 หลักฐานการชำระเงิน</div>
              <img
                v-if="order.slip_image"
                :src="order.slip_image.startsWith('http') ? order.slip_image : `/storage/${order.slip_image}`"
                class="slip-img"
                alt="Slip Image"
              />
              <div v-else class="no-slip">ไม่มีรูปสลิป</div>
            </div>

            <div class="action-box">
              <div class="status-row">
                <span class="status-label">📌 สถานะ:</span>
                <span :class="['status-badge', order.status]">
                  {{ statusText(order.status) }}
                </span>
                <button v-if="order.status === 'verifying'" @click="approvePayment(order.id)" class="approve-btn">
                  ✅ อนุมัติการชำระเงิน
                </button>
              </div>

              <div class="tracking-section">
                <div v-if="order.tracking_no" class="tracking-show">
                  <span class="tracking-icon">🚚</span>
                  <b>เลขพัสดุ:</b> <span class="tracking-number">{{ order.tracking_no }}</span>
                </div>

                <div v-if="order.status === 'paid'" class="tracking-input-group">
                  <input
                    v-model="order.tracking_input"
                    placeholder="กรอกเลขพัสดุที่นี่..."
                    class="tracking-input"
                  />
                  <button @click="saveTracking(order)" class="track-save-btn">
                    💾 บันทึกเลขพัสดุ
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modern-pagination" v-if="orders.last_page > 1">
        <button @click="goToPage(orders.current_page - 1)" :disabled="orders.current_page === 1" class="page-nav">
          «
        </button>
        <button
          v-for="page in orders.last_page"
          :key="page"
          @click="goToPage(page)"
          :class="['page-num', { active: orders.current_page === page }]"
        >
          {{ page }}
        </button>
        <button @click="goToPage(orders.current_page + 1)" :disabled="orders.current_page === orders.last_page" class="page-nav">
          Next »
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  orders: Object
})

const statusFilter = ref(new URLSearchParams(window.location.search).get('status') || '')

const fetchOrders = () => {
  router.get('/admin/orders', { status: statusFilter.value }, { preserveState: true })
}

const goToPage = (page) => {
  router.get('/admin/orders', { page, status: statusFilter.value }, { preserveState: true })
}

const groupByCenter = (items) => {
  if(!items) return {}
  const groups = {}
  items.forEach(item => {
    const key = item.category || 'ไม่ระบุศูนย์'
    if (!groups[key]) {
      groups[key] = []
    }
    groups[key].push(item)
  })
  return groups
}

const approvePayment = async (id) => {
  try {
    await axios.post(`/admin/orders/${id}/approve`)
    router.reload({ only: ['orders'] })
  } catch (error) {
    alert('เกิดข้อผิดพลาด')
  }
}

const saveTracking = async (order) => {
  if (!order.tracking_input || order.tracking_input.length <= 5) {
    return alert('กรุณากรอกเลขพัสดุให้ถูกต้อง (มากกว่า 5 ตัวอักษร)')
  }
  if (order.status !== 'paid') {
    return alert('ต้องชำระเงินก่อนจึงจะส่งสินค้าได้')
  }
  try {
    await axios.post(`/admin/orders/${order.id}/tracking`, {
      tracking_no: order.tracking_input
    })
    router.reload({ only: ['orders'] })
  } catch (error) {
    alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล')
  }
}

const statusText = (status) => {
  if (status === 'pending') return 'สร้างคำสั่งซื้อ'
  if (status === 'waiting_payment') return 'รอโอนเงิน'
  if (status === 'verifying') return 'กำลังตรวจสอบ'
  if (status === 'paid') return 'ชำระเงินแล้ว'
  if (status === 'shipped') return 'จัดส่งแล้ว'
  if (status === 'cancelled') return 'ยกเลิก'
  return status
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('th-TH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }) + ' น.'
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 32px 20px;
  font-family: 'Prompt', sans-serif;
  color: #1e293b;
}

.page-header {
  margin-bottom: 32px;
  text-align: center;
}
.page-header h2 {
  font-size: 32px;
  font-weight: 700;
  color: #065f46;
  margin: 0 0 8px 0;
}
.subtitle {
  font-size: 16px;
  color: #64748b;
  margin: 0;
}

.filter-section {
  margin-bottom: 24px;
  display: flex;
  justify-content: flex-end;
}
.status-filter {
  padding: 10px 16px;
  border-radius: 8px;
  border: 1px solid #cbd5e1;
  font-family: inherit;
  font-size: 15px;
  outline: none;
}

.order-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 28px;
  margin-bottom: 24px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 16px;
  border-bottom: 2px dashed #f1f5f9;
  margin-bottom: 20px;
}
.order-id {
  display: flex;
  align-items: center;
  gap: 10px;
}
.order-id .label {
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
}
.order-id strong {
  font-size: 20px;
  color: #059669;
  font-weight: 700;
  background: #ecfdf5;
  padding: 4px 12px;
  border-radius: 8px;
}
.order-date-badge {
  background: #f8fafc;
  color: #475569;
  font-size: 14px;
  padding: 6px 14px;
  border-radius: 999px;
  font-weight: 500;
  border: 1px solid #e2e8f0;
}

.customer-info-box {
  background: #f8fafc;
  padding: 16px 20px;
  border-radius: 12px;
  margin-bottom: 24px;
  border: 1px solid #f1f5f9;
}
.info-row {
  display: flex;
  align-items: flex-start;
  font-size: 15px;
  color: #334155;
  margin-bottom: 10px;
}
.info-row:last-child {
  margin-bottom: 0;
}
.info-row .icon {
  margin-right: 12px;
}
.info-row b {
  color: #1e293b;
  font-weight: 600;
  margin-right: 4px;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 16px;
}
.center-group {
  margin-bottom: 20px;
}
.center-header {
  background: #dcfce7;
  color: #166534;
  display: inline-flex;
  padding: 8px 16px;
  border-radius: 8px 8px 0 0;
  font-size: 14px;
  font-weight: 600;
  border: 1px solid #bbf7d0;
  border-bottom: none;
}
.center-items-box {
  border: 1px solid #e2e8f0;
  border-radius: 0 12px 12px 12px;
  background: #ffffff;
  overflow: hidden;
}
.product-line-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
}
.product-line-item:last-child {
  border-bottom: none;
}
.prod-name {
  font-weight: 600;
  font-size: 16px;
  color: #1e293b;
  margin-bottom: 4px;
}
.prod-meta {
  font-size: 14px;
  color: #64748b;
}
.prod-right {
  font-weight: 700;
  font-size: 16px;
  color: #0f172a;
}

.full-price-summary {
  background: #10b981;
  background: linear-gradient(145deg, #10b981, #059669);
  padding: 20px 24px;
  border-radius: 12px;
  color: white;
  margin-top: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 15px -3px rgba(16, 185, 129, 0.3);
}
.full-price-summary .summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 16px;
}
.summary-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.3);
  margin: 12px 0;
}
.full-price-summary .grand-total {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 0;
}

.details-row {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}
@media (max-width: 768px) {
  .details-row {
    flex-direction: column;
    gap: 20px;
  }
}

.slip-box {
  flex: 0 0 220px;
  background: #f8fafc;
  padding: 16px;
  border-radius: 12px;
  border: 1px dashed #cbd5e1;
  text-align: center;
}
.slip-title {
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 12px;
}
.slip-img {
  width: 100%;
  max-height: 250px;
  object-fit: contain;
  border-radius: 8px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
.no-slip {
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 14px;
}

.action-box {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
}
.status-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #f1f5f9;
}
.status-label {
  font-weight: 600;
  color: #475569;
}
.status-badge {
  padding: 6px 16px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 600;
}
.status-badge.pending { background: #e2e8f0; color: #475569; }
.status-badge.waiting_payment { background: #fef08a; color: #854d0e; }
.status-badge.verifying { background: #fef08a; color: #854d0e; }
.status-badge.paid { background: #bae6fd; color: #0369a1; }
.status-badge.shipped { background: #bbf7d0; color: #166534; }
.status-badge.cancelled { background: #fecaca; color: #991b1b; }

.approve-btn {
  background: #2563eb;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.approve-btn:hover {
  background: #1d4ed8;
}

.tracking-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.tracking-show {
  background: #f8fafc;
  padding: 14px 20px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-size: 16px;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 8px;
}
.tracking-number {
  font-weight: 700;
  color: #059669;
}
.tracking-input-group {
  display: flex;
  gap: 12px;
  width: 100%;
}
.tracking-input {
  flex: 1;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
  font-size: 15px;
  font-family: inherit;
}
.tracking-input:focus {
  outline: none;
  border-color: #10b981;
}
.track-save-btn {
  background: #0f172a;
  color: white;
  border: none;
  padding: 0 20px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.track-save-btn:hover {
  background: #1e293b;
}

.modern-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 6px;
  margin-top: 40px;
  margin-bottom: 20px;
}
.modern-pagination button {
  background: #89ff85;
  color: #ffffff;
  border: none;
  border-radius: 6px;
  padding: 10px 16px;
  font-size: 15px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.2s;
}
.modern-pagination button:hover:not(:disabled) {
  background: #ffb8b8;
}
.modern-pagination button.active {
  background: #f7d955;
}
.modern-pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.modern-pagination .page-nav {
  padding: 10px 18px;
}
</style>
