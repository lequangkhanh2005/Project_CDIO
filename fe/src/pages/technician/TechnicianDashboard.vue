<template>
  <div class="app-shell">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="brand">
        <div class="brand-icon">CM</div>
        <div>
          <p class="brand-name">CondoMaint</p>
          <p class="brand-tag">Vận hành & Bảo trì</p>
        </div>
      </div>

      <div class="profile">
        <div class="avatar">KT</div>
        <div>
          <p class="profile-name">Kỹ thuật viên</p>
          <p class="profile-role">Đang trực</p>
        </div>
      </div>

      <nav class="menu">
        <button class="menu-item active">
          <span class="menu-icon">🏠</span>
          Dashboard
        </button>
        <button class="menu-item">
          <span class="menu-icon">👥</span>
          Quản lý cư dân
        </button>
        <button class="menu-item">
          <span class="menu-icon">🛠️</span>
          Bảo trì
        </button>
        <button class="menu-item">
          <span class="menu-icon">💳</span>
          Tài chính
        </button>
        <button class="menu-item">
          <span class="menu-icon">⚙️</span>
          Hệ thống
        </button>
      </nav>

      <button class="logout">Đăng xuất</button>
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
        <div class="top-links">
          <button class="pill active">Trang chủ</button>
          <button class="pill">Báo cáo sự cố</button>
          <button class="pill">Lịch sử</button>
          <button class="pill">Hóa đơn</button>
        </div>
        <div class="top-right">
          <button class="ghost">Bảng điều khiển</button>
          <div class="status-pill">
            <span>API</span>
            <strong>{{ apiStatus }}</strong>
          </div>
          <button class="icon-bell">🔔</button>
          <div class="user-chip">
            <span class="avatar small">KT</span>
            <div>
              <p class="user-name">{{ user?.name || 'Kỹ thuật viên' }}</p>
              <button class="link-button user-sub" @click="logout">Đăng xuất</button>
            </div>
          </div>
        </div>
      </header>

      <main class="content">
        <div class="page-title">
          <h1>Bảng điều phối kỹ thuật viên</h1>
          <p>Quản lý job, cập nhật trạng thái, ghi worklog và hoàn thành yêu cầu.</p>
        </div>

        <section class="content-grid">
          <div class="card">
            <h2>Danh sách job</h2>
            <p class="hint">Chọn job để xem chi tiết, lịch hẹn và liên hệ cư dân.</p>
            <div class="job-list">
              <div
                v-for="job in jobs"
                :key="job.id"
                class="job-item"
                :class="{ active: selectedJob?.id === job.id }"
                @click="selectJob(job)"
              >
                <p class="job-item-title">{{ job.title }}</p>
                <div class="job-meta">
                  <span>{{ job.status }}</span>
                  <span>{{ formatDate(job.appointment_at) }}</span>
                </div>
              </div>
              <p v-if="!jobs.length && !loading" class="notice">Chưa có job nào.</p>
            </div>
          </div>

          <div class="card span-two">
            <h2>Chi tiết công việc</h2>
            <p v-if="!selectedJob" class="notice">Chọn một job ở bên trái để xem nội dung.</p>

            <template v-else>
              <div class="detail-grid">
                <div>
                  <p class="detail-label">Mã job</p>
                  <p class="detail-value">{{ selectedJob.code }}</p>
                </div>
                <div>
                  <p class="detail-label">Trạng thái</p>
                  <p class="detail-value">{{ selectedJob.status }}</p>
                </div>
                <div>
                  <p class="detail-label">Lịch hẹn / địa điểm</p>
                  <p class="detail-value">{{ selectedJob.location }}</p>
                </div>
                <div>
                  <p class="detail-label">Thời gian</p>
                  <p class="detail-value">{{ formatDateTime(selectedJob.appointment_at) }}</p>
                </div>
                <div>
                  <p class="detail-label">Cư dân</p>
                  <p class="detail-value">{{ selectedJob.resident_name }}</p>
                </div>
                <div>
                  <p class="detail-label">Liên hệ</p>
                  <p class="detail-value">{{ selectedJob.resident_phone }}</p>
                </div>
              </div>

              <div class="detail-block">
                <p class="detail-label">Mô tả công việc</p>
                <p class="detail-value">{{ selectedJob.description }}</p>
              </div>

              <div class="chip-row">
                <span class="chip">Danh sách job • Lịch hẹn • Chi tiết công việc</span>
              </div>

              <div class="actions">
                <div class="sub-card">
                  <h3>Cập nhật trạng thái</h3>
                  <div class="field">
                    <label for="status">Chọn trạng thái</label>
                    <select id="status" v-model="statusForm.status">
                      <option v-for="status in statusOptions" :key="status" :value="status">
                        {{ status }}
                      </option>
                    </select>
                  </div>
                  <button class="btn" @click="updateStatus" :disabled="loading">
                    Cập nhật
                  </button>
                </div>

                <div class="sub-card">
                  <h3>Ghi worklog + chi phí</h3>
                  <div class="field">
                    <label for="hours">Số giờ</label>
                    <input id="hours" v-model="worklogForm.hours" type="number" min="0" step="0.5" />
                  </div>
                  <div class="field">
                    <label for="cost">Chi phí (VND)</label>
                    <input id="cost" v-model="worklogForm.cost" type="number" min="0" step="1000" />
                  </div>
                  <div class="field">
                    <label for="note">Ghi chú</label>
                    <textarea id="note" v-model="worklogForm.note" placeholder="Nhập vật tư, công việc đã làm"></textarea>
                  </div>
                  <button class="btn btn-secondary" @click="addWorklog" :disabled="loading">
                    Lưu worklog
                  </button>
                </div>

                <div class="sub-card">
                  <h3>Hoàn thành yêu cầu</h3>
                  <p class="notice">Xác nhận khi đã xử lý xong toàn bộ công việc.</p>
                  <button class="btn btn-danger" @click="completeJob" :disabled="loading">
                    Hoàn thành
                  </button>
                </div>
              </div>
            </template>

            <p v-if="errorMessage" class="error" style="margin-top: 12px;">
              {{ errorMessage }}
            </p>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiFetch } from '../../composables/useApi'
