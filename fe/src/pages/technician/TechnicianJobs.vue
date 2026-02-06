<template>
  <AdminLayout>
    <section class="tech-page">
      <div class="page-title">
        <h1>Danh sách công việc</h1>
        <p>Theo dõi công việc được giao, lịch hẹn và trạng thái xử lý.</p>
      </div>

      <div class="summary-row">
        <div class="summary-card">
          <p class="summary-label">Tổng công việc</p>
          <p class="summary-value">{{ baseJobs.length }}</p>
        </div>
        <div class="summary-card">
          <p class="summary-label">Đang xử lý</p>
          <p class="summary-value">{{ activeCount }}</p>
        </div>
        <div class="summary-card accent">
          <p class="summary-label">Lịch hẹn hôm nay</p>
          <p class="summary-value">{{ todayCount }}</p>
        </div>
      </div>

      <div class="tech-list-card">
        <div class="tech-list-header">
          <h2>Danh sách kỹ thuật viên</h2>
          <p class="hint">Bấm để lọc nhanh theo kỹ thuật viên.</p>
        </div>
        <div class="tech-list">
          <div
            v-for="tech in technicianSummaries"
            :key="tech.id"
            class="tech-item"
            :class="{ active: String(technicianFilter) === String(tech.id) }"
            @click="technicianFilter = String(tech.id)"
          >
            <div>
              <p class="tech-name">{{ tech.name }}</p>
              <p class="tech-meta">{{ tech.phone || tech.email }}</p>
            </div>
            <span class="tech-status" :class="tech.status === 'Dang xu ly' ? 'busy' : 'idle'">
              {{ tech.status }} ({{ tech.active }})
            </span>
          </div>
          <button class="ghost tiny" type="button" @click="technicianFilter = ''">Bỏ lọc</button>
        </div>
      </div>

      <div class="tabs">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          class="tab"
          :class="{ active: activeTab === tab.key }"
          @click="activeTab = tab.key"
        >
          <span class="tab-label">{{ tab.label }}</span>
          <span class="tab-count">{{ tab.count }}</span>
        </button>
      </div>

      <div class="filters">
        <div class="filter">
          <label>Trạng thái</label>
          <select v-model="statusFilter">
            <option value="">Tất cả</option>
            <option value="Moi">Mới tạo</option>
            <option value="Dang xu ly">Đang xử lý</option>
            <option value="Doi vat tu">Đã hẹn</option>
            <option value="Hoan thanh">Hoàn thành</option>
          </select>
        </div>
        <div class="filter">
          <label>Thời gian</label>
          <select v-model="timeFilter">
            <option value="">Tất cả</option>
            <option value="today">Hôm nay</option>
            <option value="week">Tuần này</option>
          </select>
        </div>
        <div class="filter">
          <label>Tòa nhà</label>
          <select v-model="buildingFilter">
            <option value="">Tất cả</option>
            <option v-for="b in buildings" :key="b" :value="b">{{ b }}</option>
          </select>
        </div>
        <div class="filter">
          <label>Kỹ thuật viên</label>
          <select v-model="technicianFilter">
            <option value="">Tất cả</option>
            <option v-for="tech in technicians" :key="tech.id" :value="String(tech.id)">
              {{ tech.name }}
            </option>
          </select>
        </div>
        <div class="filter">
          <label>Ưu tiên</label>
          <select v-model="priorityFilter" disabled>
            <option value="">Chưa có dữ liệu</option>
          </select>
        </div>
        <div class="filter search">
          <label>Tìm kiếm</label>
          <input
            v-model="search"
            type="search"
            placeholder="Mã job, cư dân, số điện thoại, căn hộ"
          />
        </div>
      </div>

      <div class="job-grid">
        <div v-if="!visibleJobs.length && !loading" class="empty-state">
          Không có công việc phù hợp.
        </div>

        <article
          v-for="job in visibleJobs"
          :key="job.id"
          class="job-card"
          @click="openJob(job.id)"
        >
          <div class="job-card-header">
            <div>
              <h3>{{ job.title }}</h3>
              <p class="job-sub">{{ job.code }}</p>
            </div>
            <span class="status" :class="statusClass(job)">{{ statusLabel(job) }}</span>
          </div>

          <div class="job-meta">
            <div>
              <span class="meta-label">Địa điểm</span>
              <span class="meta-value">{{ job.location || '-' }}</span>
            </div>
            <div>
              <span class="meta-label">Lịch hẹn</span>
              <span class="meta-value">{{ formatDateTime(job.appointment_at) }}</span>
            </div>
          </div>

          <p class="job-desc">{{ job.description || 'Không có mô tả' }}</p>

          <div class="job-actions">
            <button
              v-if="canStart(job)"
              class="ghost"
              type="button"
              @click.stop="updateStatus(job.id, 'Dang xu ly')"
            >
              Bắt đầu
            </button>
            <button
              v-if="canPause(job)"
              class="ghost"
              type="button"
              @click.stop="updateStatus(job.id, 'Doi vat tu')"
            >
              Tạm dừng
            </button>
            <button
              v-if="canComplete(job)"
              class="primary"
              type="button"
              @click.stop="openComplete(job)"
            >
              Hoàn thành
            </button>
            <a
              v-if="job.resident_phone"
              class="ghost"
              :href="`tel:${job.resident_phone}`"
              @click.stop
            >
              Gọi
            </a>
            <button class="ghost" type="button" @click.stop="openJob(job.id)">Xem</button>
          </div>
        </article>
      </div>

      <div class="create-bar">
        <button class="primary" type="button" @click="showCreate = true">+ Tạo công việc</button>
      </div>

      <div v-if="showCreate" class="modal-backdrop" @click.self="closeCreate">
        <div class="modal">
          <div class="modal-header">
            <h2>Tạo công việc mới</h2>
            <button class="ghost" type="button" @click="closeCreate">Đóng</button>
          </div>
          <div class="modal-body">
            <div class="field">
              <label for="code">Mã công việc</label>
              <input id="code" v-model="form.code" placeholder="JOB-0004" />
            </div>
            <div class="field">
              <label for="title">Tiêu đề</label>
              <input id="title" v-model="form.title" placeholder="Mô tả ngắn" />
            </div>
            <div class="field">
              <label for="location">Địa điểm</label>
              <input id="location" v-model="form.location" placeholder="Tòa, tầng, căn hộ" />
            </div>
            <div class="field">
              <label for="appointment">Lịch hẹn</label>
              <input id="appointment" v-model="form.appointment_at" type="datetime-local" />
            </div>
            <div class="field">
              <label for="resident_name">Cư dân</label>
              <input id="resident_name" v-model="form.resident_name" placeholder="Họ tên cư dân" />
            </div>
            <div class="field">
              <label for="resident_phone">Số điện thoại</label>
              <input id="resident_phone" v-model="form.resident_phone" placeholder="Số điện thoại" />
            </div>
            <div class="field">
              <label for="technician_id">Kỹ thuật viên</label>
              <select id="technician_id" v-model="form.technician_id">
                <option value="">Tự gán / chưa chọn</option>
                <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
                  {{ tech.name }}
                </option>
              </select>
            </div>
            <div class="field">
              <label for="description">Mô tả</label>
              <textarea id="description" v-model="form.description"></textarea>
            </div>
            <div class="field">
              <label for="report_image">Ảnh báo hỏng</label>
              <input id="report_image" type="file" accept="image/*" @change="onReportImageChange" />
              <p v-if="reportImageName" class="hint">Đã chọn: {{ reportImageName }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button class="ghost" type="button" @click="closeCreate">Hủy</button>
            <button class="primary" type="button" @click="createJob" :disabled="saving">Tạo công việc</button>
          </div>
        </div>
      </div>

      <div v-if="showComplete" class="modal-backdrop" @click.self="closeComplete">
        <div class="modal">
          <div class="modal-header">
            <h2>Hoàn thành công việc</h2>
            <button class="ghost" type="button" @click="closeComplete">Đóng</button>
          </div>
          <div class="modal-body">
            <p class="hint">Tải ảnh sau khi sửa xong để lưu hồ sơ.</p>
            <div class="field">
              <label for="completion_image">Ảnh hoàn thành</label>
              <input id="completion_image" type="file" accept="image/*" @change="onCompletionImageChange" />
              <p v-if="completionImageName" class="hint">Đã chọn: {{ completionImageName }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button class="ghost" type="button" @click="closeComplete">Hủy</button>
            <button class="primary" type="button" @click="completeJob" :disabled="saving">Xác nhận hoàn thành</button>
          </div>
        </div>
      </div>

      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    </section>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'
import { useAuth } from '../../composables/useAuth'

const router = useRouter()
const { user } = useAuth()
const jobs = ref([])
const technicians = ref([])
const loading = ref(false)
const errorMessage = ref('')
const search = ref('')
const statusFilter = ref('')
const timeFilter = ref('')
const buildingFilter = ref('')
const technicianFilter = ref('')
const priorityFilter = ref('')
const activeTab = ref('all')
const saving = ref(false)
const showCreate = ref(false)
const showComplete = ref(false)
const selectedCompleteJob = ref(null)
const reportImageName = ref('')
const completionImageName = ref('')

const form = ref({
  code: '',
  title: '',
  description: '',
  location: '',
  appointment_at: '',
  resident_name: '',
  resident_phone: '',
  status: 'Moi',
  report_image: '',
  technician_id: '',
})

const baseJobs = computed(() => {
  if (!user.value?.id) return jobs.value
  return jobs.value.filter((job) => job.technician_id === user.value.id)
})

const normalizeDate = (value) => {
  if (!value) return null
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return null
  return date
}

const isSameDay = (a, b) => a.toDateString() === b.toDateString()

const isOverdue = (job) => {
  if (job.status === 'Hoan thanh') return false
  const date = normalizeDate(job.appointment_at)
  if (!date) return false
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return date < today
}

const isToday = (job) => {
  if (job.status === 'Hoan thanh') return false
  const date = normalizeDate(job.appointment_at)
  if (!date) return false
  return isSameDay(date, new Date())
}

const isUpcoming = (job) => {
  if (job.status === 'Hoan thanh') return false
  const date = normalizeDate(job.appointment_at)
  if (!date) return false
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return date > today
}

const buildings = computed(() => {
  const set = new Set()
  baseJobs.value.forEach((job) => {
    if (!job.location) return
    const first = job.location.split('-')[0].split(',')[0].trim()
    if (first) set.add(first)
  })
  return Array.from(set)
})

const activeCount = computed(() => baseJobs.value.filter((job) => job.status !== 'Hoan thanh').length)

const todayCount = computed(() => baseJobs.value.filter((job) => isToday(job)).length)

const activeJobsByTech = computed(() => {
  const map = new Map()
  jobs.value.forEach((job) => {
    if (!job.technician_id) return
    if (job.status === 'Hoan thanh') return
    map.set(job.technician_id, (map.get(job.technician_id) || 0) + 1)
  })
  return map
})

const technicianSummaries = computed(() =>
  technicians.value.map((tech) => {
    const active = activeJobsByTech.value.get(tech.id) || 0
    return {
      ...tech,
      active,
      status: active > 0 ? 'Dang xu ly' : 'Cho viec',
    }
  }),
)

const filtered = computed(() => {
  const keyword = search.value.trim().toLowerCase()
  const now = new Date()
  const weekEnd = new Date()
  weekEnd.setDate(weekEnd.getDate() + 7)

  return baseJobs.value.filter((job) => {
    const matchStatus = statusFilter.value ? job.status === statusFilter.value : true
    const matchBuilding = buildingFilter.value
      ? (job.location || '').toLowerCase().includes(buildingFilter.value.toLowerCase())
      : true
    const matchTechnician = technicianFilter.value
      ? String(job.technician_id || '') === technicianFilter.value
      : true

    let matchTime = true
    const date = normalizeDate(job.appointment_at)
    if (timeFilter.value === 'today') {
      matchTime = date ? isSameDay(date, now) : false
    }
    if (timeFilter.value === 'week') {
      matchTime = date ? date >= now && date <= weekEnd : false
    }

    const searchText = `${job.code} ${job.resident_name} ${job.resident_phone} ${job.location}`
      .toLowerCase()
      .trim()
    const matchText = keyword ? searchText.includes(keyword) : true

    return matchStatus && matchBuilding && matchTechnician && matchTime && matchText
  })
})

const grouped = computed(() => {
  return {
    all: filtered.value,
    overdue: filtered.value.filter((job) => isOverdue(job)),
    today: filtered.value.filter((job) => isToday(job)),
    upcoming: filtered.value.filter((job) => isUpcoming(job)),
    done: filtered.value.filter((job) => job.status === 'Hoan thanh'),
  }
})

const tabs = computed(() => [
  { key: 'all', label: 'Tất cả', count: grouped.value.all.length },
  { key: 'overdue', label: 'Quá hạn', count: grouped.value.overdue.length },
  { key: 'today', label: 'Hôm nay', count: grouped.value.today.length },
  { key: 'upcoming', label: 'Sắp tới', count: grouped.value.upcoming.length },
  { key: 'done', label: 'Hoàn thành', count: grouped.value.done.length },
])

const visibleJobs = computed(() => {
  const list = grouped.value[activeTab.value] || []
  return [...list].sort((a, b) => {
    const aDate = normalizeDate(a.appointment_at)
    const bDate = normalizeDate(b.appointment_at)
    if (!aDate && !bDate) return 0
    if (!aDate) return 1
    if (!bDate) return -1
    return aDate - bDate
  })
})

const formatDateTime = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  return date.toLocaleString('vi-VN')
}

const statusLabel = (job) => {
  if (isOverdue(job)) return 'Quá hạn'
  switch (job.status) {
    case 'Moi':
      return 'Mới tạo'
    case 'Dang xu ly':
      return 'Đang xử lý'
    case 'Doi vat tu':
      return 'Đã hẹn'
    case 'Hoan thanh':
      return 'Hoàn thành'
    default:
      return job.status
  }
}

const statusClass = (job) => {
  if (isOverdue(job)) return 'overdue'
  switch (job.status) {
    case 'Moi':
      return 'new'
    case 'Dang xu ly':
      return 'progress'
    case 'Doi vat tu':
      return 'scheduled'
    case 'Hoan thanh':
      return 'done'
    default:
      return 'new'
  }
}

const canStart = (job) => job.status === 'Moi' || job.status === 'Doi vat tu'

const canPause = (job) => job.status === 'Dang xu ly'

const canComplete = (job) => job.status === 'Dang xu ly'

const resetForm = () => {
  form.value = {
    code: '',
    title: '',
    description: '',
    location: '',
    appointment_at: '',
    resident_name: '',
    resident_phone: '',
    status: 'Moi',
    report_image: '',
    technician_id: '',
  }
  reportImageName.value = ''
}

const closeCreate = () => {
  showCreate.value = false
}

const openComplete = (job) => {
  selectedCompleteJob.value = job
  completionImageName.value = ''
  showComplete.value = true
}

const closeComplete = () => {
  showComplete.value = false
  selectedCompleteJob.value = null
  completionImageName.value = ''
}

const readFileAsDataUrl = (file) =>
  new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(reader.result)
    reader.onerror = reject
    reader.readAsDataURL(file)
  })

