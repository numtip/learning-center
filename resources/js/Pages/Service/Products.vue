<template>
  <PublicLayout>
    <Head title="ร้านค้าเกษตรพรีเมียม" />
    <div class="page-wrapper">
      <div class="bg-pattern"></div>
      <div class="page-content relative z-10">
        <div class="container">
          <header class="header sticky-header">
            <h1 class="logo" @click="goMenu">
              <div class="logo-icon-box">🌿</div>
              <span class="logo-text">ร้านค้าเกษตร</span>
            </h1>
            <div class="search-container">
              <span class="search-icon">🔍</span>
              <input
                v-model="searchKeyword"
                class="search vibrant-input"
                placeholder="ค้นหาสินค้าที่ต้องการ..."
                @input="() => { page='menu'; window.scrollTo({top: 0, behavior: 'smooth'}) }"
              />
            </div>
            <div class="header-actions">
              <button class="action-btn history-btn" @click="goHistory">
                <span class="btn-icon">📜</span> <span class="btn-text">ประวัติ</span>
              </button>
              <button class="action-btn cart-btn vibrant-cart-btn" @click="goCart">
                <span class="btn-icon animated-basket">🧺</span>
                <span class="cart-badge" v-if="totalQty > 0">{{ totalQty }}</span>
              </button>
            </div>
          </header>

          <div v-if="page === 'menu'">
            <div class="promo-section" v-if="promoBanners && promoBanners.length">
              <div class="promo-track" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                <div v-for="(b,i) in promoBanners" :key="i" class="promo-slide premium-slide">
                  <img :src="b.image" />
                  <div class="promo-gradient"></div>
                </div>
              </div>
            </div>

            <section class="products-section mt-8">
              <div v-for="cat in filteredCategories" :key="cat.category" class="category-block mb-12">
                <div class="category-banner-royal">
                  <div class="royal-aura"></div>
                  <div class="lanna-pattern-overlay"></div>
                  <div class="banner-content">
                    <div class="royal-icon-frame">
                      <div class="glass-inner">
                        <svg class="royal-svg-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <defs>
                            <linearGradient id="gold-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                              <stop offset="0%" style="stop-color:#f3d47a" />
                              <stop offset="50%" style="stop-color:#d4af37" />
                              <stop offset="100%" style="stop-color:#b8860b" />
                            </linearGradient>
                          </defs>
                          <path d="M3 21H21" stroke="url(#gold-grad)" stroke-width="2" stroke-linecap="round"/>
                          <path d="M5 21V10L12 3L19 10V21" stroke="url(#gold-grad)" stroke-width="2" stroke-linejoin="round"/>
                          <path d="M9 21V12H15V21" stroke="url(#gold-grad)" stroke-width="2" stroke-linejoin="round"/>
                          <path d="M12 7V8" stroke="url(#gold-grad)" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <div class="icon-shine"></div>
                      </div>
                    </div>
                    <div class="title-stack">
                      <div class="royal-badge">
                         <span class="sparkle">✨</span>
                         <span class="badge-text">คัดสรรคุณภาพระดับพรีเมียม</span>
                         <span class="sparkle">✨</span>
                      </div>
                      <h2 class="banner-title">{{ cat.category }}</h2>
                      <div class="title-underline"></div>
                    </div>
                  </div>
                  <div class="corner-flare flare-1"></div>
                  <div class="corner-flare flare-2"></div>
                </div>

                <div class="market-grid">
                  <div v-for="item in cat.items" :key="item.name" class="product-card vibrant-card">
                    <div class="img-box" @click="selectProduct(item)">
                      <span v-if="item.stock <= 0" class="stock-badge out">สินค้าหมด</span>
                      <span v-else-if="item.stock < 5" class="stock-badge low">เหลือน้อย</span>
                      <img v-if="item.image" :src="item.image" loading="lazy" alt="รูปภาพสินค้า">
                      <div v-else class="no-img">ไม่มีรูปภาพ</div>
                    </div>
                    <div class="product-info">
                      <div class="product-meta">
                        <span class="product-cat-tag">{{ item.category || 'ทั่วไป' }}</span>
                      </div>
                      <h3 class="product-name" @click="selectProduct(item)">{{ item.name }}</h3>
                      <p class="product-unit">{{ item.size ? item.size + ' ' : '' }}{{ item.unit }}</p>
                      <div class="price-stock-row">
                        <p class="price vibrant-price">{{ item.price }}</p>
                        <p class="stock-text" :class="{'text-red-500': item.stock <= 0}">
                        ขณะนี้มีสินค้า: {{ item.stock }}
                        </p>
                      </div>
                      <div class="card-actions-vibrant">
                        <div class="qty-pill">
                          <button @click="item.tempQty = Math.max(1, (item.tempQty || 1) - 1)">−</button>
                          <span class="qty-num">{{ item.tempQty || 1 }}</span>
                          <button @click="item.tempQty = Math.min(item.stock, (item.tempQty || 1) + 1)" :disabled="(item.tempQty || 1) >= item.stock">+</button>
                        </div>
                        <button
                          class="add-btn-vibrant"
                          :disabled="item.stock <= 0"
                          @click="addToCart(item, item.tempQty || 1)"
                        >
                          <span v-if="item.stock > 0">🛒 เพิ่มใส่ตะกร้า</span>
                          <span v-else>หมดชั่วคราว</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <section v-else-if="page === 'detail' && selectedProduct" class="premium-detail-container animate-fade-in">
            <div class="detail-nav-bar">
              <button class="glass-back-btn" @click="goMenu">
                <span class="icon">←</span> กลับหน้าร้าน
              </button>
            </div>
            <div class="product-master-card">
              <div class="product-visual-section">
                <div class="visual-wrapper">
                  <div v-if="selectedProduct.stock <= 0" class="floating-badge out">สินค้าหมด</div>
                  <div class="main-image-viewport">
                    <img v-if="selectedProduct.image" :src="selectedProduct.image" alt="Product Image" class="product-hero-img">
                  </div>
                </div>
              </div>
              <div class="product-info-section">
                <div class="info-content-wrap">
                  <div class="product-header">
                    <span class="premium-category-tag">{{ selectedProduct.category }}</span>
                    <h2 class="premium-title">{{ selectedProduct.name }}</h2>
                    <div class="premium-rating">⭐สินค้าคัดพิเศษสำหรับลูกค้าทุดท่าน⭐ </div>
                  </div>
                  <div class="premium-price-row">
                    <span class="currency">฿</span>
                    <span class="price-amount">{{ selectedProduct.price }}</span>
                    <span class="price-unit">/ {{ selectedProduct.size ? selectedProduct.size + ' ' : '' }}{{ selectedProduct.unit }}</span>
                  </div>
                  <div class="fancy-divider"><span></span></div>
                  <div class="product-description-box">
                    <h3>รายละเอียดสินค้า</h3>
                    <p>{{ selectedProduct.description || 'สัมผัสประสบการณ์ใหม่กับสินค้าคุณภาพที่เราคัดสรรมาเพื่อคุณโดยเฉพาะ' }}</p>
                  </div>
                  <div class="purchase-action-card">
                    <div class="stock-status-line">
                      <span class="pulse-dot" :class="selectedProduct.stock > 0 ? 'online' : 'offline'"></span>
                      <span class="status-text">
                        {{ selectedProduct.stock > 0 ? 'สินค้าพร้อมจัดส่งทันที' : 'สินค้าหมดชั่วคราว' }}
                      </span>
                    </div>
                    <div class="action-flex-group" v-if="selectedProduct.stock > 0">
                      <div class="premium-qty-picker">
                        <button class="qty-control" @click="detailQty--" :disabled="detailQty <= 1">−</button>
                        <input type="number" v-model.number="detailQty" readonly class="qty-display">
                        <button class="qty-control" @click="detailQty++" :disabled="detailQty >= selectedProduct.stock">+</button>
                      </div>
                      <button class="buy-btn-premium" @click="addToCart(selectedProduct, detailQty); goMenu()">
                        <span class="buy-text">เพิ่มลงตะกร้า</span>
                        <span class="buy-icon">🛒</span>
                      </button>
                    </div>
                    <div v-else class="premium-out-of-stock">
                      <i class="fas fa-clock"></i> ขออภัย สินค้านี้จะกลับมาเร็วๆ นี้
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section v-else-if="page === 'cart'">
            <div class="cart-container-vibrant">
              <h2 class="page-heading">
                <span class="heading-icon">🛒</span> ตะกร้าสินค้าของคุณ
              </h2>
              <div class="cart-layout" v-if="cart.length > 0">
                <div class="cart-items-column">
                   <div v-for="(group, center) in groupedCart" :key="center" class="cart-center-group">
                    <div class="center-header-banner">
                      <span class="center-icon">📍</span> ศูนย์: {{ center }}
                    </div>
                    <div class="cart-item-list">
                     <div v-for="(item, i) in group" :key="i" class="cart-item-row-vibrant">
                      <div class="item-thumb">
                        <img
                          v-if="item.image || item.product_image"
                          :src="item.image || item.product_image"
                          alt="สินค้า"
                        />
                        <div v-else class="item-thumb-placeholder">🌿</div>
                      </div>
                      <div class="item-details-v">
                        <div class="item-name-v">{{ item.name }}</div>
                        <div class="item-price-v">฿{{ item.price }} / {{ item.size ? item.size + ' ' : '' }}{{ item.unit }}</div>
                      </div>
                      <div class="item-actions-v">
                        <div class="qty-pill small">
                          <button @click="decreaseQty(item)">−</button>
                          <span>{{ item.qty }}</span>
                          <button @click="increaseQty(item)" :disabled="item.qty >= item.stock">+</button>
                        </div>
                        <div class="item-total-v">
                          ฿{{ Number(item.price * item.qty).toLocaleString() }}
                        </div>
                      </div>
                      <button class="delete-item-btn" @click="deleteItem(item)">×</button>
                     </div>
                    </div>
                  </div>
                </div>
                 <div class="cart-summary-column">
                  <div class="summary-card-vibrant">
                    <h3 class="summary-title">สรุปคำสั่งซื้อ</h3>
                    <div class="shipping-form-vibrant">
                      <h4>🚚 ข้อมูลจัดส่ง (กรุณากรอกให้ครบถ้วน)</h4>

                      <div class="input-group-v">
                        <input v-model="customerName" class="vibrant-input full" placeholder="👤 ชื่อ-นามสกุล ผู้รับ" />
                        <input v-model="customerPhone" class="vibrant-input full" placeholder="📱 เบอร์โทรศัพท์ (10 หลัก)" maxlength="10" />
                      </div>

                      <div class="input-group-v" style="margin-bottom: 12px;">
                        <textarea
                          v-model="addrDetail"
                          class="vibrant-input full textarea-v"
                          placeholder="📍 บ้านเลขที่, หมู่, อาคาร, ซอย, ถนน..."
                          rows="2"
                        ></textarea>
                      </div>

                      <div class="input-group-v">
                        <input v-model="addrSubDistrict" class="vibrant-input full" placeholder="ตำบล / แขวง" />
                        <input v-model="addrDistrict" class="vibrant-input full" placeholder="อำเภอ / เขต" />
                      </div>

                      <div class="input-group-v">
                        <input v-model="addrProvince" class="vibrant-input full" placeholder="จังหวัด" />
                        <input v-model="addrZipCode" class="vibrant-input full" placeholder="รหัสไปรษณีย์" maxlength="5" />
                      </div>

                    </div>

                    <div class="summary-totals">
                      <div class="total-row">
                        <span>ค่าสินค้าทั้งหมด</span>
                        <span>฿{{ Number(total).toLocaleString() }}</span>
                      </div>

                      <div class="total-row" style="color: #00b043; font-weight: bold;">
                        <span>ค่าจัดส่ง (คำนวณตามน้ำหนัก)</span>
                        <span>฿{{ Number(shippingFee).toLocaleString() }}</span>
                      </div>

                      <div class="total-divider"></div>

                      <div class="total-row grand-total-v">
                        <span>ยอดชำระรวมทั้งสิ้น</span>
                        <span class="grand-price">฿{{ Number(grandTotal).toLocaleString() }}</span>
                      </div>
                    </div>

                    <button class="checkout-btn-vibrant" :disabled="!canCheckout || isSubmitting" @click="checkout">
                      <span v-if="isSubmitting">⏳ กำลังประมวลผล...</span>
                      <span v-else>💳 สร้างคำสั่งซื้อ</span>
                    </button>

                    <p v-if="!canCheckout" class="error-tip">
                      * กรุณากรอกข้อมูลให้ครบทุกช่อง และเบอร์โทร 10 หลัก
                    </p>

                  </div>
                </div>
              </div>
               <div v-else class="empty-cart-vibrant">
                <div class="empty-icon-v">🍂</div>
                <p>ตะกร้าสินค้ายังว่างอยู่เลย</p>
                <button class="go-shop-btn-v" @click="goMenu">กลับไปเลือกสินค้า</button>
              </div>
            </div>
          </section>

          <section v-else-if="page === 'payment' && currentOrder">
            <div class="payment-container-vibrant">
              <h2 class="page-heading center">💳 ชำระเงิน</h2>
              <div class="payment-card-vibrant">
                <div class="bank-header">
                  <div class="bank-icon-kbank">K</div>
                  <div class="bank-details">
                    <h3>ธนาคารกสิกรไทย</h3>
                    <p>สาขามหาวิทยาลัย...</p>
                  </div>
                </div>
                <div class="account-box-vibrant">
                  <p class="acc-name">ชื่อบัญชี: สำนักวิจัยและส่งเสริมวิชาการการเกษตร</p>
                  <div class="acc-number-row" @click="copyText('123-4-56789-0')">
                    <span class="acc-num">123-4-56789-0</span>
                    <span class="copy-tag">คัดลอก</span>
                  </div>
                </div>
                <div class="payment-amount-box">
                  <span class="label">ยอดที่ต้องโอน</span>
                  <span class="amount">฿{{ Number(currentOrder.total).toLocaleString() }}</span>
                </div>
              </div>
               <div class="upload-section-vibrant">
                 <h3>🧾 แจ้งชำระเงิน (อัปโหลดสลิป)</h3>
                <label class="upload-area-vibrant" :class="{'uploaded': slipUploaded, 'disabled': isUploading}">
                  <input type="file" class="hidden-file-input" @change="uploadSlip" accept=".jpg,.jpeg,.png" :disabled="isUploading">
                  <div class="upload-placeholder">
                    <span class="upload-icon-v" v-if="isUploading">⏳</span>
                    <span class="upload-icon-v" v-else>{{ slipUploaded ? '✅' : '📤' }}</span>
                    <span class="upload-text" v-if="isUploading">กำลังอัปโหลด...</span>
                    <span class="upload-text" v-else>{{ slipUploaded ? 'ได้รับสลิปแล้ว' : 'คลิกเพื่อเลือกรูปภาพสลิป' }}</span>
                  </div>
                </label>
                <p v-if="slipError" class="error-msg-v">{{ slipError }}</p>
              </div>
              <button class="confirm-payment-btn-vibrant" @click="confirmPayment">
                ยืนยันการแจ้งชำระเงิน
              </button>
            </div>
          </section>

          <section v-else-if="page === 'history'" class="history-container-vibrant">
            <h2 class="page-heading">📜 ประวัติคำสั่งซื้อ</h2>
            <div v-if="orders.length === 0" class="empty-history-v">
              <div class="empty-icon-v">📭</div>
              <p>ไม่พบประวัติคำสั่งซื้อ</p>
            </div>
            <div v-else>
              <div class="history-list-v">
                <div v-for="order in paginatedOrders" :key="order.id" class="history-card-vibrant">
                  <div class="history-card-header">
                    <div class="order-id-box">
                      <span class="label">หมายเลขคำสั่งซื้อ</span>
                      <span class="val">#{{ order.order_no }}</span>
                    </div>
                    <div class="order-date">{{ formatDate(order.created_at) }}</div>
                  </div>
                  <div class="history-card-body">
                    <div class="status-section">
                      <span class="status-badge-v" :class="order.status">
                        {{ statusText(order.status) }}
                      </span>
                    </div>
                    <div class="total-section-v">
                      <span class="label">ยอดรวม</span>
                      <span class="val">฿{{ Number(order.total).toLocaleString() }}</span>
                    </div>
                  </div>
                  <div class="history-card-footer">
                    <button v-if="['pending', 'waiting_payment'].includes(order.status)" class="track-btn-vibrant" @click="continuePayment(order)">
                      💳 ชำระเงิน
                    </button>
                    <button v-else-if="['verifying', 'paid', 'shipped'].includes(order.status)" class="track-btn-vibrant" @click="viewTracking(order)">
                      🚚 ติดตามสถานะ
                    </button>
                    <button v-else class="track-btn-vibrant" disabled style="opacity: 0.5; cursor: not-allowed; border-color: #ef4444; color: #ef4444; background: transparent;">
                      ❌ ถูกยกเลิก
                    </button>
                  </div>
                </div>
              </div>
              <div class="pagination-container" v-if="totalPages > 1">
                <button
                  class="page-btn"
                  :disabled="currentPage === 1"
                  @click="changePage(currentPage - 1)">
                  &laquo; ก่อนหน้า
                </button>
                <div class="page-numbers">
                  <button
                    v-for="pageNum in totalPages"
                    :key="pageNum"
                    class="page-number-btn"
                    :class="{ active: currentPage === pageNum }"
                    @click="changePage(pageNum)">
                    {{ pageNum }}
                  </button>
                </div>
                <button
                  class="page-btn"
                  :disabled="currentPage === totalPages"
                  @click="changePage(currentPage + 1)">
                  ถัดไป &raquo;
                </button>
              </div>
            </div>
          </section>

          <section v-else-if="page === 'tracking'" class="tracking-container-vibrant">
            <div class="tracking-card-main" v-if="selectedTracking">
              <div class="tracking-header-v">
                <div class="icon-box-v">📦</div>
                <h2>สถานะการจัดส่ง</h2>
                <p>ออเดอร์ #{{ selectedTracking.order_no }}</p>
              </div>
              <div class="tracking-number-display">
                <span class="label">เลขพัสดุ (Tracking No.)</span>
                <div class="number-box" :class="{'has-track': selectedTracking.tracking_no}">
                  {{ selectedTracking.tracking_no || 'กำลังดำเนินการ...' }}
                </div>
              </div>
              <div class="vibrant-timeline">
                <div class="timeline-item done">
                  <div class="timeline-marker">📝</div>
                  <div class="timeline-content">
                    <h4>รับคำสั่งซื้อ</h4>
                    <p>ระบบได้รับข้อมูลการสั่งซื้อแล้ว</p>
                  </div>
                </div>
                 <div class="timeline-item" :class="{ done: ['verifying', 'paid', 'shipped'].includes(selectedTracking.status) }">
                  <div class="timeline-marker">⏳</div>
                  <div class="timeline-content">
                    <h4>รอตรวจสอบสลิป</h4>
                    <p>ระบบได้รับสลิปแล้ว กำลังรอแอดมินตรวจสอบ</p>
                  </div>
                </div>
                 <div class="timeline-item" :class="{ done: ['paid', 'shipped'].includes(selectedTracking.status) }">
                  <div class="timeline-marker">💰</div>
                  <div class="timeline-content">
                    <h4>ยืนยันการชำระเงิน</h4>
                    <p>ตรวจสอบสลิปและยืนยันยอดเงินสำเร็จ</p>
                  </div>
                </div>
                 <div class="timeline-item" :class="{ done: selectedTracking.tracking_no || selectedTracking.status === 'shipped' }">
                  <div class="timeline-marker">🚚</div>
                  <div class="timeline-content">
                    <h4>จัดส่งแล้ว</h4>
                    <p>พัสดุถูกส่งมอบให้บริษัทขนส่ง</p>
                  </div>
                </div>
              </div>
              <button class="back-to-history-btn-v" @click="goHistory">
                ← กลับไปหน้าประวัติ
              </button>
            </div>
          </section>

          <section v-else-if="page === 'waiting'" class="success-container-vibrant">
            <div class="success-card-vibrant animated-success">
              <div class="success-icon-box">
                <span class="checkmark">✓</span>
              </div>
              <h2>บันทึกรายการสำเร็จ!</h2>
              <p class="success-desc">
                ขอบคุณสำหรับการสั่งซื้อ<br>
                ระบบได้รับข้อมูลการแจ้งชำระเงินแล้ว<br>
                กรุณารอเจ้าหน้าที่ตรวจสอบความถูกต้อง
              </p>
              <div class="order-summary-box-v" v-if="currentOrder">
                <div class="row"><span>เลขที่คำสั่งซื้อ</span> <b>#{{ currentOrder.order_no }}</b></div>
                <div class="row total"><span>ยอดโอนแจ้งชำระ</span> <b class="grand-v">฿{{ Number(currentOrder.total).toLocaleString() }}</b></div>
              </div>
              <div class="success-actions-v">
                <button class="btn-outline-v" @click="goHistory">📜 ดูประวัติคำสั่งซื้อ</button>
                <button class="btn-primary-v" @click="goMenu">🛒 กลับไปหน้าแรก</button>
              </div>
            </div>
          </section>

        </div>
      </div>
      <FooterFarm class="page-footer relative z-10" />
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import FooterFarm from '@/Components/FooterFarm.vue'
import axios from 'axios'

