<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  products: Array
})

const page = usePage()
const rolePrefix = page.props.auth.user.roles.some(r => r.name === 'superadmin') ? '/superadmin' : '/admin'

const imgSrc = (blob) => blob ? 'data:image/jpeg;base64,' + blob : ''

const deleteProduct = (id) => {
  if (confirm('🚨 ยืนยันลบสินค้ารายการนี้ใช่หรือไม่? ข้อมูลจะไม่สามารถกู้คืนได้')) {
    router.delete(`${rolePrefix}/products/${id}`)
  }
}

const searchQuery = ref('')
const statusFilter = ref('')

const outOfStockCount = computed(() => props.products.filter(p => p.stock === 0).length)
const lowStockCount = computed(() => props.products.filter(p => p.stock > 0 && p.stock <= 5).length)
const totalProductsCount = computed(() => props.products.length)

const filteredProducts = computed(() => {
  return props.products.filter(p => {
    let matchSearch = p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    let matchStatus = true

    if (statusFilter.value === 'out_of_stock') matchStatus = p.stock === 0
    else if (statusFilter.value === 'low_stock') matchStatus = p.stock > 0 && p.stock <= 5
    else if (statusFilter.value === 'in_stock') matchStatus = p.stock > 5

    return matchSearch && matchStatus
  })
})

const getStatusBadge = (stock) => {
  if (stock === 0) return { text: 'สินค้าหมด', class: 'badge-danger', glow: 'glow-red' }
  if (stock <= 5) return { text: 'ใกล้หมด', class: 'badge-warning', glow: 'glow-orange' }
  return { text: 'พร้อมขาย', class: 'badge-success', glow: 'glow-green' }
}

const currentPage = ref(1)
const itemsPerPage = 8
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage))
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredProducts.value.slice(start, start + itemsPerPage)
})

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
</script>

<template>
  <AdminLayout>
    <div class="high-contrast-container">

      <div class="page-header slide-down">
        <div class="title-wrapper">
          <div class="icon-box">📦</div>
          <div>
            <h2 class="gradient-text">จัดการคลังสินค้า</h2>
            <p class="subtitle">ควบคุมสต๊อกและข้อมูลสินค้าทั้งหมดของคุณด้วยความมั่นใจ</p>
          </div>
        </div>
        <Link :href="`${rolePrefix}/products/create`" class="btn-animated">
          <span class="btn-icon">✨</span> เพิ่มสินค้าใหม่
        </Link>
      </div>

      <div class="widget-grid fade-in">
        <div class="opaque-widget total" @click="statusFilter = ''" :class="{'active-widget': statusFilter === ''}">
          <div class="widget-icon blue">📊</div>
          <div class="widget-info">
            <p>สินค้าทั้งหมด</p>
            <h3>{{ totalProductsCount }} <span>รายการ</span></h3>
          </div>
        </div>
        <div class="opaque-widget danger" @click="statusFilter = 'out_of_stock'" :class="{'active-widget': statusFilter === 'out_of_stock'}">
          <div class="widget-icon red pulse">⚠️</div>
          <div class="widget-info">
            <p>สินค้าหมดสต๊อก</p>
            <h3>{{ outOfStockCount }} <span>รายการ</span></h3>
          </div>
        </div>
        <div class="opaque-widget warning" @click="statusFilter = 'low_stock'" :class="{'active-widget': statusFilter === 'low_stock'}">
          <div class="widget-icon orange">📉</div>
          <div class="widget-info">
            <p>ใกล้หมดสต๊อก</p>
            <h3>{{ lowStockCount }} <span>รายการ</span></h3>
          </div>
        </div>
      </div>

      <div class="controls-opaque fade-in delay-1">
        <div class="search-box">
          <span class="search-icon">🔍</span>
          <input v-model="searchQuery" type="text" placeholder="พิมพ์ชื่อสินค้าเพื่อค้นหา..." class="opaque-input" />
        </div>
        <div class="filter-box">
          <select v-model="statusFilter" class="opaque-select">
            <option value="">🌍 ทุกสถานะ</option>
            <option value="in_stock">✅ พร้อมขาย</option>
            <option value="low_stock">📉 ใกล้หมด</option>
            <option value="out_of_stock">❌ สินค้าหมด</option>
          </select>
        </div>
      </div>

      <div class="table-container fade-in delay-2">
        <table class="floating-table">
          <thead>
            <tr>
              <th width="80">รูปภาพ</th>
              <th>รายละเอียดสินค้า</th>
              <th>หมวดหมู่</th>
              <th class="text-right">ราคา (฿)</th>
              <th class="text-center">สต๊อก</th>
              <th class="text-center">สถานะ</th>
              <th class="text-center" width="160">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="paginatedProducts.length === 0" class="empty-row">
              <td colspan="7">
                <div class="empty-state">
                  <div class="empty-icon">📭</div>
                  <h4>ไม่พบข้อมูลสินค้า</h4>
                  <p>ลองปรับเปลี่ยนเงื่อนไขการค้นหาดูสิ</p>
                </div>
              </td>
            </tr>
            <tr v-for="p in paginatedProducts" :key="p.id" class="data-row">
              <td>
                <div class="avatar-opaque">
                  <img v-if="p.image" :src="imgSrc(p.image)" alt="Product" />
                  <span v-else class="no-img">NO IMG</span>
                </div>
              </td>
              <td>
                <div class="prod-details">
                  <strong class="prod-name">{{ p.name }}</strong>
                  <span class="prod-meta">{{ p.weight ? p.weight + ' kg' : '-' }} • {{ p.unit || '-' }}</span>
                </div>
              </td>
              <td><span class="opaque-badge">{{ p.category || '-' }}</span></td>
              <td class="text-right"><strong class="price-tag">{{ Number(p.price).toLocaleString() }}</strong></td>
              <td class="text-center"><span class="stock-num">{{ p.stock }}</span></td>
              <td class="text-center">
                <div class="status-pill" :class="getStatusBadge(p.stock).class">
                  <span class="pulse-dot" :class="getStatusBadge(p.stock).glow"></span>
                  {{ getStatusBadge(p.stock).text }}
                </div>
              </td>
              <td class="text-center">
                <div class="action-group">
                  <Link :href="`${rolePrefix}/products/${p.id}/edit`" class="btn-icon-opaque edit" title="แก้ไข">✏️</Link>
                  <button @click="deleteProduct(p.id)" class="btn-icon-opaque delete" title="ลบ">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination-opaque fade-in delay-3" v-if="totalPages > 1">
        <span class="page-info">หน้า <b>{{ currentPage }}</b> จาก <b>{{ totalPages }}</b></span>
        <div class="page-controls">
          <button @click="prevPage" :disabled="currentPage === 1" class="btn-page">❮ ก่อนหน้า</button>
          <div class="page-numbers">
            <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="['btn-page-num', { active: currentPage === page }]">{{ page }}</button>
          </div>
          <button @click="nextPage" :disabled="currentPage === totalPages" class="btn-page">ถัดไป ❯</button>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

