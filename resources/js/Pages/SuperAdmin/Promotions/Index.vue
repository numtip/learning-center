<script setup>
import { ref, computed } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    promotions: {
        type: Array,
        default: () => []
    }
});

const page = usePage();

const basePath = computed(() => {
    return page.url.startsWith('/superadmin') ? '/superadmin/promotions' : '/admin/promotions';
});

// 🔥 เพิ่ม State สำหรับจัดการระบบ Tabs
const activeTab = ref('center'); // ค่าเริ่มต้นให้แสดงแท็บศูนย์การเรียนรู้ก่อน

// 🔥 ฟิลเตอร์ข้อมูลตามแท็บที่เลือก
const filteredPromotions = computed(() => {
    return props.promotions.filter(promo => promo.type === activeTab.value);
});

const showModal = ref(false);
const isEditing = ref(false);
const imagePreview = ref(null);

const form = useForm({
    id: null,
    type: 'center',
    title: '',
    description: '',
    image: null,
    status: true,
    start_date: '',
    end_date: '',
    seo_title: '',
    seo_description: '',
    _method: 'post'
});

const openAddModal = () => {
    isEditing.value = false;
    imagePreview.value = null;
    form.reset();
    form.clearErrors();
    form.type = activeTab.value; // 🔥 ตั้งค่าเริ่มต้นให้ตรงกับแท็บที่เปิดอยู่
    form._method = 'post';
    showModal.value = true;
};

const openEditModal = (promo) => {
    isEditing.value = true;
    imagePreview.value = promo.image;
    form.clearErrors();
    form.id = promo.id;
    form.type = promo.type;
    form.title = promo.title;
    form.description = promo.description;
    form.image = null;
    form.status = promo.status ? true : false;
    form.start_date = promo.start_date ? promo.start_date.split('T')[0] : '';
    form.end_date = promo.end_date ? promo.end_date.split('T')[0] : '';
    form.seo_title = promo.seo_title || '';
    form.seo_description = promo.seo_description || '';
    form._method = 'put';
    showModal.value = true;
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submitForm = () => {
    const url = isEditing.value ? `${basePath.value}/${form.id}` : basePath.value;

    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            activeTab.value = form.type; // 🔥 สลับกลับไปแท็บที่เพิ่งเพิ่ม/แก้ไขข้อมูล
        }
    });
};