const onReportImageChange = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  reportImageName.value = file.name
  form.value.report_image = await readFileAsDataUrl(file)
}

const onCompletionImageChange = async (event) => {
  const file = event.target.files?.[0]
  if (!file || !selectedCompleteJob.value) return
  completionImageName.value = file.name
  selectedCompleteJob.value.completion_image = await readFileAsDataUrl(file)
}
const updateStatus = async (id, status) => {
  try {
    await apiFetch(`/api/jobs/${id}/status`, {
      method: 'PATCH',
      body: JSON.stringify({ status }),
    })
    await loadJobs()
  } catch (error) {
    errorMessage.value = error.message
  }
}

const completeJob = async () => {
  if (!selectedCompleteJob.value) return
  saving.value = true
  errorMessage.value = ''
  try {
    await apiFetch(`/api/jobs/${selectedCompleteJob.value.id}/complete`, {
      method: 'POST',
      body: JSON.stringify({
        completion_image: selectedCompleteJob.value.completion_image || null,
      }),
    })
    await loadJobs()
    closeComplete()
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    saving.value = false
  }
}
const createJob = async () => {
  saving.value = true
  errorMessage.value = ''
  if (
    !form.value.code ||
    !form.value.title ||
    !form.value.location ||
    !form.value.resident_name ||
    !form.value.resident_phone
  ) {
    errorMessage.value = 'Vui lòng nhập đầy đủ mã, tiêu đề, địa điểm và thông tin cư dân.'
    saving.value = false
    return
  }
  try {
    const payload = { ...form.value }
    if (!payload.technician_id && user.value?.role === 'technician') {
      payload.technician_id = user.value.id
    }
    await apiFetch('/api/jobs', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
    await loadJobs()
    activeTab.value = 'all'
    resetForm()
    showCreate.value = false
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    saving.value = false
  }
}

const loadJobs = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const result = await apiFetch('/api/jobs')
    jobs.value = result.data || []
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const loadTechnicians = async () => {
  try {
    const result = await apiFetch('/api/users?role=technician')
    technicians.value = result.data || []
  } catch {
  }
}

const openJob = (id) => {
  router.push(`/technician/jobs/${id}`)
}

onMounted(async () => {
  await loadJobs()
  await loadTechnicians()
})
</script>

<style scoped>
.tech-page {
  display: grid;
  gap: 16px;
}

.summary-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}

