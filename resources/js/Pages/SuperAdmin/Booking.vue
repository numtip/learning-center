<script setup>
import { Head, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const rolePrefix = page.props.auth.user.roles.some(r => r.name === 'superadmin')
  ? '/superadmin'
  : '/admin'

const bookings = ref(page.props.bookings || [])

// === 🔍 State สำหรับ Search & Filter ===
const searchQuery = ref('')
const statusFilter = ref('')

// === สถิติสำหรับ Dashboard ===
const statTotal = computed(() => bookings.value.length)
const statPending = computed(() => bookings.value.filter(b => b.status === 'รอยืนยัน').length)
const statApproved = computed(() => bookings.value.filter(b => b.status === 'อนุมัติ' || b.status === 'เสร็จสิ้น').length)

// === 🎯 Logic คัดกรองข้อมูล ===
const filteredBookings = computed(() => {
  return bookings.value.filter(b => {
    // กรองตามคำค้นหา (ชื่อ, หน่วยงาน, วันที่)
    const matchSearch = !searchQuery.value ||
      (b.name && b.name.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (b.org && b.org.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (b.visit_date && b.visit_date.includes(searchQuery.value));

    // กรองตามสถานะ
    const matchStatus = !statusFilter.value || b.status === statusFilter.value;

    return matchSearch && matchStatus;
  })
})

// === จัดการข้อมูล (CRUD) ===
const updateStatus = (id, status) => {
  router.post(`${rolePrefix}/booking/${id}/status`, { status }, {
    preserveScroll: true,
    onSuccess: () => {
      const index = bookings.value.findIndex(b => b.id === id)
      if (index !== -1) bookings.value[index].status = status
    }
  })
}

const goEdit = (id) => {
  router.visit(`${rolePrefix}/booking/${id}/edit`)
}

const deleteBooking = (id) => {
  if (!confirm('ต้องการลบรายการจองนี้ใช่หรือไม่? ข้อมูลจะหายไปจากปฏิทินด้วย')) return

  router.delete(`${rolePrefix}/booking/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      bookings.value = bookings.value.filter(b => b.id !== id)
      if (paginatedBookings.value.length === 0 && currentPage.value > 1) {
        currentPage.value--
      }
    }
  })
}

// === 📊 Export CSV ===
const exportCSV = () => {
  if (filteredBookings.value.length === 0) return alert('ไม่มีข้อมูลสำหรับ Export')

  // ใช้ \uFEFF เพื่อให้ Excel อ่านภาษาไทยได้
  let csvContent = "\uFEFF";
  csvContent += "วันที่เข้าชม,ชื่อผู้จอง,หน่วยงาน,เบอร์โทรศัพท์,จำนวนคน,สถานที่,สถานะ,ยอดชำระ\n";

  filteredBookings.value.forEach(b => {
    const date = b.visit_date || '-';
    const name = `"${b.name || '-'}"`;
    const org = `"${b.org || '-'}"`;
    const phone = `"${b.phone || '-'}"`;
    const people = b.people || 0;
    const centers = `"${formatCenters(b.centers)}"`;
    const status = b.status || '-';
    const price = b.total_price || 0;

    csvContent += `${date},${name},${org},${phone},${people},${centers},${status},${price}\n`;
  });

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute("download", `รายการจองกิจกรรม_${new Date().toISOString().split('T')[0]}.csv`);
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// === Helpers สำหรับจัดการ Format ข้อมูล ===
const levelText = (level) => {
  const map = { primary: 'อนุบาล-ประถม', secondary: 'มัธยม-ป.ตรี', public: 'ป.โท-ป.เอก/ประชาชน', smallgroup: 'กลุ่มย่อย (<10 คน)' }
  return map[level] || level
}

const formatVisitDate = (dateString) => {
  if (!dateString) return 'ไม่ได้ระบุ'
  const date = new Date(dateString)
  return date.toLocaleDateString('th-TH', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
}

const formatCenters = (centers) => {
  if (!centers) return '-'
  if (Array.isArray(centers)) return centers.join(', ')
  try {
    const parsed = JSON.parse(centers)
    return Array.isArray(parsed) ? parsed.join(', ') : centers
  } catch (e) {
    return centers
  }
}

// === PAGINATION ===
const currentPage = ref(1)
const itemsPerPage = 10

const totalPages = computed(() => Math.max(1, Math.ceil(filteredBookings.value.length / itemsPerPage)))

const paginatedBookings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredBookings.value.slice(start, end)
})

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
</script>

<template>
  <AdminLayout>
    <Head title="จัดการการจอง" />

    <div class="admin-container">

      <div class="page-header">
        <div class="header-title">
          <h2>📝 จัดการข้อมูลการจอง</h2>
          <p class="subtitle">ตรวจสอบ อนุมัติ และจัดการคิวเข้าชมศูนย์การเรียนรู้</p>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon total">📋</div>
          <div class="stat-info">
            <span class="stat-label">การจองทั้งหมด</span>
            <span class="stat-value">{{ statTotal }} <small>รายการ</small></span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon pending">⏳</div>
          <div class="stat-info">
            <span class="stat-label">รอยืนยัน</span>
            <span class="stat-value pending-text">{{ statPending }} <small>รายการ</small></span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon approved">📅</div>
          <div class="stat-info">
            <span class="stat-label">ลงปฏิทินแล้ว (อนุมัติ/เสร็จสิ้น)</span>
            <span class="stat-value approved-text">{{ statApproved }} <small>รายการ</small></span>
          </div>
        </div>
      </div>

      <div class="card-table-wrapper">

        <div class="table-toolbar">
          <div class="toolbar-left">
            <div class="search-box">
              <span class="search-icon">🔍</span>
              <input type="text" v-model="searchQuery" @input="currentPage = 1" placeholder="ค้นหาชื่อ, หน่วยงาน, หรือวันที่ (YYYY-MM-DD)..." class="filter-input">
            </div>

            <div class="filter-box">
              <select v-model="statusFilter" @change="currentPage = 1" class="filter-select">
                <option value="">ทุกสถานะ</option>
                <option value="รอยืนยัน">รอยืนยัน</option>
                <option value="อนุมัติ">อนุมัติ</option>
                <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                <option value="ยกเลิก">ยกเลิก</option>
              </select>
            </div>
          </div>

          <div class="toolbar-right">
            <button @click="exportCSV" class="btn-export">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              Export Excel
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="modern-table">
            <thead>
              <tr>
                <th width="25%">👤 ผู้จอง / ข้อมูลติดต่อ</th>
                <th width="25%">📅 กำหนดการเข้าชม</th>
                <th width="20%">👥 รายละเอียด</th>
                <th width="15%" class="text-center">📌 สถานะ</th>
                <th width="15%" class="text-center">⚙️ จัดการ</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="paginatedBookings.length === 0">
                <td colspan="5" class="text-center empty-state">
                  <div class="empty-icon">📭</div>
                  <p>ไม่พบข้อมูลการจองในระบบ</p>
                </td>
              </tr>

              <tr v-for="b in paginatedBookings" :key="b.id" class="table-row">
                <td>
                  <div class="info-group">
                    <span class="booking-name">{{ b.name }}</span>
                    <span class="org-text" v-if="b.org">🏢 {{ b.org }}</span>
                    <span class="phone-text">📞 {{ b.phone || 'ไม่มีเบอร์โทร' }}</span>
                    <span class="text-xs text-orange-500 font-bold mt-1" v-if="b.admin_note">📝 {{ b.admin_note }}</span>
                  </div>
                </td>

                <td>
                  <div class="info-group">
                    <span class="visit-date">{{ formatVisitDate(b.visit_date) }}</span>
                    <div class="centers-list">
                      <span class="center-badge" v-for="(center, idx) in formatCenters(b.centers).split(',')" :key="idx">
                        {{ center.trim() }}
                      </span>
                    </div>
                  </div>
                </td>

                <td>
                  <div class="info-group">
                    <span class="people-count">👥 {{ b.people }} คน</span>
                    <span class="level-text">🎓 {{ levelText(b.level) }}</span>
                    <span class="price-text" v-if="b.total_price > 0">💰 ฿{{ Number(b.total_price).toLocaleString() }}</span>
                    <span class="price-text free" v-else>💰 เข้าชมฟรี</span>
                  </div>
                </td>

                <td class="text-center status-cell">
                  <div class="status-wrapper">
                    <select
                      v-model="b.status"
                      @change="updateStatus(b.id, b.status)"
                      :class="['status-select',
                        b.status === 'อนุมัติ' ? 'status-approved' :
                        b.status === 'เสร็จสิ้น' ? 'status-completed' :
                        b.status === 'ยกเลิก' ? 'status-danger' : 'status-pending'
                      ]"
                    >
                      <option class="opt-pending" value="รอยืนยัน">รอยืนยัน</option>
                      <option class="opt-approved" value="อนุมัติ">อนุมัติ</option>
                      <option class="opt-completed" value="เสร็จสิ้น">เสร็จสิ้น</option>
                      <option class="opt-danger" value="ยกเลิก">ยกเลิก</option>
                    </select>
                    <small class="calendar-hint" v-if="b.status === 'อนุมัติ' || b.status === 'เสร็จสิ้น'">
                      ✅ แสดงในปฏิทิน
                    </small>
                  </div>
                </td>

                <td class="text-center">
                  <div class="action-buttons">
                    <button @click="goEdit(b.id)" class="btn-action edit" title="แก้ไขข้อมูล/เพิ่มหมายเหตุ">✏️</button>
                    <button @click="deleteBooking(b.id)" class="btn-action delete" title="ลบรายการ">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-container" v-if="totalPages > 1">
          <div class="pagination-info">แสดงหน้า {{ currentPage }} จาก {{ totalPages }} (ทั้งหมด {{ filteredBookings.length }} รายการ)</div>
          <div class="pagination-controls">
            <button @click="prevPage" :disabled="currentPage === 1" class="page-btn">« ก่อนหน้า</button>
            <button
              v-for="pageNum in totalPages" :key="pageNum"
              @click="currentPage = pageNum"
              :class="['page-num', { active: currentPage === pageNum }]"
            >
              {{ pageNum }}
            </button>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">ถัดไป »</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

.admin-container { max-width: 1400px; margin: 0 auto; padding: 30px 20px; font-family: 'Prompt', sans-serif; color: #1e293b; }
.text-center { text-align: center; } .info-group { display: flex; flex-direction: column; gap: 4px; }

/* Header & Stats */
.page-header { margin-bottom: 24px; }
.header-title h2 { font-size: 28px; font-weight: 700; color: #065f46; margin: 0 0 6px 0; }
.subtitle { font-size: 15px; color: #64748b; margin: 0; }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; }
.stat-icon { font-size: 32px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 16px; }
.stat-icon.total { background: #f8fafc; color: #475569; } .stat-icon.pending { background: #fef9c3; color: #ca8a04; } .stat-icon.approved { background: #dcfce7; color: #16a34a; }
.stat-info { display: flex; flex-direction: column; }
.stat-label { font-size: 13px; color: #64748b; font-weight: 600; }
.stat-value { font-size: 24px; font-weight: 700; color: #0f172a; line-height: 1.2; }
.stat-value small { font-size: 14px; color: #94a3b8; font-weight: 400; }
.pending-text { color: #b45309; } .approved-text { color: #15803d; }

/* 🎯 Toolbar (Search & Export) */
.table-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #ffffff; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; gap: 16px; }
.toolbar-left { display: flex; gap: 16px; flex-wrap: wrap; flex-grow: 1; }
.search-box { position: relative; flex-grow: 1; max-width: 350px; }
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
.filter-input { width: 100%; padding: 10px 16px 10px 40px; border: 1px solid #cbd5e1; border-radius: 10px; font-family: inherit; font-size: 14px; color: #1e293b; outline: none; transition: 0.2s; }
.filter-input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
.filter-select { padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-family: inherit; font-size: 14px; color: #1e293b; outline: none; background: white; cursor: pointer; }
.btn-export { display: inline-flex; align-items: center; gap: 8px; background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(16,185,129,0.2); }
.btn-export:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(16,185,129,0.3); }

/* Table Wrapper */
.card-table-wrapper { background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; }
.table-responsive { width: 100%; overflow-x: auto; }

/* Modern Table */
.modern-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
.modern-table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.modern-table th { padding: 18px 20px; font-size: 14px; font-weight: 600; color: #475569; text-align: left; }
.modern-table td { padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
.table-row { transition: background 0.2s; } .table-row:hover { background: #f8fafc; }

/* Empty State */
.empty-state { padding: 60px 20px !important; color: #94a3b8; font-size: 16px; }
.empty-icon { font-size: 40px; margin-bottom: 10px; opacity: 0.5; }

/* Table Data Styles */
.booking-name { font-weight: 700; color: #0f172a; font-size: 16px; }
.org-text { font-size: 13px; color: #64748b; } .phone-text { font-size: 13px; color: #0ea5e9; font-weight: 500; }
.visit-date { font-weight: 700; color: #10b981; font-size: 16px; }
.centers-list { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
.center-badge { background: #f1f5f9; color: #475569; font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: 600; }
.people-count { font-weight: 600; color: #334155; font-size: 14px; }
.level-text { font-size: 13px; color: #64748b; }
.price-text { font-size: 14px; font-weight: 700; color: #059669; } .price-text.free { color: #8b5cf6; }

/* Status Select */
.status-cell { vertical-align: top; padding-top: 20px !important; }
.status-wrapper { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.status-select { padding: 8px 30px 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; font-family: inherit; cursor: pointer; appearance: none; border: 2px solid transparent; text-align: center; transition: all 0.2s; outline: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

/* สีพื้นหลังกล่อง Select */
.status-pending { background-color: #fefce8; color: #ca8a04; border-color: #fef08a; } .status-pending:hover { border-color: #facc15; }
.status-approved { background-color: #ecfdf5; color: #059669; border-color: #a7f3d0; } .status-approved:hover { border-color: #34d399; }
.status-completed { background-color: #eff6ff; color: #2563eb; border-color: #bfdbfe; } .status-completed:hover { border-color: #93c5fd; }
.status-danger { background-color: #fef2f2; color: #ef4444; border-color: #fca5a5; } .status-danger:hover { border-color: #f87171; }

/* สีพื้นหลังของ Option List ตอนกด Dropdown */
.opt-pending { background-color: #fefce8; color: #ca8a04; font-weight: 600; }
.opt-approved { background-color: #ecfdf5; color: #059669; font-weight: 600; }
.opt-completed { background-color: #eff6ff; color: #2563eb; font-weight: 600; }
.opt-danger { background-color: #fef2f2; color: #ef4444; font-weight: 600; }

.calendar-hint { font-size: 11px; color: #10b981; font-weight: 600; animation: fadeIn 0.3s; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

/* Actions */
.action-buttons { display: flex; justify-content: center; gap: 8px; }
.btn-action { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: none; cursor: pointer; font-size: 16px; transition: all 0.2s; background: #f8fafc; }
.btn-action.edit:hover { background: #dbeafe; color: #2563eb; transform: scale(1.05); }
.btn-action.delete:hover { background: #fee2e2; color: #dc2626; transform: scale(1.05); }

/* Pagination */
.pagination-container { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
.pagination-info { font-size: 14px; color: #64748b; font-weight: 500; }
.pagination-controls { display: flex; gap: 6px; }
.pagination-controls button { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.2s; }
.pagination-controls button:hover:not(:disabled) { border-color: #10b981; color: #10b981; }
.pagination-controls button.active { background: #10b981; color: #ffffff; border-color: #10b981; box-shadow: 0 4px 10px rgba(16,185,129,0.2); }
.pagination-controls button:disabled { opacity: 0.5; cursor: not-allowed; background: #f1f5f9; }

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr; }
  .table-toolbar { flex-direction: column; align-items: stretch; }
  .search-box { max-width: 100%; }
  .pagination-container { flex-direction: column; gap: 16px; align-items: center; text-align: center; }
}
</style>