/* Animations */
@keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-5px); } 100% { transform: translateY(0px); } }
@keyframes pulseGlow { 0% { box-shadow: 0 0 0 0 rgba(inherit, 0.6); } 70% { box-shadow: 0 0 0 8px rgba(inherit, 0); } 100% { box-shadow: 0 0 0 0 rgba(inherit, 0); } }

.slide-down { animation: slideDown 0.5s ease-out forwards; }
.fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }

/* Base & High Contrast */
.high-contrast-container { max-width: 1300px; margin: 0 auto; padding: 30px 20px; font-family: 'Prompt', sans-serif; color: #1e293b; background-color: #f1f5f9; /* ปรับพื้นหลัง Container ให้เข้มขึ้น */ border-radius: 20px; }
.text-center { text-align: center; } .text-right { text-align: right; }

/* Header */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: #ffffff; padding: 25px 35px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 15px 35px rgba(0,0,0,0.06); }
.title-wrapper { display: flex; align-items: center; gap: 18px; }
.icon-box { font-size: 36px; background: linear-gradient(135deg, #dcfce7, #f0fdf4); width: 66px; height: 66px; display: flex; align-items: center; justify-content: center; border-radius: 18px; animation: float 3s ease-in-out infinite; box-shadow: 0 10px 20px rgba(34, 197, 94, 0.25); border: 2px solid #ffffff; }
.gradient-text { margin: 0; font-size: 32px; font-weight: 800; background: linear-gradient(135deg, #047857, #0284c7); /* ปรับสี Gradient ให้สดและเข้มขึ้น */ -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.subtitle { margin: 6px 0 0 0; color: #64748b; font-size: 15px; }

/* Animated Button with More Shine */
.btn-animated { background: linear-gradient(45deg, #22c55e, #16a34a); /* ปรับสีปุ่มให้สดขึ้น */ color: white; padding: 14px 28px; border-radius: 16px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: all 0.3s; box-shadow: 0 12px 24px rgba(22, 163, 74, 0.4); border: 1px solid rgba(255,255,255,0.3); position: relative; overflow: hidden; }
.btn-animated:hover { transform: translateY(-4px) scale(1.03); box-shadow: 0 18px 30px rgba(22, 163, 74, 0.5); }
.btn-icon { font-size: 20px; }

/* Widgets - Opaque and Defined */
.widget-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-bottom: 35px; }
.opaque-widget { background: #ffffff; border-radius: 24px; padding: 28px; display: flex; align-items: center; gap: 24px; cursor: pointer; transition: all 0.3s; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.06); position: relative; overflow: hidden; }
.opaque-widget::before { content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%; transition: 0.3s; }
.opaque-widget.total::before { background: #2563eb; }
.opaque-widget.danger::before { background: #dc2626; }
.opaque-widget.warning::before { background: #d97706; }
.opaque-widget:hover, .active-widget { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); border-color: transparent; }
.opaque-widget.active-widget { border: 2px solid inherit; }
.widget-icon { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; border: 3px solid #ffffff; box-shadow: 0 6px 12px rgba(0,0,0,0.1); }
.widget-icon.blue { background: #eff6ff; color: #2563eb; }
.widget-icon.red { background: #fef2f2; color: #dc2626; }
.widget-icon.orange { background: #fffbeb; color: #d97706; }
.pulse { animation: float 2s infinite; }
.widget-info p { margin: 0; color: #64748b; font-size: 15px; font-weight: 500; }
.widget-info h3 { margin: 6px 0 0 0; font-size: 32px; font-weight: 800; color: #0f172a; }
.widget-info span { font-size: 15px; font-weight: 400; color: #94a3b8; }

/* Controls - Defined and Opaque */
.controls-opaque { display: flex; gap: 18px; margin-bottom: 25px; background: #ffffff; padding: 18px; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 8px 24px rgba(0,0,0,0.05); }
.search-box { position: relative; flex: 1; }
.search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); opacity: 0.6; font-size: 18px; }
.opaque-input, .opaque-select { width: 100%; padding: 14px 18px 14px 50px; border-radius: 14px; border: 2px solid #e2e8f0; background: #f8fafc; font-family: inherit; font-size: 16px; color: #1e293b; transition: 0.3s; box-shadow: inset 0 3px 6px rgba(0,0,0,0.03); box-sizing: border-box; }
.opaque-select { padding-left: 18px; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 18px center; background-size: 18px; }
.opaque-input:focus, .opaque-select:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.2); background: #ffffff; }

/* Floating Table - Deep Shadows & Sharp Details */
.table-container { overflow-x: auto; padding-bottom: 25px; }
.floating-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; /* เพิ่มระยะห่างระหว่างแถว */ }
.floating-table th { padding: 0 25px 12px; color: #1f2937; /* ปรับสีหัวตารางให้เข้มขึ้น */ font-weight: 700; font-size: 15px; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: none; }
.data-row td { background: #ffffff; padding: 20px 25px; transition: all 0.3s; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; box-shadow: 0 6px 12px rgba(0,0,0,0.03); }
.data-row td:first-child { border-radius: 18px 0 0 18px; border-left: 1px solid #e2e8f0; }
.data-row td:last-child { border-radius: 0 18px 18px 0; border-right: 1px solid #e2e8f0; }
.data-row:hover td { background: #f8fafc; transform: scale(1.02); box-shadow: 0 15px 35px rgba(0,0,0,0.08); border-color: transparent; cursor: default; }

.avatar-opaque { width: 60px; height: 60px; border-radius: 16px; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border: 3px solid #ffffff; box-shadow: 0 6px 12px rgba(0,0,0,0.08); }
.avatar-opaque img { width: 100%; height: 100%; object-fit: cover; }
.no-img { font-size: 11px; font-weight: 800; color: #94a3b8; }
.prod-details { display: flex; flex-direction: column; }
.prod-name { font-size: 17px; color: #0f172a; font-weight: 700; }
.prod-meta { font-size: 14px; color: #64748b; margin-top: 5px; }
.opaque-badge { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 7px 14px; border-radius: 10px; font-size: 14px; color: #1e293b; font-weight: 500; }
.price-tag { font-size: 18px; color: #166534; font-weight: 800; background: #dcfce7; padding: 7px 14px; border-radius: 12px; border: 1px solid #a7f3d0; }
.stock-num { font-size: 17px; font-weight: 800; color: #111827; }

/* High Contrast Status Pills */
.status-pill { display: inline-flex; align-items: center; gap: 9px; padding: 9px 16px; border-radius: 14px; font-size: 14px; font-weight: 700; border: 1px solid transparent; }
.pulse-dot { width: 9px; height: 9px; border-radius: 50%; }
.badge-success { background: #b9f6ca; color: #1b5e20; border-color: #81c784; } .glow-green { background: #00c853; box-shadow: 0 0 0 0 rgba(0, 200, 83, 0.8); animation: pulseGlow 1.8s infinite; }
.badge-warning { background: #ffe082; color: #bf360c; border-color: #ffd54f; } .glow-orange { background: #ff9100; box-shadow: 0 0 0 0 rgba(255, 145, 0, 0.8); animation: pulseGlow 1.8s infinite; }
.badge-danger { background: #ffab91; color: #b71c1c; border-color: #e57373; } .glow-red { background: #ff1744; box-shadow: 0 0 0 0 rgba(255, 23, 68, 0.8); animation: pulseGlow 1.8s infinite; }

/* Action Buttons - More Substantial */
.action-group { display: flex; gap: 10px; justify-content: center; }
.btn-icon-opaque { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 2px solid #e2e8f0; background: #ffffff; font-size: 18px; cursor: pointer; transition: all 0.3s; text-decoration: none; box-shadow: 0 4px 8px rgba(0,0,0,0.04); }
.btn-icon-opaque.edit:hover { background: #2563eb; color: white; border-color: transparent; transform: translateY(-4px) rotate(8deg); box-shadow: 0 8px 16px rgba(37, 99, 235, 0.4); }
.btn-icon-opaque.delete:hover { background: #dc2626; color: white; border-color: transparent; transform: translateY(-4px) rotate(-8deg); box-shadow: 0 8px 16px rgba(220, 38, 38, 0.4); }

/* Empty State */
.empty-row td { background: transparent !important; box-shadow: none !important; border: none !important; }
.empty-state { text-align: center; padding: 70px 20px; background: #ffffff; border-radius: 24px; border: 3px dashed #cbd5e1; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.empty-icon { font-size: 56px; margin-bottom: 18px; filter: grayscale(0.8); opacity: 0.6; }
.empty-state h4 { margin: 0; color: #1f2937; font-size: 20px; font-weight: 700; }
.empty-state p { margin: 8px 0 0; color: #64748b; font-size: 16px; }

/* Pagination - Defined */
.pagination-opaque { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 18px 28px; border-radius: 20px; margin-top: 15px; border: 1px solid #e2e8f0; box-shadow: 0 12px 30px rgba(0,0,0,0.06); }
.page-info { color: #1f2937; font-size: 15px; font-weight: 500; }
.page-info b { font-weight: 800; color: #0f172a; }
.page-controls { display: flex; gap: 12px; align-items: center; }
.btn-page { background: #ffffff; border: 2px solid #e2e8f0; padding: 10px 20px; border-radius: 12px; color: #1f2937; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; font-size: 14px; box-shadow: 0 4px 8px rgba(0,0,0,0.03); }
.btn-page:hover:not(:disabled) { background: #f8fafc; border-color: #22c55e; color: #15803d; box-shadow: 0 6px 12px rgba(34, 197, 94, 0.15); }
.btn-page:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }
.page-numbers { display: flex; gap: 7px; }
.btn-page-num { width: 40px; height: 40px; border-radius: 12px; border: 2px solid transparent; background: transparent; color: #1f2937; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; font-size: 15px; }
.btn-page-num:hover { background: #f1f5f9; color: #0f172a; }
.btn-page-num.active { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border-color: transparent; box-shadow: 0 6px 14px rgba(22, 163, 74, 0.4); }

@media (max-width: 768px) {
  .page-header { flex-direction: column; align-items: flex-start; gap: 18px; padding: 20px; }
  .icon-box { width: 56px; height: 56px; font-size: 28px; }
  .gradient-text { font-size: 26px; }
  .controls-opaque { flex-direction: column; padding: 15px; gap: 12px; }
  .widget-grid { grid-template-columns: 1fr; gap: 15px; }
  .floating-table th { font-size: 13px; padding: 0 10px 8px; }
  .data-row td { padding: 15px 10px; }
  .prod-name { font-size: 15px; }
  .pagination-opaque { flex-direction: column; gap: 18px; align-items: center; padding: 20px; }
  .btn-page { padding: 8px 14px; font-size: 13px; }
}
</style>
