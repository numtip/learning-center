<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user || {})

const notifications = computed(() => page.props.notifications || [])
const unreadCount = computed(() => page.props.unreadCount || 0)

const openNotify = ref(false)
const collapsed = ref(false)
const dark = ref(false)

const isActive = (url) => page.url.startsWith(url)

const openBiz = ref(isActive('/admin') || isActive('/superadmin/order-slips') || isActive('/superadmin/plot-fees') || isActive('/booking-calendar') || isActive('/superadmin/promotions') || isActive('/admin/promotions'))
const openWeb = ref(isActive('/superadmin/home') || isActive('/superadmin/pr') || isActive('/superadmin/activity-news') || isActive('/superadmin/articles-manager'))
const openMap = ref(isActive('/superadmin/map'))
const openSystem = ref(isActive('/superadmin/users'))

const isSuperAdmin = computed(() =>
  user.value.roles?.some(r => r.name === 'superadmin')
)

const base = computed(() =>
  isSuperAdmin.value ? '/superadmin' : '/admin'
)

setInterval(() => {
  router.reload({ only: ['notifications', 'unreadCount'] })
}, 10000)

const markAllAsRead = () => {
  router.post(route('notifications.markAsRead'), {}, {
    preserveScroll: true,
    onSuccess: () => { openNotify.value = false }
  })
}

const goToLink = (url) => {
  openNotify.value = false;
  router.get(url);
}

const logout = () => router.post('/logout')

const toggleTheme = () => {
  dark.value = !dark.value
  document.documentElement.classList.toggle('dark', dark.value)
}

</script>

