<template>
  <TechnicianLayout>
    <section class="tech-page">
      <div class="page-title">
        <h1>Job được giao</h1>
        <p>Theo dõi job, lịch hẹn và trạng thái xử lý hiện tại.</p>
      </div>

      <div class="content-grid">
        <div class="card span-two">
          <h2>Danh sách job</h2>
          <div class="job-list">
            <div
              v-for="job in filteredJobs"
              :key="job.id"
              class="job-item"
              @click="openJob(job.id)"
            >
              <p class="job-item-title">{{ job.title }}</p>
              <div class="job-meta">
                <span>{{ job.status }}</span>
                <span>{{ formatDate(job.appointment_at) }}</span>
              </div>
            </div>
            <p v-if="!filteredJobs.length && !loading" class="notice">Chưa có job nào được giao.</p>
          </div>
        </div>

        <div class="card">
          <h2>Thông tin nhanh</h2>
          <p class="hint">Tổng số job</p>
          <p class="metric">{{ filteredJobs.length }}</p>
          <p class="hint" style="margin-top: 12px;">Sẵn sàng xử lý</p>
          <p class="metric">{{ activeCount }}</p>
        </div>

        <div class="card span-two">
          <h2>Danh sách kỹ thuật viên</h2>
          <p class="hint">Tình trạng rảnh/bận theo số job đang xử lý.</p>
          <div class="tech-list">
            <div v-for="tech in technicianSummaries" :key="tech.id" class="tech-item">
              <div>
                <p class="tech-name">{{ tech.name }}</p>
                <p class="tech-meta">{{ tech.phone || tech.email }}</p>
              </div>
              <span class="tech-status" :class="tech.status === 'Dang xu ly' ? 'busy' : 'idle'">
                {{ tech.status }} ({{ tech.active }})
              </span>
            </div>
            <p v-if="!technicianSummaries.length && !loading" class="notice">Chưa có kỹ thuật viên.</p>
          </div>
        </div>
      </div>

      <p v-if="errorMessage" class="error" style="margin-top: 12px;">{{ errorMessage }}</p>
    </section>
  </TechnicianLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import TechnicianLayout from '../../layouts/TechnicianLayout.vue'
import { apiFetch } from '../../composables/useApi'
import { useAuth } from '../../composables/useAuth'

const router = useRouter()
const { user } = useAuth()
const jobs = ref([])
const technicians = ref([])
const loading = ref(false)
const errorMessage = ref('')

const formatDate = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

const filteredJobs = computed(() => {
  if (!user.value?.id) return jobs.value
  return jobs.value.filter((job) => job.technician_id === user.value.id)
})

const activeCount = computed(() =>
  filteredJobs.value.filter((job) => job.status !== 'Hoan thanh').length
)

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
  background: #fff;
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