const page = ref('menu')
const searchKeyword = ref('')
const selectedProduct = ref(null)
const detailQty = ref(1)
const slipUploaded = ref(false)
const slipError = ref('')
const selectedTracking = ref(null)

const isSubmitting = ref(false)
const isUploading = ref(false)

const currentPage = ref(1)
const itemsPerPage = 6

const props = defineProps({
  menuList: Array,
  promoBanners: Array
})

const menuList = ref(props.menuList || [])
const promoBanners = ref(props.promoBanners || [])

const currentSlide = ref(0)
let slideInterval

const filteredCategories = computed(() => {
  if (!searchKeyword.value) return menuList.value
  const key = searchKeyword.value.toLowerCase()
  return menuList.value.map(cat => ({
    ...cat,
    items: (cat.items || []).filter(i =>
      (i.name || '').toLowerCase().includes(key)
    )
  })).filter(c => c.items.length)
})

const cart = ref([])
const orders = ref([])
const currentOrder = ref(null)

// --- ปรับปรุงข้อมูลที่อยู่ให้ละเอียดขึ้น ---
const customerName = ref('')
const customerPhone = ref('')
const addrDetail = ref('')      // บ้านเลขที่ หมู่ ซอย ถนน
const addrSubDistrict = ref('') // ตำบล
const addrDistrict = ref('')    // อำเภอ
const addrProvince = ref('')    // จังหวัด
const addrZipCode = ref('')     // รหัสไปรษณีย์
// ------------------------------------

