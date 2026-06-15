<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Swal from 'sweetalert2'
import { assetUrl } from '@/utils/urls.js'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const logoUrl = computed(() => page.props.logoUrl ?? assetUrl('/build/assets/HeroSection/06.png'))

const isHome = computed(() => page.url === '/')

const logout = () => {
  router.post(route('logout'))
}

const requireLogin = (url) => {
  isMobileMenuOpen.value = false
  if (!user.value) {
    Swal.fire({
      icon: 'warning',
      title: '<span style="font-size: 24px; font-weight: 800; color: #064e3b; font-family: \'Anuphan\', sans-serif;">กรุณาเข้าสู่ระบบก่อน</span>',
      text: 'ต้องสมัครสมาชิกหรือเข้าสู่ระบบก่อนจึงจะใช้งานในส่วนนี้ได้ค่ะ',
      confirmButtonText: 'ไปหน้าเข้าสู่ระบบ',
      cancelButtonText: 'ยกเลิก',
      showCancelButton: true,
      confirmButtonColor: '#eab308',
      cancelButtonColor: '#ef4444',
      background: '#ffffff',
      backdrop: 'rgba(6, 78, 59, 0.7)',
      customClass: {
        popup: 'rounded-3xl shadow-2xl border-t-4 border-yellow-500',
        confirmButton: 'rounded-full px-6 py-2 font-bold text-green-900 shadow-lg shadow-yellow-500/30',
        cancelButton: 'rounded-full px-6 py-2 font-bold'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        router.visit(route('login'))
      }
    })
  } else {
    router.visit(url)
  }
}

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 30
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

