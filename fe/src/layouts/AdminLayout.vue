<template>
  <div class="app-shell">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="brand">
        <div class="brand-icon">CM</div>
        <div>
          <p class="brand-name">CondoMaint</p>
          <p class="brand-tag">Admin Center</p>
        </div>
      </div>

      <div class="profile">
        <div class="avatar">AD</div>
        <div>
          <p class="profile-name">Quản trị</p>
          <p class="profile-role">Toàn quyền</p>
        </div>
      </div>

      <nav class="menu">
        <RouterLink class="menu-item" to="/admin/dashboard" active-class="active">
          <span class="menu-icon">📊</span>
          Dashboard
        </RouterLink>
        <RouterLink class="menu-item" to="/admin/jobs" active-class="active">
          <span class="menu-icon">🛠️</span>
          Quản lý job
        </RouterLink>
        <RouterLink class="menu-item" to="/admin/residents" active-class="active">
          <span class="menu-icon">👥</span>
          Cư dân
        </RouterLink>
        <RouterLink class="menu-item" to="/admin/worklogs" active-class="active">
          <span class="menu-icon">🧾</span>
          Worklog
        </RouterLink>
        <RouterLink class="menu-item" to="/admin/invoices" active-class="active">
          <span class="menu-icon">💳</span>
          Hóa đơn
        </RouterLink>
        <RouterLink class="menu-item" to="/admin/reports" active-class="active">
          <span class="menu-icon">📈</span>
          Báo cáo
        </RouterLink>
        <RouterLink class="menu-item" to="/admin/history" active-class="active">
          <span class="menu-icon">🕒</span>
          Lịch sử
        </RouterLink>
      </nav>

      <button class="logout" @click="logout">Đăng xuất</button>
    </aside>

    <div class="main-area">
      <header class="topbar">
        <div class="top-left">
          <button class="icon-button" @click="sidebarOpen = !sidebarOpen">☰</button>
          <div class="top-brand">
            <div class="brand-icon small">CM</div>
            <span>CondoMaint</span>
          </div>
        </div>
        <div class="top-right">
          <div class="status-pill">
            <span>Vai trò</span>
            <strong>Admin</strong>
          </div>
          <div class="user-chip">
            <span class="avatar small">AD</span>
            <div>
              <p class="user-name">Quản trị</p>
              <p class="user-sub">Toàn quyền</p>
            </div>
          </div>
        </div>
      </header>

      <main class="content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiFetch } from '../composables/useApi'
import { useAuth } from '../composables/useAuth'

const sidebarOpen = ref(false)
const router = useRouter()
const { clearSession } = useAuth()

const logout = async () => {
  try {
    await apiFetch('/api/auth/logout', { method: 'POST' })
  } catch {
  } finally {
    clearSession()
    router.push('/login')
  }
}
</script>
