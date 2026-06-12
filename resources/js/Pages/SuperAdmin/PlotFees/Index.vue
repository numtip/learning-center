<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({ fees: Array })

const keyword = ref('')
const edit = ref(null)

const currentPage = ref(1)
const itemsPerPage = 10

const filtered = computed(() => {
  if (!keyword.value) return props.fees
  return props.fees.filter(f =>
    f.title.toLowerCase().includes(keyword.value.toLowerCase()) ||
    f.topic.toLowerCase().includes(keyword.value.toLowerCase())
  )
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / itemsPerPage)))

const paginatedFiltered = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filtered.value.slice(start, start + itemsPerPage)
})

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const form = useForm({
  topic: '',
  title: '',
  detail1: '', price1: '',
  detail2: '', price2: '',
  detail3: '', price3: '',
  detail4: '', price4: '',
})

const openNew = () => {
  edit.value = null
  form.reset()
}

const openEdit = (f) => {
  edit.value = f
  form.topic = f.topic || ''
  form.title = f.title || ''
  form.detail1 = f.detail1 || ''
  form.price1 = f.price1 || ''
  form.detail2 = f.detail2 || ''
  form.price2 = f.price2 || ''
  form.detail3 = f.detail3 || ''
  form.price3 = f.price3 || ''
  form.detail4 = f.detail4 || ''
  form.price4 = f.price4 || ''
}