watch(() => page.url, () => {
  isMobileMenuOpen.value = false
})

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <div class="min-h-screen app-container">

    <transition name="fade">
      <div
        v-if="isMobileMenuOpen"
        class="mobile-overlay"
        @click="toggleMobileMenu"
      ></div>
    </transition>

    <nav :class="['navbar', { 'nav-scrolled': isScrolled || !isHome, 'nav-transparent': !isScrolled && isHome }]">
      <div class="nav-container">
        <div class="nav-inner">

          <div class="logo-wrapper-left">
            <Link href="/" class="nav-logo group-logo">
              <div class="logo-img-wrapper">
                <img :src="logoUrl" alt="Logo" class="logo-img" />
                <div class="logo-glow"></div>
              </div>
              <div class="logo-text">
                <h1 class="title">ศูนย์การเรียนรู้</h1>
                <p class="subtitle">มหาวิทยาลัยแม่โจ้</p>
              </div>
            </Link>
          </div>

          <button class="hamburger-btn" @click="toggleMobileMenu" aria-label="Menu">
            <span :class="['bar', { 'bar-1-active': isMobileMenuOpen }]"></span>
            <span :class="['bar', { 'bar-2-active': isMobileMenuOpen }]"></span>
            <span :class="['bar', { 'bar-3-active': isMobileMenuOpen }]"></span>
          </button>

          <ul class="nav-menu desktop-menu">
            <li><Link href="/" class="nav-link">หน้าแรก</Link></li>

            <li class="relative group">
              <span class="nav-link cursor-pointer flex items-center gap-1.5">
                เกี่ยวกับเรา
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:-rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
              </span>
              <div class="dropdown-wrapper">
                <ul class="dropdown-content mega-dropdown">
                  <div class="dropdown-grid">
                    <div>
                      <li class="dropdown-header">
                        <span class="icon-box text-yellow-500 bg-yellow-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                        ข้อมูลทั่วไป
                      </li>
                      <li><Link href="/about/history" class="dropdown-item">ความเป็นมา</Link></li>
                        <!-- เมนูเกี่ยวกับหน่วยงาน<li><Link href="/about/vision" class="dropdown-item">ปรัชญา วิสัยทัศน์ และพันธกิจ</Link></li>
                      <li><Link href="/about/structure" class="dropdown-item">โครงสร้างองค์กร</Link></li> -->
                      <li><Link href="/about/addmin" class="dropdown-item">บุคลากรและเจ้าหน้าที่</Link></li>
                    </div>
                    <div>
                      <li class="dropdown-header">
                        <span class="icon-box text-green-600 bg-green-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></span>
                        ความเป็นมาของศูนย์เรียนรู้
                      </li>
                      <li><Link href="/center-detail/lanna" class="dropdown-item">ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา</Link></li>
                      <li><Link href="/center-detail/royal" class="dropdown-item">ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่</Link></li>
                      <li><Link href="/center-detail/banpong" class="dropdown-item">ศูนย์เรียนรู้พัฒนาบ้านโปง</Link></li>
                      <li><Link href="/center-detail/hemp" class="dropdown-item">ศูนย์ทดสอบและพัฒนากัญชง</Link></li>
                      <li><Link href="/center-detail/farm" class="dropdown-item">ฟาร์มสาธิตสำนักวิจัยฯ</Link></li>
                    </div>
                  </div>
                </ul>
              </div>
            </li>

            <li class="relative group">
              <span class="nav-link cursor-pointer flex items-center gap-1.5">
                ศูนย์การเรียนรู้
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:-rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
              </span>
              <div class="dropdown-wrapper">
                <ul class="dropdown-content standard-dropdown">
                  <li><Link href="/center/lanna" class="dropdown-item">ศูนย์เรียนรู้วัฒนธรรมเกษตรล้านนา</Link></li>
                  <li><Link href="/center/royal" class="dropdown-item">ศูนย์การเรียนรู้เกษตรทฤษฎีใหม่</Link></li>
                  <li><Link href="/center/banpong" class="dropdown-item">ศูนย์เรียนรู้พัฒนาบ้านโปง</Link></li>
                  <li><Link href="/center/hemp" class="dropdown-item">ศูนย์ทดสอบ วิจัยและพัฒนากัญชง</Link></li>
                  <li><Link href="/center/farm" class="dropdown-item">ฟาร์มสาธิตสำนักวิจัยฯ</Link></li>
                </ul>
              </div>
            </li>

            <li class="relative group">
              <span class="nav-link cursor-pointer flex items-center gap-1.5">
                การบริการ
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:-rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
              </span>
              <div class="dropdown-wrapper">
                <ul class="dropdown-content standard-dropdown p-2">
                  <li>
                    <a @click.prevent="requireLogin('/service/booking')" class="dropdown-item-feature cursor-pointer">
                      <div class="feature-icon bg-yellow-100 text-yellow-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </div>
                      <div class="feature-text">
                        <span class="feature-title">การจองศูนย์การเรียนรู้</span>
                        <span class="feature-sub">ระบบจองคิวออนไลน์</span>
                      </div>
                    </a>
                  </li>
                  <li>
                    <a @click.prevent="requireLogin('/service/products')" class="dropdown-item-feature cursor-pointer mt-1">
                      <div class="feature-icon bg-green-100 text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                      </div>
                      <div class="feature-text">
                        <span class="feature-title">สินค้าศูนย์การเรียนรู้</span>
                        <span class="feature-sub">ผลิตภัณฑ์การเกษตร</span>
                      </div>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <div class="user-actions">
              <template v-if="user">
                <li class="nav-user relative group">
                  <div class="user-badge">
                    <div class="avatar-circle">{{ user.name.charAt(0).toUpperCase() }}</div>
                    <span class="truncate max-w-[100px]">{{ user.name }}</span>
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                  <div class="dropdown-wrapper right-0">
                    <ul class="dropdown-content standard-dropdown min-w-[200px]">
                      <li>
                        <Link href="/profile" class="dropdown-item flex items-center gap-2">
                          <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                          โปรไฟล์ของฉัน
                        </Link>
                      </li>
                      <li class="divider"></li>
                      <li>
                        <button @click="logout" class="dropdown-item text-red-500 hover:text-red-600 hover:bg-red-50 w-full text-left flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                          ออกจากระบบ
                        </button>
                      </li>
                    </ul>
                  </div>
                </li>
              </template>

              <template v-else>
                <li>
                  <Link href="/login" class="login-btn">
                    <span class="btn-text">เข้าสู่ระบบ / สมัครสมาชิก</span>
                    <div class="btn-glow"></div>
                  </Link>
                </li>
              </template>
            </div>
          </ul>

        </div>
      </div>
    </nav>

    <aside :class="['mobile-menu', { 'mobile-menu-open': isMobileMenuOpen }]">
      <div class="mobile-menu-inner">
        <div class="mobile-user-section">
          <template v-if="user">
            <div class="flex items-center gap-4 mb-5">
              <div class="avatar-circle-large">{{ user.name.charAt(0).toUpperCase() }}</div>
              <div>
                <div class="text-green-900 font-bold text-xl">{{ user.name }}</div>
                <Link href="/profile" class="text-yellow-600 text-sm font-semibold hover:text-yellow-500">จัดการโปรไฟล์</Link>
              </div>
            </div>
            <button @click="logout" class="mobile-logout-btn">ออกจากระบบ</button>
          </template>
          <template v-else>
            <Link href="/login" class="mobile-login-btn">เข้าสู่ระบบ / สมัครสมาชิก</Link>
          </template>
        </div>

        <ul class="mobile-nav-links">
          <li><Link href="/" class="mobile-link"><span class="icon">🏠</span> หน้าแรก</Link></li>

          <li class="mobile-divider">เกี่ยวกับเรา</li>
          <li><Link href="/about/history" class="mobile-link pl-8">ความเป็นมา</Link></li>
          <li><Link href="/about/addmin" class="mobile-link pl-8">บุคลากร</Link></li>

          <li class="mobile-divider">ศูนย์การเรียนรู้</li>
          <li><Link href="/center/lanna" class="mobile-link pl-8">ศูนย์วัฒนธรรมเกษตรล้านนา</Link></li>
          <li><Link href="/center/royal" class="mobile-link pl-8">ศูนย์เกษตรทฤษฎีใหม่</Link></li>
          <li><Link href="/center/banpong" class="mobile-link pl-8">ศูนย์พัฒนาบ้านโปง</Link></li>
          <li><Link href="/center/hemp" class="mobile-link pl-8">ศูนย์วิจัยกัญชง</Link></li>
          <li><Link href="/center/farm" class="mobile-link pl-8">ฟาร์มสาธิตฯ</Link></li>

          <li class="mobile-divider">การบริการ</li>
          <li><a @click.prevent="requireLogin('/service/booking')" class="mobile-link pl-8 highlight">📅 การจองศูนย์การเรียนรู้</a></li>
          <li><a @click.prevent="requireLogin('/service/products')" class="mobile-link pl-8 highlight">🛍️ สินค้าศูนย์การเรียนรู้</a></li>
        </ul>
      </div>
    </aside>

    <main :class="['transition-all duration-300', { 'pt-[110px]': !isHome, 'pt-0': isHome }]">
      <slot />
    </main>

  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Anuphan:wght@400;500;600;700;800&display=swap');