.summary-card {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 14px;
  padding: 14px 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.summary-card.accent {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
}

.summary-label {
  margin: 0;
  font-size: 12px;
  opacity: 0.8;
}

.summary-value {
  margin: 6px 0 0;
  font-size: 22px;
  font-weight: 700;
}

.tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tab {
  border: 1px solid #d8e2f0;
  background: #fff;
  border-radius: 999px;
  padding: 6px 12px;
  font-size: 13px;
  cursor: pointer;
  display: inline-flex;
  gap: 6px;
  align-items: center;
}

.tab.active {
  border-color: #1d4ed8;
  color: #1d4ed8;
  background: #eff6ff;
}

.tab-count {
  font-weight: 700;
}

.filters {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 10px;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.06);
  padding: 12px;
  border-radius: 12px;
}

.filter {
  display: grid;
  gap: 6px;
}

.filter label {
  font-size: 12px;
  color: #64748b;
}

.filter select,
.filter input {
  border: 1px solid #d5dbe7;
  border-radius: 10px;
  padding: 8px 10px;
  font-size: 14px;
}

.filter.search {
  grid-column: span 2;
}

.job-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 12px;
}

.create-bar {
  display: flex;
  justify-content: flex-end;
}

.job-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 14px;
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
  cursor: pointer;
}