import { useAuth } from '../../composables/useAuth'

const router = useRouter()
const { user, clearSession } = useAuth()
const jobs = ref([])
const selectedJob = ref(null)
const loading = ref(false)
const errorMessage = ref('')
const apiStatus = ref('đang kiểm tra')
const sidebarOpen = ref(false)

const statusForm = ref({
  status: 'Dang xu ly',
})

const worklogForm = ref({
  hours: 2,
  cost: 150000,
  note: 'Thay bóng đèn hành lang tầng 5.',
})

const statusOptions = ['Moi', 'Dang xu ly', 'Doi vat tu', 'Hoan thanh']

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  return date.toLocaleDateString('vi-VN')
}

const formatDateTime = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  return date.toLocaleString('vi-VN')
}

const fetchJobs = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const result = await apiFetch('/api/jobs')
    apiStatus.value = 'sẵn sàng'
    jobs.value = result.data || []
    selectedJob.value = jobs.value[0] || null
    if (selectedJob.value) {
      statusForm.value.status = selectedJob.value.status
    }
  } catch (error) {
    apiStatus.value = 'không kết nối'
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const selectJob = (job) => {
  selectedJob.value = job
  statusForm.value.status = job.status
}

const updateStatus = async () => {
  if (!selectedJob.value) return
  loading.value = true
  errorMessage.value = ''
  try {
    const data = await apiFetch(`/api/jobs/${selectedJob.value.id}/status`, {
      method: 'PATCH',
      body: JSON.stringify({ status: statusForm.value.status }),
    })
    selectedJob.value.status = data.data.status
    jobs.value = jobs.value.map((job) =>
      job.id === selectedJob.value.id ? { ...job, status: data.data.status } : job
    )
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const addWorklog = async () => {
  if (!selectedJob.value) return
  loading.value = true
  errorMessage.value = ''
  try {
    await apiFetch(`/api/jobs/${selectedJob.value.id}/worklogs`, {
      method: 'POST',
      body: JSON.stringify(worklogForm.value),
    })
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const completeJob = async () => {
  if (!selectedJob.value) return
  loading.value = true
  errorMessage.value = ''
  try {
    const data = await apiFetch(`/api/jobs/${selectedJob.value.id}/complete`, {
      method: 'POST',
    })
    selectedJob.value.status = data.data.status
    jobs.value = jobs.value.map((job) =>
      job.id === selectedJob.value.id ? { ...job, status: data.data.status } : job
    )
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const logout = async () => {
  try {
    await apiFetch('/api/auth/logout', { method: 'POST' })
  } catch {
  } finally {
    clearSession()
    router.push('/login')
  }
}

onMounted(fetchJobs)
</script>