const shippingFee = ref(0)

watch(cart, () => {
  calculateShipping()
}, { deep: true })

const total = computed(() =>
  cart.value.reduce((sum, i) => sum + (Number(i.price) * Number(i.qty)), 0)
)

const grandTotal = computed(() => {
  return Number(total.value) + Number(shippingFee.value)
})

const totalQty = computed(() =>
  cart.value.reduce((sum, i) => sum + Number(i.qty), 0)
)

// --- อัปเดตเงื่อนไขให้บังคับกรอกทุกช่อง ---
const canCheckout = computed(() =>
  customerName.value &&
  customerPhone.value.length === 10 &&
  addrDetail.value &&
  addrSubDistrict.value &&
  addrDistrict.value &&
  addrProvince.value &&
  addrZipCode.value.length === 5 &&
  cart.value.length > 0
)

const groupedCart = computed(() => {
  const groups = {}
  cart.value.forEach(item => {
    const key = item.category || 'สินค้าอื่นๆ'
    if (!groups[key]) groups[key] = []
    groups[key].push(item)
  })
  return groups
})

const calculateShipping = async () => {
  if (cart.value.length === 0) {
    shippingFee.value = 0
    return
  }
  try {
    const payload = cart.value.map(i => ({ id: i.id, qty: i.qty }))
    const res = await axios.post('/api/calculate-shipping', { items: payload })
    shippingFee.value = res.data.shipping_fee || 0
  } catch (e) {
    shippingFee.value = 40
  }
}