.job-card h3 {
  margin: 0 0 4px;
  font-size: 16px;
}

.job-sub {
  margin: 0;
  font-size: 12px;
  color: #94a3b8;
}

.job-card-header {
  display: flex;
  justify-content: space-between;
  gap: 8px;
  align-items: flex-start;
}

.status {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

.status.new {
  background: #dbeafe;
  color: #1d4ed8;
}

.status.progress {
  background: #fef9c3;
  color: #a16207;
}

.status.scheduled {
  background: #ffedd5;
  color: #c2410c;
}

.status.overdue {
  background: #fee2e2;
  color: #b91c1c;
}

.status.done {
  background: #dcfce7;
  color: #15803d;
}

.job-meta {
  display: grid;
  gap: 8px;
  margin: 10px 0;
}

.meta-label {
  display: block;
  font-size: 11px;
  color: #94a3b8;
}

.meta-value {
  font-size: 13px;
  color: #1f2937;
}

.job-desc {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 10px;
  min-height: 38px;
}

.job-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tech-list-card {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 14px;
  padding: 14px 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.tech-list-header {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 12px;
}

.tech-list {
  display: grid;
  gap: 8px;
  margin-top: 10px;
}

.tech-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  background: #fff;
}

.tech-item.active {
  border-color: #2563eb;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.15);
}

.tech-name {
  margin: 0 0 4px;
  font-weight: 600;
}

.tech-meta {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}

.tech-status {
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.tech-status.busy {
  background: #fef9c3;
  color: #a16207;
}

.tech-status.idle {
  background: #dcfce7;
  color: #15803d;
}

.ghost.tiny {
  width: fit-content;
  padding: 4px 10px;
  font-size: 12px;
}

.ghost,
.primary {
  border-radius: 10px;
  padding: 6px 10px;
  font-size: 12px;
  border: 1px solid #d5dbe7;
  background: #fff;
  cursor: pointer;
  text-decoration: none;
  color: #1f2937;
}

.primary {
  background: #1d4ed8;
  border-color: #1d4ed8;
  color: #fff;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: grid;
  place-items: center;
  padding: 20px;
  z-index: 50;
}

.modal {
  width: min(640px, 100%);
  background: #fff;
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 24px 48px rgba(15, 23, 42, 0.2);
}

.modal-header,
.modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.modal-body {
  margin-top: 12px;
  display: grid;
  gap: 10px;
}

.empty-state {
  padding: 16px;
  text-align: center;
  color: #64748b;
}

.error {
  margin-top: 12px;
  color: #b91c1c;
  font-weight: 600;
}

@media (max-width: 1100px) {
  .filters {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .filter.search {
    grid-column: span 2;
  }
}

@media (max-width: 700px) {
  .summary-row {
    grid-template-columns: 1fr;
  }

  .filters {
    grid-template-columns: 1fr;
  }

  .filter.search {
    grid-column: span 1;
  }
}
</style>