.app-container {
  font-family: 'Anuphan', sans-serif;
  -webkit-font-smoothing: antialiased;
}

.navbar {
  --nav-bg: transparent;
  --nav-text: #ffffff;
  --nav-subtext: #fef08a;
  --nav-hover: #facc15;
  --logo-bg: #ffffff;
  --btn-bg: linear-gradient(135deg, #facc15, #eab308);
  --btn-text: #064e3b;
  --border-color: rgba(255, 255, 255, 0.2);
  --hamburger-color: #ffffff;

  position: fixed;
  top: 0;
  width: 100%;
  height: 110px;
  z-index: 50;
  background: var(--nav-bg);
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  border-bottom: 1px solid transparent;
  display: flex;
  align-items: center;
}

.navbar.nav-scrolled {
  --nav-bg: rgba(255, 255, 255, 0.85);
  --nav-text: #064e3b;
  --nav-subtext: #059669;
  --nav-hover: #d97706;
  --logo-bg: #f8fafc;
  --btn-bg: linear-gradient(135deg, #10b981, #059669);
  --btn-text: #ffffff;
  --border-color: rgba(6, 78, 59, 0.1);
  --hamburger-color: #064e3b;

  backdrop-filter: blur(20px) saturate(160%);
  -webkit-backdrop-filter: blur(20px) saturate(160%);
  border-bottom: 1px solid var(--border-color);
  box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.15);
  height: 85px;
}

.nav-container {
  width: 100%;
  max-width: 100%;
  padding: 0 60px;
}
.nav-inner { display: flex; justify-content: space-between; align-items: center; width: 100%; }

.logo-wrapper-left { flex: 0 1 auto; margin-right: auto; }
.nav-logo { display: flex; align-items: center; gap: 20px; text-decoration: none; z-index: 60; }

.logo-img-wrapper {
  position: relative;
  background: var(--logo-bg);
  border-radius: 50%;
  padding: 6px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.12);
  transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.group-logo:hover .logo-img-wrapper { transform: scale(1.1) rotate(5deg); }

.logo-glow {
  position: absolute; inset: -4px; border-radius: 50%;
  background: conic-gradient(from 0deg, transparent, var(--nav-hover), transparent 50%);
  z-index: -1; animation: spin-glow 3s linear infinite; opacity: 0; transition: opacity 0.3s;
}
.group-logo:hover .logo-glow { opacity: 1; }
@keyframes spin-glow { 100% { transform: rotate(360deg); } }

.logo-img { width: 62px; height: 62px; object-fit: contain; transition: all 0.5s ease; }
.nav-scrolled .logo-img { width: 48px; height: 48px; }

.logo-text { display: flex; flex-direction: column; text-align: left; }
.title {
  color: var(--nav-text); font-size: 28px; font-weight: 800; line-height: 1;
  letter-spacing: -0.5px; transition: all 0.4s ease;
  text-shadow: 0 2px 10px rgba(0,0,0,0.15);
}
.nav-scrolled .title { font-size: 23px; text-shadow: none; }

.subtitle {
  color: var(--nav-subtext); font-size: 15px; font-weight: 600; margin-top: 4px;
  transition: all 0.4s ease; opacity: 0.95;
}
.nav-scrolled .subtitle { font-size: 13px; font-weight: 700; }

.desktop-menu { display: flex; align-items: center; gap: 36px; list-style: none; margin: 0; padding: 0; }

.nav-link {
  position: relative; color: var(--nav-text); font-size: 17px; font-weight: 700;
  text-decoration: none; padding: 10px 0; transition: color 0.3s ease;
  letter-spacing: 0.2px;
}
.nav-link:hover, .group:hover .nav-link { color: var(--nav-hover); }

.nav-link::after {
  content: ''; position: absolute; width: 0; height: 4px; bottom: -2px; left: 0;
  background: var(--nav-hover); border-radius: 10px;
  transition: width 0.3s cubic-bezier(0.65, 0, 0.35, 1);
}
.nav-link:hover::after, .group:hover .nav-link::after { width: 100%; box-shadow: 0 2px 10px var(--nav-hover); }

.dropdown-wrapper {
  position: absolute; top: 100%; left: 50%;
  transform: translateX(-50%) translateY(20px);
  padding-top: 20px; visibility: hidden; opacity: 0; pointer-events: none;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); z-index: 100;
}
.dropdown-wrapper.right-0 { left: auto; right: 0; transform: translateX(0) translateY(20px); }