watch(cart, () => {
  calculateShipping()
}, { deep: true })

const selectProduct = (item) => {
  selectedProduct.value = item
  detailQty.value = 1
  page.value = 'detail'
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const addToCart = (p, q) => {
  if (p.stock <= 0) return alert('สินค้าหมด')
  if (q <= 0) return alert('จำนวนต้องมากกว่า 0')
  const exist = cart.value.find(i => i.id === p.id)
  if (exist) {
    exist.qty = Math.min(exist.qty + q, p.stock)
  } else {
    cart.value.push({
      id: p.id,
      name: p.name,
      price: Number(p.price),
      qty: Math.min(q, p.stock),
      size: p.size,
      unit: p.unit,
      stock: p.stock,
      category: p.category,
      image: p.image || null,
      product_image: p.product_image || null
    })
  }
}

const increaseQty = (item) => item.qty < item.stock && item.qty++
const decreaseQty = (item) => item.qty > 1 ? item.qty-- : deleteItem(item)
const deleteItem = (item) => cart.value = cart.value.filter(i => i.id !== item.id)

const checkout = async () => {
  if (!canCheckout.value) return alert('กรอกข้อมูลไม่ครบ หรือเบอร์โทรไม่ครบ 10 หลัก')
  if (isSubmitting.value) return
  isSubmitting.value = true
  try {
    // --- รวมช่องต่างๆ ให้กลายเป็นข้อความยาวๆ เพื่อส่งไปบันทึก ---
    const fullAddress = `${addrDetail.value} ต.${addrSubDistrict.value} อ.${addrDistrict.value} จ.${addrProvince.value} ${addrZipCode.value}`

    const res = await axios.post('/orders', {
      items: cart.value.map(i => ({ id: i.id, qty: i.qty })),
      shipping_address: fullAddress, // <--- ส่งข้อความที่รวมแล้ว
      customer_name: customerName.value,
      customer_phone: customerPhone.value
    })
    currentOrder.value = res.data

    slipUploaded.value = false
    page.value = 'payment'
  } catch (e) {
    if(e.response && e.response.data && e.response.data.error) {
       alert(e.response.data.error)
    } else {
       alert('สั่งซื้อไม่สำเร็จ')
    }
  } finally {
    isSubmitting.value = false
  }
}

const loadOrders = async () => {
  try {
    const res = await axios.get('/orders')
    let data = res.data.data ? res.data.data : res.data
    data.sort((a, b) => b.id - a.id)
    orders.value = data
    currentPage.value = 1
  } catch (e) {
    orders.value = []
  }
}

const totalPages = computed(() => Math.ceil(orders.value.length / itemsPerPage))

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return orders.value.slice(start, start + itemsPerPage)
})

const changePage = (p) => {
  if (p >= 1 && p <= totalPages.value) {
    currentPage.value = p
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const viewTracking = (order) => {
  selectedTracking.value = order
  page.value = 'tracking'
}

const continuePayment = (order) => {
  currentOrder.value = order
  slipUploaded.value = false
  page.value = 'payment'
}

const uploadSlip = async (e) => {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
      return slipError.value = 'ขนาดไฟล์เกิน 2MB'
  }
  const ext = file.name.split('.').pop().toLowerCase()
  if (!['jpg', 'jpeg', 'png'].includes(ext)) {
      return slipError.value = 'รองรับเฉพาะ JPG และ PNG'
  }

  if (isUploading.value) return
  isUploading.value = true
  slipError.value = ''

  try {
    const formData = new FormData()
    formData.append('slip', file)
    await axios.post(`/orders/${currentOrder.value.id}/upload-slip`, formData)
    slipUploaded.value = true
  } catch (e) {
    if(e.response && e.response.data && e.response.data.error) {
       slipError.value = e.response.data.error
       alert(e.response.data.error)
       loadOrders()
       page.value = 'history'
    } else {
       slipError.value = 'อัปโหลดไม่สำเร็จ'
    }
  } finally {
    isUploading.value = false
  }
}

const confirmPayment = () => {
  if (!slipUploaded.value) return alert('อัปโหลดสลิปก่อน')
  cart.value = []
  page.value = 'waiting'
}

const copyText = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    alert('คัดลอกเลขบัญชีแล้ว');
  });
}

const goMenu = () => page.value = 'menu'
const goCart = () => page.value = 'cart'

const goHistory = async () => {
  await loadOrders()
  page.value = 'history'
}

const statusText = (status) => {
  if (status === 'pending') return 'รอชำระเงิน'
  if (status === 'waiting_payment') return 'กำลังรอสลิป'
  if (status === 'verifying') return 'กำลังตรวจสอบ'
  if (status === 'paid') return 'ชำระเงินแล้ว ✅'
  if (status === 'shipped') return 'จัดส่งแล้ว 🚚'
  if (status === 'cancelled') return 'ยกเลิก (สินค้าหมด) ❌'
  return status
}

const formatDate = (date) => {
  if(!date) return ''
  return new Date(date).toLocaleString('th-TH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }) + ' น.'
}

onMounted(() => {
  loadOrders()
  slideInterval = setInterval(() => {
    if (promoBanners.value.length) {
      currentSlide.value =
        (currentSlide.value + 1) % promoBanners.value.length
    }
  }, 5000)
})

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval)
})

</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap');

