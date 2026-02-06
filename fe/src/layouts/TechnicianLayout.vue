<template>
  <div class="app-shell">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="brand">
        <div class="brand-icon">CM</div>
        <div>
          <p class="brand-name">CondoMaint</p>
          <p class="brand-tag">Kỹ thuật viên</p>
        </div>
      </div>

      <div class="profile">
        <div class="avatar">KT</div>
        <div>
          <p class="profile-name">{{ displayName }}</p>
          <p class="profile-role">Đang trực</p>
        </div>
      </div>

      <nav class="menu">
        <RouterLink class="menu-item" to="/technician/menu" active-class="active">
          <span class="menu-icon">DB</span>
          Dashboard
        </RouterLink>
        <RouterLink class="menu-item" to="/technician/menu" active-class="active">
          <span class="menu-icon">JB</span>
          Job được giao
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
            <strong>Kỹ thuật viên</strong>
          </div>
          <div class="user-chip">
            <span class="avatar small">KT</span>
            <div>
              <p class="user-name">{{ displayName }}</p>
              <p class="user-sub">Sẵn sàng nhận việc</p>
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiFetch } from '../composables/useApi'
import { useAuth } from '../composables/useAuth'

const sidebarOpen = ref(false)
const router = useRouter()
const { user, clearSession } = useAuth()
const displayName = computed(() => user.value?.name || 'Kỹ thuật viên')

const logout = async () => {
  try {
    await apiFetch('/api/auth/logout', { method: 'POST' })
  } catch {
  } finally {
    clearSession()
    router.push('/technician/login')
  }
}
</script>