const save = () => {
  if (!form.topic || !form.title) {
    alert('กรุณากรอกประเภทและชื่อชุดให้ครบถ้วน')
    return
  }

  edit.value
    ? form.put(`/superadmin/plot-fees/${edit.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
          edit.value = null
          form.reset()
        }
      })
    : form.post('/superadmin/plot-fees', {
        preserveScroll: true,
        onSuccess: () => {
          form.reset()
        }
      })
}

const remove = (id) => {
  if (confirm('ยืนยันการลบชุดค่าธรรมเนียมนี้ใช่หรือไม่?')) {
    router.delete(`/superadmin/plot-fees/${id}`, {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <AdminLayout>
    <div class="admin-wrapper">

      <div class="header-section">
        <div class="title-block">
          <div class="title-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
          </div>
          <div>
            <h2 class="page-title">จัดการอัตราค่าธรรมเนียม</h2>
            <p class="page-subtitle">ระบบตั้งค่าและกำหนดราคาค่าธรรมเนียมต่างๆ ภายในระบบ</p>
          </div>
        </div>
      </div>

      <div class="content-grid">

        <div class="data-panel">

          <div class="toolbar">
            <div class="search-box">
              <svg class="icon-search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              <input v-model="keyword" class="input-field pl-10" placeholder="ค้นหาประเภท หรือชื่อชุดค่าธรรมเนียม..." @input="currentPage = 1" />
            </div>
          </div>

          <div class="table-card">
            <div class="table-container">
              <table class="modern-table">
                <thead>
                  <tr>
                    <th width="15%">ประเภท</th>
                    <th width="40%">ชื่อชุดค่าธรรมเนียม</th>
                    <th class="text-right" width="25%">ช่วงราคา (THB)</th>
                    <th class="text-center" width="20%">จัดการ</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-if="paginatedFiltered.length === 0">
                    <td colspan="4" class="empty-state">
                      <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                      <p>ไม่พบข้อมูลค่าธรรมเนียมในระบบ</p>
                    </td>
                  </tr>
                  <tr v-for="f in paginatedFiltered" :key="f.id">
                    <td>
                      <span class="badge-green">{{ f.topic }}</span>
                    </td>
                    <td>
                      <span class="fw-semibold text-dark">{{ f.title }}</span>
                    </td>
                    <td class="text-right">
                      <span class="price-text">
                        <template v-if="f.price1 && f.price4">{{ Number(f.price1).toLocaleString() }} - {{ Number(f.price4).toLocaleString() }}</template>
                        <template v-else-if="f.price1">{{ Number(f.price1).toLocaleString() }}</template>
                        <template v-else>-</template>
                      </span>
                    </td>
                    <td class="text-center">
                      <div class="action-group">
                        <button class="btn-action edit" @click="openEdit(f)" title="แก้ไขข้อมูล">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button class="btn-action delete" @click="remove(f.id)" title="ลบข้อมูล">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="pagination-wrapper" v-if="totalPages > 1">
              <span class="page-info">หน้า {{ currentPage }} จาก {{ totalPages }}</span>
              <div class="page-controls">
                <button @click="prevPage" :disabled="currentPage === 1" class="btn-page">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button v-for="pageNum in totalPages" :key="pageNum" @click="currentPage = pageNum" :class="['btn-page', { active: currentPage === pageNum }]">
                  {{ pageNum }}
                </button>
                <button @click="nextPage" :disabled="currentPage === totalPages" class="btn-page">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="form-panel">
          <div class="form-card">

            <div class="form-header" :class="{ 'is-editing': edit }">
              <h3 class="form-title">
                <svg v-if="!edit" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                {{ edit ? 'แก้ไขข้อมูล' : 'เพิ่มรายการใหม่' }}
              </h3>
              <button v-if="edit" @click="openNew" class="btn-cancel" title="ยกเลิกการแก้ไขและเพิ่มใหม่">เคลียร์ฟอร์ม</button>
            </div>

            <div class="form-body">
              <div class="input-group">
                <label>ประเภท <span class="required">*</span></label>
                <input v-model="form.topic" class="input-field" placeholder="เช่น ค่าบริการ, ค่าที่พัก" />
              </div>

              <div class="input-group">
                <label>ชื่อชุด <span class="required">*</span></label>
                <input v-model="form.title" class="input-field" placeholder="เช่น บริการเสริม VIP" />
              </div>

              <div class="price-section">
                <div class="section-heading">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                  รายละเอียด และ ราคา (THB)
                </div>

                <div class="price-items">
                  <div class="price-row">
                    <div class="num-badge">1</div>
                    <input v-model="form.detail1" class="input-field detail-input" placeholder="รายละเอียดที่ 1" />
                    <input v-model="form.price1" type="number" class="input-field price-input" placeholder="0" />
                  </div>

                  <div class="price-row">
                    <div class="num-badge">2</div>
                    <input v-model="form.detail2" class="input-field detail-input" placeholder="รายละเอียดที่ 2" />
                    <input v-model="form.price2" type="number" class="input-field price-input" placeholder="0" />
                  </div>

                  <div class="price-row">
                    <div class="num-badge">3</div>
                    <input v-model="form.detail3" class="input-field detail-input" placeholder="รายละเอียดที่ 3" />
                    <input v-model="form.price3" type="number" class="input-field price-input" placeholder="0" />
                  </div>

                  <div class="price-row">
                    <div class="num-badge">4</div>
                    <input v-model="form.detail4" class="input-field detail-input" placeholder="รายละเอียดที่ 4" />
                    <input v-model="form.price4" type="number" class="input-field price-input" placeholder="0" />
                  </div>
                </div>
              </div>
            </div>

            <div class="form-footer">
              <button class="btn-submit" @click="save" :disabled="form.processing">
                <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                <svg v-else class="spinner" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                <span>{{ form.processing ? 'กำลังบันทึก...' : (edit ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล') }}</span>
              </button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');

/* Base & Typography */
.admin-wrapper {
  max-width: 1440px;
  margin: 0 auto;
  padding: 32px 24px;
  font-family: 'Sarabun', sans-serif;
  color: #334155;
  box-sizing: border-box;
}
*, *::before, *::after { box-sizing: inherit; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.fw-semibold { font-weight: 600; }
.text-dark { color: #0f172a; }
.pl-10 { padding-left: 40px !important; }

/* Header */
.header-section { margin-bottom: 32px; }
.title-block { display: flex; align-items: center; gap: 16px; }
.title-icon {
  display: flex; align-items: center; justify-content: center;
  width: 48px; height: 48px; border-radius: 12px;
  background: #d1fae5; color: #059669;
}
.title-icon svg { width: 24px; height: 24px; }
.page-title { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.page-subtitle { font-size: 15px; color: #64748b; margin: 0; }

/* Grid Layout */
.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 400px;
  gap: 24px;
  align-items: start;
}

/* Toolbar & Search */
.toolbar { margin-bottom: 20px; }
.search-box { position: relative; max-width: 400px; width: 100%; }
.icon-search {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  width: 18px; height: 18px; color: #94a3b8;
}

/* Common Card Style */
.table-card, .form-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  overflow: hidden;
}

/* Table */
.table-container { width: 100%; overflow-x: auto; }
.modern-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
.modern-table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.modern-table th {
  padding: 16px; font-size: 14px; font-weight: 600; color: #64748b;
  text-align: left;
}
.modern-table th.text-center { text-align: center; }
.modern-table th.text-right { text-align: right; }
.modern-table td { padding: 16px; font-size: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
.modern-table tbody tr { transition: background-color 0.2s; }
.modern-table tbody tr:hover { background-color: #f0fdf4; } /* Soft green hover */

/* Empty State */
.empty-state { padding: 60px 20px !important; text-align: center; color: #94a3b8; }
.empty-icon { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5; }

/* Badges & Texts */
.badge-green {
  display: inline-block; padding: 6px 12px; border-radius: 8px;
  font-size: 13px; font-weight: 600;
  background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;
}
.price-text { font-weight: 700; color: #059669; }

/* Action Buttons */
.action-group { display: flex; justify-content: center; gap: 8px; }
.btn-action {
  display: flex; align-items: center; justify-content: center;
  width: 36px; height: 36px; border-radius: 8px;
  border: none; cursor: pointer; transition: all 0.2s;
}
.btn-action svg { width: 18px; height: 18px; }
.btn-action.edit { background: #f1f5f9; color: #0ea5e9; }
.btn-action.edit:hover { background: #e0f2fe; color: #0284c7; transform: translateY(-2px); }
.btn-action.delete { background: #f1f5f9; color: #ef4444; }
.btn-action.delete:hover { background: #fee2e2; color: #dc2626; transform: translateY(-2px); }

/* Pagination */
.pagination-wrapper {
  display: flex; justify-content: space-between; align-items: center;
  padding: 16px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;
}
.page-info { font-size: 14px; color: #64748b; font-weight: 500; }
.page-controls { display: flex; gap: 6px; }
.btn-page {
  display: flex; align-items: center; justify-content: center;
  min-width: 36px; height: 36px; padding: 0 10px;
  background: #ffffff; border: 1px solid #cbd5e1; color: #475569;
  border-radius: 8px; font-size: 14px; font-weight: 600;
  cursor: pointer; transition: all 0.2s; font-family: inherit;
}
.btn-page:hover:not(:disabled) { border-color: #10b981; color: #10b981; }
.btn-page.active { background: #10b981; color: #ffffff; border-color: #10b981; }
.btn-page:disabled { opacity: 0.5; cursor: not-allowed; background: #f1f5f9; }

/* Form Panel */
.form-card { position: sticky; top: 24px; }
.form-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 24px; border-bottom: 1px solid #e2e8f0; background: #ffffff;
}
.form-header.is-editing { background: #f0fdf4; border-bottom-color: #bbf7d0; }
.form-title {
  margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;
  display: flex; align-items: center; gap: 8px;
}
.form-title svg { color: #10b981; }
.btn-cancel {
  background: none; border: none; color: #64748b; font-size: 14px;
  font-weight: 500; cursor: pointer; text-decoration: underline; font-family: inherit;
}
.btn-cancel:hover { color: #ef4444; }

.form-body { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
.form-footer { padding: 20px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; }

/* Inputs */
.input-group { display: flex; flex-direction: column; gap: 8px; }
.input-group label { font-size: 14px; font-weight: 600; color: #334155; }
.required { color: #ef4444; }

.input-field {
  width: 100%; padding: 12px 16px; font-size: 15px; font-family: inherit; color: #1e293b;
  background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px;
  transition: all 0.2s ease;
}
.input-field:focus {
  outline: none; border-color: #10b981; background-color: #ffffff;
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}
.input-field::placeholder { color: #94a3b8; }

/* Price Section inside Form */
.price-section {
  background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;
}
.section-heading {
  font-size: 15px; font-weight: 700; color: #0f172a;
  margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.section-heading svg { color: #10b981; }

.price-items { display: flex; flex-direction: column; gap: 12px; }
.price-row {
  display: grid; grid-template-columns: auto 1fr 110px; gap: 12px; align-items: center;
}
.num-badge {
  display: flex; align-items: center; justify-content: center;
  width: 28px; height: 28px; background: #e2e8f0; color: #475569;
  border-radius: 8px; font-size: 13px; font-weight: 700;
}
.detail-input { background: #ffffff; }
.price-input { background: #ffffff; text-align: right; font-weight: 600; color: #059669; }

/* Submit Button */
.btn-submit {
  width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
  background-color: #10b981; color: #ffffff; border: none; padding: 14px 20px;
  border-radius: 10px; font-size: 16px; font-weight: 600; font-family: inherit;
  cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
}
.btn-submit:hover:not(:disabled) { background-color: #059669; transform: translateY(-2px); }
.btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; box-shadow: none; }

/* Spinner Animation */
.spinner { animation: spin 1s linear infinite; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Responsive Adjustments */
@media (max-width: 1024px) {
  .content-grid { grid-template-columns: 1fr; }
  .form-card { position: static; }
}
@media (max-width: 480px) {
  .price-row {
    grid-template-columns: 1fr; gap: 8px;
    padding-bottom: 16px; border-bottom: 1px dashed #cbd5e1;
  }
  .price-row:last-child { border-bottom: none; padding-bottom: 0; }
  .num-badge { display: none; }
}
</style>