:root {
  --grad-primary: linear-gradient(135deg, #00b043, #96c93d);
  --grad-primary-hover: linear-gradient(135deg, #009e00, #85b633);
  --grad-accent: linear-gradient(135deg, #38ef7d, #119933);
  --grad-secondary: linear-gradient(135deg, #ff9966, #ff5e62);
  --grad-secondary-hover: linear-gradient(135deg, #e88a5a, #e84e52);
  --grad-gold: linear-gradient(135deg, #fce38a, #f38181);
  --bg-main: #f8fafc;
  --bg-card: #ffffff;
  --text-heading: #1a202c;
  --text-body: #4a5568;
  --text-light: #a0aec0;
  --text-on-primary: #ffffff;
  --color-price: #ff5e62;
  --color-success: #38ef7d;
  --color-danger: #ff5e62;
  --color-warning: #fce38a;
  --shadow-sm-v: 0 2px 8px rgba(0, 176, 155, 0.1);
  --shadow-md-v: 0 8px 24px rgba(0, 176, 155, 0.15);
  --shadow-lg-v: 0 16px 32px rgba(0, 176, 155, 0.2);
  --shadow-glow-primary: 0 0 20px rgba(0, 176, 155, 0.4);
  --shadow-glow-secondary: 0 0 20px rgba(255, 94, 98, 0.4);
  --radius-sm: 8px;
  --radius-md: 16px;
  --radius-lg: 24px;
  --radius-pill: 50px;
}

* {
  font-family: 'Sarabun', sans-serif;
  box-sizing: border-box;
}

.bg-pattern {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background-color: var(--bg-main);
    background-image:
        radial-gradient(at 10% 20%, hsla(146,68%,85%,1) 0px, transparent 50%),
        radial-gradient(at 90% 80%, rgb(230, 253, 201) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgb(217, 252, 220) 0px, transparent 50%);
    background-attachment: fixed;
    background-size: cover;
    z-index: 0;
    opacity: 0.6;
    pointer-events: none;
}

.page-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
  color: var(--text-body);
}

.container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 20px;
}

.sticky-header {
  position: sticky;
  top: 16px;
  z-index: 100;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(16, 185, 129, 0.25);
  box-shadow:
    0 10px 25px rgba(0, 0, 0, 0.08),
    0 4px 10px rgba(16, 185, 129, 0.10);
  border-radius: 16px;
  padding: 14px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  transition: all 0.25s ease;
}

.sticky-header:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(16, 185, 129, 0.4);
  box-shadow:
    0 16px 35px rgba(0, 0, 0, 0.12),
    0 6px 15px rgba(16, 185, 129, 0.15);
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  margin: 0;
}

.logo-icon-box {
  font-size: 26px;
  color: #10b981;
}

.logo-text {
  font-size: 22px;
  font-weight: 800;
  color: #064e3b;
  letter-spacing: -0.5px;
}

.search-container {
  flex: 1;
  max-width: 450px;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 18px;
}

.vibrant-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border-radius: 999px;
  border: 1.5px solid rgba(16, 185, 129, 0.3);
  background: #ffffff;
  font-size: 15px;
  color: #1e293b;
  transition: all 0.25s ease;
}

.vibrant-input::placeholder {
  color: #94a3b8;
}

.vibrant-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}

.header-actions {
  display: flex;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 999px;
  border: none;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.history-btn {
  background: #ffffff;
  color: #1e293b;
  border: 1px solid rgba(0,0,0,0.05);
}

.history-btn:hover {
  background: #f8fafc;
  transform: translateY(-2px);
}

.vibrant-cart-btn {
  position: relative;
  background: #10b981;
  color: #ffffff;
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.35);
}

.vibrant-cart-btn:hover {
  background: #059669;
  transform: translateY(-2px);
}

.cart-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  min-width: 20px;
  height: 20px;
  padding: 0 5px;
  border-radius: 999px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #ffffff;
  font-size: 11px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #ffffff;
  box-shadow:
    0 3px 8px rgba(239, 68, 68, 0.35);
  transition: all 0.2s ease;
}

.vibrant-cart-btn:hover .cart-badge {
  transform: scale(1.15);
}

@keyframes popBadge {
  0% { transform: scale(0.6); opacity: 0; }
  60% { transform: scale(1.2); opacity: 1; }
  100% { transform: scale(1); }
}

.cart-badge.animate {
  animation: popBadge 0.35s ease;
}

.promo-section {
    margin-top: 24px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md-v);
    position: relative;
}
.promo-track { display: flex; transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1); }
.premium-slide {
    flex: 0 0 100%;
    height: 320px;
    position: relative;
}
.premium-slide img {
    width: 100%; height: 100%; object-fit: cover;
}
.promo-gradient {
    position: absolute;
    bottom: 0; left: 0; width: 100%; height: 50%;
    background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
}

.category-banner-royal {
    display: flex;
    align-items: center;
    padding: 15px 40px;
    border-radius: 20px;
    margin-bottom: 30px;
    background: radial-gradient(circle at top left, #065f46, #064e3b 70%);
    box-shadow:
        0 15px 30px rgba(0, 0, 0, 0.25),
        inset 0 0 0 1.5px #d4af37,
        inset 0 0 20px rgba(212, 175, 55, 0.2);
    position: relative;
    overflow: hidden;
    transition: all 0.8s cubic-bezier(0.2, 1, 0.2, 1);
}

.category-banner-royal:hover {
    transform: translateY(-12px) scale(1.01);
    box-shadow:
        0 35px 60px rgba(0, 0, 0, 0.4),
        inset 0 0 0 3px #f3d47a,
        inset 0 0 40px rgba(212, 175, 55, 0.5);
}

.royal-aura {
    position: absolute;
    inset: -50%;
    background: conic-gradient(
        from 0deg at 50% 50%,
        transparent 0deg,
        rgba(52, 211, 153, 0.15) 60deg,
        rgba(16, 185, 129, 0.25) 120deg,
        transparent 180deg
    );
    filter: blur(50px);
    animation: rotateAura 15s linear infinite;
    z-index: 1;
}

@keyframes rotateAura {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.lanna-pattern-overlay {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0c16.568 0 30 13.432 30 30s-13.432 30-30 30S0 46.568 0 30 13.432 0 30 0zm0 10C18.954 10 10 18.954 10 30s8.954 20 20 20 20-8.954 20-20S41.046 10 30 10z' fill='%23ffffff' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    z-index: 2;
    opacity: 0.6;
}

.banner-content {
    display: flex;
    align-items: center;
    gap: 35px;
    z-index: 10;
}

.royal-icon-frame {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #d4af37, #f3d47a);
    border-radius: 18px;
    padding: 2.5px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
    transition: all 0.5s ease;
}

.glass-inner {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
    background: rgba(6, 78, 59, 0.9);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.royal-svg-icon {
    width: 36px;
    height: 36px;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.4));
    z-index: 2;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.icon-shine {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 0%,
        rgba(255, 255, 255, 0) 45%,
        rgba(255, 255, 255, 0.4) 50%,
        rgba(255, 255, 255, 0) 55%,
        transparent 100%
    );
    z-index: 1;
    transform: rotate(45deg);
    animation: iconShine 4s infinite;
}

@keyframes iconShine {
    0% { transform: translateX(-100%) rotate(45deg); }
    20%, 100% { transform: translateX(100%) rotate(45deg); }
}

.title-stack {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.royal-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(212, 175, 55, 0.2);
    padding: 3px 12px;
    border-radius: 50px;
    border: 1px solid rgba(212, 175, 55, 0.4);
    width: fit-content;
}

.badge-text {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #f3d47a;
}

.banner-title {
    margin: 0;
    font-size: 28px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.1;
    text-shadow: 0 0 25px rgba(212, 175, 55, 0.4), 0 4px 10px rgba(0,0,0,0.5);
}

.title-underline {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #d4af37, transparent);
    border-radius: 10px;
    transition: width 0.6s ease;
}

.category-banner-royal:hover .title-underline {
    width: 120px;
}

.corner-flare {
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.25) 0%, transparent 70%);
    filter: blur(25px);
    z-index: 5;
    pointer-events: none;
}

.flare-1 { top: -60px; right: -60px; }
.flare-2 { bottom: -80px; left: 5%; opacity: 0.6; }

@media (max-width: 768px) {
    .category-banner-royal {
        padding: 20px 25px;
        border-radius: 15px;
    }
    .banner-content { gap: 20px; }
    .royal-icon-frame { width: 50px; height: 50px; }
    .banner-title { font-size: 22px; }
}

.market-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 32px;
    padding: 10px;
}

.vibrant-card {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.03);
    box-shadow:
        0 10px 20px rgba(0, 0, 0, 0.02),
        0 4px 6px rgba(0, 0, 0, 0.02);
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    display: flex;
    flex-direction: column;
    position: relative;
    height: 100%;
}

.vibrant-card:hover {
    transform: translateY(-12px);
    box-shadow:
        0 30px 60px -12px rgba(6, 78, 59, 0.15),
        inset 0 0 0 1.5px rgba(212, 175, 55, 0.3);
    border-color: transparent;
}

.vibrant-card::after {
    content: '';
    position: absolute;
    top: 0; left: -150%;
    width: 50%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transform: skewX(-25deg);
    transition: 0.75s;
    z-index: 5;
}
.vibrant-card:hover::after {
    left: 150%;
}

.img-box {
    height: 240px;
    background: #f8fafc;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.img-box img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.vibrant-card:hover .img-box img {
    transform: scale(1.1);
}

