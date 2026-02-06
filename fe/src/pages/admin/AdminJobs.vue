<template>
  <AdminLayout>
    <div class="page-title">
      <h1>Quản lý job</h1>
      <p>Tạo, cập nhật, giao kỹ thuật viên và theo dõi trạng thái.</p>
    </div>

    <div class="content-grid">
      <div class="card span-two">
        <h2>Danh sách job</h2>
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
              <span>{{ job.code }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <h2>Thông tin job</h2>
        <div class="field">
          <label for="code">Mã job</label>
          <input id="code" v-model="form.code" placeholder="JOB-0001" />
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
          <input id="resident_name" v-model="form.resident_name" />
        </div>
        <div class="field">
          <label for="resident_phone">SĐT</label>
          <input id="resident_phone" v-model="form.resident_phone" />
        </div>
        <div class="field">
          <label for="description">Mô tả</label>
          <textarea id="description" v-model="form.description"></textarea>
        </div>
        <div class="field">
          <label for="status">Trạng thái</label>
          <select id="status" v-model="form.status">
            <option value="Moi">Moi</option>
            <option value="Dang xu ly">Dang xu ly</option>
            <option value="Doi vat tu">Doi vat tu</option>
            <option value="Hoan thanh">Hoan thanh</option>
          </select>
        </div>
        <button class="btn" @click="saveJob">{{ selectedJob ? 'Cập nhật' : 'Tạo mới' }}</button>
        <button v-if="selectedJob" class="btn btn-danger" style="margin-top: 8px;" @click="deleteJob">
          Xóa job
        </button>
      </div>

      <div class="card">
        <h2>Giao kỹ thuật viên</h2>
        <div class="field">
          <label for="technician">Kỹ thuật viên</label>
          <select id="technician" v-model="assignTechnicianId">
            <option value="">Chọn kỹ thuật viên</option>
            <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
              {{ tech.name }}
            </option>
          </select>
        </div>
        <button class="btn" @click="assignTechnician">Giao việc</button>
      </div>
      <div class="card">
        <h2>Danh sach ky thuat vien</h2>
        <div class="tech-list">
          <div
            v-for="tech in technicianSummaries"
            :key="tech.id"
            class="tech-item"
            :class="{ active: String(assignTechnicianId) === String(tech.id) }"
            @click="assignTechnicianId = tech.id"
          >
            <div>
              <p class="tech-name">{{ tech.name }}</p>
              <p class="tech-meta">{{ tech.phone || tech.email }}</p>
            </div>
            <span class="tech-status" :class="tech.status === 'Dang xu ly' ? 'busy' : 'idle'">
              {{ tech.status }} ({{ tech.active }})
            </span>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import AdminLayout from '../../layouts/AdminLayout.vue'
import { apiFetch } from '../../composables/useApi'

const jobs = ref([])
const technicians = ref([])
const selectedJob = ref(null)
const assignTechnicianId = ref('')

const form = ref({
  code: '',
  title: '',
  description: '',
  location: '',
  appointment_at: '',
  resident_name: '',
  resident_phone: '',
  status: 'Moi',
})

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

const loadJobs = async () => {
  const result = await apiFetch('/api/jobs')
  jobs.value = result.data
}

const loadTechnicians = async () => {
  const result = await apiFetch('/api/users?role=technician')
  technicians.value = result.data
}

const selectJob = (job) => {
  selectedJob.value = job
  form.value = {
    code: job.code,
    title: job.title,
    description: job.description || '',
    location: job.location,
    appointment_at: job.appointment_at ? job.appointment_at.slice(0, 16) : '',
    resident_name: job.resident_name,
    resident_phone: job.resident_phone,
    status: job.status || 'Moi',
  }
  assignTechnicianId.value = job.technician_id || ''
}

const resetForm = () => {
  selectedJob.value = null
  form.value = {
    code: '',
    title: '',
    description: '',
    location: '',
    appointment_at: '',
    resident_name: '',
    resident_phone: '',
    status: 'Moi',
  }
  assignTechnicianId.value = ''
}

const saveJob = async () => {
  const payload = { ...form.value }
  if (selectedJob.value) {
    await apiFetch(`/api/jobs/${selectedJob.value.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  } else {
    await apiFetch('/api/jobs', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  }
  await loadJobs()
  resetForm()
}

const deleteJob = async () => {
  if (!selectedJob.value) return
  await apiFetch(`/api/jobs/${selectedJob.value.id}`, { method: 'DELETE' })
  await loadJobs()
  resetForm()
}

const assignTechnician = async () => {
  if (!selectedJob.value || !assignTechnicianId.value) return
  await apiFetch(`/api/jobs/${selectedJob.value.id}/assign`, {
    method: 'PATCH',
    body: JSON.stringify({ technician_id: assignTechnicianId.value }),
  })
  await loadJobs()
}

onMounted(async () => {
  await loadJobs()
  await loadTechnicians()
})
</script>

<style scoped>
.tech-list {
  display: grid;
  gap: 8px;
  margin: 10px 0 12px;
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
</style>