const deletePromotion = (id) => {
    if (confirm('ยืนยันการลบโปรโมชันนี้ใช่หรือไม่? ข้อมูลจะไม่สามารถกู้คืนได้')) {
        router.delete(`${basePath.value}/${id}`, {
            preserveScroll: true
        });
    }
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    imagePreview.value = null;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const d = new Date(dateString);
    return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear() + 543}`;
};

const getStatusBadge = (status, endDate) => {
    const today = new Date();
    today.setHours(0,0,0,0);
    const end = new Date(endDate);
    end.setHours(0,0,0,0);

    if (!status) {
        return { text: 'ปิดใช้งาน', class: 'badge-red' };
    }
    if (end < today) {
        return { text: 'หมดอายุ', class: 'badge-gray' };
    }
    return { text: 'เปิดใช้งาน', class: 'badge-green' };
};
</script>

<template>
    <AdminLayout>
        <div class="header-section">
            <div>
                <h1 class="page-title">🔥 จัดการโปรโมชัน</h1>
                <p class="page-subtitle">เพิ่ม ลบ แก้ไข โปรโมชันแพ็กเกจศูนย์การเรียนรู้ และสินค้า</p>
            </div>
            <button @click="openAddModal" class="btn-primary">
                + เพิ่มรายการใหม่
            </button>
        </div>

        <div class="tabs-container">
            <button
                :class="['tab-btn', { active: activeTab === 'center' }]"
                @click="activeTab = 'center'"
            >
                🏛️ แพ็กเกจศูนย์การเรียนรู้
                <span class="count-badge" v-if="promotions.filter(p => p.type === 'center').length">
                    {{ promotions.filter(p => p.type === 'center').length }}
                </span>
            </button>
            <button
                :class="['tab-btn', { active: activeTab === 'shop' }]"
                @click="activeTab = 'shop'"
            >
                🛍️ ผลิตภัณฑ์จากฟาร์ม (สินค้า)
                <span class="count-badge" v-if="promotions.filter(p => p.type === 'shop').length">
                    {{ promotions.filter(p => p.type === 'shop').length }}
                </span>
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="12%">รูปภาพ</th>
                        <th width="35%">ชื่อโปรโมชัน/แพ็กเกจ</th>
                        <th width="20%">ระยะเวลา</th>
                        <th width="15%">สถานะ</th>
                        <th width="18%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filteredPromotions.length === 0">
                        <td colspan="5" class="text-center py-8 text-gray-500">
                            ยังไม่มีข้อมูลในหมวดหมู่นี้
                        </td>
                    </tr>
                    <tr v-for="promo in filteredPromotions" :key="promo.id">
                        <td>
                            <div class="img-thumbnail">
                                <img v-if="promo.image" :src="promo.image" alt="promo image" />
                                <div v-else class="no-img">ไม่มีรูป</div>
                            </div>
                        </td>
                        <td>
                            <div class="font-semibold text-gray-800">{{ promo.title }}</div>
                            <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ promo.description }}</div>
                        </td>
                        <td class="text-sm text-gray-600">
                            {{ formatDate(promo.start_date) }} - {{ formatDate(promo.end_date) }}
                        </td>
                        <td>
                            <span :class="['status-badge', getStatusBadge(promo.status, promo.end_date).class]">
                                {{ getStatusBadge(promo.status, promo.end_date).text }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button @click="openEditModal(promo)" class="btn-edit">แก้ไข</button>
                                <button @click="deletePromotion(promo.id)" class="btn-delete">ลบ</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content custom-scrollbar">
                <div class="modal-header">
                    <h2>{{ isEditing ? '✏️ แก้ไขข้อมูล' : '✨ เพิ่มรายการใหม่' }}</h2>
                    <button @click="closeModal" class="btn-close">&times;</button>
                </div>

                <form @submit.prevent="submitForm" class="modal-body">
                    <div class="form-group">
                        <label>หมวดหมู่จัดเก็บ</label>
                        <select v-model="form.type" class="form-control" required>
                            <option value="center">แพ็กเกจศูนย์การเรียนรู้</option>
                            <option value="shop">ผลิตภัณฑ์จากฟาร์ม (สินค้า)</option>
                        </select>
                        <span class="error-text" v-if="form.errors.type">{{ form.errors.type }}</span>
                    </div>

                    <div class="form-group">
                        <label>ชื่อโปรโมชัน/แพ็กเกจ</label>
                        <input type="text" v-model="form.title" class="form-control" placeholder="ระบุชื่อที่ต้องการแสดง" required />
                        <span class="error-text" v-if="form.errors.title">{{ form.errors.title }}</span>
                    </div>

                    <div class="form-group">
                        <label>รายละเอียด</label>
                        <textarea v-model="form.description" class="form-control" rows="4" placeholder="เงื่อนไขและรายละเอียด" required></textarea>
                        <span class="error-text" v-if="form.errors.description">{{ form.errors.description }}</span>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>วันที่เริ่มต้น</label>
                            <input type="date" v-model="form.start_date" class="form-control" required />
                            <span class="error-text" v-if="form.errors.start_date">{{ form.errors.start_date }}</span>
                        </div>
                        <div class="form-group">
                            <label>วันที่สิ้นสุด</label>
                            <input type="date" v-model="form.end_date" class="form-control" required />
                            <span class="error-text" v-if="form.errors.end_date">{{ form.errors.end_date }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>สถานะการแสดงผล</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="statusToggle" v-model="form.status">
                            <label for="statusToggle" class="toggle-label"></label>
                            <span class="ml-3 font-medium">{{ form.status ? 'เปิดใช้งาน' : 'ปิดซ่อนไว้' }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>รูปภาพ (ขนาดแนะนำ 800x600px)</label>
                        <input type="file" @change="handleImageUpload" class="form-control" accept="image/*" />
                        <span class="error-text" v-if="form.errors.image">{{ form.errors.image }}</span>

                        <div v-if="imagePreview" class="preview-box mt-3">
                            <img :src="imagePreview" alt="Preview" />
                        </div>
                    </div>

                    <hr class="divider">
                    <h3 class="seo-title">ตั้งค่า SEO (ตัวเลือก)</h3>

                    <div class="form-group">
                        <label>หัวข้อสำหรับ SEO</label>
                        <input type="text" v-model="form.seo_title" class="form-control" placeholder="ไม่เกิน 60 ตัวอักษร" />
                    </div>

                    <div class="form-group">
                        <label>คำอธิบายสำหรับ SEO</label>
                        <textarea v-model="form.seo_description" class="form-control" rows="2" placeholder="ไม่เกิน 160 ตัวอักษร"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="closeModal" class="btn-cancel">ยกเลิก</button>
                        <button type="submit" class="btn-save" :disabled="form.processing">
                            {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

* {
    font-family: 'Kanit', sans-serif;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.page-subtitle {
    font-size: 14px;
    color: #64748b;
    margin-top: 4px;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
}

/* 🔥 สไตล์สำหรับ Tabs */
.tabs-container {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0;
}

.tab-btn {
    padding: 12px 24px;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 16px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: -2px; /* ให้เส้นทับกับเส้นกรอบล่างพอดี */
    display: flex;
    align-items: center;
    gap: 8px;
}

.tab-btn:hover {
    color: #1e293b;
}

.tab-btn.active {
    color: #10b981;
    border-bottom-color: #10b981;
}

.count-badge {
    background: #e2e8f0;
    color: #475569;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
}

.tab-btn.active .count-badge {
    background: #dcfce7;
    color: #166534;
}

.table-container {
    background: white;
    border-radius: 16px;
    padding: 1px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f8fafc;
    padding: 16px;
    text-align: left;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
}

.data-table td {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.img-thumbnail {
    width: 100px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
}

.img-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-img {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #94a3b8;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.badge-green { background: #dcfce7; color: #166534; }
.badge-red { background: #fee2e2; color: #991b1b; }
.badge-gray { background: #f1f5f9; color: #475569; }

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-delete {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-edit { background: #eff6ff; color: #2563eb; }
.btn-edit:hover { background: #dbeafe; }

.btn-delete { background: #fef2f2; color: #dc2626; }
.btn-delete:hover { background: #fee2e2; }

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    width: 100%;
    max-width: 650px;
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 24px 32px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
}

.btn-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
}

.btn-close:hover { color: #0f172a; }

.modal-body {
    padding: 32px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
    color: #334155;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    font-size: 15px;
    font-family: inherit;
    transition: all 0.3s;
    background: #f8fafc;
}

.form-control:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.error-text {
    color: #ef4444;
    font-size: 13px;
    margin-top: 4px;
    display: block;
}

.preview-box {
    width: 100%;
    height: 200px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px dashed #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.toggle-switch {
    display: flex;
    align-items: center;
}

.toggle-switch input {
    display: none;
}

.toggle-label {
    width: 50px;
    height: 26px;
    background: #cbd5e1;
    border-radius: 20px;
    position: relative;
    cursor: pointer;
    transition: 0.3s;
}

.toggle-label::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-switch input:checked + .toggle-label {
    background: #10b981;
}

.toggle-switch input:checked + .toggle-label::after {
    transform: translateX(24px);
}

.ml-3 { margin-left: 12px; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.text-center { text-align: center; }
.text-sm { font-size: 14px; }
.text-gray-600 { color: #475569; }
.text-gray-800 { color: #1e293b; }
.text-xs { font-size: 12px; }
.truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.max-w-xs { max-width: 20rem; }
.py-8 { padding-top: 32px; padding-bottom: 32px; }
.text-gray-500 { color: #64748b; }
.mt-1 { margin-top: 4px; }
.mt-3 { margin-top: 12px; }

.divider {
    border: 0;
    height: 1px;
    background: #e2e8f0;
    margin: 32px 0 24px;
}

.seo-title {
    font-size: 16px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 16px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 32px;
}

.btn-cancel {
    padding: 12px 24px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-cancel:hover { background: #e2e8f0; }

.btn-save {
    padding: 12px 24px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-save:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.btn-save:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