.group:hover .dropdown-wrapper {
  visibility: visible; opacity: 1; pointer-events: auto;
  transform: translateX(-50%) translateY(0);
}
.group:hover .dropdown-wrapper.right-0 { transform: translateX(0) translateY(0); }

.dropdown-content {
  background: #ffffff;
  border-top: 5px solid var(--nav-hover);
  border-radius: 20px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
  padding: 24px; list-style: none; position: relative;
  overflow: hidden;
}

.standard-dropdown { min-width: 290px; }
.mega-dropdown { min-width: 650px; padding: 35px; }
.dropdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }

.dropdown-header {
  display: flex; align-items: center; gap: 12px;
  color: #064e3b; font-size: 16px; font-weight: 800; margin-bottom: 18px; padding-bottom: 12px;
  border-bottom: 2px solid #f1f5f9;
}
.icon-box { display: flex; justify-content: center; align-items: center; width: 34px; height: 34px; border-radius: 10px; }

.dropdown-item {
  display: block; color: #334155; font-size: 16px; font-weight: 600;
  padding: 12px 18px; border-radius: 12px; text-decoration: none;
  transition: all 0.3s ease; margin-bottom: 4px;
}
.dropdown-item:hover {
  background: linear-gradient(90deg, #fef9c3 0%, transparent 100%);
  color: #064e3b; transform: translateX(8px);
}

.dropdown-item-feature {
  display: flex; align-items: center; gap: 18px; padding: 16px;
  border-radius: 16px; transition: all 0.4s ease; text-decoration: none;
}
.dropdown-item-feature:hover { background: #f8fafc; transform: translateY(-3px); box-shadow: 0 12px 25px rgba(0,0,0,0.06); }
.feature-icon { width: 52px; height: 52px; display: flex; justify-content: center; align-items: center; border-radius: 15px; }
.feature-text { display: flex; flex-direction: column; }
.feature-title { color: #064e3b; font-weight: 800; font-size: 17px; }
.feature-sub { color: #64748b; font-size: 13px; font-weight: 600; margin-top: 2px; }

.user-actions {
  display: flex; align-items: center; gap: 24px; margin-left: 24px; padding-left: 24px;
  border-left: 2.5px solid var(--border-color);
}

.login-btn {
  position: relative; overflow: hidden;
  background: var(--btn-bg); color: var(--btn-text);
  font-size: 16px; font-weight: 800; padding: 14px 34px; border-radius: 60px;
  text-decoration: none; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  display: inline-flex; align-items: center; justify-content: center;
  box-shadow: 0 12px 24px -6px rgba(234, 179, 8, 0.45);
}
.login-btn:hover { transform: translateY(-4px) scale(1.03); box-shadow: 0 20px 35px -8px rgba(234, 179, 8, 0.5); }

.user-badge {
  display: flex; align-items: center; gap: 14px; color: var(--nav-text); font-weight: 700;
  cursor: pointer; padding: 8px 20px 8px 10px; background: rgba(0,0,0,0.08);
  border: 2px solid var(--border-color); border-radius: 60px; transition: all 0.3s ease;
}
.user-badge:hover { background: rgba(234, 179, 8, 0.15); border-color: #facc15; }

.avatar-circle {
  width: 40px; height: 40px; background: linear-gradient(135deg, #facc15, #eab308);
  color: #064e3b; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: 19px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.hamburger-btn {
  display: none; flex-direction: column; justify-content: space-between;
  width: 36px; height: 26px; background: transparent; border: none; cursor: pointer; z-index: 100;
}
.hamburger-btn .bar { width: 100%; height: 3.5px; background-color: var(--hamburger-color); border-radius: 10px; transition: all 0.4s cubic-bezier(0.68, -0.6, 0.32, 1.6); }
.bar-1-active { transform: translateY(11.5px) rotate(45deg); background-color: #064e3b !important; }
.bar-2-active { opacity: 0; transform: translateX(-20px); }
.bar-3-active { transform: translateY(-11px) rotate(-45deg); background-color: #064e3b !important; }

.mobile-overlay { position: fixed; inset: 0; background: rgba(6, 78, 59, 0.7); backdrop-filter: blur(12px); z-index: 80; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.mobile-menu {
  position: fixed; top: 0; right: -100%; width: 360px; max-width: 90vw; height: 100vh;
  background: #ffffff; z-index: 90; transition: right 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  overflow-y: auto; box-shadow: -25px 0 60px rgba(0, 0, 0, 0.25);
  border-left: 8px solid #facc15;
}
.mobile-menu-open { right: 0; }
.mobile-menu-inner { padding: 120px 28px 60px; }

.mobile-user-section { padding-bottom: 35px; border-bottom: 2px solid #f1f5f9; margin-bottom: 35px; }
.avatar-circle-large {
  width: 70px; height: 70px; background: linear-gradient(135deg, #facc15, #eab308);
  color: #064e3b; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 30px; font-weight: 800; box-shadow: 0 12px 25px rgba(234, 179, 8, 0.35);
}
.mobile-login-btn, .mobile-logout-btn {
  display: block; width: 100%; text-align: center; padding: 18px; border-radius: 16px;
  font-weight: 800; font-size: 18px; text-decoration: none; transition: all 0.3s;
}
.mobile-login-btn { background: linear-gradient(135deg, #facc15, #eab308); color: #064e3b; }
.mobile-logout-btn { background: #fef2f2; color: #ef4444; border: 2px solid #fecaca; }

.mobile-nav-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
.mobile-link {
  display: flex; align-items: center; color: #334155; font-size: 18px; font-weight: 700;
  text-decoration: none; padding: 15px 22px; border-radius: 16px; transition: all 0.3s;
}
.mobile-link:hover { background: #fef9c3; color: #064e3b; transform: translateX(12px); }
.mobile-link.highlight { color: #059669; background: #ecfdf5; border-left: 4px solid #059669; }

.mobile-divider {
  color: #059669; font-size: 15px; font-weight: 800; text-transform: uppercase;
  margin: 30px 0 10px; letter-spacing: 2px; padding: 10px 18px;
  background: #f0fdf4; border-radius: 10px;
}

@media (max-width: 1300px) {
  .nav-container { padding: 0 30px; }
  .desktop-menu { gap: 24px; }
  .user-actions { gap: 16px; margin-left: 16px; padding-left: 16px; }
}

@media (max-width: 1200px) {
  .desktop-menu { display: none; }
  .hamburger-btn { display: flex; }
  .navbar { height: 95px; }
  .navbar.nav-scrolled { height: 80px; }
  .title { font-size: 24px; }
  .logo-img { width: 54px; height: 54px; }
}

@media (max-width: 640px) {
  .subtitle { display: none; }
  .title { font-size: 22px; }
  .logo-img { width: 48px; height: 48px; }
  .nav-container { padding: 0 20px; }
  .mobile-menu { width: 100%; max-width: 100%; border-left: none; }
}
</style>