.stock-badge {
    position: absolute;
    top: 15px; left: 15px;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 11px; font-weight: 800;
    color: white; z-index: 2;
    backdrop-filter: blur(8px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.stock-badge.out { background: rgba(239, 68, 68, 0.85); }
.stock-badge.low { background: rgba(245, 158, 11, 0.85); color: white; }

.product-info {
    padding: 24px;
    display: flex; flex-direction: column; flex: 1;
    background: linear-gradient(to bottom, #ffffff, #fafafa);
}

.product-cat-tag {
    font-size: 11px;
    font-weight: 700;
    color: #059669;
    background: rgba(16, 185, 129, 0.08);
    padding: 4px 12px;
    border-radius: 50px;
    display: inline-block;
    margin-bottom: 10px;
    width: fit-content;
    border: 1px solid rgba(16, 185, 129, 0.1);
}

.product-name {
    font-size: 18px;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 8px 0;
    line-height: 1.4;
    cursor: pointer;
    transition: color 0.3s;
}
.vibrant-card:hover .product-name { color: #064e3b; }

.product-unit { font-size: 14px; color: #94a3b8; margin-bottom: 18px; }

.price-stock-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px;
}

.vibrant-price {
    font-size: 26px;
    font-weight: 900;
    color: #065f46;
    letter-spacing: -0.5px;
}
.vibrant-price::before {
    content: '฿';
    font-size: 16px; margin-right: 2px; font-weight: 700;
}

.stock-text {
    font-size: 12px; font-weight: 700;
    color: #64748b; background: #f1f5f9;
    padding: 4px 10px; border-radius: 8px;
}

.card-actions-vibrant {
    margin-top: auto;
    display: flex; gap: 12px;
    align-items: center;
}

.qty-pill {
    display: flex; align-items: center;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 3px;
    transition: border-color 0.3s;
}
.vibrant-card:hover .qty-pill { border-color: #cbd5e0; }

.qty-pill button {
    width: 32px; height: 32px;
    border-radius: 12px; border: none;
    background: transparent; color: #1e293b;
    font-size: 18px; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
}
.qty-pill button:hover:not(:disabled) {
    background: #f1f5f9; color: #059669;
}
.qty-num {
    font-weight: 800; font-size: 16px;
    min-width: 32px; text-align: center;
    color: #1e293b;
}

.add-btn-vibrant {
    flex: 1;
    height: 46px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #ffffff;
    font-weight: 800; font-size: 14px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
    display: flex; align-items: center; justify-content: center; gap: 8px;
}

.add-btn-vibrant:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.6);
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
}

.add-btn-vibrant:active { transform: scale(0.95); }

.add-btn-vibrant:disabled {
    background: #e2e8f0; color: #94a3b8;
    box-shadow: none; cursor: not-allowed;
}

.add-btn-vibrant {
    position: relative;
    overflow: hidden;
}
.add-btn-vibrant::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
}
.add-btn-vibrant:hover::before {
    left: 100%;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fadeInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards; }

.premium-detail-container {
  max-width: 1100px;
  margin: 40px auto;
  padding: 0 20px;
}

.glass-back-btn {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(0,0,0,0.05);
  padding: 12px 24px;
  border-radius: 50px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 24px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}
.glass-back-btn:hover {
  background: #000;
  color: #fff;
  transform: translateX(-5px);
}

.product-master-card {
  background: #fff;
  border-radius: 35px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  overflow: hidden;
  box-shadow: 0 30px 60px rgba(0,0,0,0.08);
}

.product-visual-section {
  background: #f8fbfa;
  padding: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.main-image-viewport {
  width: 100%;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.product-hero-img {
  width: 100%;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1));
}
.product-visual-section:hover .main-image-viewport {
  transform: scale(1.05) translateY(-10px);
}

.floating-badge {
  position: absolute;
  top: 30px;
  left: 30px;
  z-index: 5;
  padding: 10px 20px;
  border-radius: 15px;
  font-weight: 800;
  font-size: 12px;
  letter-spacing: 1px;
  color: #fff;
}
.floating-badge.hot { background: linear-gradient(45deg, #ff416c, #ff4b2b); }
.floating-badge.out { background: #2d3436; }

.product-info-section {
  padding: 60px;
  background: white;
}
.premium-category-tag {
  color: #00b09b;
  font-weight: 800;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 2px;
}
.premium-title {
  font-size: 42px;
  font-weight: 900;
  color: #1a1a1a;
  margin: 10px 0;
  line-height: 1.1;
}
.premium-rating {
  font-size: 14px;
  margin-bottom: 30px;
}
.review-count { color: #a0aec0; font-weight: 400; margin-left: 5px; }

.premium-price-row {
  margin-bottom: 30px;
  display: flex;
  align-items: baseline;
  gap: 5px;
}
.currency { font-size: 24px; font-weight: 700; color: #205a33; }
.price-amount { font-size: 54px; font-weight: 900; color: #205a33; letter-spacing: -2px; }
.price-unit { font-size: 18px; color: #718096; }

.fancy-divider {
  width: 100%;
  height: 1px;
  background: #eee;
  margin: 40px 0;
  position: relative;
}
.fancy-divider span {
  position: absolute;
  left: 0;
  top: -1px;
  width: 60px;
  height: 3px;
  background: #00b09b;
  border-radius: 10px;
}

.product-description-box h3 { font-size: 18px; margin-bottom: 15px; color: #1a1a1a; }
.product-description-box p { color: #4a5568; line-height: 1.8; font-size: 16px; margin-bottom: 40px; }

.purchase-action-card {
  background: #f9fbfb;
  padding: 30px;
  border-radius: 25px;
  border: 1px solid #edf2f7;
}

.stock-status-line {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 25px;
}
.pulse-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}
.pulse-dot.online {
  background: #2ecc71;
  box-shadow: 0 0 10px #2ecc71;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0% { transform: scale(1); opacity: 1; }
  100% { transform: scale(3); opacity: 0; }
}

.action-flex-group { display: flex; gap: 15px; }

.premium-qty-picker {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 15px;
  padding: 5px;
  border: 1px solid #e2e8f0;
}
.qty-control {
  width: 45px;
  height: 45px;
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
  transition: 0.2s;
  border-radius: 10px;
}
.qty-control:hover:not(:disabled) { background: #f7fafc; }
.qty-display {
  width: 50px;
  text-align: center;
  border: none;
  font-size: 18px;
  font-weight: 800;
  outline: none;
}

.buy-btn-premium {
  flex: 1;
  background: #1a1a1a;
  color: #fff;
  border: none;
  border-radius: 18px;
  padding: 0 30px;
  font-size: 18px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  position: relative;
  overflow: hidden;
}
.buy-btn-premium:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
  background: #000;
}

@media (max-width: 900px) {
  .product-master-card { grid-template-columns: 1fr; }
  .product-info-section { padding: 35px; }
  .premium-title { font-size: 32px; }
}

.cart-container-vibrant { max-width: 1100px; margin: 20px auto; }
.page-heading {
    font-size: 28px; font-weight: 800; color: var(--text-heading);
    margin-bottom: 32px; display: flex; align-items: center; gap: 12px;
}
.heading-icon { font-size: 32px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); }

.cart-layout { display: grid; grid-template-columns: 3fr 2fr; gap: 32px; }

.cart-center-group {
    background: white; border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm-v); overflow: hidden; margin-bottom: 24px;
}
.center-header-banner {
    background: var(--grad-primary); color: white;
    padding: 12px 20px; font-weight: 700; font-size: 16px;
    display: flex; align-items: center; gap: 10px;
}
.cart-item-row-vibrant {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 20px; border-bottom: 1px solid #f1f5f9;
    position: relative;
}
.item-thumb-placeholder {
    width: 64px; height: 64px; background: #f1f5f9;
    border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;
    font-size: 24px;
}
.item-details-v { flex: 1; }
.item-name-v { font-weight: 700; color: var(--text-heading); margin-bottom: 4px; }
.item-price-v { font-size: 13px; color: var(--text-light); }
.item-actions-v { display: flex; align-items: center; gap: 24px; }
.qty-pill.small { height: 36px; }
.qty-pill.small button { width: 28px; height: 28px; font-size: 16px; }
.item-total-v {
    font-weight: 800; font-size: 18px;
    color: var(--color-price); min-width: 80px; text-align: right;
}
.delete-item-btn {
    position: absolute; top: 8px; right: 8px;
    background: none; border: none; color: #cbd5e0; font-size: 20px;
    cursor: pointer; transition: 0.2s;
}
.delete-item-btn:hover { color: var(--color-danger); }

.summary-card-vibrant {
    background: white; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md-v); padding: 24px;
    position: sticky; top: 100px;
}
.summary-title { font-size: 20px; font-weight: 800; margin-bottom: 20px; }

.shipping-form-vibrant {
    background: #f8fafc;
    padding: 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px; border: 1px solid #e2e8f0;
}
.shipping-form-vibrant h4 { font-size: 15px; font-weight: 700; margin-bottom: 16px; }

.shipping-form-vibrant .vibrant-input {
    border-radius: 4px;
    padding-left: 16px;
}

.input-group-v { display: flex; gap: 12px; margin-bottom: 12px; }
.vibrant-input.full { width: 100%; }
.textarea-v { resize: none; }

.summary-totals { padding: 10px 0; }
.total-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; color: var(--text-body); }
.total-divider { height: 2px; background: #f1f5f9; margin: 16px 0; }
.grand-total-v { align-items: center; }
.grand-total-v span:first-child { font-weight: 700; font-size: 18px; }
.grand-price {
    font-size: 28px; font-weight: 800;
    background: var(--grad-secondary); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}

.checkout-btn-vibrant {
    width: 100%; padding: 16px; border: none; border-radius: var(--radius-pill);
    background: var(--grad-secondary);
    color: rgb(0, 0, 0);
    font-size: 18px; font-weight: 800;
    cursor: pointer; transition: all 0.3s;
    box-shadow: 0 8px 20px rgba(255, 94, 98, 0.4);
}
.checkout-btn-vibrant:hover:not(:disabled) {
    background: var(--grad-secondary-hover); transform: translateY(-3px);
    box-shadow: var(--shadow-glow-secondary);
}
.checkout-btn-vibrant:disabled { background: #e2e8f0; box-shadow: none; cursor: not-allowed; }
.error-tip { color: var(--color-danger); font-size: 13px; text-align: center; margin-top: 12px; font-weight: 600; }

.empty-cart-vibrant {
    text-align: center; padding: 60px 20px; background: white;
    border-radius: var(--radius-lg); box-shadow: var(--shadow-sm-v);
}
.empty-icon-v { font-size: 64px; margin-bottom: 16px; opacity: 0.6; filter: grayscale(0.5); }
.empty-cart-vibrant p { font-size: 18px; color: var(--text-light); margin-bottom: 24px; }
.go-shop-btn-v {
    padding: 12px 32px; border-radius: var(--radius-pill);
    border: 2px solid #00b09b; background: white;
    color: #00b09b; font-weight: 700; cursor: pointer; transition: 0.3s;
}
.go-shop-btn-v:hover { background: #00b09b; color: white; box-shadow: var(--shadow-glow-primary); }

.payment-container-vibrant { max-width: 550px; margin: 40px auto; text-align: center; }
.payment-card-vibrant {
    background: white; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md-v); padding: 32px;
    margin-bottom: 32px; position: relative; overflow: hidden;
}
.payment-card-vibrant::before {
    content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px;
    background: var(--grad-primary);
}
.bank-header { display: flex; align-items: center; gap: 20px; margin-bottom: 32px; text-align: left; }
.bank-icon-kbank {
    width: 64px; height: 64px; background: #138f2d; color: white;
    font-size: 32px; font-weight: 800; display: flex; align-items: center; justify-content: center;
    border-radius: 16px; box-shadow: 0 8px 16px rgba(19, 143, 45, 0.3);
}
.bank-details h3 { font-size: 22px; font-weight: 800; margin: 0 0 4px 0; }
.bank-details p { color: var(--text-light); margin: 0; }

.account-box-vibrant {
    background: #f8fafc; padding: 24px; border-radius: var(--radius-md);
    border: 2px dashed #e2e8f0; margin-bottom: 24px;
}
.acc-name { font-weight: 600; margin-bottom: 16px; color: var(--text-body); }
.acc-number-row {
    display: flex; align-items: center; justify-content: center; gap: 16px;
    background: white; padding: 12px 20px; border-radius: var(--radius-pill);
    box-shadow: var(--shadow-sm-v); cursor: pointer; transition: 0.3s;
}
.acc-number-row:hover { transform: scale(1.03); box-shadow: var(--shadow-md-v); }
.acc-num { font-size: 26px; font-weight: 800; color: #00b09b; letter-spacing: 1px; }
.copy-tag {
    background: #00b09b; color: white; padding: 4px 10px;
    border-radius: var(--radius-pill); font-size: 12px; font-weight: 700;
}

.payment-amount-box {
    background: var(--grad-secondary); padding: 20px; border-radius: var(--radius-md);
    color: rgb(0, 0, 0);
    display: flex; flex-direction: column; gap: 8px;
}
.payment-amount-box .label { font-weight: 600; opacity: 0.9; }
.payment-amount-box .amount { font-size: 36px; font-weight: 800; }

.upload-section-vibrant h3 { font-size: 18px; font-weight: 700; margin-bottom: 16px; }
.upload-area-vibrant {
    display: block; padding: 40px; background: white;
    border: 3px dashed #cbd5e0; border-radius: var(--radius-lg);
    cursor: pointer; transition: all 0.3s;
}
.upload-area-vibrant:hover { border-color: #00b09b; background: #f0fdfa; }
.upload-area-vibrant.uploaded { border-color: #38ef7d; background: #ecfdf5; }
.upload-area-vibrant.disabled { opacity: 0.7; cursor: not-allowed; pointer-events: none; }
.hidden-file-input { display: none; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 12px; }
.upload-icon-v { font-size: 48px; }
.upload-text { font-weight: 600; color: var(--text-body); }
.error-msg-v { color: var(--color-danger); margin-top: 12px; font-weight: 600; }

.confirm-payment-btn-vibrant {
    width: 100%; padding: 18px; margin-top: 32px;
    background: var(--grad-primary);
    color: rgb(0, 0, 0);
    font-size: 20px; font-weight: 800; border: none; border-radius: var(--radius-pill);
    cursor: pointer; transition: 0.3s; box-shadow: var(--shadow-md-v);
}
.confirm-payment-btn-vibrant:hover {
    background: var(--grad-primary-hover); transform: translateY(-3px);
    box-shadow: var(--shadow-glow-primary);
}

.history-container-vibrant { max-width: 700px; margin: 30px auto; }
.empty-history-v { text-align: center; padding: 60px; background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm-v); color: var(--text-light); font-size: 18px; }
.history-card-vibrant {
    background: white; border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm-v); margin-bottom: 24px;
    overflow: hidden; border-left: 6px solid #00b09b;
    transition: 0.3s;
}
.history-card-vibrant:hover { transform: translateY(-4px); box-shadow: var(--shadow-md-v); }
.history-card-header {
    display: flex; justify-content: space-between; padding: 16px 24px;
    background: #f8fafc; border-bottom: 1px solid #f1f5f9;
}
.order-id-box { display: flex; flex-direction: column; }
.order-id-box .label { font-size: 12px; color: var(--text-light); font-weight: 600; }
.order-id-box .val { font-size: 18px; font-weight: 800; color: var(--text-heading); }
.order-date { font-size: 13px; color: var(--text-light); font-weight: 600; }

.history-card-body { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
.status-badge-v { padding: 8px 16px; border-radius: var(--radius-pill); font-weight: 700; font-size: 14px; }
.status-badge-v.paid { background: #d1fae5; color: #065f46; }
.status-badge-v.verifying { background: #fef3c7; color: #92400e; }
.status-badge-v.waiting_payment { background: #fef3c7; color: #92400e; }
.status-badge-v.pending { background: #e2e8f0; color: #475569; }
.status-badge-v.shipped { background: #e0e7ff; color: #1e40af; }
.status-badge-v.cancelled { background: #fecaca; color: #991b1b; }

.total-section-v { text-align: right; }
.total-section-v .label { display: block; font-size: 13px; color: var(--text-light); }
.total-section-v .val { font-size: 22px; font-weight: 800; color: var(--color-price); }

.history-card-footer { padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; }
.track-btn-vibrant {
    width: 100%; padding: 12px; border-radius: var(--radius-pill);
    background: white; border: 2px solid var(--grad-primary);
    border-color: #00b09b; color: #00b09b; font-weight: 800;
    cursor: pointer; transition: 0.3s;
}
.track-btn-vibrant:hover { background: var(--grad-primary); color: white; box-shadow: var(--shadow-glow-primary); }

.pagination-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 32px;
  padding-bottom: 20px;
}
.page-btn {
  padding: 8px 16px;
  background-color: white;
  border: 1px solid #e2e8f0;
  border-radius: var(--radius-pill);
  color: var(--text-heading);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.page-btn:hover:not(:disabled) {
  background: #f1f5f9;
  border-color: #cbd5e0;
}
.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.page-numbers {
  display: flex;
  gap: 8px;
}
.page-number-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid #e2e8f0;
  background-color: white;
  color: var(--text-body);
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.page-number-btn:hover {
  background-color: #f8fafc;
}
.page-number-btn.active {
  background: var(--grad-primary);
  color: white;
  border: none;
  box-shadow: var(--shadow-sm-v);
}

.tracking-container-vibrant { max-width: 600px; margin: 40px auto; }
.tracking-card-main {
    background: white; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg-v); padding: 40px;
}
.tracking-header-v { text-align: center; margin-bottom: 32px; }
.icon-box-v { font-size: 64px; margin-bottom: 16px; filter: drop-shadow(0 8px 16px rgba(0,176,155,0.3)); animation: float 3s ease-in-out infinite; }
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
.tracking-header-v h2 { font-size: 28px; font-weight: 800; margin-bottom: 8px; }
.tracking-header-v p { color: var(--text-light); font-weight: 600; }

.tracking-number-display {
    text-align: center; margin-bottom: 40px;
    background: #f0fdfa; padding: 24px; border-radius: var(--radius-md);
    border: 2px dashed #00b09b;
}
.tracking-number-display .label { display: block; font-weight: 700; color: #00b09b; margin-bottom: 12px; }
.number-box { font-size: 24px; font-weight: 800; color: var(--text-light); letter-spacing: 2px; }
.number-box.has-track { color: var(--text-heading); }

.vibrant-timeline {
  position: relative;
  padding-left: 40px;
  margin-bottom: 40px;
}
.vibrant-timeline::before {
    content: ''; position: absolute; left: 14px; top: 0; bottom: 0;
    width: 4px; background: #e2e8f0; border-radius: 4px;
}
.timeline-item {
  position: relative;
  margin-bottom: 40px;
  opacity: 0.5; transition: 0.5s;
}
.timeline-item.done { opacity: 1; }
.timeline-marker {
    position: absolute;
    left: -40px;
    width: 44px;
    height: 44px;
    background: white; border: 4px solid #e2e8f0; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; z-index: 2; transition: 0.5s;
}
.timeline-item.done .timeline-marker {
    border-color: #00b09b; background: var(--grad-primary); color: white;
    box-shadow: 0 0 0 6px rgba(0, 176, 155, 0.2);
}
.timeline-item.done::before {
    content: ''; position: absolute; left: -38px; top: -32px; bottom: 0;
    width: 4px; background: var(--grad-primary); z-index: 1;
}
.timeline-content {
  padding-left: 16px;
}
.timeline-content h4 { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--text-heading); }
.timeline-content p { color: var(--text-body); font-size: 15px; margin: 0; line-height: 1.5; }

.back-to-history-btn-v {
    width: 100%; padding: 16px; background: #f1f5f9;
    border: none; border-radius: var(--radius-pill); font-weight: 700;
    color: var(--text-body); cursor: pointer; transition: 0.3s;
}
.back-to-history-btn-v:hover { background: #e2e8f0; color: var(--text-heading); }

.item-thumb {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  overflow: hidden;
  background: #f3f4f6;
}

.item-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.success-container-vibrant { display: flex; justify-content: center; padding-top: 60px; }
.success-card-vibrant {
    background: white; padding: 48px; border-radius: 32px;
    box-shadow: var(--shadow-lg-v); text-align: center;
    max-width: 500px; width: 100%;
    animation: popUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes popUp { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
.success-icon-box {
    width: 100px; height: 100px; margin: 0 auto 32px;
    background: var(--grad-primary); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 0 12px rgba(0, 176, 155, 0.15);
    animation: pulseSuccess 2s infinite;
}
@keyframes pulseSuccess { 0% { box-shadow: 0 0 0 0 rgba(0, 176, 155, 0.4); } 70% { box-shadow: 0 0 0 20px rgba(0, 176, 155, 0); } 100% { box-shadow: 0 0 0 0 rgba(0, 176, 155, 0); } }
.checkmark { font-size: 48px; color: white; font-weight: 800; }
.success-card-vibrant h2 { font-size: 32px; font-weight: 800; color: #00b09b; margin-bottom: 16px; }
.success-desc { font-size: 16px; color: var(--text-body); line-height: 1.6; margin-bottom: 32px; }

.order-summary-box-v {
    background: #f8fafc; padding: 24px; border-radius: var(--radius-md);
    text-align: left; margin-bottom: 32px;
}
.order-summary-box-v .row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; }
.order-summary-box-v .row.total { border-top: 2px dashed #e2e8f0; padding-top: 16px; margin-top: 16px; align-items: center; }
.grand-v { font-size: 24px; font-weight: 800; color: var(--color-price); }

.success-actions-v { display: flex; gap: 16px; }
.success-actions-v button { flex: 1; padding: 16px; border-radius: var(--radius-pill); font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.3s; }
.btn-outline-v { background: white; border: 2px solid #00b09b; color: #00b09b; }
.btn-outline-v:hover { background: #f0fdfa; }
.btn-primary-v { background: var(--grad-primary); border: none; color: white; box-shadow: var(--shadow-md-v); }
.btn-primary-v:hover { background: var(--grad-primary-hover); box-shadow: var(--shadow-glow-primary); transform: translateY(-3px); }

@media(max-width: 768px) {
    .sticky-header { padding: 12px 16px; flex-wrap: wrap; gap: 12px; }
    .logo-text { font-size: 18px; }
    .search-container { order: 3; max-width: 100%; margin-top: 4px; }
    .btn-text { display: none; }
    .market-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .vibrant-detail-box { grid-template-columns: 1fr; padding: 20px; }
    .detail-price { font-size: 32px; }
    .cart-layout { grid-template-columns: 1fr; }
    .summary-card-vibrant { position: static; }
    .item-actions-v { flex-direction: column; align-items: flex-end; gap: 12px; }
    .payment-card-vibrant { padding: 24px; }
    .bank-header { flex-direction: column; text-align: center; gap: 12px; }
    .acc-number-row { flex-direction: column; gap: 8px; }
    .acc-num { font-size: 20px; }
    .history-card-body { flex-direction: column; align-items: flex-start; gap: 16px; }
    .total-section-v { text-align: left; }
    .success-card-vibrant { padding: 32px 20px; }
    .success-actions-v { flex-direction: column; }
    .pagination-container { flex-wrap: wrap; }
    .input-group-v { flex-direction: column; gap: 8px; } /* ปรับฟอร์มมือถือให้เรียงลงล่าง */
}
.summary-totals {
  margin-bottom: 20px;
}
.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 16px;
}
.total-divider {
  height: 1px;
  background: #eee;
  margin: 15px 0;
}
.grand-price {
  font-size: 28px;
  font-weight: 900;
  color: #ff5e62;
}
</style>