<template>
  <div :class="['colorful-app', { dark }]">

    <div class="bg-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
      <div class="blob blob-4"></div>
    </div>

    <aside :class="['sidebar', { collapsed }]">
      <div class="sidebar-header">
        <div class="logo-box bounce-hover">
          <span class="logo-emoji">🌱</span>
        </div>
        <div v-if="!collapsed" class="brand-text">
          <span class="title">ระบบจัดการ</span>
          <span class="subtitle">ศูนย์การเรียนรู้</span>
        </div>
      </div>

      <div class="menu-container custom-scrollbar">
        <ul class="menu-list">

          <li class="nav-item">
            <Link :href="`${base}/dashboard`" class="nav-link color-blue" :class="{ active: isActive(`${base}/dashboard`) }">
              <div class="icon-cube"><span class="emoji">🚀</span></div>
              <span v-if="!collapsed" class="nav-text">แดชบอร์ดภาพรวม</span>
            </Link>
          </li>

          <div v-if="!collapsed" class="divider-pill"></div>

          <li class="nav-item group">
            <div class="nav-link color-orange dropdown-toggle" @click="openBiz = !openBiz" :class="{ 'is-expanded': openBiz }">
              <div class="nav-link-left">
                <div class="icon-cube"><span class="emoji">🛍️</span></div>
                <span v-if="!collapsed" class="nav-text">ธุรกิจและบริการ</span>
              </div>
              <span v-if="!collapsed" class="arrow" :class="{ rotated: openBiz }">🔻</span>
            </div>
            <div class="submenu-wrapper" :style="{ maxHeight: openBiz && !collapsed ? '500px' : '0' }">
              <ul class="submenu color-orange">
                <li><Link href="/admin/orders" class="sub-link" :class="{ active: isActive('/admin/orders') }"><span class="sub-emoji">📦</span> <span class="sub-text">คำสั่งซื้อสินค้า</span></Link></li>
                <li><Link href="/admin/products" class="sub-link" :class="{ active: isActive('/admin/products') }"><span class="sub-emoji">🥑</span> <span class="sub-text">คลังสินค้าเกษตร</span></Link></li>
                <li><Link href="/admin/booking" class="sub-link" :class="{ active: isActive('/admin/booking') }"><span class="sub-emoji">🎟️</span> <span class="sub-text">จองกิจกรรม</span></Link></li>
                <li><Link href="/booking-calendar" class="sub-link" :class="{ active: isActive('/booking-calendar') }"><span class="sub-emoji">📆</span> <span class="sub-text">ปฏิทินคิวงาน</span></Link></li>
                <li><Link :href="`${base}/promotions`" class="sub-link" :class="{ active: isActive(`${base}/promotions`) }"><span class="sub-emoji">🔥</span> <span class="sub-text">จัดการโปรโมชัน</span></Link></li>
                <template v-if="isSuperAdmin">
                  <li><Link href="/superadmin/order-slips" class="sub-link" :class="{ active: isActive('/superadmin/order-slips') }"><span class="sub-emoji">🧾</span> <span class="sub-text">สลิปโอนเงิน</span></Link></li>
                  <li><Link href="/superadmin/plot-fees" class="sub-link" :class="{ active: isActive('/superadmin/plot-fees') }"><span class="sub-emoji">🪙</span> <span class="sub-text">ค่าธรรมเนียม</span></Link></li>
                </template>
              </ul>
            </div>
          </li>

          <template v-if="isSuperAdmin">

            <li class="nav-item group">
              <div class="nav-link color-purple dropdown-toggle" @click="openWeb = !openWeb" :class="{ 'is-expanded': openWeb }">
                <div class="nav-link-left">
                  <div class="icon-cube"><span class="emoji">📰</span></div>
                  <span v-if="!collapsed" class="nav-text">หน้าแรกและข่าวสาร</span>
                </div>
                <span v-if="!collapsed" class="arrow" :class="{ rotated: openWeb }">🔻</span>
              </div>
              <div class="submenu-wrapper" :style="{ maxHeight: openWeb && !collapsed ? '500px' : '0' }">
                <ul class="submenu color-purple">
                  <li><Link href="/superadmin/home/slides" class="sub-link" :class="{ active: isActive('/superadmin/home/slides') }"><span class="sub-emoji">🖼️</span> <span class="sub-text">แบนเนอร์หน้าแรก</span></Link></li>
                  <li><Link href="/superadmin/home/videos" class="sub-link" :class="{ active: isActive('/superadmin/home/videos') }"><span class="sub-emoji">🎬</span> <span class="sub-text">วิดีโอแนะนำ</span></Link></li>
                  <li><Link href="/superadmin/pr" class="sub-link" :class="{ active: isActive('/superadmin/pr') }"><span class="sub-emoji">📣</span> <span class="sub-text">ข่าวประชาสัมพันธ์</span></Link></li>
                  <li><Link href="/superadmin/activity-news" class="sub-link" :class="{ active: isActive('/superadmin/activity-news') }"><span class="sub-emoji">🎉</span> <span class="sub-text">ข่าวกิจกรรม</span></Link></li>
                  <li><Link href="/superadmin/articles-manager" class="sub-link" :class="{ active: isActive('/superadmin/articles-manager') }"><span class="sub-emoji">📖</span> <span class="sub-text">บทความและสาระ</span></Link></li>
                </ul>
              </div>
            </li>

            <li class="nav-item">
              <Link href="/superadmin/centers-hub" class="nav-link color-blue" :class="{ active: isActive('/superadmin/centers-hub') || isActive('/superadmin/history') || isActive('/superadmin/vision') || isActive('/superadmin/personnel') || isActive('/superadmin/centers') }">
                <div class="nav-link-left">
                  <div class="icon-cube"><span class="emoji">🏛️</span></div>
                  <span v-if="!collapsed" class="nav-text">ข้อมูลศูนย์และประวัติ</span>
                </div>
              </Link>
            </li>

            <li class="nav-item group">
              <div class="nav-link color-green dropdown-toggle" @click="openMap = !openMap" :class="{ 'is-expanded': openMap }">
                <div class="nav-link-left">
                  <div class="icon-cube"><span class="emoji">🗺️</span></div>
                  <span v-if="!collapsed" class="nav-text">ระบบแผนที่จำลอง</span>
                </div>
                <span v-if="!collapsed" class="arrow" :class="{ rotated: openMap }">🔻</span>
              </div>
              <div class="submenu-wrapper" :style="{ maxHeight: openMap && !collapsed ? '500px' : '0' }">
                <ul class="submenu color-green">
                  <li><Link href="/superadmin/map" class="sub-link" :class="{ active: isActive('/superadmin/map') }"><span class="sub-emoji">📍</span> <span class="sub-text">จัดการพิกัดแผนที่</span></Link></li>
                </ul>
              </div>
            </li>

            <li class="nav-item group">
              <div class="nav-link color-pink dropdown-toggle" @click="openSystem = !openSystem" :class="{ 'is-expanded': openSystem }">
                <div class="nav-link-left">
                  <div class="icon-cube"><span class="emoji">⚙️</span></div>
                  <span v-if="!collapsed" class="nav-text">ตั้งค่าระบบหลังบ้าน</span>
                </div>
                <span v-if="!collapsed" class="arrow" :class="{ rotated: openSystem }">🔻</span>
              </div>
              <div class="submenu-wrapper" :style="{ maxHeight: openSystem && !collapsed ? '500px' : '0' }">
                <ul class="submenu color-pink">
                  <li><Link href="/superadmin/users" class="sub-link" :class="{ active: isActive('/superadmin/users') }"><span class="sub-emoji">👾</span> <span class="sub-text">ผู้ใช้งานระบบ</span></Link></li>
                </ul>
              </div>
            </li>
          </template>

        </ul>
      </div>
    </aside>

    <div class="main-content">

      <header class="topbar ultra-glass">
        <div class="topbar-left">
          <button class="action-btn menu-btn" @click="collapsed = !collapsed">
            <span class="burger-line" :class="{ 'line-1-active': !collapsed }"></span>
            <span class="burger-line" :class="{ 'line-2-active': !collapsed }"></span>
            <span class="burger-line" :class="{ 'line-3-active': !collapsed }"></span>
          </button>
          <div class="welcome-tag" v-if="!collapsed">
            <span class="wave-emoji">👋</span> <span class="welcome-text">สวัสดี,</span> <strong class="gradient-text">{{ user?.name || 'Loading...' }}</strong>
          </div>
        </div>

        <div class="topbar-right">
          <button class="action-btn theme-toggle bounce-hover" @click="toggleTheme" title="สลับโหมด">
            <span class="theme-icon" :class="{ 'spin-out': dark }">☀️</span>
            <span class="theme-icon dark-icon" :class="{ 'spin-in': dark }">🌙</span>
          </button>

          <div class="notify-wrapper">
            <button class="action-btn bell-btn bounce-hover" @click="openNotify = !openNotify">
              <span class="emoji" :class="{ 'shake': unreadCount > 0 }">🔔</span>
              <span v-if="unreadCount > 0" class="badge-pop">{{ unreadCount }}</span>
            </button>

            <div v-if="openNotify" class="notify-overlay" @click="openNotify = false"></div>

            <transition name="pop-dropdown">
              <div v-if="openNotify" class="notify-panel ultra-glass">
                <div class="panel-header">
                  <div class="title-with-icon">
                    <span class="emoji">📫</span> กล่องข้อความ
                  </div>
                  <button v-if="unreadCount > 0" @click="markAllAsRead" class="read-all-btn">
                    อ่านหมดแล้ว ✨
                  </button>
                </div>

                <div class="panel-body custom-scrollbar">
                  <div v-if="notifications.length === 0" class="empty-state">
                    <div class="float-emoji">🎈</div>
                    <p>เงียบกริบ... ไม่มีแจ้งเตือนใหม่จ้า</p>
                  </div>

                  <div v-for="(n, i) in notifications" :key="n.id"
                       class="notify-card"
                       :class="{ 'is-new': !n.read_at }"
                       @click="goToLink(n.data.url)"
                       :style="{ animationDelay: `${i * 0.05}s` }">
                    <div class="card-icon"><span class="emoji">✉️</span></div>
                    <div class="card-info">
                      <div class="card-title">
                        <span class="text-clamp">{{ n.data.title }}</span>
                        <span v-if="!n.read_at" class="new-dot"></span>
                      </div>
                      <div class="card-desc">{{ n.data.message }}</div>
                      <div class="card-time">⏱️ {{ n.created_at }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </div>

          <div class="spacer-dot"></div>

          <div class="profile-pill bounce-hover">
            <div class="avatar">{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</div>
            <div class="role-badge">{{ isSuperAdmin ? '👑 Super' : '🛡️ Admin' }}</div>
          </div>

          <button class="action-btn logout-btn bounce-hover" @click="logout" title="ออกจากระบบ">
            <span class="emoji">🚪</span>
          </button>
        </div>
      </header>

      <main class="page-container">
        <div class="content-box ultra-glass">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
  --c-blue: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  --c-orange: linear-gradient(135deg, #ff9a44 0%, #fc6076 100%);
  --c-purple: linear-gradient(135deg, #c471ed 0%, #f64f59 100%);
  --c-green: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  --c-pink: linear-gradient(135deg, #ff0844 0%, #ffb199 100%);

  --bg-app: #f0f7ff;
  --bg-sidebar: rgba(255,255,255,0.95);
  --glass-bg: rgba(255, 255, 255, 0.65);
  --glass-border: rgba(255, 255, 255, 0.9);
  --glass-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), inset 0 0 0 1px rgba(255, 255, 255, 0.5);
  --card-hover-bg: #ffffff;

  --text-main: #1e293b;
  --text-muted: #64748b;
  --text-inverse: #ffffff;

  --font-family: 'Kanit', sans-serif;
  --radius-pill: 50px;
  --radius-box: 24px;
}

.dark {
  --bg-app: #020617;
  --bg-sidebar: #0f172a;

  --glass-bg: rgba(15, 23, 42, 0.7);
  --glass-border: rgba(255, 255, 255, 0.1);
  --glass-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
  --card-hover-bg: rgba(30, 41, 59, 0.8);

  --text-main: #f8fafc;
  --text-muted: #94a3b8;
  --text-inverse: #020617;
}

.colorful-app {
  display: flex; min-height: 100vh; font-family: var(--font-family);
  background-color: var(--bg-app); color: var(--text-main);
  overflow-x: hidden; position: relative; z-index: 1; transition: background-color 0.5s ease, color 0.5s ease;
}

.bg-blobs { position: fixed; inset: 0; overflow: hidden; z-index: -1; filter: blur(80px); opacity: 0.6; pointer-events: none; }
.dark .bg-blobs { opacity: 0.25; }
.blob { position: absolute; border-radius: 50%; animation: floatBlobs 15s infinite alternate ease-in-out; }
.blob-1 { width: 500px; height: 500px; background: #ff9a44; top: -10%; left: -10%; animation-delay: 0s; }
.blob-2 { width: 600px; height: 600px; background: #00f2fe; bottom: -20%; right: -10%; animation-delay: -5s; }
.blob-3 { width: 400px; height: 400px; background: #c471ed; top: 40%; left: 30%; animation-delay: -10s; }
.blob-4 { width: 300px; height: 300px; background: #43e97b; bottom: 10%; left: 10%; animation-delay: -7s; }

@keyframes floatBlobs {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(50px, -50px) scale(1.1); }
  100% { transform: translate(-50px, 50px) scale(0.9); }
}

.ultra-glass {
  background: var(--glass-bg); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
  border: 1px solid var(--glass-border); box-shadow: var(--glass-shadow);
}

.gradient-text {
  background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 700;
}
.dark .gradient-text { background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

.bounce-hover { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.bounce-hover:hover { transform: scale(1.1) translateY(-2px); }

.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 10px; border: 2px solid transparent; background-clip: content-box; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(16, 185, 129, 0.6); }

.sidebar {
  width: 280px;
  margin: 20px 0 20px 20px;
  border-radius: 30px;

  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);

  display: flex;
  flex-direction: column;

  transition: all 0.3s ease;
  z-index: 50;

  height: fit-content;
}

.dark .sidebar {
  box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

.sidebar.collapsed {
  width: 90px;
}

.sidebar-header {
  min-height: 90px;
  display: flex;
  align-items: center;
  padding: 0 20px;
  gap: 14px;
}
.logo-box {
  width: 50px; height: 50px; background: var(--glass-bg); border-radius: 18px;
  border: 2px solid var(--glass-border); display: flex; justify-content: center; align-items: center;
  font-size: 24px; flex-shrink: 0; box-shadow: 0 8px 16px rgba(0,0,0,0.05);
}
.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.25;
  gap: 4px;
}

.brand-text .title {
  font-size: 26px;
  font-weight: 900;
  background: linear-gradient(90deg,#4f46e5,#6366f1,#8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: 1px;
}

.brand-text .subtitle {
  font-size: 15px;
  font-weight: 700;
  background: linear-gradient(90deg,#16a34a,#22c55e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: 0.5px;
}

.menu-container {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
}
.menu-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }

.divider-pill { height: 4px; width: 30px; background: var(--glass-border); border-radius: 10px; margin: 12px auto; opacity: 0.5; }

.nav-link {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 14px; border-radius: 20px; text-decoration: none; color: var(--text-main);
  font-weight: 600; cursor: pointer; transition: all 0.3s ease; position: relative;
  border: 2px solid transparent; background: transparent;
}
.nav-link-left { display: flex; align-items: center; gap: 14px; }
.nav-text {
  font-size: 17px;
  font-weight: 700;

  background: linear-gradient(90deg,#2563eb,#6366f1,#8b5cf6,#ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;

  letter-spacing: 0.5px;
}
.icon-cube {
  width: 40px; height: 40px; border-radius: 14px; background: var(--bg-app);
  display: flex; justify-content: center; align-items: center; font-size: 20px;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
}
.dark .icon-cube { background: rgba(255,255,255,0.05); box-shadow: none; }

.color-blue:hover, .color-blue.active, .color-blue.is-expanded { background: rgba(0, 242, 254, 0.08); border-color: rgba(0, 242, 254, 0.3); }
.color-blue:hover .icon-cube, .color-blue.active .icon-cube, .color-blue.is-expanded .icon-cube { background: var(--c-blue); color: white; transform: scale(1.1) rotate(-5deg); box-shadow: 0 10px 20px rgba(0, 242, 254, 0.3); }
.color-blue {
  background: rgba(79,70,229,0.06);
  border: 2px solid rgba(79,70,229,0.18);
}

.color-orange:hover, .color-orange.active, .color-orange.is-expanded { background: rgba(255, 154, 68, 0.08); border-color: rgba(255, 154, 68, 0.3); }
.color-orange:hover .icon-cube, .color-orange.active .icon-cube, .color-orange.is-expanded .icon-cube { background: var(--c-orange); color: white; transform: scale(1.1) rotate(5deg); box-shadow: 0 10px 20px rgba(255, 154, 68, 0.3); }

.color-purple:hover, .color-purple.active, .color-purple.is-expanded { background: rgba(196, 113, 237, 0.08); border-color: rgba(196, 113, 237, 0.3); }
.color-purple:hover .icon-cube, .color-purple.active .icon-cube, .color-purple.is-expanded .icon-cube { background: var(--c-purple); color: white; transform: scale(1.1) rotate(-5deg); box-shadow: 0 10px 20px rgba(196, 113, 237, 0.3); }

.color-green:hover, .color-green.active, .color-green.is-expanded { background: rgba(67, 233, 123, 0.08); border-color: rgba(67, 233, 123, 0.3); }
.color-green:hover .icon-cube, .color-green.active .icon-cube, .color-green.is-expanded .icon-cube { background: var(--c-green); color: white; transform: scale(1.1) rotate(5deg); box-shadow: 0 10px 20px rgba(67, 233, 123, 0.3); }

.color-pink:hover, .color-pink.active, .color-pink.is-expanded { background: rgba(255, 8, 68, 0.08); border-color: rgba(255, 8, 68, 0.3); }
.color-pink:hover .icon-cube, .color-pink.active .icon-cube, .color-pink.is-expanded .icon-cube { background: var(--c-pink); color: white; transform: scale(1.1) rotate(-5deg); box-shadow: 0 10px 20px rgba(255, 8, 68, 0.3); }

.arrow { font-size: 12px; transition: transform 0.3s; opacity: 0.7; color: var(--text-main); }
.arrow.rotated { transform: rotate(180deg); }

.submenu-wrapper { overflow: hidden; transition: max-height 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); }
.submenu { list-style: none; padding: 8px 10px; margin: 4px 0 0 18px; border-left: 3px dashed var(--glass-border); display: flex; flex-direction: column; gap: 4px; }
.sub-link { display: flex; align-items: center; gap: 10px; padding: 8px 16px; border-radius: 14px; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s; position: relative; }
.sub-emoji { font-size: 16px; transition: transform 0.2s; }
.sub-text { color: var(--text-muted); transition: color 0.2s; }

.sub-link:hover { transform: translateX(5px); background: var(--bg-app); }
.sub-link:hover .sub-text { color: var(--text-main); }
.sub-link:hover .sub-emoji { transform: scale(1.2); }
.sub-link.active { font-weight: 700; background: var(--bg-app); }

.color-blue .sub-link.active .sub-text { color: #4facfe; }
.dark .color-blue .sub-link.active .sub-text { color: #00f2fe; }
.color-blue .sub-link.active { box-shadow: inset 4px 0 0 #4facfe; }

.color-orange .sub-link.active .sub-text { color: #ff9a44; }
.color-orange .sub-link.active { box-shadow: inset 4px 0 0 #ff9a44; }

.color-purple .sub-link.active .sub-text { color: #c471ed; }
.dark .color-purple .sub-link.active .sub-text { color: #e0c3fc; }
.color-purple .sub-link.active { box-shadow: inset 4px 0 0 #c471ed; }

.color-green .sub-link.active .sub-text { color: #43e97b; }
.color-green .sub-link.active { box-shadow: inset 4px 0 0 #43e97b; }

.color-pink .sub-link.active .sub-text { color: #ff0844; }
.dark .color-pink .sub-link.active .sub-text { color: #ffb199; }
.color-pink .sub-link.active { box-shadow: inset 4px 0 0 #ff0844; }

.main-content { flex: 1; display: flex; flex-direction: column; width: calc(100% - 320px); min-width: 0; }
.topbar {
  margin: 20px 20px 0 10px;
  height: 76px;

  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;

  position: sticky;
  top: 20px;
  z-index: 40;

  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.topbar-left, .topbar-right { display: flex; align-items: center; gap: 12px; }

.action-btn {
  width: 48px; height: 48px; border-radius: 50%; border: none; background: var(--glass-bg);
  box-shadow: 0 4px 10px rgba(0,0,0,0.05); cursor: pointer; display: flex; justify-content: center; align-items: center;
  font-size: 20px; color: var(--text-main); transition: all 0.3s ease; position: relative; border: 1px solid var(--glass-border);
}
.action-btn:hover { background: var(--card-hover-bg); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

.menu-btn { flex-direction: column; gap: 5px; }
.burger-line { width: 20px; height: 3px; background: #000000; border-radius: 3px; transition: 0.3s; }
.dark .burger-line { background: #ffffff; }

.line-1-active { transform: translateY(8px) rotate(45deg); }
.line-2-active { opacity: 0; }
.line-3-active { transform: translateY(-8px) rotate(-45deg); }

.welcome-tag { font-size: 16px; font-weight: 500; background: var(--glass-bg); padding: 8px 18px; border-radius: 20px; border: 1px solid var(--glass-border); display: flex; align-items: center; gap: 8px; color: var(--text-main); }
.welcome-text { color: var(--text-main); }
.wave-emoji { display: inline-block; animation: wave 2s infinite; transform-origin: 70% 70%; }
@keyframes wave { 0%, 100% { transform: rotate(0); } 20% { transform: rotate(15deg); } 40% { transform: rotate(-10deg); } 60% { transform: rotate(15deg); } 80% { transform: rotate(-10deg); } }

.theme-toggle { position: relative; overflow: hidden; }
.theme-icon { position: absolute; transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.26, 1.55), opacity 0.5s; }
.dark-icon { opacity: 0; transform: translateY(30px) rotate(90deg); }
.spin-out { transform: translateY(-30px) rotate(-90deg); opacity: 0; }
.spin-in { transform: translateY(0) rotate(0); opacity: 1; }

.shake { animation: shakeRing 2s infinite ease-in-out; transform-origin: top center; display: inline-block; }
@keyframes shakeRing { 0%, 100% { transform: rotate(0); } 10%, 30%, 50% { transform: rotate(20deg) scale(1.1); } 20%, 40% { transform: rotate(-20deg) scale(1.1); } }
.badge-pop {
  position: absolute; top: -2px; right: -2px; background: #ff0844; color: white;
  font-size: 11px; font-weight: 800; width: 22px; height: 22px; border-radius: 50%;
  display: flex; justify-content: center; align-items: center; border: 2px solid white;
  animation: popInBadge 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 4px 10px rgba(255, 8, 68, 0.5);
}
@keyframes popInBadge { 0% { transform: scale(0); } 100% { transform: scale(1); } }
.dark .badge-pop { border-color: var(--bg-app); }

.spacer-dot { width: 6px; height: 6px; background: var(--glass-border); border-radius: 50%; margin: 0 4px; }

.profile-pill {
  display: flex; align-items: center; gap: 10px; padding: 6px 16px 6px 6px;
  background: var(--glass-bg); border-radius: 30px; border: 1px solid var(--glass-border);
  cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: background 0.3s;
}
.profile-pill:hover { background: var(--card-hover-bg); }
.avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: #ffffff; border: 2px solid #e2e8f0;
  color: #000000;
  display: flex; justify-content: center; align-items: center;
  font-weight: 800; font-size: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.role-badge { font-size: 13px; font-weight: 700; color: var(--text-main); }

.logout-btn { background: rgba(255, 8, 68, 0.1); border-color: rgba(255, 8, 68, 0.2); }
.logout-btn:hover { background: #ff0844; color: white; transform: rotate(10deg) scale(1.1); box-shadow: 0 8px 20px rgba(255, 8, 68, 0.4); }
.dark .logout-btn:hover { color: white; }

.notify-wrapper { position: relative; }
.notify-overlay { position: fixed; inset: 0; z-index: 90; }
.notify-panel {
  position: absolute;
  right: 0;
  top: 70px;
  width: 380px;

  border-radius: 22px;

  background: linear-gradient(135deg,#ffffff,#f8fafc,#eef2ff);
  backdrop-filter: blur(14px);

  border: 1px solid rgba(99,102,241,0.15);
  box-shadow: 0 20px 45px rgba(0,0,0,0.18);

  z-index: 9999;
  overflow: hidden;
}
.notify-card.is-new {
  border-left: 4px solid #ef4444;
  background: linear-gradient(90deg,#fff1f2,#ffffff);
}
.pop-dropdown-enter-active { animation: popDropdown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.pop-dropdown-leave-active { animation: popDropdownOut 0.2s ease-in; }
@keyframes popDropdown { 0% { opacity: 0; transform: scale(0.8) translateY(-20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
@keyframes popDropdownOut { 0% { opacity: 1; transform: scale(1) translateY(0); } 100% { opacity: 0; transform: scale(0.9) translateY(-10px); } }

.panel-header { padding: 16px 20px; border-bottom: 1px dashed var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.02); }
.dark .panel-header { background: rgba(255,255,255,0.02); }
.title-with-icon { font-weight: 700; font-size: 16px; display: flex; gap: 8px; align-items: center; color: var(--text-main); }

.read-all-btn { background: var(--glass-bg); border: 1px solid var(--glass-border); padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: var(--font-family); color: var(--text-main); }
.read-all-btn:hover { background: var(--text-main); color: var(--bg-app); transform: translateY(-2px); }
.dark .read-all-btn:hover { background: var(--text-inverse); color: var(--text-main); }

.panel-body { max-height: 400px; overflow-y: auto; padding: 10px; }

.empty-state { padding: 40px 20px; text-align: center; color: var(--text-muted); font-weight: 500; }
.float-emoji { font-size: 40px; animation: floatEmoji 3s infinite ease-in-out; margin-bottom: 10px; }
@keyframes floatEmoji { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

.notify-card {
  padding: 14px;

  background: linear-gradient(135deg,#ffffff,#f8fafc);
  border-radius: 16px;

  border: 1px solid rgba(0,0,0,0.06);
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);

  margin-bottom: 10px;

  display: flex;
  gap: 14px;
  cursor: pointer;
  transition: all 0.25s ease;
}
@keyframes slideInNotify { to { opacity: 1; transform: translateX(0); } }

.notify-card:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 14px 28px rgba(0,0,0,0.15);
}

.card-icon { width: 40px; height: 40px; border-radius: 12px; background: var(--bg-app); display: flex; justify-content: center; align-items: center; font-size: 20px; flex-shrink: 0; }
.notify-card.is-new { border-left: 4px solid #ff0844; background: linear-gradient(90deg, rgba(255, 8, 68, 0.05), transparent); }
.new-dot { display: inline-block; width: 8px; height: 8px; background: #ff0844; border-radius: 50%; box-shadow: 0 0 8px #ff0844; margin-left: 6px; }

.card-info { flex: 1; overflow: hidden; }
.card-title { font-weight: 700; font-size: 14px; margin-bottom: 4px; display: flex; align-items: center; color: var(--text-main); }
.text-clamp { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 90%; }
.card-desc { font-size: 13px; color: var(--text-muted); line-height: 1.4; margin-bottom: 8px; }
.card-time { font-size: 11px; font-weight: 600; color: var(--text-muted); background: var(--bg-app); padding: 4px 10px; border-radius: 8px; display: inline-block; border: 1px solid var(--glass-border); }

.page-container { padding: 20px 20px 20px 10px; flex: 1; display: flex; flex-direction: column; }
.content-box {
  flex: 1;
  padding: 24px;

  background: rgba(255,255,255,0.97);
  backdrop-filter: blur(6px);

  border-radius: 20px;
  border: 1px solid #e2e8f0;

  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}
@keyframes fadeUpIn { from { opacity: 0; transform: translateY(30px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
.nav-link.active {
  background: linear-gradient(90deg,#6366f1,#8b5cf6);
  border: 2px solid #6366f1;
  box-shadow: 0 6px 18px rgba(99,102,241,0.35);
}

.nav-link.active .nav-text{
  background: linear-gradient(90deg,#ffffff,#e0e7ff,#f5d0fe);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.nav-link.active .icon-cube{
  background: linear-gradient(135deg,#6366f1,#8b5cf6);
  color:white;
}
</style>
